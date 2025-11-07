<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelSuggestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'model_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'model_number',
        'name',
        'manufacturer',
        'category',
    ];
}
