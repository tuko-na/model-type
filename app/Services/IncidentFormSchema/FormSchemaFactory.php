<?php

namespace App\Services\IncidentFormSchema;

use App\Services\IncidentFormSchema\Categories\AudioSchema;
use App\Services\IncidentFormSchema\Categories\DefaultSchema;
use App\Services\IncidentFormSchema\Categories\HomeApplianceSchema;
use App\Services\IncidentFormSchema\Categories\PCSchema;
use App\Services\IncidentFormSchema\Categories\SmartphoneSchema;

/**
 * フォームスキーマを管理するファクトリークラス
 * Strategy Patternで製品カテゴリに応じた動的フォームスキーマを提供
 */
class FormSchemaFactory
{
    /**
     * カテゴリ名とスキーマクラスのマッピング
     * @var array<string, class-string<CategorySchemaInterface>>
     */
    protected static array $categoryMap = [
        // スマートフォン関連
        'smartphone' => SmartphoneSchema::class,
        'mobile' => SmartphoneSchema::class,
        'phone' => SmartphoneSchema::class,
        'iphone' => SmartphoneSchema::class,
        'android' => SmartphoneSchema::class,
        'tablet' => SmartphoneSchema::class,
        'ipad' => SmartphoneSchema::class,

        // PC関連
        'pc' => PCSchema::class,
        'computer' => PCSchema::class,
        'laptop' => PCSchema::class,
        'desktop' => PCSchema::class,
        'notebook' => PCSchema::class,
        'macbook' => PCSchema::class,

        // オーディオ関連
        'audio' => AudioSchema::class,
        'headphone' => AudioSchema::class,
        'headphones' => AudioSchema::class,
        'earphone' => AudioSchema::class,
        'speaker' => AudioSchema::class,
        'amplifier' => AudioSchema::class,
        'receiver' => AudioSchema::class,

        // 家電関連
        'home_appliance' => HomeApplianceSchema::class,
        'appliance' => HomeApplianceSchema::class,
        'tv' => HomeApplianceSchema::class,
        'television' => HomeApplianceSchema::class,
        'refrigerator' => HomeApplianceSchema::class,
        'washer' => HomeApplianceSchema::class,
        'dryer' => HomeApplianceSchema::class,
        'microwave' => HomeApplianceSchema::class,
        'air_conditioner' => HomeApplianceSchema::class,
        'vacuum' => HomeApplianceSchema::class,
    ];

    /**
     * 利用可能なカテゴリ一覧を取得
     * @return array<string, string>
     */
    public static function getAvailableCategories(): array
    {
        return [
            'smartphone' => SmartphoneSchema::getCategoryLabel(),
            'pc' => PCSchema::getCategoryLabel(),
            'audio' => AudioSchema::getCategoryLabel(),
            'home_appliance' => HomeApplianceSchema::getCategoryLabel(),
            'default' => DefaultSchema::getCategoryLabel(),
        ];
    }

    /**
     * カテゴリ名からスキーマクラスを取得
     *
     * @param string|null $category
     * @return class-string<CategorySchemaInterface>
     */
    public static function getSchemaClass(?string $category): string
    {
        if (!$category) {
            return DefaultSchema::class;
        }

        $normalizedCategory = strtolower(trim($category));

        return static::$categoryMap[$normalizedCategory] ?? DefaultSchema::class;
    }

    /**
     * カテゴリに応じたフォームフィールドを取得
     *
     * @param string|null $category
     * @return array<string, array>
     */
    public static function getFields(?string $category): array
    {
        $schemaClass = static::getSchemaClass($category);
        return $schemaClass::getFields();
    }

    /**
     * カテゴリに応じたバリデーションルールを取得
     *
     * @param string|null $category
     * @return array<string, string|array>
     */
    public static function getValidationRules(?string $category): array
    {
        $schemaClass = static::getSchemaClass($category);
        return $schemaClass::getValidationRules();
    }

    /**
     * カテゴリラベルを取得
     *
     * @param string|null $category
     * @return string
     */
    public static function getCategoryLabel(?string $category): string
    {
        $schemaClass = static::getSchemaClass($category);
        return $schemaClass::getCategoryLabel();
    }

    /**
     * フィールドのデフォルト値を取得
     *
     * @param string|null $category
     * @return array<string, mixed>
     */
    public static function getDefaultValues(?string $category): array
    {
        $fields = static::getFields($category);
        $defaults = [];

        foreach ($fields as $key => $field) {
            if (isset($field['default'])) {
                $defaults[$key] = $field['default'];
            } elseif ($field['type'] === FieldType::BOOLEAN) {
                $defaults[$key] = false;
            } else {
                $defaults[$key] = null;
            }
        }

        return $defaults;
    }
}
