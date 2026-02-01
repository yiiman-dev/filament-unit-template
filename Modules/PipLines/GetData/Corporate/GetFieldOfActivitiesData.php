<?php

namespace PipLines\GetData\Corporate;

use Modules\Basic\BaseKit\BaseService;
use Units\Corporates\FieldOfActivity\Common\Models\FieldOfActivityModel;

class GetFieldOfActivitiesData extends BaseService
{
    public function handle(array $conditions = []): self
    {
        $query = FieldOfActivityModel::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        $fieldOfActivities = $query->get();

        $this->setSuccessResponse(compact('fieldOfActivities'));

        return $this;
    }
}
