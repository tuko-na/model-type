<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Incident;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class PortalDashboard extends Component
{
    public $selectedProductId = null;
    public $search = '';

    // Category Average Lifespan (in years) - Hardcoded for prototype
    protected $categoryLifespans = [
        'Smartphone' => 3,
        'Laptop' => 4,
        'Tablet' => 3,
        'TV' => 7,
        'Appliance' => 10,
        'Other' => 5,
    ];

    public function mount()
    {
        $firstProduct = Product::where('group_id', $this->getGroupId())->first();
        $this->selectedProductId = $firstProduct?->id;
    }
    
    public function getGroupId()
    {
        return Auth::user()->groups->first()?->id; 
    }

    public function selectProduct($id)
    {
        $this->selectedProductId = $id;
        $this->dispatch('product-selected', data: $this->focusMonitorData);
    }
    
    #[Computed]
    public function selectedProduct()
    {
        if (!$this->selectedProductId) return null;
        return Product::with('incidents')->find($this->selectedProductId);
    }
    
    #[Computed]
    public function allProducts()
    {
        $query = Product::where('group_id', $this->getGroupId());
        
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('model_number', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->take(50)->get(); // Limit for dropdown
    }

    #[Computed]
    public function globalKpis()
    {
        $groupId = $this->getGroupId();
        if (!$groupId) return ['avg_lifespan' => 0, 'annual_maintenance_cost' => 0, 'incident_rate' => 0];

        $products = Product::where('group_id', $groupId)->get();
        
        // Avg Lifespan
        $totalLifespanDays = 0;
        $count = 0;
        foreach ($products as $p) {
            if ($p->purchase_date) {
                $start = Carbon::parse($p->purchase_date);
                $end = ($p->status === 'disposed') ? $p->updated_at : now();
                $totalLifespanDays += $start->diffInDays($end);
                $count++;
            }
        }
        $avgLifespan = $count > 0 ? round(($totalLifespanDays / $count) / 365, 1) : 0;

        // Annual Maintenance Cost
        $annualCost = Incident::whereHas('product', fn($q) => $q->where('group_id', $groupId))
            ->where('occurred_at', '>=', now()->subYear())
            ->sum('cost');

        // Incident Rate
        $productsWithIncidents = Product::where('group_id', $groupId)->has('incidents')->count();
        $totalProducts = $products->count();
        $incidentRate = $totalProducts > 0 ? round(($productsWithIncidents / $totalProducts) * 100, 1) : 0;

        return [
            'avg_lifespan' => $avgLifespan,
            'annual_maintenance_cost' => $annualCost,
            'incident_rate' => $incidentRate,
        ];
    }

    #[Computed]
    public function focusMonitorData()
    {
        $product = $this->selectedProduct;
        if (!$product) return null;

        // Days Owned & Years Owned
        $daysOwned = 1;
        $yearsOwned = 0;
        if ($product->purchase_date) {
            $start = Carbon::parse($product->purchase_date);
            $end = ($product->status === 'disposed') ? $product->updated_at : now();
            $daysOwned = max(1, $start->diffInDays($end));
            $yearsOwned = round($start->diffInYears($end), 1);
        }

        // CPD Calculation
        $totalCost = ($product->price ?? 0) + $product->incidents->sum('cost');
        $cpd = round($totalCost / $daysOwned, 1);

        // Category Avg CPD
        $catAvgLife = $this->categoryLifespans[$product->category] ?? 5;
        $catAvgDays = $catAvgLife * 365;
        
        $catProducts = Product::where('category', $product->category)
            ->where('group_id', $this->getGroupId())
            ->where('id', '!=', $product->id)
            ->get();
            
        $avgPrice = $catProducts->count() > 0 ? $catProducts->avg('price') : ($product->price ?? 0);
        $avgCpd = round($avgPrice / $catAvgDays, 1);

        // Lifespan Percentage
        $lifespanPercentage = 0;
        if ($product->purchase_date) {
             $start = Carbon::parse($product->purchase_date);
             $elapsedYears = $start->diffInYears(now());
             $lifespanPercentage = ($catAvgLife > 0) ? min(100, round(($elapsedYears / $catAvgLife) * 100)) : 0;
        }

        // Stability Score Calculation
        // Score = 100 - (incident_count * 10) - (severity_penalty)
        // severity_penalty: critical=15, high=10, medium=5, low=2
        $incidents = $product->incidents;
        $incidentCount = $incidents->count();
        
        $severityPenalty = 0;
        foreach ($incidents as $incident) {
            $severityPenalty += match($incident->severity) {
                'critical' => 15,
                'high' => 10,
                'medium' => 5,
                'low' => 2,
                default => 3,
            };
        }
        
        // Also penalize based on frequency (incidents per year)
        $incidentsPerYear = $yearsOwned > 0 ? $incidentCount / max(1, $yearsOwned) : $incidentCount;
        $frequencyPenalty = min(30, round($incidentsPerYear * 10));
        
        $stabilityScore = max(0, min(100, 100 - $severityPenalty - $frequencyPenalty));

        // Timeline Data
        $timeline = [];
        
        // Add purchase event
        if ($product->purchase_date) {
            $timeline[] = [
                'type' => 'purchase',
                'title' => '購入',
                'date' => Carbon::parse($product->purchase_date)->format('Y/m/d'),
                'timestamp' => Carbon::parse($product->purchase_date)->timestamp,
            ];
        }
        
        // Add incidents
        foreach ($incidents->sortByDesc('occurred_at')->take(10) as $incident) {
            $timeline[] = [
                'type' => $incident->incident_type ?? 'other',
                'title' => $incident->title ?? ($incident->incident_type ? (\App\Models\Incident::INCIDENT_TYPES[$incident->incident_type] ?? $incident->incident_type) : 'インシデント'),
                'date' => $incident->occurred_at ? Carbon::parse($incident->occurred_at)->format('Y/m/d') : '-',
                'timestamp' => $incident->occurred_at ? Carbon::parse($incident->occurred_at)->timestamp : 0,
            ];
        }
        
        // Sort by timestamp descending
        usort($timeline, fn($a, $b) => $b['timestamp'] - $a['timestamp']);

        return [
            'cpd' => $cpd,
            'avg_cpd' => $avgCpd,
            'lifespan_percentage' => $lifespanPercentage,
            'category_life_years' => $catAvgLife,
            'days_owned' => $daysOwned,
            'years_owned' => $yearsOwned,
            'stability_score' => $stabilityScore,
            'incident_count' => $incidentCount,
            'timeline' => $timeline,
        ];
    }

    #[Computed]
    public function discovery()
    {
        $groupId = $this->getGroupId();
        if (!$groupId) return [
            'recent_incidents' => collect(),
            'alerts' => collect(),
        ];

        // Recent Incidents (last 5)
        $recentIncidents = Incident::whereHas('product', fn($q) => $q->where('group_id', $groupId))
            ->with('product')
            ->orderByDesc('occurred_at')
            ->take(5)
            ->get();

        // Alerts: 修理中、保証期間終了間近
        $alerts = Product::where('group_id', $groupId)
            ->where(function($q) {
                $q->where('status', 'repairing')
                  ->orWhereBetween('warranty_expires_on', [now(), now()->addDays(30)]);
            })->get();

        return [
            'recent_incidents' => $recentIncidents,
            'alerts' => $alerts,
        ];
    }
    
    public function render()
    {
        return view('livewire.portal-dashboard')->layout('layouts.app');
    }
}