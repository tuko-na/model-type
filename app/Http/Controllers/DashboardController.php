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

        if (!$group) {
            return view('dashboard', ['stats' => [], 'depreciationData' => null, 'depreciableProducts' => []]);
        }

        // 1. TCO by Category
        $tcoByCategory = Product::where('group_id', $group->id)
            ->select('category', DB::raw('SUM(price) as total_price'))
            ->groupBy('category')
            ->pluck('total_price', 'category')->all();

        $incidentCosts = Incident::whereIn('product_id', function ($query) use ($group) {
            $query->select('id')->from('products')->where('group_id', $group->id);
        })
        ->join('products', 'incidents.product_id', '=', 'products.id')
        ->select('products.category', DB::raw('SUM(incidents.cost) as total_incident_cost'))
        ->groupBy('products.category')
        ->pluck('total_incident_cost', 'products.category')->all();

        foreach ($tcoByCategory as $category => &$total) {
            $total += $incidentCosts[$category] ?? 0;
        }

        // 2. Incidents by Type
        $incidentsByTypeRaw = Incident::whereIn('product_id', function ($query) use ($group) {
            $query->select('id')->from('products')->where('group_id', $group->id);
        })
        ->select('incident_type', DB::raw('count(*) as count'))
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
        $productsPerCategory = Product::where('group_id', $group->id)
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')->all();

        // 4. Depreciation Data
        $depreciableProducts = Product::where('group_id', $group->id)
            ->whereNotNull('price')
            ->whereNotNull('useful_life')
            ->where('useful_life', '>', 0)
            ->get();

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

        return view('dashboard', compact('stats', 'depreciationData', 'depreciableProducts'));
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
