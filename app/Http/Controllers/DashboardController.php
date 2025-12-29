<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Incident;
use App\Models\Category; // Assuming you have a Category model, if not, we can adjust
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $group = $user->groups()->first();
        $isPublic = session('view_mode') === 'public';

        if (!$group && !$isPublic) {
            return view('dashboard', ['stats' => [], 'depreciationData' => null, 'depreciableProducts' => [], 'isPublic' => $isPublic]);
        }

        // Helper function to apply scope
        $applyScope = function ($query) use ($group, $isPublic) {
            if (!$isPublic) {
                $query->where('group_id', $group->id);
            }
            // In public mode, we query all data (no group filter)
        };
        
        // Helper for incidents scope (via products)
        $applyIncidentScope = function ($query) use ($group, $isPublic) {
             if (!$isPublic) {
                $query->whereIn('product_id', function ($q) use ($group) {
                    $q->select('id')->from('products')->where('group_id', $group->id);
                });
            }
        };


        // 1. TCO by Category
        $tcoQuery = Product::query();
        $applyScope($tcoQuery);
        $tcoByCategory = $tcoQuery->select('category', DB::raw('SUM(price) as total_price'))
            ->groupBy('category')
            ->pluck('total_price', 'category')->all();

        $incidentCostsQuery = Incident::query();
        $applyIncidentScope($incidentCostsQuery);
        $incidentCosts = $incidentCostsQuery->join('products', 'incidents.product_id', '=', 'products.id')
        ->select('products.category', DB::raw('SUM(incidents.cost) as total_incident_cost'))
        ->groupBy('products.category')
        ->pluck('total_incident_cost', 'products.category')->all();

        foreach ($tcoByCategory as $category => &$total) {
            $total += $incidentCosts[$category] ?? 0;
        }

        // 2. Incidents by Type
        $incidentsByTypeQuery = Incident::query();
        $applyIncidentScope($incidentsByTypeQuery);
        $incidentsByTypeRaw = $incidentsByTypeQuery->select('incident_type', DB::raw('count(*) as count'))
        ->groupBy('incident_type')
        ->pluck('count', 'incident_type')->all();

        $incidentsByType = [
            'labels' => [],
            'data' => [],
        ];
        foreach ($incidentsByTypeRaw as $type => $count) {
            $incidentsByType['labels'][] = Incident::INCIDENT_TYPES[$type] ?? $type;
            $incidentsByType['data'][] = $count;
        }


        // 3. Number of products per category
        $productsPerCategoryQuery = Product::query();
        $applyScope($productsPerCategoryQuery);
        $productsPerCategory = $productsPerCategoryQuery->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')->all();

        // 4. Depreciation Data (Only for private mode or if we want to show a demo product)
        // For public mode, let's just pick one sample product or disable it if confusing.
        // Let's keep it but scope it.
        $depreciationQuery = Product::whereNotNull('price')
            ->whereNotNull('useful_life')
            ->where('useful_life', '>', 0);
        $applyScope($depreciationQuery);
        $depreciableProducts = $depreciationQuery->limit(50)->get(); // Limit for public performance

        $depreciationData = null;
        if ($depreciableProducts->isNotEmpty()) {
            $selectedProductId = $request->query('product_id');
            $productForDepreciation = $depreciableProducts->firstWhere('id', $selectedProductId);

            if (!$productForDepreciation) {
                $productForDepreciation = $depreciableProducts->first();
            }
            
            $depreciationData = $this->calculateDepreciation($productForDepreciation);
        }


        $stats = [
            'tco_by_category' => [
                'labels' => array_keys($tcoByCategory),
                'data' => array_values($tcoByCategory),
            ],
            'incidents_by_type' => [
                'labels' => $incidentsByType['labels'],
                'data' => $incidentsByType['data'],
            ],
            'products_per_category' => [
                'labels' => array_keys($productsPerCategory),
                'data' => array_values($productsPerCategory),
            ],
        ];

        return view('dashboard', compact('stats', 'depreciationData', 'depreciableProducts', 'isPublic'));
    }

    /**
     * Calculate depreciation data for a given product.
     *
     * @param Product $product
     * @return array
     */
    private function calculateDepreciation(Product $product)
    {
        $purchasePrice = (float) $product->price;
        $usefulLife = (int) $product->useful_life;

        if ($usefulLife <= 0) {
            return null;
        }

        $annualDepreciation = $purchasePrice / $usefulLife;
        // 残存価額を1円とする
        $residualValue = 1;

        $labels = [];
        $data = [];

        $purchaseDate = Carbon::parse($product->purchase_date);
        $currentBookValue = $purchasePrice;

        for ($year = 0; $year <= $usefulLife; $year++) {
            $labels[] = $purchaseDate->copy()->addYears($year)->format('Y-m-d');
            $data[] = round($currentBookValue);
            
            if ($currentBookValue > $residualValue) {
                $currentBookValue -= $annualDepreciation;
            }
             // 簿価が残存価額を下回らないように調整
            if ($currentBookValue < $residualValue) {
                $currentBookValue = $residualValue;
            }
        }
        
        // 最後の年の簿価を強制的に1円にする
        if(count($data) > $usefulLife){
            $data[$usefulLife] = $residualValue;
        }


        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
