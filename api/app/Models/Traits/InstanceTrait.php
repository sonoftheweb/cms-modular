<?php


namespace App\Models\Traits;


trait InstanceTrait
{
    public function instance()
    {
        return $this->belongsTo('\App\Models\Instance');
    }
}
