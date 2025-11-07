<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ModelSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $group = $user->groups()->first();
        $products = $group ? $group->products()->get() : collect();

        return view('products.index', compact('products'));
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
        $validatedData = $request->validate([
            'model_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'purchase_date' => 'required|date',
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
            // 例外が発生した場合のログ
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
    /*
    public function show(string $id)
    {
        //
    }
    */

    /**
     * Show the form for editing the specified resource.
     */
    /*
    public function edit(string $id)
    {
        //
    }
    */

    /**
     * Update the specified resource in storage.
     */
    /*
    public function update(Request $request, string $id)
    {
        //
    }
    */

    /**
     * Remove the specified resource from storage.
     */
    /*
    public function destroy(string $id)
    {
        //
    }
    */}
