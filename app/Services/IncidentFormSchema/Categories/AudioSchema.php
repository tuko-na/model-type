<?php

namespace App\Services\IncidentFormSchema\Categories;

use App\Services\IncidentFormSchema\CategorySchemaInterface;
use App\Services\IncidentFormSchema\FieldType;

/**
 * オーディオ機器用のフォームスキーマ
 */
class AudioSchema implements CategorySchemaInterface
{
    public static function getCategory(): string
    {
        return 'audio';
    }

    public static function getCategoryLabel(): string
    {
        return 'オーディオ機器';
    }

    public static function getFields(): array
    {
        return [
            'connection_type' => [
                'label' => '接続方式',
                'type' => FieldType::BUTTON_GROUP,
                'options' => [
                    'bluetooth' => ['label' => 'Bluetooth', 'icon' => 'bluetooth'],
                    'wired' => ['label' => '有線', 'icon' => 'cable'],
                    'wifi' => ['label' => 'Wi-Fi', 'icon' => 'wifi'],
                    'usb' => ['label' => 'USB', 'icon' => 'usb'],
                ],
            ],
            'noise_type' => [
                'label' => 'ノイズの種類',
                'type' => FieldType::SELECT,
                'options' => [
                    'none' => 'ノイズなし',
                    'white_noise' => 'ホワイトノイズ（サー音）',
                    'hum_noise' => 'ハムノイズ（ブーン音）',
                    'crackling' => 'パチパチ音',
                    'intermittent' => '断続的な途切れ',
                    'other' => 'その他',
                ],
                'icon' => 'volume-x',
            ],
            'volume_issue' => [
                'label' => '音量の問題',
                'type' => FieldType::BUTTON_GROUP,
                'options' => [
                    'none' => ['label' => '問題なし', 'icon' => 'volume-2'],
                    'too_low' => ['label' => '音が小さい', 'icon' => 'volume-1'],
                    'too_loud' => ['label' => '音が大きすぎ', 'icon' => 'volume'],
                    'no_sound' => ['label' => '音が出ない', 'icon' => 'volume-x'],
                ],
            ],
            'battery_issue' => [
                'label' => 'バッテリーの問題（該当する場合）',
                'type' => FieldType::BOOLEAN,
                'icon' => 'battery-low',
                'labels' => ['なし', 'あり'],
            ],
            'pairing_issue' => [
                'label' => 'ペアリング/接続の問題',
                'type' => FieldType::BOOLEAN,
                'icon' => 'link',
                'labels' => ['なし', 'あり'],
            ],
        ];
    }

    public static function getValidationRules(): array
    {
        return [
            'details.connection_type' => 'nullable|string',
            'details.noise_type' => 'nullable|string',
            'details.volume_issue' => 'nullable|string',
            'details.battery_issue' => 'nullable|boolean',
            'details.pairing_issue' => 'nullable|boolean',
        ];
    }
}
