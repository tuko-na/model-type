<?php

namespace App\Services\IncidentFormSchema\Categories;

use App\Services\IncidentFormSchema\CategorySchemaInterface;
use App\Services\IncidentFormSchema\FieldType;

/**
 * 家電製品用のフォームスキーマ
 */
class HomeApplianceSchema implements CategorySchemaInterface
{
    public static function getCategory(): string
    {
        return 'home_appliance';
    }

    public static function getCategoryLabel(): string
    {
        return '家電製品';
    }

    public static function getFields(): array
    {
        return [
            'installation_environment' => [
                'label' => '設置環境',
                'type' => FieldType::BUTTON_GROUP,
                'options' => [
                    'indoor_normal' => ['label' => '室内（通常）', 'icon' => 'home'],
                    'indoor_humid' => ['label' => '室内（湿気多）', 'icon' => 'droplet'],
                    'outdoor' => ['label' => '屋外', 'icon' => 'sun'],
                    'garage' => ['label' => 'ガレージ等', 'icon' => 'warehouse'],
                ],
            ],
            'power_supply' => [
                'label' => '電源状態',
                'type' => FieldType::SELECT,
                'options' => [
                    'normal' => '正常',
                    'unstable' => '不安定（たまに落ちる）',
                    'no_power' => '電源が入らない',
                    'trips_breaker' => 'ブレーカーが落ちる',
                ],
                'icon' => 'plug',
            ],
            'usage_frequency' => [
                'label' => '使用頻度',
                'type' => FieldType::BUTTON_GROUP,
                'options' => [
                    'daily' => ['label' => '毎日', 'icon' => 'calendar'],
                    'weekly' => ['label' => '週数回', 'icon' => 'calendar-days'],
                    'monthly' => ['label' => '月数回', 'icon' => 'calendar-range'],
                    'rarely' => ['label' => 'ほぼ使わない', 'icon' => 'calendar-off'],
                ],
            ],
            'unusual_smell' => [
                'label' => '異臭の有無',
                'type' => FieldType::BOOLEAN,
                'icon' => 'wind',
                'labels' => ['なし', 'あり'],
            ],
            'unusual_heat' => [
                'label' => '異常な発熱',
                'type' => FieldType::BOOLEAN,
                'icon' => 'thermometer',
                'labels' => ['なし', 'あり'],
            ],
            'error_display' => [
                'label' => 'エラー表示（ある場合）',
                'type' => FieldType::TEXT,
                'placeholder' => '例: E01, F2など',
                'icon' => 'alert-circle',
            ],
        ];
    }

    public static function getValidationRules(): array
    {
        return [
            'details.installation_environment' => 'nullable|string',
            'details.power_supply' => 'nullable|string',
            'details.usage_frequency' => 'nullable|string',
            'details.unusual_smell' => 'nullable|boolean',
            'details.unusual_heat' => 'nullable|boolean',
            'details.error_display' => 'nullable|string|max:50',
        ];
    }
}
