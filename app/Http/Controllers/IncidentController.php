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
    public function index()
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
