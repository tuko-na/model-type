<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    public const INCIDENT_TYPES = [
        'failure' => '故障',
        'maintenance' => 'メンテナンス',
        'damage' => '破損',
        'loss' => '紛失',
    ];

    public const RESOLUTION_TYPES = [
        'repair' => '修理',
        'replacement' => '交換',
        'self_resolved' => '自己解決',
        'unresolved' => '未対応',
    ];

    public const SYMPTOM_TAGS = [
        'power_issue' => '電源が入らない',
        'strange_noise' => '異音がする',
        'no_response' => '反応しない',
        'physical_damage' => '物理的な損傷',
        'software_glitch' => 'ソフトウェアの不具合',
    ];

    protected $fillable = [
        'product_id',
        'group_id',
        'user_id',
        'title',
        'occurred_at',
        'description',
        'cost',
        'incident_type',
        'resolution_type',
        'symptom_tags',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}