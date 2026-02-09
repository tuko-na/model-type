<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\RakutenApiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateProduct extends Component
{
    // ジャンルカテゴリ定数
    public const GENRE_CATEGORIES = [
        'キッチン家電' => [
            '調理器具（炊飯器・電子レンジ等）',
            '飲料・コーヒー関連',
            'フードプロセッサー類',
            '大型家電（冷蔵庫・冷凍庫等）',
            'キッチン家電その他',
        ],
        '生活家電' => [
            '掃除・洗濯',
            '衣類ケア',
            '通信・住宅設備',
            '電池・充電器',
            '生活家電その他',
        ],
        '季節・空調家電' => [
            '冷房・扇風機',
            '暖房器具',
            '空気調整（加湿器・除湿器等）',
            '季節家電その他',
        ],
        '美容・健康家電' => [
            '理美容',
            '健康管理',
            'リラクゼーション',
            '美容・健康その他',
        ],
        'AV機器・カメラ・デジタル家電' => [
            'テレビ・レコーダー',
            'オーディオ',
            'カメラ',
            'デジタル家電その他',
        ],
    ];

    // ステップ管理（1: 検索, 2: 詳細入力）
    public int $step = 1;

    // 楽天サジェスト関連 + DB検索
    public string $searchQuery = '';
    public array $suggestions = [];
    public array $dbProducts = [];  // DBから検索された製品
    public bool $isSearching = false;
    public bool $hasSearched = false;
    public ?array $selectedSuggestion = null;
    public ?Product $selectedDbProduct = null;  // DBから選択した製品

    // フォーム入力値
    public string $name = '';
    public string $model_number = '';
    public string $manufacturer = '';
    public string $genre_id = '';
    public string $genre_name = '';
    public string $rakuten_url = '';
    public ?string $purchase_date = null;
    public ?string $warranty_expires_on = null;
    public ?int $price = null;
    public string $purchase_condition = '';
    public ?int $useful_life = null;
    public string $status = '';
    public ?string $notes = null;

    protected RakutenApiService $rakutenApi;

    protected $rules = [
        'name' => 'required|string|max:255',
        'model_number' => 'required|string|max:255',
        'manufacturer' => 'required|string|max:255',
        'genre_name' => 'required|string|max:255',
        'purchase_date' => 'required|date',
        'status' => 'required|string|in:active,in_storage,in_repair,disposed',
        'purchase_condition' => 'required|string|in:新品,中古,再生品,不明',
        'warranty_expires_on' => 'nullable|date',
        'price' => 'nullable|integer|min:0',
        'useful_life' => 'nullable|integer|min:0',
        'notes' => 'nullable|string',
    ];

    public function boot(RakutenApiService $rakutenApi)
    {
        $this->rakutenApi = $rakutenApi;
    }

    /**
     * 製品を検索（DB優先 → 楽天APIフォールバック）
     */
    public function updatedSearchQuery($value)
    {
        $this->selectedSuggestion = null;
        $this->selectedDbProduct = null;
        $this->hasSearched = false;
        
        if (mb_strlen($value) < 2) {
            $this->suggestions = [];
            $this->dbProducts = [];
            $this->isSearching = false;
            return;
        }

        $this->isSearching = true;

        // まずDBから検索（型番 or 製品名 or メーカー）
        $this->dbProducts = Product::where(function ($query) use ($value) {
                $query->where('model_number', 'like', '%' . $value . '%')
                      ->orWhere('name', 'like', '%' . $value . '%')
                      ->orWhere('manufacturer', 'like', '%' . $value . '%');
            })
            ->distinct()
            ->select('id', 'name', 'model_number', 'manufacturer', 'genre_name')
            ->limit(5)
            ->get()
            ->toArray();

        // 楽天APIも呼び出し（APIが設定されている場合のみ）
        if ($this->rakutenApi->isConfigured()) {
            $this->suggestions = $this->rakutenApi->search($value, 5);
        } else {
            $this->suggestions = [];
        }

        $this->isSearching = false;
        $this->hasSearched = true;
    }

    /**
     * サジェストから製品を選択 → ステップ2へ
     */
    public function selectSuggestion(int $index)
    {
        if (!isset($this->suggestions[$index])) {
            return;
        }

        $suggestion = $this->suggestions[$index];
        $this->selectedSuggestion = $suggestion;

        // フォームに自動入力（不動の情報のみ）
        $this->name = $suggestion['product_name'];
        $this->genre_id = $suggestion['genre_id'];
        $this->genre_name = $suggestion['genre_name'];
        $this->rakuten_url = $suggestion['rakuten_url'];

        // 製品検索APIからの場合のみ追加情報を取得
        if ($suggestion['source'] === 'product_api') {
            if (!empty($suggestion['maker_name'])) {
                $this->manufacturer = $suggestion['maker_name'];
            }
            if (!empty($suggestion['model_number'])) {
                $this->model_number = $suggestion['model_number'];
            }
        }

        // 検索クエリを製品名に更新
        $this->searchQuery = $suggestion['product_name'];
        
        // サジェストをクリアしてステップ2へ
        $this->suggestions = [];
        $this->step = 2;
    }

    /**
     * DBから製品を選択 → ステップ2へ（情報をコピー）
     */
    public function selectDbProduct(int $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        // DBの製品情報をフォームにコピー
        $this->name = $product->name;
        $this->model_number = $product->model_number ?? '';
        $this->manufacturer = $product->manufacturer ?? '';
        $this->genre_name = $product->genre_name ?? '';
        $this->genre_id = $product->genre_id ?? '';
        
        // 選択状態を記録
        $this->selectedDbProduct = $product;
        $this->searchQuery = $product->name;
        
        // サジェストをクリアしてステップ2へ
        $this->suggestions = [];
        $this->dbProducts = [];
        $this->step = 2;
    }

    /**
     * 手動で製品情報を入力する場合（API結果がない場合）
     */
    public function skipToManualEntry()
    {
        $this->name = $this->searchQuery;
        $this->selectedSuggestion = null;
        $this->selectedDbProduct = null;
        $this->suggestions = [];
        $this->dbProducts = [];
        $this->step = 2;
    }

    /**
     * ステップ1に戻る
     */
    public function backToSearch()
    {
        $this->step = 1;
        $this->selectedSuggestion = null;
        $this->selectedDbProduct = null;
        $this->dbProducts = [];
        $this->hasSearched = false;
        $this->resetForm();
    }

    /**
     * フォームをリセット
     */
    protected function resetForm()
    {
        $this->name = '';
        $this->model_number = '';
        $this->manufacturer = '';
        $this->genre_id = '';
        $this->genre_name = '';
        $this->rakuten_url = '';
        $this->purchase_date = null;
        $this->warranty_expires_on = null;
        $this->price = null;
        $this->purchase_condition = '';
        $this->useful_life = null;
        $this->status = '';
        $this->notes = null;
    }

    /**
     * 製品を登録
     */
    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $group = $user->groups()->first();

        if (!$group) {
            session()->flash('error', '所属するグループが見つかりません。');
            return;
        }

        $product = Product::create([
            'group_id' => $group->id,
            'name' => $this->name,
            'model_number' => $this->model_number,
            'manufacturer' => $this->manufacturer,
            'genre_id' => $this->genre_id ?: null,
            'genre_name' => $this->genre_name,
            'rakuten_url' => $this->rakuten_url ?: null,
            'purchase_date' => $this->purchase_date,
            'warranty_expires_on' => $this->warranty_expires_on ?: null,
            'price' => $this->price,
            'purchase_condition' => $this->purchase_condition,
            'useful_life' => $this->useful_life,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        // サジェストデータを破棄（登録後は持たない）
        $this->selectedSuggestion = null;
        $this->suggestions = [];

        session()->flash('success', '製品を登録しました。');
        return redirect()->route('products.index');
    }

    /**
     * 楽天APIが設定されているかチェック
     */
    public function isRakutenConfigured(): bool
    {
        return $this->rakutenApi->isConfigured();
    }

    /**
     * ジャンルカテゴリを取得
     */
    public function getGenreCategories(): array
    {
        return self::GENRE_CATEGORIES;
    }

    public function render()
    {
        return view('livewire.create-product', [
            'genreCategories' => self::GENRE_CATEGORIES,
        ])->layout('layouts.app');
    }
}
