<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('インシデントの編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-visible bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if (session()->has('success'))
                        <div class="p-4 mb-4 text-sm font-medium text-green-800 bg-green-100 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="p-4 mb-4 text-sm font-medium text-red-800 bg-red-100 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Progress Bar --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-2">
                            @foreach($this->stepTitles as $step => $title)
                                <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                                    <button
                                        type="button"
                                        wire:click="goToStep({{ $step }})"
                                        class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-200 cursor-pointer
                                            {{ $currentStep >= $step ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}"
                                    >
                                        @if($currentStep > $step)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            {{ $step }}
                                        @endif
                                    </button>
                                    <span class="ml-2 text-sm font-medium {{ $currentStep >= $step ? 'text-indigo-600' : 'text-gray-500' }} hidden sm:inline">
                                        {{ $title }}
                                    </span>
                                    @if($step < $totalSteps)
                                        <div class="flex-1 h-1 mx-4 {{ $currentStep > $step ? 'bg-indigo-600' : 'bg-gray-200' }} rounded"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Selected Product Card --}}
                    <div class="p-4 mb-6 border border-indigo-200 rounded-lg bg-gradient-to-r from-indigo-50 to-purple-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-4 bg-white rounded-lg shadow-sm">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-indigo-900">{{ $selectedProduct->name }}</h3>
                                    <p class="text-sm text-indigo-700">{{ $selectedProduct->model_number }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium text-indigo-800 bg-indigo-100 rounded-full">
                                {{ $this->categoryLabel }}
                            </span>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        {{-- Step 1: Basic Info --}}
                        @if($currentStep === 1)
                            <div class="space-y-6">
                                <h3 class="pb-2 text-lg font-semibold text-gray-900 border-b">Step 1: 基本情報</h3>

                                {{-- Occurred Date --}}
                                <div>
                                    <x-input-label for="occurred_at" :value="__('発生日')" class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        発生日
                                    </x-input-label>
                                    <x-text-input id="occurred_at" class="block w-full max-w-xs mt-1" type="date" wire:model="occurred_at" required />
                                    @error('occurred_at') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                {{-- Incident Type - Button Group --}}
                                <div>
                                    <x-input-label :value="__('インシデント種別')" class="mb-3" />
                                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                        @foreach($incident_types as $key => $label)
                                            @php $isSelected = $incident_type === $key; @endphp
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" wire:model.live="incident_type" value="{{ $key }}" class="sr-only">
                                                <div class="p-4 text-center border-2 rounded-lg transition-all duration-200 active:scale-95
                                                    {{ $isSelected ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-lg ring-2 ring-indigo-600 ring-offset-2' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 hover:shadow-md' }}">
                                                    <div class="flex flex-col items-center">
                                                        @if($key === 'failure')
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                            </svg>
                                                        @elseif($key === 'maintenance')
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                        @elseif($key === 'damage')
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @endif
                                                        <span class="text-sm font-medium">{{ $label }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('incident_type') <span class="block mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                {{-- Severity - Visual Selector --}}
                                <div>
                                    <x-input-label :value="__('重大度')" class="mb-3" />
                                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                        @foreach($severity_levels as $key => $data)
                                            @php
                                                $isSelected = $severity === $key;
                                                $colorClasses = match($key) {
                                                    'low' => [
                                                        'selected' => 'border-green-500 bg-green-50 shadow-lg ring-2 ring-green-500 ring-offset-2',
                                                        'hover' => 'hover:border-green-300 hover:shadow-md',
                                                        'icon' => 'text-green-600 bg-green-100',
                                                    ],
                                                    'medium' => [
                                                        'selected' => 'border-yellow-500 bg-yellow-50 shadow-lg ring-2 ring-yellow-500 ring-offset-2',
                                                        'hover' => 'hover:border-yellow-300 hover:shadow-md',
                                                        'icon' => 'text-yellow-600 bg-yellow-100',
                                                    ],
                                                    'high' => [
                                                        'selected' => 'border-orange-500 bg-orange-50 shadow-lg ring-2 ring-orange-500 ring-offset-2',
                                                        'hover' => 'hover:border-orange-300 hover:shadow-md',
                                                        'icon' => 'text-orange-600 bg-orange-100',
                                                    ],
                                                    'critical' => [
                                                        'selected' => 'border-red-500 bg-red-50 shadow-lg ring-2 ring-red-500 ring-offset-2',
                                                        'hover' => 'hover:border-red-300 hover:shadow-md',
                                                        'icon' => 'text-red-600 bg-red-100',
                                                    ],
                                                    default => [
                                                        'selected' => 'border-gray-500 bg-gray-50 shadow-lg ring-2 ring-gray-500 ring-offset-2',
                                                        'hover' => 'hover:border-gray-300 hover:shadow-md',
                                                        'icon' => 'text-gray-600 bg-gray-100',
                                                    ],
                                                };
                                            @endphp
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" wire:model.live="severity" value="{{ $key }}" class="sr-only">
                                                <div class="p-4 text-center border-2 rounded-lg transition-all duration-200 active:scale-95
                                                    {{ $isSelected ? $colorClasses['selected'] : 'border-gray-200 bg-white ' . $colorClasses['hover'] }}">
                                                    <div class="flex flex-col items-center">
                                                        <div class="flex items-center justify-center w-8 h-8 mb-2 rounded-full {{ $colorClasses['icon'] }}">
                                                            @if($key === 'low')
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            @elseif($key === 'medium')
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                                </svg>
                                                            @elseif($key === 'high')
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            @endif
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-700">{{ $data['label'] }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('severity') <span class="block mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        {{-- Step 2: Dynamic Details --}}
                        @if($currentStep === 2)
                            <div class="space-y-6">
                                <h3 class="pb-2 text-lg font-semibold text-gray-900 border-b">
                                    Step 2: {{ $this->categoryLabel }}の詳細状況
                                </h3>

                                @foreach($formSchema as $fieldKey => $field)
                                    @php
                                        $fieldType = $field['type'];
                                    @endphp

                                    <div class="space-y-2">
                                        <x-input-label :value="$field['label']" />

                                        @if(isset($field['help']))
                                            <p class="text-xs text-gray-500">{{ $field['help'] }}</p>
                                        @endif

                                        {{-- SLIDER --}}
                                        @if($fieldType === \App\Services\IncidentFormSchema\FieldType::SLIDER)
                                            <div class="flex items-center gap-4" x-data="{ value: $wire.entangle('details.{{ $fieldKey }}') }">
                                                <input
                                                    type="range"
                                                    min="{{ $field['min'] ?? 0 }}"
                                                    max="{{ $field['max'] ?? 100 }}"
                                                    step="{{ $field['step'] ?? 1 }}"
                                                    x-model="value"
                                                    wire:model="details.{{ $fieldKey }}"
                                                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                                                >
                                                <span class="w-16 px-3 py-1 font-semibold text-center text-indigo-600 rounded-lg bg-indigo-50" x-text="value + '{{ $field['unit'] ?? '' }}'"></span>
                                            </div>

                                        {{-- SELECT --}}
                                        @elseif($fieldType === \App\Services\IncidentFormSchema\FieldType::SELECT)
                                            <select
                                                wire:model="details.{{ $fieldKey }}"
                                                class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">選択してください</option>
                                                @foreach($field['options'] as $optKey => $optLabel)
                                                    <option value="{{ $optKey }}">{{ $optLabel }}</option>
                                                @endforeach
                                            </select>

                                        {{-- BOOLEAN --}}
                                        @elseif($fieldType === \App\Services\IncidentFormSchema\FieldType::BOOLEAN)
                                            <div class="flex items-center gap-4">
                                                <button
                                                    type="button"
                                                    wire:click="$set('details.{{ $fieldKey }}', false)"
                                                    class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-200 cursor-pointer
                                                        {{ $details[$fieldKey] === false ? 'bg-gray-700 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                                >
                                                    <span class="flex items-center justify-center gap-2">
                                                        @if($details[$fieldKey] === false)
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        @endif
                                                        {{ $field['labels'][0] ?? 'なし' }}
                                                    </span>
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="$set('details.{{ $fieldKey }}', true)"
                                                    class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-200 cursor-pointer
                                                        {{ $details[$fieldKey] === true ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                                >
                                                    <span class="flex items-center justify-center gap-2">
                                                        @if($details[$fieldKey] === true)
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        @endif
                                                        {{ $field['labels'][1] ?? 'あり' }}
                                                    </span>
                                                </button>
                                            </div>

                                        {{-- BUTTON_GROUP --}}
                                        @elseif($fieldType === \App\Services\IncidentFormSchema\FieldType::BUTTON_GROUP)
                                            <div class="grid grid-cols-2 md:grid-cols-{{ min(count($field['options']), 4) }} gap-3">
                                                @foreach($field['options'] as $optKey => $optData)
                                                    @php $isOptSelected = ($details[$fieldKey] ?? null) === $optKey; @endphp
                                                    <label class="relative cursor-pointer group">
                                                        <input type="radio" wire:model.live="details.{{ $fieldKey }}" value="{{ $optKey }}" class="sr-only">
                                                        <div class="p-3 text-center border-2 rounded-lg transition-all duration-200 active:scale-95
                                                            {{ $isOptSelected ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-lg ring-2 ring-indigo-600 ring-offset-2' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 hover:shadow-md' }}">
                                                            <span class="text-sm font-medium">{{ $optData['label'] ?? $optData }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>

                                        {{-- TEXT --}}
                                        @elseif($fieldType === \App\Services\IncidentFormSchema\FieldType::TEXT)
                                            <x-text-input
                                                wire:model="details.{{ $fieldKey }}"
                                                type="text"
                                                class="block w-full mt-1"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                            />

                                        {{-- TEXTAREA --}}
                                        @elseif($fieldType === \App\Services\IncidentFormSchema\FieldType::TEXTAREA)
                                            <textarea
                                                wire:model="details.{{ $fieldKey }}"
                                                class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                rows="3"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                            ></textarea>

                                        {{-- NUMBER --}}
                                        @elseif($fieldType === \App\Services\IncidentFormSchema\FieldType::NUMBER)
                                            <x-text-input
                                                wire:model="details.{{ $fieldKey }}"
                                                type="number"
                                                class="block w-full mt-1"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                                min="{{ $field['min'] ?? '' }}"
                                                max="{{ $field['max'] ?? '' }}"
                                            />
                                        @endif

                                        @error('details.' . $fieldKey)
                                            <span class="text-sm text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach

                                @if(empty($formSchema))
                                    <div class="py-8 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p>このカテゴリには追加の詳細項目がありません</p>
                                        <p class="mt-1 text-sm">次のステップに進んでください</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Step 3: Resolution & Cost --}}
                        @if($currentStep === 3)
                            <div class="space-y-6">
                                <h3 class="pb-2 text-lg font-semibold text-gray-900 border-b">Step 3: 解決・コスト</h3>

                                {{-- Title --}}
                                <div>
                                    <x-input-label for="title" :value="__('インシデントタイトル')" />
                                    <x-text-input id="title" class="block w-full mt-1" type="text" wire:model="title" required placeholder="例: 画面が反応しなくなった" />
                                    @error('title') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                {{-- Resolution Type --}}
                                <div>
                                    <x-input-label :value="__('対応種別')" class="mb-3" />
                                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                        @foreach($resolution_types as $key => $label)
                                            @php $isSelected = $resolution_type === $key; @endphp
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" wire:model.live="resolution_type" value="{{ $key }}" class="sr-only">
                                                <div class="p-4 text-center border-2 rounded-lg transition-all duration-200 active:scale-95
                                                    {{ $isSelected ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-lg ring-2 ring-indigo-600 ring-offset-2' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 hover:shadow-md' }}">
                                                    <div class="flex flex-col items-center">
                                                        @if($key === 'repair')
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                                                            </svg>
                                                        @elseif($key === 'replacement')
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                        @elseif($key === 'self_resolved')
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @endif
                                                        <span class="text-sm font-medium">{{ $label }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('resolution_type') <span class="block mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                {{-- Symptom Tags --}}
                                <div>
                                    <x-input-label :value="__('症状タグ（複数選択可）')" class="mb-3" />
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($symptom_tags_master as $key => $label)
                                            <button
                                                type="button"
                                                wire:click="toggleSymptomTag('{{ $key }}')"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 cursor-pointer
                                                    {{ in_array($key, $symptom_tags) ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                            >
                                                @if(in_array($key, $symptom_tags))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @endif
                                                {{ $label }}
                                            </button>
                                        @endforeach
                                    </div>
                                    <div class="mt-3">
                                        <x-text-input
                                            id="other_symptom"
                                            class="block w-full"
                                            type="text"
                                            wire:model="other_symptom"
                                            placeholder="その他の症状を入力..."
                                        />
                                    </div>
                                </div>

                                {{-- Cost --}}
                                <div>
                                    <x-input-label for="cost" :value="__('費用（円）')" />
                                    <div class="relative max-w-xs mt-1">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                        <x-text-input id="cost" class="block w-full pl-8" type="number" wire:model="cost" min="0" placeholder="0" />
                                    </div>
                                    @error('cost') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                {{-- Description --}}
                                <div>
                                    <x-input-label for="description" :value="__('詳細メモ（任意）')" />
                                    <textarea
                                        id="description"
                                        wire:model="description"
                                        class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="4"
                                        placeholder="状況の詳細やメモを記入..."
                                    ></textarea>
                                    @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        {{-- Navigation Buttons --}}
                        <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200">
                            <div>
                                @if($currentStep > 1)
                                    <button
                                        type="button"
                                        wire:click="prevStep"
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        戻る
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('products.show', $selectedProduct) }}">
                                    <x-secondary-button type="button">
                                        {{ __('キャンセル') }}
                                    </x-secondary-button>
                                </a>

                                @if($currentStep < $totalSteps)
                                    <button
                                        type="button"
                                        wire:click="nextStep"
                                        class="inline-flex items-center px-6 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700"
                                    >
                                        次へ
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                @else
                                    <x-primary-button class="px-6">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('更新する') }}
                                    </x-primary-button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
