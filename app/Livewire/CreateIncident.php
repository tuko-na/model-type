<?php

namespace App\Livewire;

use App\Models\Incident;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;


class CreateIncident extends Component
{
    public $search = '';
    public $products = [];
    public ?Product $selectedProduct = null;

    // Incident properties
    public $title;
    public $occurred_at;
    public $incident_type;
    public $resolution_type;
    public $symptom_tags = [];
    public $other_symptom;
    public $description;
    public $cost;

    // Master data
    public $incident_types = [];
    public $resolution_types = [];
    public $symptom_tags_master = [];

    public function mount()
    {
        $this->incident_types = Incident::INCIDENT_TYPES;
        $this->resolution_types = Incident::RESOLUTION_TYPES;
        $this->symptom_tags_master = Incident::SYMPTOM_TAGS;
        $this->occurred_at = now()->format('Y-m-d');
    }

    public function updatedSearch($value)
    {
        if (empty($value)) {
            $this->products = [];
            return;
        }

        $user = Auth::user();
        $group = $user->groups()->first();

        if ($group) {
            $this->products = Product::where('group_id', $group->id)
                ->where(function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                          ->orWhere('model_number', 'like', '%' . $value . '%');
                })
                ->limit(5)
                ->get();
        }
    }

    public function selectProduct(Product $product)
    {
        $this->selectedProduct = $product;
        $this->search = $product->name; // Update search box with the selected product name
        $this->products = []; // Clear the search results
    }

    public function save()
    {
        if (!$this->selectedProduct) {
            // This should not happen if the form is shown only when a product is selected.
            // Add an error message or handle it as you see fit.
            session()->flash('error', '製品が選択されていません。');
            return;
        }

        $validatedData = $this->validate([
            'title' => 'required|string|max:255',
            'occurred_at' => 'required|date',
            'incident_type' => ['required', 'string', Rule::in(array_keys($this->incident_types))],
            'resolution_type' => ['required', 'string', Rule::in(array_keys($this->resolution_types))],
            'symptom_tags' => 'nullable|array',
            'symptom_tags.*' => ['string', Rule::in(array_keys($this->symptom_tags_master))],
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

        $incident->product_id = $this->selectedProduct->id;
        $incident->group_id = $this->selectedProduct->group_id;
        $incident->user_id = Auth::id();
        $incident->save();

        return redirect()->route('incidents.index')->with('success', 'インシデントを登録しました。');
    }


    public function render()
    {
        return view('livewire.create-incident')
            ->layout('layouts.app');
    }
}