<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            インシデントの編集 for <span class="font-bold">{{ $product->name }}</span>
        </h2>
    </x-slot>

    @php
        $current_tags = explode(',', old('symptom_tags', $incident->symptom_tags));
        $other_symptom = '';
        $predefined_tags = array_keys($symptom_tags);
        $other_tags = array_diff($current_tags, $predefined_tags);
        if (!empty($other_tags)) {
            $other_symptom = implode(',', $other_tags);
        }
    @endphp

    @if ($errors->any())
        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 text-red-800 bg-red-100 border border-red-400 rounded">
                    <strong class="font-bold">入力内容にエラーがあります。</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="py-12" x-data="{ showOtherSymptom: {{ old('other_symptom', $other_symptom) ? 'true' : 'false' }} }">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('products.incidents.update', [$product, $incident]) }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- タイトル -->
                            <div class="md:col-span-2">
                                <x-input-label for="title" :value="__('タイトル')" />
                                <x-text-input id="title" class="block w-full mt-1" type="text" name="title" :value="old('title', $incident->title)" required autofocus />
                            </div>

                            <!-- 発生日 -->
                            <div>
                                <x-input-label for="occurred_at" :value="__('発生日')" />
                                <x-text-input id="occurred_at" class="block w-full mt-1" type="date" name="occurred_at" :value="old('occurred_at', $incident->occurred_at)" required />
                            </div>

                            <!-- 費用 -->
                            <div>
                                <x-input-label for="cost" :value="__('費用')" />
                                <x-text-input id="cost" class="block w-full mt-1" type="number" name="cost" :value="old('cost', $incident->cost)" />
                            </div>

                            <!-- インシデント種別 -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('インシデント種別')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($incident_types as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="incident_type" value="{{ $key }}" @checked(old('incident_type', $incident->incident_type) == $key) class="text-indigo-600 form-radio">
                                        <span class="ml-2">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 対応種別 -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('対応種別')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($resolution_types as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="resolution_type" value="{{ $key }}" @checked(old('resolution_type', $incident->resolution_type) == $key) class="text-indigo-600 form-radio">
                                        <span class="ml-2">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 症状タグ -->
                            <div class="md:col-span-2">
                                <x-input-label :value="__('症状タグ')" />
                                <div class="mt-2 space-y-2">
                                    @foreach($symptom_tags as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="symptom_tags[]" value="{{ $key }}" @checked(in_array($key, $current_tags)) class="text-indigo-600 rounded form-checkbox">
                                        <span class="ml-2">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" @click="showOtherSymptom = !showOtherSymptom" @checked($other_symptom) class="text-indigo-600 rounded form-checkbox">
                                        <span class="ml-2">その他</span>
                                    </label>
                                </div>
                                <div x-show="showOtherSymptom" class="mt-2">
                                    <x-text-input name="other_symptom" class="block w-full mt-1" type="text" :value="old('other_symptom', $other_symptom)" placeholder="具体的な症状を入力" />
                                </div>
                            </div>

                            <!-- 詳細 -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('詳細')" />
                                <textarea id="description" name="description" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4">{{ old('description', $incident->description) }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('products.show', $product) }}" class="mr-4">
                                <x-secondary-button type="button">
                                    {{ __('キャンセル') }}
                                </x-secondary-button>
                            </a>
                            <x-primary-button>
                                {{ __('更新する') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
