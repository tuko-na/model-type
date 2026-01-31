<?php

namespace App\Services\IncidentFormSchema\Categories;

use App\Services\IncidentFormSchema\CategorySchemaInterface;
use App\Services\IncidentFormSchema\FieldType;

/**
 * スマートフォン用のフォームスキーマ
 */
class SmartphoneSchema implements CategorySchemaInterface
{
    public static function getCategory(): string
    {
        return 'smartphone';
    }

    public static function getCategoryLabel(): string
    {
        return 'スマートフォン';
    }

    public static function getFields(): array
    {
        return [
            'battery_health' => [
                'label' => 'バッテリー最大容量',
                'type' => FieldType::SLIDER,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'unit' => '%',
                'default' => 80,
                'icon' => 'battery-half',
                'help' => '設定 > バッテリー > バッテリーの状態で確認できます',
            ],
            'os_version' => [
                'label' => 'OSバージョン',
                'type' => FieldType::SELECT,
                'options' => [
                    'ios_17' => 'iOS 17.x',
                    'ios_16' => 'iOS 16.x',
                    'ios_15' => 'iOS 15.x以前',
                    'android_14' => 'Android 14',
                    'android_13' => 'Android 13',
                    'android_12' => 'Android 12以前',
                    'other' => 'その他',
                ],
                'icon' => 'cpu',
            ],
            'water_damage' => [
                'label' => '水没・水濡れ経験',
                'type' => FieldType::BOOLEAN,
                'icon' => 'droplet',
                'labels' => ['なし', 'あり'],
            ],
            'screen_condition' => [
                'label' => '画面の状態',
                'type' => FieldType::BUTTON_GROUP,
                'options' => [
                    'perfect' => ['label' => '良好', 'icon' => 'check-circle'],
                    'scratched' => ['label' => '傷あり', 'icon' => 'slash'],
                    'cracked' => ['label' => '割れ', 'icon' => 'x-circle'],
                ],
            ],
            'storage_usage' => [
                'label' => 'ストレージ使用率',
                'type' => FieldType::SLIDER,
                'min' => 0,
                'max' => 100,
                'step' => 5,
                'unit' => '%',
                'default' => 50,
                'icon' => 'hard-drive',
            ],
        ];
    }

    public static function getValidationRules(): array
    {
        return [
            'details.battery_health' => 'nullable|integer|min:0|max:100',
            'details.os_version' => 'nullable|string',
            'details.water_damage' => 'nullable|boolean',
            'details.screen_condition' => 'nullable|string',
            'details.storage_usage' => 'nullable|integer|min:0|max:100',
        ];
    }
}
