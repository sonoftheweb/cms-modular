<?php

namespace App\Models;

use App\Models\Scopes\InstanceScope;
use App\Models\Traits\Scopable;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Model;

class ScopableModel extends Model
{
    use Scopable;

    protected static function booted()
    {
        parent::booted();
        $class = get_called_class();
        //$className = basename(str_replace('\\','/', $class));
        $class::observe(new ModelObserver);
        static::addGlobalScope(new InstanceScope);
    }
}
