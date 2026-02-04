<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Incident;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class PublicDashboard extends Component
{
    #[Url]
    public string $search = '';
    
    public ?int $selectedProductId = null;
    public ?string $selectedCategory = null;
    public array $compareProducts = [];

    // Category Average Lifespan (in years)
    protected array $categoryLifespans = [
        'Smartphone' => 3,
        'Laptop' => 4,
        'Tablet' => 3,
        'TV' => 7,
        'Appliance' => 10,
        'Other' => 5,
    ];

    // Category Labels
    protected array $categoryLabels = [
        'Smartphone' => 'スマートフォン',
        'Laptop' => 'ノートPC',
        'Tablet' => 'タブレット',
        'TV' => 'テレビ',
        'Appliance' => '家電',
        'Other' => 'その他',
    ];

    public function mount()
    {
        // No default selection for public mode
    }

    public function searchProducts()
    {
        // Trigger search via wire:model.live
    }

    public function selectProduct($id)
    {
        $this->selectedProductId = (int) $id;
        $this->dispatch('product-selected', data: $this->productAnalytics);
    }

    public function clearSelection()
    {
        $this->selectedProductId = null;
        $this->search = '';
    }

    public function setCategory(?string $category)
    {
        $this->selectedCategory = $category;
    }

    public function toggleCompare($productId)
    {
        if (in_array($productId, $this->compareProducts)) {
            $this->compareProducts = array_filter($this->compareProducts, fn($id) => $id !== $productId);
        } else {
            if (count($this->compareProducts) < 3) {
                $this->compareProducts[] = $productId;
            }
        }
        $this->compareProducts = array_values($this->compareProducts);
    }

    public function clearCompare()
    {
        $this->compareProducts = [];
    }

    #[Computed]
    public function categories(): array
    {
        return $this->categoryLabels;
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        return Product::where(function($q) {
                $q->where('model_number', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%')
                  ->orWhere('manufacturer', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory, fn($q) => $q->where('category', $this->selectedCategory))
            ->select('id', 'name', 'model_number', 'manufacturer', 'category', 'price')
            ->take(20)
            ->get()
            ->groupBy('model_number')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'model_number' => $first->model_number,
                    'name' => $first->name,
                    'manufacturer' => $first->manufacturer,
                    'category' => $first->category,
                    'sample_count' => $group->count(),
                    'avg_price' => round($group->avg('price')),
                    'product_ids' => $group->pluck('id')->toArray(),
                ];
            })
            ->values();
    }

    #[Computed]
    public function selectedProduct()
    {
        if (!$this->selectedProductId) return null;
        return Product::with('incidents')->find($this->selectedProductId);
    }

    #[Computed]
    public function productAnalytics(): ?array
    {
        $product = $this->selectedProduct;
        if (!$product) return null;

        $modelNumber = $product->model_number;
        
        // 同型番の全製品を取得
        $sameModelProducts = Product::where('model_number', $modelNumber)
            ->with('incidents')
            ->get();

        $sampleCount = $sameModelProducts->count();
        
        // 1. 信頼性スコア計算
        $productsWithIncidents = $sameModelProducts->filter(fn($p) => $p->incidents->count() > 0)->count();
        $incidentRate = $sampleCount > 0 ? round(($productsWithIncidents / $sampleCount) * 100, 1) : 0;
        $reliabilityScore = max(0, 100 - $incidentRate);

        // 2. CPD計算（集合知ベース）
        $totalCpd = 0;
        $cpdCount = 0;
        foreach ($sameModelProducts as $p) {
            if ($p->purchase_date && $p->price) {
                $days = max(1, Carbon::parse($p->purchase_date)->diffInDays(now()));
                $totalCost = $p->price + $p->incidents->sum('cost');
                $totalCpd += $totalCost / $days;
                $cpdCount++;
            }
        }
        $avgCpd = $cpdCount > 0 ? round($totalCpd / $cpdCount, 1) : 0;

        // 3. 平均使用期間
        $totalLifespanYears = 0;
        $lifespanCount = 0;
        foreach ($sameModelProducts as $p) {
            if ($p->purchase_date) {
                $years = Carbon::parse($p->purchase_date)->diffInYears(now());
                $totalLifespanYears += $years;
                $lifespanCount++;
            }
        }
        $avgLifespanYears = $lifespanCount > 0 ? round($totalLifespanYears / $lifespanCount, 1) : 0;

        // 4. インシデント種別分布
        $allIncidents = $sameModelProducts->flatMap(fn($p) => $p->incidents);
        $incidentTypeDistribution = $allIncidents
            ->groupBy('incident_type')
            ->map(fn($group) => $group->count())
            ->toArray();

        // 5. 深刻度分布
        $severityDistribution = $allIncidents
            ->groupBy('severity')
            ->map(fn($group) => $group->count())
            ->toArray();

        // 6. 時系列発生パターン（購入後何ヶ月目）
        $timePatterns = [];
        foreach ($sameModelProducts as $p) {
            if (!$p->purchase_date) continue;
            $purchaseDate = Carbon::parse($p->purchase_date);
            foreach ($p->incidents as $incident) {
                if ($incident->occurred_at) {
                    $monthsAfterPurchase = $purchaseDate->diffInMonths($incident->occurred_at);
                    $bucket = match(true) {
                        $monthsAfterPurchase < 3 => '0-3ヶ月',
                        $monthsAfterPurchase < 6 => '3-6ヶ月',
                        $monthsAfterPurchase < 12 => '6-12ヶ月',
                        $monthsAfterPurchase < 24 => '1-2年',
                        $monthsAfterPurchase < 36 => '2-3年',
                        default => '3年以上'
                    };
                    $timePatterns[$bucket] = ($timePatterns[$bucket] ?? 0) + 1;
                }
            }
        }

        // 7. よくある問題TOP5
        $topProblems = $allIncidents
            ->groupBy('incident_type')
            ->map(function ($group, $type) {
                return [
                    'type' => $type,
                    'label' => Incident::INCIDENT_TYPES[$type] ?? $type,
                    'count' => $group->count(),
                    'avg_cost' => round($group->avg('cost')),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->toArray();

        // 8. 価格情報
        $prices = $sameModelProducts->pluck('price')->filter();
        $avgPrice = $prices->count() > 0 ? round($prices->avg()) : 0;
        $minPrice = $prices->count() > 0 ? $prices->min() : 0;
        $maxPrice = $prices->count() > 0 ? $prices->max() : 0;

        // 9. 1年以内の故障確率
        $oneYearFailureCount = 0;
        foreach ($sameModelProducts as $p) {
            if (!$p->purchase_date) continue;
            $purchaseDate = Carbon::parse($p->purchase_date);
            $hasOneYearIncident = $p->incidents->filter(function($i) use ($purchaseDate) {
                return $i->occurred_at && 
                       $i->incident_type === 'failure' &&
                       $purchaseDate->diffInMonths($i->occurred_at) <= 12;
            })->count() > 0;
            if ($hasOneYearIncident) $oneYearFailureCount++;
        }
        $oneYearFailureRate = $sampleCount > 0 ? round(($oneYearFailureCount / $sampleCount) * 100, 1) : 0;

        // 10. 平均修理費用
        $repairIncidents = $allIncidents->filter(fn($i) => $i->cost > 0);
        $avgRepairCost = $repairIncidents->count() > 0 ? round($repairIncidents->avg('cost')) : 0;

        // カテゴリ平均寿命
        $categoryLifeYears = $this->categoryLifespans[$product->category] ?? 5;

        // ライフサイクルコスト予測
        $expectedMaintenanceCost = $avgRepairCost * ($allIncidents->count() / max(1, $sampleCount));
        $lifecycleCost = $avgPrice + ($expectedMaintenanceCost * $categoryLifeYears);

        return [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'model_number' => $product->model_number,
                'manufacturer' => $product->manufacturer,
                'category' => $product->category,
                'category_label' => $this->categoryLabels[$product->category] ?? $product->category,
            ],
            'sample_count' => $sampleCount,
            'reliability_score' => $reliabilityScore,
            'incident_rate' => $incidentRate,
            'avg_cpd' => $avgCpd,
            'avg_lifespan_years' => $avgLifespanYears,
            'category_life_years' => $categoryLifeYears,
            'incident_type_distribution' => $incidentTypeDistribution,
            'severity_distribution' => $severityDistribution,
            'time_patterns' => $timePatterns,
            'top_problems' => $topProblems,
            'price' => [
                'avg' => $avgPrice,
                'min' => $minPrice,
                'max' => $maxPrice,
            ],
            'one_year_failure_rate' => $oneYearFailureRate,
            'avg_repair_cost' => $avgRepairCost,
            'lifecycle_cost' => round($lifecycleCost),
            'total_incidents' => $allIncidents->count(),
        ];
    }

    #[Computed]
    public function globalStats(): array
    {
        // 全体統計
        $totalProducts = Product::count();
        $totalIncidents = Incident::count();
        
        // カテゴリ別統計
        $categoryStats = Product::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => [
                    'label' => $this->categoryLabels[$item->category] ?? $item->category,
                    'count' => $item->count,
                ]];
            })
            ->toArray();

        // 全体の平均CPD
        $allCpd = [];
        Product::whereNotNull('purchase_date')
            ->whereNotNull('price')
            ->with('incidents')
            ->chunk(100, function ($products) use (&$allCpd) {
                foreach ($products as $p) {
                    $days = max(1, Carbon::parse($p->purchase_date)->diffInDays(now()));
                    $totalCost = $p->price + $p->incidents->sum('cost');
                    $allCpd[] = $totalCost / $days;
                }
            });
        $globalAvgCpd = count($allCpd) > 0 ? round(array_sum($allCpd) / count($allCpd), 1) : 0;

        // 全体の故障率
        $productsWithIncidents = Product::has('incidents')->count();
        $globalIncidentRate = $totalProducts > 0 ? round(($productsWithIncidents / $totalProducts) * 100, 1) : 0;

        return [
            'total_products' => $totalProducts,
            'total_incidents' => $totalIncidents,
            'category_stats' => $categoryStats,
            'global_avg_cpd' => $globalAvgCpd,
            'global_incident_rate' => $globalIncidentRate,
        ];
    }

    #[Computed]
    public function comparisonData(): array
    {
        if (empty($this->compareProducts)) {
            return [];
        }

        $products = Product::whereIn('id', $this->compareProducts)->get();
        $result = [];

        foreach ($products as $product) {
            $sameModelProducts = Product::where('model_number', $product->model_number)
                ->with('incidents')
                ->get();
            
            $sampleCount = $sameModelProducts->count();
            $allIncidents = $sameModelProducts->flatMap(fn($p) => $p->incidents);
            
            // 信頼性スコア
            $productsWithIncidents = $sameModelProducts->filter(fn($p) => $p->incidents->count() > 0)->count();
            $incidentRate = $sampleCount > 0 ? round(($productsWithIncidents / $sampleCount) * 100, 1) : 0;
            $reliabilityScore = max(0, 100 - $incidentRate);

            // CPD
            $totalCpd = 0;
            $cpdCount = 0;
            foreach ($sameModelProducts as $p) {
                if ($p->purchase_date && $p->price) {
                    $days = max(1, Carbon::parse($p->purchase_date)->diffInDays(now()));
                    $totalCost = $p->price + $p->incidents->sum('cost');
                    $totalCpd += $totalCost / $days;
                    $cpdCount++;
                }
            }
            $avgCpd = $cpdCount > 0 ? round($totalCpd / $cpdCount, 1) : 0;

            // 価格
            $prices = $sameModelProducts->pluck('price')->filter();
            $avgPrice = $prices->count() > 0 ? round($prices->avg()) : 0;

            // 修理費用
            $repairIncidents = $allIncidents->filter(fn($i) => $i->cost > 0);
            $avgRepairCost = $repairIncidents->count() > 0 ? round($repairIncidents->avg('cost')) : 0;

            $result[] = [
                'id' => $product->id,
                'name' => $product->name,
                'model_number' => $product->model_number,
                'manufacturer' => $product->manufacturer,
                'category' => $this->categoryLabels[$product->category] ?? $product->category,
                'sample_count' => $sampleCount,
                'reliability_score' => $reliabilityScore,
                'incident_rate' => $incidentRate,
                'avg_cpd' => $avgCpd,
                'avg_price' => $avgPrice,
                'avg_repair_cost' => $avgRepairCost,
                'total_incidents' => $allIncidents->count(),
            ];
        }

        return $result;
    }

    public function render()
    {
        return view('livewire.public-dashboard')->layout('layouts.app');
    }
}
