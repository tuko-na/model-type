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

    public const SEVERITY_LEVELS = [
        'low' => ['label' => '軽微', 'color' => 'green', 'icon' => 'info'],
        'medium' => ['label' => '中程度', 'color' => 'yellow', 'icon' => 'alert-triangle'],
        'high' => ['label' => '重大', 'color' => 'orange', 'icon' => 'alert-circle'],
        'critical' => ['label' => '致命的', 'color' => 'red', 'icon' => 'x-octagon'],
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
        'details',
        'severity',
    ];

    protected $casts = [
        'details' => 'array',
        'occurred_at' => 'date',
        'cost' => 'integer',
    ];

    protected $appends = [
        'incident_type_label',
        'resolution_type_label',
        'severity_label',
        'severity_color',
    ];

    public function getIncidentTypeLabelAttribute(): string
    {
        return self::INCIDENT_TYPES[$this->incident_type] ?? $this->incident_type ?? '---';
    }

    public function getResolutionTypeLabelAttribute(): string
    {
        return self::RESOLUTION_TYPES[$this->resolution_type] ?? $this->resolution_type ?? '---';
    }

    public function getSeverityLabelAttribute(): string
    {
        return self::SEVERITY_LEVELS[$this->severity]['label'] ?? $this->severity ?? '---';
    }

    public function getSeverityColorAttribute(): string
    {
        return self::SEVERITY_LEVELS[$this->severity]['color'] ?? 'gray';
    }

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