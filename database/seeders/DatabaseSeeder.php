<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // グループを作成
        $group = Group::create([
            'name' => 'マイ家電',
            'owner_id' => $user->id,
        ]);

        // ユーザーをグループに紐付け
        $user->groups()->attach($group->id);

        // テスト用製品を作成（各カテゴリ）
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'model_number' => 'A3104',
                'manufacturer' => 'Apple',
                'category' => 'smartphone',
                'purchase_date' => '2024-09-22',
                'status' => 'active',
                'price' => 159800,
            ],
            [
                'name' => 'MacBook Air M3',
                'model_number' => 'MLY33J/A',
                'manufacturer' => 'Apple',
                'category' => 'pc',
                'purchase_date' => '2024-03-15',
                'status' => 'active',
                'price' => 164800,
            ],
            [
                'name' => 'AirPods Pro 2',
                'model_number' => 'MQD83J/A',
                'manufacturer' => 'Apple',
                'category' => 'audio',
                'purchase_date' => '2023-12-01',
                'status' => 'active',
                'price' => 39800,
            ],
            [
                'name' => 'AQUOS 4K液晶テレビ',
                'model_number' => '4T-C50EN1',
                'manufacturer' => 'SHARP',
                'category' => 'home_appliance',
                'purchase_date' => '2023-06-10',
                'status' => 'active',
                'price' => 89800,
            ],
            [
                'name' => 'ドラム式洗濯乾燥機',
                'model_number' => 'NA-LX129BL',
                'manufacturer' => 'Panasonic',
                'category' => 'home_appliance',
                'purchase_date' => '2024-01-20',
                'status' => 'active',
                'price' => 298000,
            ],
            [
                'name' => 'Galaxy S24 Ultra',
                'model_number' => 'SM-S928',
                'manufacturer' => 'Samsung',
                'category' => 'smartphone',
                'purchase_date' => '2024-04-11',
                'status' => 'active',
                'price' => 189800,
            ],
        ];

        foreach ($products as $productData) {
            Product::create(array_merge($productData, [
                'group_id' => $group->id,
            ]));
        }

        $this->command->info('テストデータを作成しました:');
        $this->command->info('- ユーザー: test@example.com / password');
        $this->command->info('- グループ: マイ家電');
        $this->command->info('- 製品: ' . count($products) . '件');
    }
}
