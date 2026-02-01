<?php

namespace PipLines\GetData\StateCity;

use Modules\Basic\BaseKit\BaseService;
use Units\StateCity\Common\Models\City;

class GetCitiesData extends BaseService
{
    public function handle(array $conditions = []): self
    {
        $query = City::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        $cities = $query->get();

        $this->setSuccessResponse(compact('cities'));

        return $this;
    }
}
