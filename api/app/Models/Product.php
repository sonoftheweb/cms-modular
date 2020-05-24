<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'stripe_product_identifier',
        'name',
        'statement_descriptor',
        'unit_label',
        'active',
        'created',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function plans()
    {
        return $this->hasMany(Plan::class, 'product_id', 'stripe_product_identifier');
    }
}
