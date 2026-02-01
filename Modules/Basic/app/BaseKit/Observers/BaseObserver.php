<?php

namespace Modules\Basic\BaseKit\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseObserver
{
    public static function creating($model)
    {
        $auth_phone = 'CONSOLE';
        if (auth()->check()) {
            /**
             * @var $model Model
             */
            if (!app()->runningInConsole()) {
                $auth_phone = filament()?->getCurrentPanel()?->getId() . '_' . auth()?->user()?->phone_number;
            }
        }
//        if ($model->hasAttribute('updated_by')){
        $model->updated_by = $auth_phone;
//        }
//        if ($model->hasAttribute('updated_at')){
        $model->updated_at = date('Y-m-d H:i:s');
//        }
//        if ($model->hasAttribute('created_by')){
        if (!empty($model->created_by)) {
            $model->created_by = $auth_phone;
        }
//        }
//        if ($model->hasAttribute('created_at')){
        $model->created_at = date('Y-m-d H:i:s');
//        }

//        if ($model->hasAttribute('id')) {
        if ($model->hasCast('id') && $model->getCasts()['id'] == 'string') {
            $model->setIncrementing(false);
            $model->id = Str::uuid()->toString();
        }
//        }
    }

    public static function updating($model)
    {
        /**
         * @var $model Model
         */
        $auth_phone = 'CONSOLE';
        if (auth()->check()) {
            /**
             * @var $model Model
             */
            if (!app()->runningInConsole()) {
                $auth_phone = filament()?->getCurrentPanel()?->getId() . '_' . auth()?->user()?->phone_number;
            }
        }
//        if ($model->hasAttribute('updated_by')){
        if (empty($model->updated_by)) {
            $model->updated_by = $auth_phone;
        }
//        }
//        if ($model->hasAttribute('updated_at')){
        $model->updated_at = date('Y-m-d H:i:s');
//        }
    }


    /**
     * Handle the Post "deleting" event.
     * This runs before the model is deleted
     */
    public function deleting($model): void
    {
        /**
         * @var $model Model
         */
        $auth_phone = 'CONSOLE';
        if (auth()->check()) {
            /**
             * @var $model Model
             */
            if (!app()->runningInConsole()) {
                $auth_phone = filament()?->getCurrentPanel()?->getId() . '_' . auth()?->user()?->phone_number;
            }
        }
//        if ($model->hasAttribute('deleted_at')){
        $model->deleted_at = date('Y-m-d H:i:s');
//        }
//        if ($model->hasAttribute('deleted_by')){
        $model->deleted_by = $auth_phone;
//        }
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted($model): void
    {
        // Log the deletion for audit purposes
        logger($model::class . ' deleted');
    }

    /**
     * Helper method to update search index
     */
    private function updateSearchIndex($model): void
    {
        // Implementation would depend on your search solution
        // This could be Elasticsearch, Algolia, etc.
    }

    /**
     * Helper method to remove from search index
     */
    private function removeFromSearchIndex($model): void
    {
        // Remove from search index implementation
    }
}
