<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'group_id',
        'model_number',
        'name',
        'manufacturer',
        'genre_id',
        'genre_name',
        'rakuten_url',
        'purchase_date',
        'purchase_condition',
        'useful_life',
        'status',
        'notes',
        'warranty_expires_on',
        'price',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * 楽天リンクがあるかチェック
     */
    public function hasRakutenUrl(): bool
    {
        return !empty($this->rakuten_url);
    }
}