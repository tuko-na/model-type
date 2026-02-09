<?php

namespace App\Livewire;

use App\Models\Incident;
use App\Models\Product;
use App\Services\IncidentFormSchema\FieldType;
use App\Services\IncidentFormSchema\FormSchemaFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditIncident extends Component
{
    // Wizard state
    public int $currentStep = 1;
    public int $totalSteps = 3;

    // Incident being edited
    public ?Incident $incident = null;
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

    public function mount(Incident $incident)
    {
        $user = Auth::user();
        $group = $user->groups()->first();

        // インシデントがユーザーのグループに属しているか確認
        if ($incident->group_id !== $group?->id) {
            abort(403);
        }

        $this->incident = $incident;
        $this->selectedProduct = $incident->product;

        // マスターデータをロード
        $this->incident_types = Incident::INCIDENT_TYPES;
        $this->resolution_types = Incident::RESOLUTION_TYPES;
        $this->symptom_tags_master = Incident::SYMPTOM_TAGS;
        $this->severity_levels = Incident::SEVERITY_LEVELS;

        // フォームスキーマをロード
        $this->productCategory = $this->selectedProduct->genre_name ?? 'default';
        $this->loadFormSchema();

        // 既存のデータをフォームに設定
        $this->occurred_at = $incident->occurred_at?->format('Y-m-d');
        $this->incident_type = $incident->incident_type;
        $this->severity = $incident->severity;
        $this->title = $incident->title;
        $this->resolution_type = $incident->resolution_type;
        $this->description = $incident->description;
        $this->cost = $incident->cost;

        // 詳細データをロード（既存のデータがあれば上書き）
        if (is_array($incident->details) && !empty($incident->details)) {
            $this->details = array_merge($this->details, $incident->details);
        }

        // 症状タグを解析
        $existingTags = $incident->symptom_tags ? explode(',', $incident->symptom_tags) : [];
        $predefinedKeys = array_keys($this->symptom_tags_master);
        $this->symptom_tags = array_values(array_intersect($existingTags, $predefinedKeys));
        $otherTags = array_diff($existingTags, $predefinedKeys);
        $this->other_symptom = !empty($otherTags) ? implode(',', $otherTags) : '';
    }

    protected function loadFormSchema()
    {
        $this->formSchema = FormSchemaFactory::getFields($this->productCategory);
        // デフォルト値をロード（既存データで上書きされる）
        $this->details = FormSchemaFactory::getDefaultValues($this->productCategory);
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
        // 編集時は任意のステップに移動可能
        if ($step >= 1 && $step <= $this->totalSteps) {
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

        // Update incident
        $this->incident->update([
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

        return redirect()->route('products.show', $this->selectedProduct)->with('success', 'インシデント情報を更新しました。');
    }

    public function getProgressPercentageProperty(): int
    {
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
        return view('livewire.edit-incident', [
            'fieldTypes' => FieldType::class,
        ])->layout('layouts.app');
    }
}
