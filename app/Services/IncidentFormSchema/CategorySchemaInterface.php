<?php

namespace App\Services\IncidentFormSchema;

/**
 * カテゴリ別フォームスキーマのインターフェース
 */
interface CategorySchemaInterface
{
    /**
     * カテゴリ識別子を取得
     */
    public static function getCategory(): string;

    /**
     * カテゴリ表示名を取得
     */
    public static function getCategoryLabel(): string;

    /**
     * フォームフィールドのスキーマを取得
     * @return array<string, array>
     */
    public static function getFields(): array;

    /**
     * バリデーションルールを取得
     * @return array<string, string|array>
     */
    public static function getValidationRules(): array;
}
