<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('製品の新規登録') }}
        </h2>
    </x-slot>

    @if ($errors->any())
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 1em; background-color: #fee;">
            <strong>入力内容にエラーがあります。</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <!-- 型番 -->
                        <div>
                            <x-input-label for="model_number" :value="__('型番')" />
                            <x-text-input id="model_number" class="block w-full mt-1" type="text" name="model_number" :value="old('model_number')" required autofocus />
                            <x-input-error :messages="$errors->get('model_number')" class="mt-2" />
                        </div>

                        <!-- 製品名 -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('製品名')" />
                            <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- メーカー -->
                        <div class="mt-4">
                            <x-input-label for="manufacturer" :value="__('メーカー')" />
                            <x-text-input id="manufacturer" class="block w-full mt-1" type="text" name="manufacturer" :value="old('manufacturer')" required />
                            <x-input-error :messages="$errors->get('manufacturer')" class="mt-2" />
                        </div>

                        <!-- カテゴリ -->
                        <div class="mt-4">
                            <x-input-label for="category" :value="__('カテゴリ')" />
                            <x-text-input id="category" class="block w-full mt-1" type="text" name="category" :value="old('category')" required />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- 購入日 -->
                        <div class="mt-4">
                            <x-input-label for="purchase_date" :value="__('購入日')" />
                            <x-text-input id="purchase_date" class="block w-full mt-1" type="date" name="purchase_date" :value="old('purchase_date')" required />
                            <x-input-error :messages="$errors->get('purchase_date')" class="mt-2" />
                        </div>

                        <!-- 保証終了日 -->
                        <div class="mt-4">
                            <x-input-label for="warranty_expires_on" :value="__('保証終了日')" />
                            <x-text-input id="warranty_expires_on" class="block w-full mt-1" type="date" name="warranty_expires_on" :value="old('warranty_expires_on')" />
                            <x-input-error :messages="$errors->get('warranty_expires_on')" class="mt-2" />
                        </div>

                        <!-- 購入金額 -->
                        <div class="mt-4">
                            <x-input-label for="price" :value="__('購入金額')" />
                            <x-text-input id="price" class="block w-full mt-1" type="number" name="price" :value="old('price')" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- 購入状態 -->
                        <div class="mt-4">
                            <x-input-label for="purchase_condition" :value="__('購入状態')" />
                            <select id="purchase_condition" name="purchase_condition" class="block w-full mt-1" required>
                                <option value="">選択してください</option>
                                <option value="新品" {{ old('purchase_condition') == '新品' ? 'selected' : '' }}>新品</option>
                                <option value="中古" {{ old('purchase_condition') == '中古' ? 'selected' : '' }}>中古</option>
                                <option value="再生品" {{ old('purchase_condition') == '再生品' ? 'selected' : '' }}>再生品</option>
                                <option value="不明" {{ old('purchase_condition') == '不明' ? 'selected' : '' }}>不明</option>
                            </select>
                            <x-input-error :messages="$errors->get('purchase_condition')" class="mt-2" />
                        </div>

                        <!-- 耐用年数 -->
                        <div class="mt-4">
                            <x-input-label for="useful_life" :value="__('耐用年数 (年)')" />
                            <x-text-input id="useful_life" class="block w-full mt-1" type="number" name="useful_life" :value="old('useful_life')" placeholder="例: 5" min="0" step="1" />
                            <x-input-error :messages="$errors->get('useful_life')" class="mt-2" />
                        </div>

                        <!-- ステータス -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('ステータス')" />
                            <select id="status" name="status" class="block w-full mt-1" required>
                                <option value="">選択してください</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>使用中</option>
                                <option value="in_storage" {{ old('status') == 'in_storage' ? 'selected' : '' }}>保管中</option>
                                <option value="in_repair" {{ old('status') == 'in_repair' ? 'selected' : '' }}>修理中</option>
                                <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>廃棄済み</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- 備考 -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('備考')" />
                            <textarea id="notes" name="notes" class="block w-full mt-1" rows="4">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('登録する') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
