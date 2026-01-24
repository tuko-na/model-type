<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ModelSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $group = $user->groups()->first();

        if (!$group) {
            return view('products.index', ['products' => collect()]);
        }

        $query = $group->products();

        // キーワード検索
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('model_number', 'like', $searchTerm)
                    ->orWhere('category', 'like', $searchTerm)
                    ->orWhere('manufacturer', 'like', $searchTerm);
            });
        }

        // 在庫ステータスによる絞り込み
        if ($request->filled('status')) {
            $query->whereIn('status', $request->input('status'));
        }

        // 購入時の状態による絞り込み
        if ($request->filled('condition')) {
            $query->whereIn('purchase_condition', $request->input('condition'));
        }

        // カテゴリによる絞り込み
        if ($request->filled('category')) {
            $query->whereIn('category', $request->input('category'));
        }

        $products = $query->latest('purchase_date')->get();

        return view('products.index', compact('products'));
    }

    /**
     * Display the global catalog of products.
     */
    public function catalog(Request $request)
    {
        $query = ModelSuggestion::query();

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('model_number', 'like', $searchTerm)
                    ->orWhere('category', 'like', $searchTerm)
                    ->orWhere('manufacturer', 'like', $searchTerm);
            });
        }

        // カテゴリによる絞り込み
        if ($request->filled('category')) {
            $query->whereIn('category', $request->input('category'));
        }

        $models = $query->orderBy('model_number')->paginate(20);

        return view('products.catalog', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::debug($request->all());
        $validatedData = $request->validate([
            'model_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'status' => 'required|string|in:active,in_storage,in_repair,disposed',
            'purchase_condition' => 'required|string|in:新品,中古,再生品,不明',           
            'notes' => 'nullable|string',
            'warranty_expires_on' => 'nullable|date',
            'price' => 'nullable|integer|min:0',
            'useful_life' => 'nullable|integer|min:0',
        ]);

        $group = $request->user()->groups()->first();

        if (!$group) {
            return back()->with('error', '所属するグループが見つかりません。');
        }

        try {
            $product = new Product($validatedData);
            $product->group_id = $group->id;
            
            if (!$product->save()) {
                // save()がfalseを返した場合のログ
                \Illuminate\Support\Facades\Log::error('Product save failed for an unknown reason.');
                return back()->with('error', '製品の保存に失敗しました。管理者に連絡してください。')->withInput();
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception caught while saving product: ' . $e->getMessage());
            return back()->with('error', '製品の保存中にエラーが発生しました。管理者に連絡してください。')->withInput();
        }

        // PM-03: 内部辞書の更新
        ModelSuggestion::updateOrCreate(
            ['model_number' => $product->model_number],
            [
                'name' => $product->name,
                'manufacturer' => $product->manufacturer,
                'category' => $product->category,
            ]
        );

        return redirect()->route('products.index')->with('success', '製品を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('incidents');

        $incidents = $product->incidents->map(function ($incident) {
            $incident->incident_type_label = \App\Models\Incident::INCIDENT_TYPES[$incident->incident_type] ?? $incident->incident_type;
            $incident->resolution_type_label = \App\Models\Incident::RESOLUTION_TYPES[$incident->resolution_type] ?? $incident->resolution_type;
            
            $tagKeys = explode(',', $incident->symptom_tags);
            $tagLabels = array_map(function ($key) {
                return \App\Models\Incident::SYMPTOM_TAGS[$key] ?? $key;
            }, $tagKeys);
            $incident->symptom_tags = implode(', ', $tagLabels);

            return $incident;
        });

        return view('products.show', compact('product', 'incidents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'model_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'status' => 'required|string|in:active,in_storage,in_repair,disposed',
            'purchase_condition' => 'required|string|in:新品,中古,再生品,不明',
            'notes' => 'nullable|string',
            'warranty_expires_on' => 'nullable|date',
            'price' => 'nullable|integer|min:0',
            'useful_life' => 'nullable|integer|min:0',
        ]);

        $product->update($validatedData);

        // PM-03: 内部辞書の更新
        ModelSuggestion::updateOrCreate(
            ['model_number' => $product->model_number],
            [
                'name' => $product->name,
                'manufacturer' => $product->manufacturer,
                'category' => $product->category,
            ]
        );

        return redirect()->route('products.show', $product)->with('success', '製品情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', '製品を削除しました。');
    }
}
