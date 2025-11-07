<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'group_id', 'model_number', 'name', 'manufacturer', 'category', 'purchase_date', 'status'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
