<?php

namespace App\Services\IncidentFormSchema\Categories;

use App\Services\IncidentFormSchema\CategorySchemaInterface;
use App\Services\IncidentFormSchema\FieldType;

/**
 * PC用のフォームスキーマ
 */
class PCSchema implements CategorySchemaInterface
{
    public static function getCategory(): string
    {
        return 'pc';
    }

    public static function getCategoryLabel(): string
    {
        return 'パソコン';
    }

    public static function getFields(): array
    {
        return [
            'os_type' => [
                'label' => 'OS種別',
                'type' => FieldType::BUTTON_GROUP,
                'options' => [
                    'windows' => ['label' => 'Windows', 'icon' => 'layout'],
                    'mac' => ['label' => 'macOS', 'icon' => 'apple'],
                    'linux' => ['label' => 'Linux', 'icon' => 'terminal'],
                    'chrome' => ['label' => 'ChromeOS', 'icon' => 'chrome'],
                ],
            ],
            'os_version' => [
                'label' => 'OSバージョン',
                'type' => FieldType::SELECT,
                'options' => [
                    'win11' => 'Windows 11',
                    'win10' => 'Windows 10',
                    'macos_14' => 'macOS Sonoma',
                    'macos_13' => 'macOS Ventura',
                    'macos_12' => 'macOS Monterey以前',
                    'other' => 'その他',
                ],
                'icon' => 'cpu',
            ],
            'ram_size' => [
                'label' => 'メモリ容量',
                'type' => FieldType::SELECT,
                'options' => [
                    '4' => '4GB',
                    '8' => '8GB',
                    '16' => '16GB',
                    '32' => '32GB',
                    '64' => '64GB以上',
                ],
                'icon' => 'memory',
            ],
            'storage_type' => [
                'label' => 'ストレージ種別',
                'type' => FieldType::BUTTON_GROUP,
                'options' => [
                    'ssd' => ['label' => 'SSD', 'icon' => 'zap'],
                    'hdd' => ['label' => 'HDD', 'icon' => 'hard-drive'],
                    'both' => ['label' => '両方', 'icon' => 'layers'],
                ],
            ],
            'peripheral_issue' => [
                'label' => '周辺機器の問題',
                'type' => FieldType::BOOLEAN,
                'icon' => 'usb',
                'labels' => ['なし', 'あり'],
            ],
            'error_code' => [
                'label' => 'エラーコード（表示されていれば）',
                'type' => FieldType::TEXT,
                'placeholder' => '例: 0x80070005',
                'icon' => 'alert-triangle',
            ],
        ];
    }

    public static function getValidationRules(): array
    {
        return [
            'details.os_type' => 'nullable|string',
            'details.os_version' => 'nullable|string',
            'details.ram_size' => 'nullable|string',
            'details.storage_type' => 'nullable|string',
            'details.peripheral_issue' => 'nullable|boolean',
            'details.error_code' => 'nullable|string|max:100',
        ];
    }
}
