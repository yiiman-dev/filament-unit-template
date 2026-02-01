<?php

namespace Modules\Basic\Models;

use Filament\Facades\Filament;
use Illuminate\Cache\HasCacheLock;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Basic\Observers\Contracts\ObserverCacheTrait;
use mysql_xdevapi\Result;

class APIConnection extends Connection
{
    public function __construct(array $config)
    {
//        $grammar_claas=$this->sendRequest('','get_grammar');
//        $this->setQueryGrammar(new $grammar_claas($this));
        $this->pdo = [];

        // First we will setup the default properties. We keep track of the DB
        // name we are connected to since it is needed when some reflective
        // type commands are run such as checking whether a table exists.
        $this->database = '';

        $this->tablePrefix = '';

        $this->config = $config;

        // We need to initialize a query grammar and the query post processors
        // which are both very important parts of the database abstractions
        // so we initialize these to their default values while starting.
        $this->useDefaultQueryGrammar();

        $this->useDefaultPostProcessor();
    }


    /**
     * Get a new query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return new APIQuery(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }

    /**
     * Run a select statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            // For select statements, we'll simply execute the query and return an array
            // of the database result set. Each element in the array will be a single
            // row from the database table, and will either be an array or objects.
            return $this->sendRequest($query, 'direct_query_select');


        });
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return true;
            }
            $this->recordsHaveBeenModified();
            $result= $this->sendRequest($query, 'direct_query_statement',$values=$bindings);
            return $result;
        });
    }





    /**
     * ارسال درخواست به API
     * @param string $action
     * @param array|string $columns
     * @return array|null
     */
    protected function sendRequest(string $query, string $action,array $bind_values=[])
    {

        $url = $this->config['remote_url'] . "/models";

        $data = [
            'action' => $action,
            'data' =>
                ['query' => $query, 'remote_connection' => $this->config['remote_connection'],'bind_values'=>$bind_values],
            'actor_number' => Filament::auth()->user()->phone_number,
        ];
        $response = Http::post($url, $data);

        if ($response->successful()) {
            $result=$response->json();
            if (!empty($result)){
                return $data = $result['data'];
            }else{
                return $result;
            }
        }
        $trace = !empty($response->json()['trace']) ?
            [$response->json()['trace']] :
            [];
        Log::error("Remote model call Error Tracing", $trace);
        throw new \Exception("Remote model Response:  " . $response->json()['error'], $response->getStatusCode());
        return null;
    }
}
