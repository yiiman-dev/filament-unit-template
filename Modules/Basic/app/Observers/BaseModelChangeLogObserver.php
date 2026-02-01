<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/19/25, 7:22 PM
 */

namespace Modules\Basic\Observers;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Basic\Observers\Contracts\ObserverCacheTrait;
use Modules\Basic\Services\BaseActLogService;

class BaseModelChangeLogObserver
{
    use ObserverCacheTrait;
    protected BaseActLogService|string $actLogService;

    public function __construct()
    {
        $this->actLogService = app( $this->actLogService);
    }


    protected function getActor(Model $model)
    {
        if (!empty($model->remote_actor)){
            return $model->remote_actor;
        }
        if (!empty(Filament::getCurrentPanel()->auth()->user())){
            return Filament::getCurrentPanel()->auth()->user()->phone_number;
        }
        if (!empty($remote_actor=$this->cacheGet($model,'saving'))){
            return $remote_actor;
        }
        return 'unknown_actor';
    }

    public function updating()
    {
        Log::info('Observer Updating');
    }

    public function saving(Model $model): void
    {
        Log::info('Observe saving model '.$model::class.' : ',(array)$model);

        if (!empty($model->remote_actor)){
            $this->cachePut($model, $model->remote_actor,3,'saving');
            unset($model->remote_actor);
        }
        Log::info('Actor is '.$this->getActor($model));
    }

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
//        if (!isset($model->remote_actor)){
//            throw new \Exception('Model '.get_called_class().' should extent from Modules\Basic\BaseKit\Model\BaseModel to active activation Logs');
//        }
        Log::info('Observe created model :'.$model::class,(array)$this);
        $this->actLogService->actLog(
            action: 'create.'.Str::snake(get_class($model)),
            type: 'create',
            targetUrl: $this->getModelUrl($model),
            targetTitle: $this->getModelTitle($model),
            details: [
                'model' => get_class($model),
                'attributes' => $model->getAttributes(),
            ],
            remote_actor_number: $this->getActor($model)
        );
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        Log::info('Observe updated model :'.$model::class,(array)$this);
        $this->actLogService->actLog(
            action: 'update.'.Str::snake(get_class($model)),
            type: 'update',
            targetUrl: $this->getModelUrl($model),
            targetTitle: $this->getModelTitle($model),
            details: [
                'model' => get_class($model),
                'old_attributes' => $model->getOriginal(),
                'new_attributes' => $model->getChanges(),
            ],
             remote_actor_number:$this->getActor($model)
        );
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        Log::info('Observe deleted model :'.$model::class,(array)$this);
        $this->actLogService->actLog(
            action: 'delete.'.Str::snake(get_class($model)),
            type: 'delete',
            targetUrl: $this->getModelUrl($model),
            targetTitle: $this->getModelTitle($model),
            details: [
                'model' => get_class($model),
                'deleted_attributes' => $model->getAttributes(),
            ],
            remote_actor_number: $this->getActor($model)
        );
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        Log::info('Observe restored model :'.$model::class,(array)$this);
        $this->actLogService->actLog(
            action: 'restore.'.Str::snake(get_class($model)),
            type: 'restore',
            targetUrl: $this->getModelUrl($model),
            targetTitle: $this->getModelTitle($model),
            details: [
                'model' => get_class($model),
                'restored_attributes' => $model->getAttributes(),
            ],
            remote_actor_number: $this->getActor($model)
        );
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        Log::info('Observe forceDeleted model :'.$model::class,(array)$this);
        $this->actLogService->actLog(
            action: 'force_delete.'.Str::snake(get_class($model)),
            type: 'force_delete',
            targetUrl: $this->getModelUrl($model),
            targetTitle: $this->getModelTitle($model),
            details: [
                'model' => get_class($model),
                'deleted_attributes' => $model->getAttributes(),
            ],
            remote_actor_number: $this->getActor($model)
        );
    }

    /**
     * Get the URL for the model
     */
    protected function getModelUrl(Model $model): string
    {
        return request()->url();
    }

    /**
     * Get the title for the model
     */
    protected function getModelTitle(Model $model): string
    {
        return class_basename($model) . ' #' . $model->getKey();
    }
}
