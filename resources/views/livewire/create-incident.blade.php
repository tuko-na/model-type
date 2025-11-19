<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('インシデントの新規登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-visible bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if (session()->has('success'))
                        <div class="mb-4 text-sm font-medium text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="mb-4 text-sm font-medium text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- 製品検索 --}}
                    <div class="mb-6">
                        <x-input-label for="search" :value="__('製品を検索 (製品名または型番)')" />
                        <div class="relative">
                            <x-text-input id="search" class="block w-full mt-1" type="text" wire:model.live.debounce.300ms="search" placeholder="例: MacBook Air, 4T-C50EN1" />
                            @if(!empty($products))
                                <ul class="absolute z-10 w-full mt-1 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg max-h-60">
                                    @foreach($products as $product)
                                        <li class="px-4 py-2 cursor-pointer hover:bg-gray-100" wire:click="selectProduct({{ $product->id }})">
                                            <div class="font-bold">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $product->model_number }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        @error('search') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- インシデント登録フォーム --}}
                    @if($selectedProduct)
                    <form wire:submit.prevent="save">
                        <div class="p-4 mb-6 bg-indigo-100 border-l-4 border-indigo-500">
                            <h3 class="text-lg font-bold text-indigo-900">選択中の製品</h3>
                            <p class="text-indigo-800">{{ $selectedProduct->name }} ({{ $selectedProduct->model_number }})</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- タイトル -->
                            <div class="md:col-span-2">
                                <x-input-label for="title" :value="__('タイトル')" />
                                <x-text-input id="title" class="block w-full mt-1" type="text" wire:model="title" required autofocus />
                                @error('title') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <!-- 発生日 -->
                            <div>
                                <x-input-label for="occurred_at" :value="__('発生日')" />
                                <x-text-input id="occurred_at" class="block w-full mt-1" type="date" wire:model="occurred_at" required />
                                @error('occurred_at') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <!-- 費用 -->
                            <div>
                                <x-input-label for="cost" :value="__('費用')" />
                                <x-text-input id="cost" class="block w-full mt-1" type="number" wire:model="cost" />
                                @error('cost') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <!-- インシデント種別 -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('インシデント種別')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($incident_types as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="incident_type" value="{{ $key }}" class="text-indigo-600 form-radio">
                                        <span class="ml-2">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @error('incident_type') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <!-- 対応種別 -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('対応種別')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($resolution_types as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="resolution_type" value="{{ $key }}" class="text-indigo-600 form-radio">
                                        <span class="ml-2">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @error('resolution_type') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <!-- 症状タグ -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('症状タグ')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($symptom_tags_master as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="symptom_tags" value="{{ $key }}" class="text-indigo-600 rounded form-checkbox">
                                        <span class="ml-2">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @error('symptom_tags') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                <div class="mt-2">
                                    <x-input-label for="other_symptom" :value="__('その他症状')" />
                                    <x-text-input id="other_symptom" class="block w-full mt-1" type="text" wire:model="other_symptom" placeholder="具体的な症状を入力" />
                                    @error('other_symptom') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- 詳細 -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('詳細')" />
                                <textarea id="description" wire:model="description" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4"></textarea>
                                @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('incidents.index') }}" class="mr-4">
                                <x-secondary-button type="button">
                                    {{ __('キャンセル') }}
                                </x-secondary-button>
                            </a>
                            <x-primary-button>
                                {{ __('登録する') }}
                            </x-primary-button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>