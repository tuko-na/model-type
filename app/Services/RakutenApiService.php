<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenApiService
{
    protected string $appId;
    protected string $productSearchUrl;
    protected string $ichibaSearchUrl;

    public function __construct()
    {
        $this->appId = (string) config('services.rakuten.app_id');
        $this->productSearchUrl = config('services.rakuten.product_search_url');
        $this->ichibaSearchUrl = config('services.rakuten.ichiba_search_url');
    }

    /**
     * 製品を検索（製品検索API → 商品検索APIフォールバック）
     * 
     * @param string $keyword 検索キーワード
     * @param int $hits 取得件数（API制限対策で少なめに）
     * @return array
     */
    public function search(string $keyword, int $hits = 5): array
    {
        if (empty($this->appId)) {
            Log::warning('Rakuten API: App ID is not configured');
            return [];
        }

        $keyword = trim($keyword);
        if (empty($keyword) || mb_strlen($keyword) < 2) {
            return [];
        }

        // デモモード（APP環境がlocalかつapp_idが特定値の場合）
        if (config('app.env') === 'local' && config('services.rakuten.demo_mode', false)) {
            return $this->getDemoResults($keyword, $hits);
        }

        // まず製品検索API（商品価格ナビ）を試行
        $results = $this->searchProductApi($keyword, $hits);
        
        if (!empty($results)) {
            return $results;
        }

        // ヒットしなければ商品検索API（楽天市場）にフォールバック
        return $this->searchIchibaApi($keyword, $hits);
    }

    /**
     * デモモード用のモックデータを返す
     */
    protected function getDemoResults(string $keyword, int $hits): array
    {
        $demoData = [
            [
                'source' => 'product_api',
                'product_name' => 'Apple iPhone 15 Pro 256GB',
                'maker_name' => 'Apple',
                'model_number' => 'A3104',
                'genre_id' => '101240',
                'genre_name' => 'スマートフォン・タブレット',
                'rakuten_url' => 'https://product.rakuten.co.jp/product/-/demo1/',
                '_display_image' => 'https://placehold.co/200x200/f0f0f0/666?text=iPhone',
                '_display_price' => 159800,
            ],
            [
                'source' => 'product_api',
                'product_name' => 'Sony WH-1000XM5',
                'maker_name' => 'SONY',
                'model_number' => 'WH-1000XM5',
                'genre_id' => '211742',
                'genre_name' => 'TV・オーディオ・カメラ',
                'rakuten_url' => 'https://product.rakuten.co.jp/product/-/demo2/',
                '_display_image' => 'https://placehold.co/200x200/f0f0f0/666?text=XM5',
                '_display_price' => 44000,
            ],
            [
                'source' => 'ichiba_api',
                'product_name' => 'Microsoft Surface Pro 9',
                'maker_name' => 'Microsoft',
                'model_number' => null,
                'genre_id' => '100026',
                'genre_name' => 'パソコン・周辺機器',
                'rakuten_url' => 'https://item.rakuten.co.jp/demo/surface/',
                '_display_image' => 'https://placehold.co/200x200/f0f0f0/666?text=Surface',
                '_display_price' => 188000,
            ],
        ];

        // キーワードで簡易フィルター
        $filtered = collect($demoData)
            ->filter(fn($item) => 
                str_contains(strtolower($item['product_name']), strtolower($keyword)) ||
                str_contains(strtolower($item['maker_name'] ?? ''), strtolower($keyword))
            )
            ->values()
            ->take($hits)
            ->toArray();

        // フィルター結果がなければ全部返す（デモなので）
        return !empty($filtered) ? $filtered : array_slice($demoData, 0, $hits);
    }

    /**
     * 製品検索API（商品価格ナビ）
     * クリーンなデータが取得可能
     */
    protected function searchProductApi(string $keyword, int $hits): array
    {
        try {
            $response = Http::timeout(5)->get($this->productSearchUrl, [
                'applicationId' => $this->appId,
                'keyword' => $keyword,
                'hits' => $hits,
                'formatVersion' => 2,
            ]);

            if (!$response->successful()) {
                Log::debug('Rakuten Product API: Response not successful', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $data = $response->json();
            
            if (empty($data['Products'])) {
                return [];
            }

            return collect($data['Products'])
                ->map(function ($item) {
                    $product = $item['Product'] ?? $item;
                    return [
                        'source' => 'product_api',
                        'product_name' => $product['productName'] ?? '',
                        'maker_name' => $product['makerName'] ?? null,
                        'model_number' => $product['modelNumber'] ?? null,
                        'genre_id' => (string) ($product['genreId'] ?? ''),
                        'genre_name' => $product['genreName'] ?? '',
                        'rakuten_url' => $product['productUrlPC'] ?? '',
                        // 画像・価格は参照のみ（保存しない）
                        '_display_image' => $product['mediumImageUrl'] ?? null,
                        '_display_price' => $product['averagePrice'] ?? null,
                    ];
                })
                ->filter(fn($item) => !empty($item['product_name']))
                ->values()
                ->toArray();

        } catch (\Exception $e) {
            Log::debug('Rakuten Product API error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 商品検索API（楽天市場）
     * フォールバック用 - 販売中商品から情報を抽出
     */
    protected function searchIchibaApi(string $keyword, int $hits): array
    {
        try {
            $response = Http::timeout(5)->get($this->ichibaSearchUrl, [
                'applicationId' => $this->appId,
                'keyword' => $keyword,
                'hits' => $hits,
                'formatVersion' => 2,
                'sort' => '-reviewCount', // レビュー件数順で信頼性高いものを優先
            ]);

            if (!$response->successful()) {
                Log::debug('Rakuten Ichiba API: Response not successful', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $data = $response->json();
            
            if (empty($data['Items'])) {
                return [];
            }

            return collect($data['Items'])
                ->map(function ($item) {
                    $product = $item['Item'] ?? $item;
                    
                    // ジャンルIDからジャンル名を取得（簡易マッピング）
                    $genreId = (string) ($product['genreId'] ?? '');
                    
                    return [
                        'source' => 'ichiba_api',
                        'product_name' => $this->cleanProductName($product['itemName'] ?? ''),
                        'maker_name' => null, // 市場APIでは正確なメーカー名は取れない
                        'model_number' => null, // 市場APIでは正確な型番は取れない
                        'genre_id' => $genreId,
                        'genre_name' => '', // 別途ジャンルAPIで解決する必要あり
                        'rakuten_url' => $product['itemUrl'] ?? '',
                        // 画像・価格は参照のみ（保存しない）
                        '_display_image' => $product['mediumImageUrls'][0]['imageUrl'] ?? null,
                        '_display_price' => $product['itemPrice'] ?? null,
                    ];
                })
                ->filter(fn($item) => !empty($item['product_name']))
                ->unique('product_name')
                ->values()
                ->take($hits)
                ->toArray();

        } catch (\Exception $e) {
            Log::debug('Rakuten Ichiba API error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 商品名のクリーニング（市場API用）
     * 【送料無料】などの不要な修飾語を除去
     */
    protected function cleanProductName(string $name): string
    {
        // よくある不要パターンを除去
        $patterns = [
            '/【[^】]*】/',           // 【送料無料】など
            '/\[[^\]]*\]/',           // [正規品]など
            '/（[^）]*）/',           // （税込）など
            '/\s+/',                  // 連続スペース
        ];
        
        $cleaned = preg_replace($patterns, ' ', $name);
        return trim($cleaned);
    }

    /**
     * APIが有効かチェック
     */
    public function isConfigured(): bool
    {
        return !empty($this->appId);
    }
}
