<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $group = $user->groups()->first();

        if (!$group) {
            return view('incidents.index', ['incidents' => collect()]);
        }

        $query = Incident::where('group_id', $group->id)
            ->with('product');

        // キーワード検索
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhereHas('product', function ($productQuery) use ($searchTerm) {
                        $productQuery->where('name', 'like', $searchTerm)
                            ->orWhere('model_number', 'like', $searchTerm);
                    });
            });
        }

        // 発生日による絞り込み
        if ($request->filled('start_date')) {
            $query->where('occurred_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->where('occurred_at', '<=', $request->input('end_date'));
        }

        // 費用による絞り込み
        if ($request->filled('min_cost') || $request->filled('max_cost') || $request->filled('cost_is_null')) {
            $query->where(function ($q) use ($request) {
                // Cost Range sub-query
                if ($request->filled('min_cost') || $request->filled('max_cost')) {
                    $q->where(function ($rangeQuery) use ($request) {
                        $minCost = (int) $request->input('min_cost', 0);
                        $rangeQuery->where('cost', '>=', $minCost);

                        if ($request->filled('max_cost') && $request->input('max_cost') > 0) {
                            $maxCost = (int) $request->input('max_cost');
                            $rangeQuery->where('cost', '<=', $maxCost);
                        }
                    });
                }

                // Cost is NULL sub-query
                if ($request->filled('cost_is_null')) {
                    $q->orWhereNull('cost');
                }
            });
        }

        // インシデント種別による絞り込み
        if ($request->filled('incident_type')) {
            $query->whereIn('incident_type', $request->input('incident_type'));
        }

        // 対応種別による絞り込み
        if ($request->filled('resolution_type')) {
            $query->whereIn('resolution_type', $request->input('resolution_type'));
        }

        // 症状タグによる絞り込み (AND検索)
        if ($request->filled('symptom_tags')) {
            $tags = $request->input('symptom_tags');
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->where('symptom_tags', 'like', '%' . $tag . '%');
                }
            });
        }

        $incidents = $query->orderBy('occurred_at', 'desc')->get();


        return view('incidents.index', compact('incidents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        return view('incidents.create', [
            'product' => $product,
            'incident_types' => Incident::INCIDENT_TYPES,
            'resolution_types' => Incident::RESOLUTION_TYPES,
            'symptom_tags' => Incident::SYMPTOM_TAGS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'occurred_at' => 'required|date',
            'incident_type' => ['required', 'string', Rule::in(array_keys(Incident::INCIDENT_TYPES))],
            'resolution_type' => ['required', 'string', Rule::in(array_keys(Incident::RESOLUTION_TYPES))],
            'symptom_tags' => 'nullable|array',
            'symptom_tags.*' => ['string', Rule::in(array_keys(Incident::SYMPTOM_TAGS))],
            'other_symptom' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|integer|min:0',
        ]);

        $tags = $validatedData['symptom_tags'] ?? [];
        if (!empty($validatedData['other_symptom'])) {
            $tags[] = $validatedData['other_symptom'];
        }
        $symptomTagsString = implode(',', $tags);

        $incident = new Incident([
            'title' => $validatedData['title'],
            'occurred_at' => $validatedData['occurred_at'],
            'incident_type' => $validatedData['incident_type'],
            'resolution_type' => $validatedData['resolution_type'],
            'description' => $validatedData['description'],
            'cost' => $validatedData['cost'],
            'symptom_tags' => $symptomTagsString,
        ]);

        $incident->product_id = $product->id;
        $incident->group_id = $product->group_id;
        $incident->user_id = Auth::id();
        $incident->save();

        return redirect()->route('products.show', $product)->with('success', 'インシデントを登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, Incident $incident)
    {
        return view('incidents.edit', [
            'product' => $product,
            'incident' => $incident,
            'incident_types' => Incident::INCIDENT_TYPES,
            'resolution_types' => Incident::RESOLUTION_TYPES,
            'symptom_tags' => Incident::SYMPTOM_TAGS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, Incident $incident)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'occurred_at' => 'required|date',
            'incident_type' => ['required', 'string', Rule::in(array_keys(Incident::INCIDENT_TYPES))],
            'resolution_type' => ['required', 'string', Rule::in(array_keys(Incident::RESOLUTION_TYPES))],
            'symptom_tags' => 'nullable|array',
            'symptom_tags.*' => ['string', Rule::in(array_keys(Incident::SYMPTOM_TAGS))],
            'other_symptom' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|integer|min:0',
        ]);

        $tags = $validatedData['symptom_tags'] ?? [];
        if (!empty($validatedData['other_symptom'])) {
            $tags[] = $validatedData['other_symptom'];
        }
        $symptomTagsString = implode(',', $tags);

        $updateData = $validatedData;
        $updateData['symptom_tags'] = $symptomTagsString;
        unset($updateData['other_symptom']);

        $incident->update($updateData);

        return redirect()->route('products.show', $product)->with('success', 'インシデント情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, Incident $incident)
    {
        $incident->delete();

        return redirect()->route('products.show', $product)->with('success', 'インシデントを削除しました。');
    }
}
