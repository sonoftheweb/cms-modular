<?php

namespace App\Models;

use App\Models\Scopes\InstanceScope;
use Illuminate\Database\Eloquent\Model;

class ScopableModel extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new InstanceScope);
    }
}
