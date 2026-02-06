<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Incident;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MassDataSeeder extends Seeder
{
    /**
     * 大量のダミーデータを生成するSeeder
     */
    public function run(): void
    {
        $this->command->info('大量のダミーデータを生成中...');

        // 1. テストユーザーを作成
        $mainUser = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // 追加のユーザーを50人作成
        $users = User::factory(50)->create();
        $users->push($mainUser);

        $this->command->info('✓ ユーザー: ' . $users->count() . '人作成');

        // 2. グループを作成
        $groups = collect();
        
        // メインユーザーのグループ
        $mainGroup = Group::create([
            'name' => 'マイ家電',
            'owner_id' => $mainUser->id,
        ]);
        $mainUser->groups()->attach($mainGroup->id);
        $groups->push($mainGroup);

        // 各ユーザーに1-3個のグループを作成
        foreach ($users as $user) {
            $numGroups = rand(1, 3);
            $groupNames = ['自宅', '会社', '実家', 'オフィス', '別荘'];
            
            for ($i = 0; $i < $numGroups; $i++) {
                $group = Group::create([
                    'name' => $groupNames[array_rand($groupNames)] . 'の家電',
                    'owner_id' => $user->id,
                ]);
                $user->groups()->attach($group->id);
                $groups->push($group);
            }
        }

        $this->command->info('✓ グループ: ' . $groups->count() . '件作成');

        // 3. 製品データ（実際の製品ベース）
        $productTemplates = $this->getProductTemplates();

        // 4. 製品を作成（各グループに5-20個）
        $productCount = 0;
        foreach ($groups as $group) {
            $numProducts = rand(5, 20);
            
            for ($i = 0; $i < $numProducts; $i++) {
                $template = $productTemplates[array_rand($productTemplates)];
                
                // 購入日をランダムに（過去5年以内）
                $purchaseDate = Carbon::now()->subDays(rand(1, 1825));
                
                // 価格にランダムなバラつきを追加（±20%）
                $priceVariation = $template['price'] * (rand(80, 120) / 100);
                
                Product::create([
                    'group_id' => $group->id,
                    'model_number' => $template['model_number'],
                    'name' => $template['name'],
                    'manufacturer' => $template['manufacturer'],
                    'category' => $template['category'],
                    'purchase_date' => $purchaseDate,
                    'purchase_condition' => collect(['new', 'used', 'refurbished'])->random(),
                    'status' => collect(['active', 'inactive', 'disposed'])->random(),
                    'price' => round($priceVariation),
                    'warranty_expires_on' => $purchaseDate->copy()->addYear(),
                ]);
                
                $productCount++;
            }
        }

        $this->command->info('✓ 製品: ' . $productCount . '件作成');

        // 5. インシデントを作成（製品の30%にインシデント）
        $products = Product::all();
        $incidentCount = 0;

        foreach ($products as $product) {
            // 30%の確率でインシデントを持つ
            if (rand(1, 100) <= 30) {
                $numIncidents = rand(1, 4);
                
                for ($i = 0; $i < $numIncidents; $i++) {
                    $incident = $this->createIncident($product);
                    if ($incident) {
                        $incidentCount++;
                    }
                }
            }
        }

        $this->command->info('✓ インシデント: ' . $incidentCount . '件作成');

        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('ダミーデータの生成が完了しました！');
        $this->command->info('========================================');
        $this->command->info('ログイン情報: test@example.com / password');
        $this->command->info('ユーザー数: ' . $users->count());
        $this->command->info('グループ数: ' . $groups->count());
        $this->command->info('製品数: ' . $productCount);
        $this->command->info('インシデント数: ' . $incidentCount);
    }

    /**
     * 製品テンプレートを取得
     */
    private function getProductTemplates(): array
    {
        return [
            // Smartphone
            ['model_number' => 'iPhone 15 Pro', 'name' => 'iPhone 15 Pro', 'manufacturer' => 'Apple', 'category' => 'Smartphone', 'price' => 159800],
            ['model_number' => 'iPhone 15', 'name' => 'iPhone 15', 'manufacturer' => 'Apple', 'category' => 'Smartphone', 'price' => 124800],
            ['model_number' => 'iPhone 14', 'name' => 'iPhone 14', 'manufacturer' => 'Apple', 'category' => 'Smartphone', 'price' => 112800],
            ['model_number' => 'iPhone SE 3rd', 'name' => 'iPhone SE (第3世代)', 'manufacturer' => 'Apple', 'category' => 'Smartphone', 'price' => 62800],
            ['model_number' => 'Galaxy S24 Ultra', 'name' => 'Galaxy S24 Ultra', 'manufacturer' => 'Samsung', 'category' => 'Smartphone', 'price' => 189800],
            ['model_number' => 'Galaxy S24', 'name' => 'Galaxy S24', 'manufacturer' => 'Samsung', 'category' => 'Smartphone', 'price' => 124800],
            ['model_number' => 'Galaxy Z Fold5', 'name' => 'Galaxy Z Fold5', 'manufacturer' => 'Samsung', 'category' => 'Smartphone', 'price' => 252800],
            ['model_number' => 'Pixel 8 Pro', 'name' => 'Google Pixel 8 Pro', 'manufacturer' => 'Google', 'category' => 'Smartphone', 'price' => 159900],
            ['model_number' => 'Pixel 8', 'name' => 'Google Pixel 8', 'manufacturer' => 'Google', 'category' => 'Smartphone', 'price' => 112900],
            ['model_number' => 'Pixel 7a', 'name' => 'Google Pixel 7a', 'manufacturer' => 'Google', 'category' => 'Smartphone', 'price' => 62700],
            ['model_number' => 'Xperia 1 V', 'name' => 'Xperia 1 V', 'manufacturer' => 'Sony', 'category' => 'Smartphone', 'price' => 194700],
            ['model_number' => 'Xperia 5 V', 'name' => 'Xperia 5 V', 'manufacturer' => 'Sony', 'category' => 'Smartphone', 'price' => 139700],
            ['model_number' => 'AQUOS R8 pro', 'name' => 'AQUOS R8 pro', 'manufacturer' => 'Sharp', 'category' => 'Smartphone', 'price' => 191600],
            
            // Laptop
            ['model_number' => 'MacBook Air M3', 'name' => 'MacBook Air M3', 'manufacturer' => 'Apple', 'category' => 'Laptop', 'price' => 164800],
            ['model_number' => 'MacBook Air M2', 'name' => 'MacBook Air M2', 'manufacturer' => 'Apple', 'category' => 'Laptop', 'price' => 148800],
            ['model_number' => 'MacBook Pro 14 M3', 'name' => 'MacBook Pro 14インチ M3', 'manufacturer' => 'Apple', 'category' => 'Laptop', 'price' => 248800],
            ['model_number' => 'MacBook Pro 16 M3', 'name' => 'MacBook Pro 16インチ M3 Pro', 'manufacturer' => 'Apple', 'category' => 'Laptop', 'price' => 398800],
            ['model_number' => 'ThinkPad X1 Carbon', 'name' => 'ThinkPad X1 Carbon Gen 11', 'manufacturer' => 'Lenovo', 'category' => 'Laptop', 'price' => 229900],
            ['model_number' => 'ThinkPad T14s', 'name' => 'ThinkPad T14s Gen 4', 'manufacturer' => 'Lenovo', 'category' => 'Laptop', 'price' => 189900],
            ['model_number' => 'XPS 13 Plus', 'name' => 'Dell XPS 13 Plus', 'manufacturer' => 'Dell', 'category' => 'Laptop', 'price' => 209800],
            ['model_number' => 'XPS 15', 'name' => 'Dell XPS 15', 'manufacturer' => 'Dell', 'category' => 'Laptop', 'price' => 279800],
            ['model_number' => 'Surface Laptop 5', 'name' => 'Surface Laptop 5', 'manufacturer' => 'Microsoft', 'category' => 'Laptop', 'price' => 177980],
            ['model_number' => 'Surface Pro 9', 'name' => 'Surface Pro 9', 'manufacturer' => 'Microsoft', 'category' => 'Laptop', 'price' => 193380],
            ['model_number' => 'VAIO SX14', 'name' => 'VAIO SX14', 'manufacturer' => 'VAIO', 'category' => 'Laptop', 'price' => 234800],
            ['model_number' => 'dynabook GZ/HW', 'name' => 'dynabook GZ/HW', 'manufacturer' => 'Dynabook', 'category' => 'Laptop', 'price' => 189800],
            ['model_number' => 'HP Spectre x360', 'name' => 'HP Spectre x360 14', 'manufacturer' => 'HP', 'category' => 'Laptop', 'price' => 219800],
            ['model_number' => 'ASUS ZenBook 14', 'name' => 'ASUS ZenBook 14 OLED', 'manufacturer' => 'ASUS', 'category' => 'Laptop', 'price' => 149800],
            
            // Tablet
            ['model_number' => 'iPad Pro 12.9 M2', 'name' => 'iPad Pro 12.9インチ M2', 'manufacturer' => 'Apple', 'category' => 'Tablet', 'price' => 172800],
            ['model_number' => 'iPad Pro 11 M2', 'name' => 'iPad Pro 11インチ M2', 'manufacturer' => 'Apple', 'category' => 'Tablet', 'price' => 124800],
            ['model_number' => 'iPad Air M1', 'name' => 'iPad Air (第5世代)', 'manufacturer' => 'Apple', 'category' => 'Tablet', 'price' => 92800],
            ['model_number' => 'iPad 10th', 'name' => 'iPad (第10世代)', 'manufacturer' => 'Apple', 'category' => 'Tablet', 'price' => 68800],
            ['model_number' => 'iPad mini 6', 'name' => 'iPad mini (第6世代)', 'manufacturer' => 'Apple', 'category' => 'Tablet', 'price' => 78800],
            ['model_number' => 'Galaxy Tab S9 Ultra', 'name' => 'Galaxy Tab S9 Ultra', 'manufacturer' => 'Samsung', 'category' => 'Tablet', 'price' => 189800],
            ['model_number' => 'Galaxy Tab S9', 'name' => 'Galaxy Tab S9', 'manufacturer' => 'Samsung', 'category' => 'Tablet', 'price' => 124800],
            ['model_number' => 'Surface Pro 9', 'name' => 'Surface Pro 9', 'manufacturer' => 'Microsoft', 'category' => 'Tablet', 'price' => 176000],
            
            // TV
            ['model_number' => 'BRAVIA XR A95L', 'name' => 'BRAVIA XR A95L 65型', 'manufacturer' => 'Sony', 'category' => 'TV', 'price' => 550000],
            ['model_number' => 'BRAVIA XR X90L', 'name' => 'BRAVIA XR X90L 55型', 'manufacturer' => 'Sony', 'category' => 'TV', 'price' => 220000],
            ['model_number' => 'REGZA Z870M', 'name' => 'REGZA Z870M 55型', 'manufacturer' => 'TVS REGZA', 'category' => 'TV', 'price' => 198000],
            ['model_number' => 'REGZA M550M', 'name' => 'REGZA M550M 50型', 'manufacturer' => 'TVS REGZA', 'category' => 'TV', 'price' => 89800],
            ['model_number' => 'AQUOS XLED EP1', 'name' => 'AQUOS XLED EP1 65型', 'manufacturer' => 'Sharp', 'category' => 'TV', 'price' => 330000],
            ['model_number' => 'AQUOS 4K EN1', 'name' => 'AQUOS 4K EN1 50型', 'manufacturer' => 'Sharp', 'category' => 'TV', 'price' => 89800],
            ['model_number' => 'VIERA MZ2500', 'name' => 'VIERA MZ2500 55型', 'manufacturer' => 'Panasonic', 'category' => 'TV', 'price' => 330000],
            ['model_number' => 'VIERA MX950', 'name' => 'VIERA MX950 55型', 'manufacturer' => 'Panasonic', 'category' => 'TV', 'price' => 198000],
            ['model_number' => 'Neo QLED QN90C', 'name' => 'Neo QLED QN90C 55型', 'manufacturer' => 'Samsung', 'category' => 'TV', 'price' => 220000],
            ['model_number' => 'LG OLED C3', 'name' => 'LG OLED C3 55型', 'manufacturer' => 'LG', 'category' => 'TV', 'price' => 238000],
            
            // Appliance (家電)
            ['model_number' => 'NA-LX129BL', 'name' => 'ドラム式洗濯乾燥機', 'manufacturer' => 'Panasonic', 'category' => 'Appliance', 'price' => 298000],
            ['model_number' => 'NA-FA12V2', 'name' => '全自動洗濯機 12kg', 'manufacturer' => 'Panasonic', 'category' => 'Appliance', 'price' => 148000],
            ['model_number' => 'ES-W114', 'name' => 'ドラム式洗濯乾燥機', 'manufacturer' => 'Sharp', 'category' => 'Appliance', 'price' => 268000],
            ['model_number' => 'BD-STX120HL', 'name' => 'ビッグドラム', 'manufacturer' => 'Hitachi', 'category' => 'Appliance', 'price' => 258000],
            ['model_number' => 'NR-F609WPX', 'name' => '冷蔵庫 600L', 'manufacturer' => 'Panasonic', 'category' => 'Appliance', 'price' => 348000],
            ['model_number' => 'R-HXCC62T', 'name' => '冷蔵庫 617L', 'manufacturer' => 'Hitachi', 'category' => 'Appliance', 'price' => 358000],
            ['model_number' => 'SJ-MF46M', 'name' => '冷蔵庫 457L', 'manufacturer' => 'Sharp', 'category' => 'Appliance', 'price' => 218000],
            ['model_number' => 'NE-BS8A', 'name' => 'ビストロ スチームオーブンレンジ', 'manufacturer' => 'Panasonic', 'category' => 'Appliance', 'price' => 128000],
            ['model_number' => 'MRO-W10A', 'name' => 'ヘルシーシェフ', 'manufacturer' => 'Hitachi', 'category' => 'Appliance', 'price' => 98000],
            ['model_number' => 'RE-WF264', 'name' => 'ヘルシオ', 'manufacturer' => 'Sharp', 'category' => 'Appliance', 'price' => 138000],
            ['model_number' => 'MC-SB85K', 'name' => 'コードレススティック掃除機', 'manufacturer' => 'Panasonic', 'category' => 'Appliance', 'price' => 78000],
            ['model_number' => 'Dyson V15', 'name' => 'Dyson V15 Detect', 'manufacturer' => 'Dyson', 'category' => 'Appliance', 'price' => 108900],
            ['model_number' => 'Roomba j7+', 'name' => 'Roomba j7+', 'manufacturer' => 'iRobot', 'category' => 'Appliance', 'price' => 129800],
            ['model_number' => 'CS-X634D2', 'name' => 'Eolia Xシリーズ エアコン', 'manufacturer' => 'Panasonic', 'category' => 'Appliance', 'price' => 298000],
            ['model_number' => 'RAS-X56N2', 'name' => '白くまくん Xシリーズ', 'manufacturer' => 'Hitachi', 'category' => 'Appliance', 'price' => 278000],
            ['model_number' => 'MSZ-ZW5624S', 'name' => '霧ヶ峰 Zシリーズ', 'manufacturer' => 'Mitsubishi', 'category' => 'Appliance', 'price' => 288000],
            
            // Other (ヘッドホン、カメラ等)
            ['model_number' => 'WH-1000XM5', 'name' => 'ワイヤレスノイキャンヘッドホン', 'manufacturer' => 'Sony', 'category' => 'Other', 'price' => 49500],
            ['model_number' => 'WF-1000XM5', 'name' => 'ワイヤレスノイキャンイヤホン', 'manufacturer' => 'Sony', 'category' => 'Other', 'price' => 41800],
            ['model_number' => 'AirPods Pro 2', 'name' => 'AirPods Pro (第2世代)', 'manufacturer' => 'Apple', 'category' => 'Other', 'price' => 39800],
            ['model_number' => 'AirPods Max', 'name' => 'AirPods Max', 'manufacturer' => 'Apple', 'category' => 'Other', 'price' => 84800],
            ['model_number' => 'Bose QC Ultra', 'name' => 'QuietComfort Ultra Headphones', 'manufacturer' => 'Bose', 'category' => 'Other', 'price' => 59400],
            ['model_number' => 'α7 IV', 'name' => 'α7 IV ミラーレス一眼', 'manufacturer' => 'Sony', 'category' => 'Other', 'price' => 328900],
            ['model_number' => 'α7C II', 'name' => 'α7C II ミラーレス一眼', 'manufacturer' => 'Sony', 'category' => 'Other', 'price' => 295900],
            ['model_number' => 'EOS R6 Mark II', 'name' => 'EOS R6 Mark II', 'manufacturer' => 'Canon', 'category' => 'Other', 'price' => 359700],
            ['model_number' => 'EOS R8', 'name' => 'EOS R8', 'manufacturer' => 'Canon', 'category' => 'Other', 'price' => 259600],
            ['model_number' => 'Z8', 'name' => 'Nikon Z8', 'manufacturer' => 'Nikon', 'category' => 'Other', 'price' => 599500],
            ['model_number' => 'Z6 III', 'name' => 'Nikon Z6 III', 'manufacturer' => 'Nikon', 'category' => 'Other', 'price' => 429000],
            ['model_number' => 'Nintendo Switch OLED', 'name' => 'Nintendo Switch 有機ELモデル', 'manufacturer' => 'Nintendo', 'category' => 'Other', 'price' => 37980],
            ['model_number' => 'PS5', 'name' => 'PlayStation 5', 'manufacturer' => 'Sony', 'category' => 'Other', 'price' => 66980],
            ['model_number' => 'Xbox Series X', 'name' => 'Xbox Series X', 'manufacturer' => 'Microsoft', 'category' => 'Other', 'price' => 59978],
            ['model_number' => 'Apple Watch Ultra 2', 'name' => 'Apple Watch Ultra 2', 'manufacturer' => 'Apple', 'category' => 'Other', 'price' => 128800],
            ['model_number' => 'Apple Watch Series 9', 'name' => 'Apple Watch Series 9', 'manufacturer' => 'Apple', 'category' => 'Other', 'price' => 59800],
        ];
    }

    /**
     * インシデントを作成
     */
    private function createIncident(Product $product): ?Incident
    {
        if (!$product->purchase_date) {
            return null;
        }

        $purchaseDate = Carbon::parse($product->purchase_date);
        $now = Carbon::now();
        
        if ($purchaseDate->isAfter($now)) {
            return null;
        }

        // インシデント発生日（購入後）
        $daysOwned = $purchaseDate->diffInDays($now);
        if ($daysOwned < 1) {
            return null;
        }
        
        $occurredAt = $purchaseDate->copy()->addDays(rand(1, $daysOwned));

        // インシデントタイプと関連データ
        $incidentTypes = ['failure', 'maintenance', 'damage', 'loss'];
        $incidentType = $incidentTypes[array_rand($incidentTypes)];

        $titles = [
            'failure' => [
                '電源が入らなくなった',
                '動作が不安定になった',
                '突然シャットダウンする',
                '画面が映らなくなった',
                'バッテリーが膨張した',
                '異音がするようになった',
                '充電できなくなった',
                'ボタンが反応しなくなった',
            ],
            'maintenance' => [
                '定期メンテナンス',
                'ファームウェアアップデート',
                'クリーニング実施',
                '部品交換（消耗品）',
                'バッテリー交換',
                'フィルター交換',
            ],
            'damage' => [
                '落下による破損',
                '画面にひびが入った',
                '水濡れ被害',
                '外装に傷がついた',
                'コネクタ部分が破損',
            ],
            'loss' => [
                '紛失',
                '盗難',
            ],
        ];

        $resolutionTypes = ['repair', 'replacement', 'self_resolved', 'unresolved'];
        $resolutionType = $resolutionTypes[array_rand($resolutionTypes)];

        $severities = ['low', 'medium', 'high', 'critical'];
        $severity = $severities[array_rand($severities)];

        // コストの計算（タイプと製品価格に基づく）
        $cost = 0;
        if ($resolutionType === 'repair') {
            $cost = round($product->price * (rand(5, 30) / 100));
        } elseif ($resolutionType === 'replacement') {
            $cost = round($product->price * (rand(30, 100) / 100));
        }

        $title = $titles[$incidentType][array_rand($titles[$incidentType])];

        return Incident::create([
            'product_id' => $product->id,
            'group_id' => $product->group_id,
            'user_id' => null,
            'title' => $title,
            'occurred_at' => $occurredAt,
            'description' => $this->generateDescription($incidentType, $title),
            'cost' => $cost,
            'incident_type' => $incidentType,
            'resolution_type' => $resolutionType,
            'severity' => $severity,
        ]);
    }

    /**
     * 説明文を生成
     */
    private function generateDescription(string $type, string $title): string
    {
        $descriptions = [
            'failure' => [
                '使用中に突然{title}。メーカーサポートに連絡して対応を相談した。',
                '朝起きたら{title}状態だった。保証期間内だったため無償修理を依頼。',
                '数日前から調子が悪かったが、{title}。修理に出すことにした。',
                '購入してからあまり日が経っていないのに{title}。初期不良の可能性あり。',
            ],
            'maintenance' => [
                '定期的なメンテナンスとして{title}を実施。',
                '動作が重くなってきたため{title}を行った。',
                'マニュアルに従って{title}を実施。',
            ],
            'damage' => [
                '不注意で{title}。修理費用がかかった。',
                '子供が触って{title}。今後は気をつける。',
                '予期せぬ事故により{title}。',
            ],
            'loss' => [
                '外出先で{title}してしまった。',
                '引っ越しの際に{title}した可能性。',
            ],
        ];

        $templates = $descriptions[$type] ?? ['内容: {title}'];
        $template = $templates[array_rand($templates)];

        return str_replace('{title}', $title, $template);
    }
}
