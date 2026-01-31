<?php

namespace App\Services\IncidentFormSchema\Categories;

use App\Services\IncidentFormSchema\CategorySchemaInterface;
use App\Services\IncidentFormSchema\FieldType;

/**
 * デフォルト（汎用）のフォームスキーマ
 */
class DefaultSchema implements CategorySchemaInterface
{
    public static function getCategory(): string
    {
        return 'default';
    }

    public static function getCategoryLabel(): string
    {
        return 'その他';
    }

    public static function getFields(): array
    {
        return [
            'condition_before' => [
                'label' => '発生前の状態',
                'type' => FieldType::TEXTAREA,
                'placeholder' => '問題発生前の製品の状態を記入してください',
                'icon' => 'file-text',
            ],
            'usage_environment' => [
                'label' => '使用環境',
                'type' => FieldType::SELECT,
                'options' => [
                    'home' => '家庭',
                    'office' => 'オフィス',
                    'outdoor' => '屋外',
                    'other' => 'その他',
                ],
                'icon' => 'map-pin',
            ],
            'physical_damage' => [
                'label' => '外観の損傷',
                'type' => FieldType::BOOLEAN,
                'icon' => 'eye',
                'labels' => ['なし', 'あり'],
            ],
        ];
    }

    public static function getValidationRules(): array
    {
        return [
            'details.condition_before' => 'nullable|string|max:1000',
            'details.usage_environment' => 'nullable|string',
            'details.physical_damage' => 'nullable|boolean',
        ];
    }
}
