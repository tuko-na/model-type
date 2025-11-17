<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'group_id', 'model_number', 'name', 'manufacturer', 'category', 'purchase_date', 'purchase_condition', 'status', 'notes', 'warranty_expires_on', 'price'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
