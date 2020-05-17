<?php

namespace App\Observers;

use App\Helpers\InstanceHelper;

class ModelObserver
{
    public function creating($model)
    {
        if ($model->instance_id)
            return;

        $instanceId = InstanceHelper::getInstanceId();
        if ($instanceId !== null)
            $model->instance_id = $instanceId;
    }
}
