<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('インシデントの新規登録') }}
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

                    {{-- 製品検索 (Step 0) --}}
                    @if($currentStep === 0)
                        <div class="mb-6">
                            <div class="mb-8 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 mb-4 bg-indigo-100 rounded-full">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">製品を選択してください</h3>
                                <p class="mt-2 text-sm text-gray-500">インシデントを登録する製品を検索して選択します</p>
                            </div>

                            <div class="relative max-w-md mx-auto">
                                <x-text-input
                                    id="search"
                                    class="block w-full pl-10 pr-4"
                                    type="text"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="製品名または型番で検索..."
                                />
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>

                                @if(!empty($products))
                                    <ul class="absolute z-10 w-full mt-1 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg max-h-60">
                                        @foreach($products as $product)
                                            <li
                                                class="px-4 py-3 transition-all duration-150 border-b cursor-pointer hover:bg-indigo-50 active:bg-indigo-100 last:border-b-0 hover:pl-6"
                                                wire:click="selectProduct({{ $product->id }})"
                                            >
                                                <div class="flex items-center">
                                                    <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 mr-3 bg-gray-100 rounded-lg">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $product->model_number }} @if($product->category)<span class="ml-2 px-2 py-0.5 text-xs bg-gray-200 rounded-full">{{ $product->category }}</span>@endif</div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Wizard Form --}}
                    @if($selectedProduct && $currentStep >= 1)
                        {{-- Progress Bar --}}
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-2">
                                @foreach($this->stepTitles as $step => $title)
                                    <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                                        <button
                                            type="button"
                                            wire:click="goToStep({{ $step }})"
                                            class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-200
                                                {{ $currentStep >= $step ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }}
                                                {{ $currentStep > $step ? 'cursor-pointer hover:bg-indigo-700' : '' }}
                                                {{ $currentStep < $step ? 'cursor-not-allowed' : '' }}"
                                            @if($currentStep < $step) disabled @endif
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
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 text-xs font-medium text-indigo-800 bg-indigo-100 rounded-full">
                                        {{ $this->categoryLabel }}
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="clearProduct"
                                        class="p-2 text-gray-400 transition-colors hover:text-red-500"
                                        title="製品を変更"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form wire:submit.prevent="save">
                            {{-- Step 1: Basic Info --}}
                            @if($currentStep === 1)
                                <div class="space-y-6" x-data x-init="$el.querySelector('input, select, button')?.focus()">
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
                                    <div x-data="{ selected: @entangle('incident_type') }">
                                        <x-input-label :value="__('インシデント種別')" class="mb-3" />
                                        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                            @foreach($incident_types as $key => $label)
                                                <label
                                                    class="relative cursor-pointer group"
                                                    @click="selected = '{{ $key }}'"
                                                >
                                                    <input type="radio" wire:model="incident_type" value="{{ $key }}" class="sr-only">
                                                    {{-- 選択時のチェックマーク --}}
                                                    <div
                                                        class="absolute z-10 items-center justify-center w-6 h-6 text-white transition-all duration-200 bg-indigo-600 rounded-full shadow-lg -top-2 -right-2"
                                                        :class="selected === '{{ $key }}' ? 'flex scale-100 opacity-100' : 'hidden scale-0 opacity-0'"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                    <div
                                                        class="p-4 text-center border-2 rounded-lg transition-all duration-200
                                                            hover:border-gray-300 hover:bg-gray-50 hover:shadow-sm hover:scale-[1.02]
                                                            active:scale-[0.98]"
                                                        :class="selected === '{{ $key }}'
                                                            ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-md scale-[1.02]'
                                                            : 'border-gray-200 bg-white'"
                                                    >
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
                                    <div x-data="{ selected: @entangle('severity') }">
                                        <x-input-label :value="__('重大度')" class="mb-3" />
                                        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                            @foreach($severity_levels as $key => $data)
                                                @php
                                                    $colorClasses = match($key) {
                                                        'low' => ['bg' => 'bg-green-500', 'border' => 'border-green-500', 'bgLight' => 'bg-green-50', 'ring' => 'ring-green-500', 'hover' => 'hover:border-green-300', 'icon' => 'bg-green-100 text-green-600'],
                                                        'medium' => ['bg' => 'bg-yellow-500', 'border' => 'border-yellow-500', 'bgLight' => 'bg-yellow-50', 'ring' => 'ring-yellow-500', 'hover' => 'hover:border-yellow-300', 'icon' => 'bg-yellow-100 text-yellow-600'],
                                                        'high' => ['bg' => 'bg-orange-500', 'border' => 'border-orange-500', 'bgLight' => 'bg-orange-50', 'ring' => 'ring-orange-500', 'hover' => 'hover:border-orange-300', 'icon' => 'bg-orange-100 text-orange-600'],
                                                        default => ['bg' => 'bg-red-500', 'border' => 'border-red-500', 'bgLight' => 'bg-red-50', 'ring' => 'ring-red-500', 'hover' => 'hover:border-red-300', 'icon' => 'bg-red-100 text-red-600'],
                                                    };
                                                @endphp
                                                <label
                                                    class="relative cursor-pointer group"
                                                    @click="selected = '{{ $key }}'"
                                                >
                                                    <input type="radio" wire:model="severity" value="{{ $key }}" class="sr-only">
                                                    {{-- 選択時のチェックマーク --}}
                                                    <div
                                                        class="absolute -top-2 -right-2 w-6 h-6 {{ $colorClasses['bg'] }} rounded-full items-center justify-center text-white z-10 shadow-lg transition-all duration-200"
                                                        :class="selected === '{{ $key }}' ? 'flex scale-100 opacity-100' : 'hidden scale-0 opacity-0'"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                    <div
                                                        class="p-4 text-center border-2 rounded-lg transition-all duration-200 hover:shadow-md hover:scale-[1.02] active:scale-[0.98] {{ $colorClasses['hover'] }}"
                                                        :class="selected === '{{ $key }}'
                                                            ? '{{ $colorClasses['border'] }} {{ $colorClasses['bgLight'] }} ring-2 ring-offset-2 {{ $colorClasses['ring'] }} scale-[1.02]'
                                                            : 'border-gray-200 bg-white'"
                                                    >
                                                        <div class="flex flex-col items-center">
                                                            <div class="w-8 h-8 rounded-full mb-2 flex items-center justify-center {{ $colorClasses['icon'] }}">
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
                                            <x-input-label :value="$field['label']" class="flex items-center gap-2">
                                                @if(isset($field['icon']))
                                                    <span class="text-gray-400">●</span>
                                                @endif
                                                {{ $field['label'] }}
                                            </x-input-label>

                                            @if(isset($field['help']))
                                                <p class="text-xs text-gray-500">{{ $field['help'] }}</p>
                                            @endif

                                            {{-- SLIDER --}}
                                            @if($fieldType === \App\Services\IncidentFormSchema\FieldType::SLIDER)
                                                <div class="flex items-center gap-4" x-data="{ value: @entangle('details.' . $fieldKey) }">
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
                                                <div class="flex items-center gap-4" x-data="{ checked: @entangle('details.' . $fieldKey) }">
                                                    <button
                                                        type="button"
                                                        @click="checked = false"
                                                        :class="!checked ? 'bg-gray-600 text-white shadow-md scale-[1.02]' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:shadow-sm'"
                                                        class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-200 cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
                                                    >
                                                        <span class="flex items-center justify-center gap-2">
                                                            <svg x-show="!checked" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            {{ $field['labels'][0] ?? 'いいえ' }}
                                                        </span>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="checked = true"
                                                        :class="checked ? 'bg-indigo-600 text-white shadow-md scale-[1.02]' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:shadow-sm'"
                                                        class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-200 cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
                                                    >
                                                        <span class="flex items-center justify-center gap-2">
                                                            <svg x-show="checked" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            {{ $field['labels'][1] ?? 'はい' }}
                                                        </span>
                                                    </button>
                                                </div>

                                            {{-- BUTTON_GROUP --}}
                                            @elseif($fieldType === \App\Services\IncidentFormSchema\FieldType::BUTTON_GROUP)
                                                <div class="grid grid-cols-2 md:grid-cols-{{ min(count($field['options']), 4) }} gap-3" x-data="{ selected: @entangle('details.' . $fieldKey) }">
                                                    @foreach($field['options'] as $optKey => $optData)
                                                        <label
                                                            class="relative cursor-pointer group"
                                                            @click="selected = '{{ $optKey }}'"
                                                        >
                                                            <input type="radio" wire:model="details.{{ $fieldKey }}" value="{{ $optKey }}" class="sr-only">
                                                            {{-- 選択時のチェックマーク --}}
                                                            <div
                                                                class="absolute z-10 items-center justify-center w-5 h-5 text-white transition-all duration-200 bg-indigo-600 rounded-full shadow -top-2 -right-2"
                                                                :class="selected === '{{ $optKey }}' ? 'flex scale-100 opacity-100' : 'hidden scale-0 opacity-0'"
                                                            >
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                            </div>
                                                            <div
                                                                class="p-3 text-center border-2 rounded-lg transition-all duration-200
                                                                    hover:border-gray-300 hover:bg-gray-50 hover:shadow-sm hover:scale-[1.02]
                                                                    active:scale-[0.98]"
                                                                :class="selected === '{{ $optKey }}'
                                                                    ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-md scale-[1.02]'
                                                                    : 'border-gray-200 bg-white'"
                                                            >
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

                                    {{-- Resolution Type - Button Group --}}
                                    <div x-data="{ selected: @entangle('resolution_type') }">
                                        <x-input-label :value="__('対応種別')" class="mb-3" />
                                        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                            @foreach($resolution_types as $key => $label)
                                                <label
                                                    class="relative cursor-pointer group"
                                                    @click="selected = '{{ $key }}'"
                                                >
                                                    <input type="radio" wire:model="resolution_type" value="{{ $key }}" class="sr-only">
                                                    {{-- 選択時のチェックマーク --}}
                                                    <div
                                                        class="absolute z-10 items-center justify-center w-6 h-6 text-white transition-all duration-200 bg-indigo-600 rounded-full shadow-lg -top-2 -right-2"
                                                        :class="selected === '{{ $key }}' ? 'flex scale-100 opacity-100' : 'hidden scale-0 opacity-0'"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                    <div
                                                        class="p-4 text-center border-2 rounded-lg transition-all duration-200
                                                            hover:border-gray-300 hover:bg-gray-50 hover:shadow-sm hover:scale-[1.02]
                                                            active:scale-[0.98]"
                                                        :class="selected === '{{ $key }}'
                                                            ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-md scale-[1.02]'
                                                            : 'border-gray-200 bg-white'"
                                                    >
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
                                    <div x-data="{ selectedTags: @entangle('symptom_tags') }">
                                        <x-input-label :value="__('症状タグ（複数選択可）')" class="mb-3" />
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($symptom_tags_master as $key => $label)
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 cursor-pointer
                                                        hover:scale-105 active:scale-95"
                                                    :class="selectedTags.includes('{{ $key }}')
                                                        ? 'bg-indigo-600 text-white shadow-md scale-105'
                                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    @click="selectedTags.includes('{{ $key }}') ? selectedTags = selectedTags.filter(t => t !== '{{ $key }}') : selectedTags.push('{{ $key }}')"
                                                >
                                                    {{-- 選択時のチェックマーク --}}
                                                    <svg
                                                        class="w-4 h-4 transition-all duration-200"
                                                        :class="selectedTags.includes('{{ $key }}') ? 'block' : 'hidden'"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                    >
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
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
                                        <x-input-label for="cost" :value="__('費用（円）')" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            費用（円）
                                        </x-input-label>
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
                                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                            戻る
                                        </button>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3">
                                    <a href="{{ route('incidents.index') }}">
                                        <x-secondary-button type="button">
                                            {{ __('キャンセル') }}
                                        </x-secondary-button>
                                    </a>

                                    @if($currentStep < $totalSteps)
                                        <button
                                            type="button"
                                            wire:click="nextStep"
                                            class="inline-flex items-center px-6 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                                            {{ __('登録する') }}
                                        </x-primary-button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>