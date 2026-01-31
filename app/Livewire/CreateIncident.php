<?php

namespace App\Livewire;

use App\Models\Incident;
use App\Models\Product;
use App\Services\IncidentFormSchema\FieldType;
use App\Services\IncidentFormSchema\FormSchemaFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateIncident extends Component
{
    // Wizard state
    public int $currentStep = 0;
    public int $totalSteps = 3;

    // Product search
    public string $search = '';
    public $products = [];
    public ?Product $selectedProduct = null;

    // Step 1: Basic Info
    public $occurred_at;
    public $incident_type;
    public $severity;
    public $title;

    // Step 2: Dynamic Details (category-specific)
    public array $details = [];
    public array $formSchema = [];
    public string $productCategory = '';

    // Step 3: Resolution & Cost
    public $resolution_type;
    public $symptom_tags = [];
    public $other_symptom;
    public $description;
    public $cost;

    // Master data
    public array $incident_types = [];
    public array $resolution_types = [];
    public array $symptom_tags_master = [];
    public array $severity_levels = [];

    protected $listeners = ['productSelected'];

    public function mount()
    {
        $this->incident_types = Incident::INCIDENT_TYPES;
        $this->resolution_types = Incident::RESOLUTION_TYPES;
        $this->symptom_tags_master = Incident::SYMPTOM_TAGS;
        $this->severity_levels = Incident::SEVERITY_LEVELS;
        $this->occurred_at = now()->format('Y-m-d');

        // クエリパラメータから製品IDを取得
        $product_id = request()->query('product_id');

        // 製品IDが渡された場合、製品を選択してステップ1から開始
        if ($product_id) {
            $user = Auth::user();
            $group = $user->groups()->first();

            $product = Product::where('id', $product_id)
                ->where('group_id', $group?->id)
                ->first();

            if ($product) {
                $this->selectedProduct = $product;
                $this->search = $product->name;
                $this->productCategory = $product->category ?? 'default';
                $this->loadFormSchema();
                $this->currentStep = 1;
            }
        }
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
        $this->search = $product->name;
        $this->products = [];

        // Set category and load dynamic form schema
        $this->productCategory = $product->category ?? 'default';
        $this->loadFormSchema();

        // Start wizard at step 1
        $this->currentStep = 1;
    }

    protected function loadFormSchema()
    {
        $this->formSchema = FormSchemaFactory::getFields($this->productCategory);
        $this->details = FormSchemaFactory::getDefaultValues($this->productCategory);
    }

    public function clearProduct()
    {
        $this->selectedProduct = null;
        $this->search = '';
        $this->productCategory = '';
        $this->formSchema = [];
        $this->details = [];
        $this->currentStep = 0;
    }

    // Step navigation
    public function nextStep()
    {
        if ($this->validateCurrentStep()) {
            $this->currentStep = min($this->currentStep + 1, $this->totalSteps);
        }
    }

    public function prevStep()
    {
        $this->currentStep = max($this->currentStep - 1, 1);
    }

    public function goToStep(int $step)
    {
        // Only allow going back or to current step
        if ($step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    protected function validateCurrentStep(): bool
    {
        $rules = $this->getStepValidationRules($this->currentStep);

        if (empty($rules)) {
            return true;
        }

        $this->validate($rules);
        return true;
    }

    protected function getStepValidationRules(int $step): array
    {
        return match ($step) {
            1 => [
                'occurred_at' => 'required|date',
                'incident_type' => ['required', 'string', Rule::in(array_keys($this->incident_types))],
                'severity' => ['required', 'string', Rule::in(array_keys($this->severity_levels))],
            ],
            2 => FormSchemaFactory::getValidationRules($this->productCategory),
            3 => [
                'title' => 'required|string|max:255',
                'resolution_type' => ['required', 'string', Rule::in(array_keys($this->resolution_types))],
                'cost' => 'nullable|integer|min:0',
            ],
            default => [],
        };
    }

    public function save()
    {
        if (!$this->selectedProduct) {
            session()->flash('error', '製品が選択されていません。');
            return;
        }

        // Validate all steps
        $allRules = array_merge(
            $this->getStepValidationRules(1),
            $this->getStepValidationRules(2),
            $this->getStepValidationRules(3)
        );

        $this->validate($allRules);

        // Prepare symptom tags
        $tags = $this->symptom_tags ?? [];
        if (!empty($this->other_symptom)) {
            $tags[] = $this->other_symptom;
        }
        $symptomTagsString = implode(',', $tags);

        // Create incident
        $incident = new Incident([
            'title' => $this->title,
            'occurred_at' => $this->occurred_at,
            'incident_type' => $this->incident_type,
            'resolution_type' => $this->resolution_type,
            'description' => $this->description,
            'cost' => $this->cost,
            'symptom_tags' => $symptomTagsString,
            'severity' => $this->severity,
            'details' => $this->details,
        ]);

        $incident->product_id = $this->selectedProduct->id;
        $incident->group_id = $this->selectedProduct->group_id;
        $incident->user_id = Auth::id();
        $incident->save();

        return redirect()->route('incidents.index')->with('success', 'インシデントを登録しました。');
    }

    public function getProgressPercentageProperty(): int
    {
        if ($this->currentStep === 0) {
            return 0;
        }
        return (int) (($this->currentStep / $this->totalSteps) * 100);
    }

    public function getStepTitlesProperty(): array
    {
        return [
            1 => '基本情報',
            2 => '詳細状況',
            3 => '解決・コスト',
        ];
    }

    public function getCategoryLabelProperty(): string
    {
        return FormSchemaFactory::getCategoryLabel($this->productCategory);
    }

    public function getFieldTypeEnum(): string
    {
        return FieldType::class;
    }

    public function render()
    {
        return view('livewire.create-incident', [
            'fieldTypes' => FieldType::class,
        ])->layout('layouts.app');
    }
}