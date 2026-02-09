<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('製品の編集') }}
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
                    <form method="POST" action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PATCH')

                        <!-- 型番 -->
                        <div>
                            <x-input-label for="model_number" :value="__('型番')" />
                            <x-text-input id="model_number" class="block w-full mt-1" type="text" name="model_number" :value="old('model_number', $product->model_number)" required autofocus />
                            <x-input-error :messages="$errors->get('model_number')" class="mt-2" />
                        </div>

                        <!-- 製品名 -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('製品名')" />
                            <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name', $product->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- メーカー -->
                        <div class="mt-4">
                            <x-input-label for="manufacturer" :value="__('メーカー')" />
                            <x-text-input id="manufacturer" class="block w-full mt-1" type="text" name="manufacturer" :value="old('manufacturer', $product->manufacturer)" required />
                            <x-input-error :messages="$errors->get('manufacturer')" class="mt-2" />
                        </div>

                        <!-- ジャンル -->
                        <div class="mt-4">
                            <x-input-label for="genre_name" :value="__('ジャンル')" />
                            <x-text-input id="genre_name" class="block w-full mt-1" type="text" name="genre_name" :value="old('genre_name', $product->genre_name)" required placeholder="例: スマートフォン・タブレット" />
                            <x-input-error :messages="$errors->get('genre_name')" class="mt-2" />
                        </div>

                        <!-- 楽天リンク（表示のみ） -->
                        @if($product->rakuten_url)
                            <div class="mt-4">
                                <x-input-label :value="__('楽天リンク')" />
                                <a 
                                    href="{{ $product->rakuten_url }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 mt-1 px-3 py-2 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    楽天で見る
                                </a>
                            </div>
                        @endif

                        <!-- 購入日 -->
                        <div class="mt-4">
                            <x-input-label for="purchase_date" :value="__('購入日')" />
                            <x-text-input id="purchase_date" class="block w-full mt-1" type="date" name="purchase_date" :value="old('purchase_date', $product->purchase_date)" required />
                            <x-input-error :messages="$errors->get('purchase_date')" class="mt-2" />
                        </div>

                        <!-- 保証終了日 -->
                        <div class="mt-4">
                            <x-input-label for="warranty_expires_on" :value="__('保証終了日')" />
                            <x-text-input id="warranty_expires_on" class="block w-full mt-1" type="date" name="warranty_expires_on" :value="old('warranty_expires_on', $product->warranty_expires_on)" />
                            <x-input-error :messages="$errors->get('warranty_expires_on')" class="mt-2" />
                        </div>

                        <!-- 購入金額 -->
                        <div class="mt-4">
                            <x-input-label for="price" :value="__('購入金額')" />
                            <x-text-input id="price" class="block w-full mt-1" type="number" name="price" :value="old('price', $product->price)" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- 購入状態 -->
                        <div class="mt-4">
                            <x-input-label for="purchase_condition" :value="__('購入状態')" />
                            <select id="purchase_condition" name="purchase_condition" class="block w-full mt-1" required>
                                <option value="新品" {{ old('purchase_condition', $product->purchase_condition) == '新品' ? 'selected' : '' }}>新品</option>
                                <option value="中古" {{ old('purchase_condition', $product->purchase_condition) == '中古' ? 'selected' : '' }}>中古</option>
                                <option value="再生品" {{ old('purchase_condition', $product->purchase_condition) == '再生品' ? 'selected' : '' }}>再生品</option>
                                <option value="不明" {{ old('purchase_condition', $product->purchase_condition) == '不明' ? 'selected' : '' }}>不明</option>
                            </select>
                            <x-input-error :messages="$errors->get('purchase_condition')" class="mt-2" />
                        </div>

                        <!-- 耐用年数 -->
                        <div class="mt-4">
                            <x-input-label for="useful_life" :value="__('耐用年数 (年)')" />
                            <x-text-input id="useful_life" class="block w-full mt-1" type="number" name="useful_life" :value="old('useful_life', $product->useful_life)" placeholder="例: 5" min="0" step="1" />
                            <x-input-error :messages="$errors->get('useful_life')" class="mt-2" />
                        </div>

                        <!-- ステータス -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('ステータス')" />
                            <select id="status" name="status" class="block w-full mt-1" required>
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>使用中</option>
                                <option value="in_storage" {{ old('status', $product->status) == 'in_storage' ? 'selected' : '' }}>保管中</option>
                                <option value="in_repair" {{ old('status', $product->status) == 'in_repair' ? 'selected' : '' }}>修理中</option>
                                <option value="disposed" {{ old('status', $product->status) == 'disposed' ? 'selected' : '' }}>廃棄済み</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- 備考 -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('備考')" />
                            <textarea id="notes" name="notes" class="block w-full mt-1" rows="4">{{ old('notes', $product->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('products.show', $product) }}" class="mr-4">
                                <x-secondary-button type="button">
                                    {{ __('キャンセル') }}
                                </x-secondary-button>
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('更新する') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
