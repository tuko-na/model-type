<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('製品の新規登録') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

        {{-- ======================================== --}}
        {{-- ステップ1: 検索 --}}
        {{-- ======================================== --}}
        @if($step === 1)
            <div class="min-h-[60vh] flex flex-col items-center justify-center">
                {{-- ヘッダー --}}
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 mb-6 shadow-lg bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h2 class="mb-2 text-3xl font-bold text-gray-800">製品を検索</h2>
                    <p class="text-gray-500">型番や製品名を入力してください</p>
                </div>

                {{-- 検索ボックス --}}
                <div class="w-full max-w-2xl">
                    <div class="relative">
                        <input 
                            type="text"
                            wire:model.live.debounce.300ms="searchQuery"
                            placeholder="例: iPhone 15 Pro, WH-1000XM5, Surface Pro..."
                            class="w-full px-6 py-5 text-xl transition-all duration-200 border-2 border-gray-200 shadow-lg rounded-2xl focus:border-red-500 focus:ring-4 focus:ring-red-100"
                            autofocus
                        >
                        
                        {{-- ローディングスピナー --}}
                        @if($isSearching)
                            <div class="absolute -translate-y-1/2 right-5 top-1/2">
                                <svg class="w-6 h-6 text-red-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- DBからの検索結果（既存製品） --}}
                    @if(count($dbProducts) > 0)
                        <div class="mt-6 space-y-3">
                            <p class="flex items-center gap-2 mb-2 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                                データベースに登録済みの製品
                            </p>
                            @foreach($dbProducts as $product)
                                <div 
                                    wire:click="selectDbProduct({{ $product['id'] }})"
                                    class="flex items-center gap-4 p-4 transition-all duration-200 bg-white border-2 border-blue-100 cursor-pointer rounded-xl hover:border-blue-400 hover:shadow-lg group"
                                >
                                    <div class="flex items-center justify-center flex-shrink-0 rounded-lg w-14 h-14 bg-blue-50">
                                        <svg class="text-blue-500 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-gray-900 transition-colors group-hover:text-blue-600">
                                            {{ $product['name'] }}
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            @if(!empty($product['model_number']))
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                                    {{ $product['model_number'] }}
                                                </span>
                                            @endif
                                            @if(!empty($product['manufacturer']))
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                                    {{ $product['manufacturer'] }}
                                                </span>
                                            @endif
                                            @if(!empty($product['genre_name']))
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs bg-blue-50 text-blue-600 rounded">
                                                    {{ $product['genre_name'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center px-2.5 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path></svg>
                                        情報をコピー
                                    </span>

                                    <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-colors group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- 検索結果（楽天API候補カード） --}}
                    @if(count($suggestions) > 0)
                        <div class="mt-6 space-y-3">
                            <p class="mb-2 text-sm text-gray-500">楽天の候補から選択してください</p>
                            @foreach($suggestions as $index => $suggestion)
                                <div 
                                    wire:click="selectSuggestion({{ $index }})"
                                    class="flex items-center gap-4 p-4 transition-all duration-200 bg-white border-2 border-gray-100 cursor-pointer rounded-xl hover:border-red-400 hover:shadow-lg group"
                                >
                                    {{-- サムネイル --}}
                                    @if(!empty($suggestion['_display_image']))
                                        <img 
                                            src="{{ $suggestion['_display_image'] }}" 
                                            alt="" 
                                            class="flex-shrink-0 object-contain w-20 h-20 rounded-lg bg-gray-50"
                                        >
                                    @else
                                        <div class="flex items-center justify-center flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <div class="text-lg font-bold text-gray-900 transition-colors group-hover:text-red-600">
                                            {{ $suggestion['product_name'] }}
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                            @if(!empty($suggestion['maker_name']))
                                                <span class="inline-flex items-center px-2.5 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg">
                                                    {{ $suggestion['maker_name'] }}
                                                </span>
                                            @endif
                                            @if(!empty($suggestion['genre_name']))
                                                <span class="inline-flex items-center px-2.5 py-1 text-sm bg-blue-50 text-blue-700 rounded-lg">
                                                    {{ $suggestion['genre_name'] }}
                                                </span>
                                            @endif
                                            @if($suggestion['source'] === 'product_api')
                                                <span class="inline-flex items-center px-2.5 py-1 text-sm bg-green-50 text-green-700 rounded-lg">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                    カタログ情報あり
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- 参考価格 --}}
                                    @if(!empty($suggestion['_display_price']))
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-lg font-bold text-gray-800">¥{{ number_format($suggestion['_display_price']) }}</div>
                                            <div class="text-xs text-gray-400">参考価格</div>
                                        </div>
                                    @endif

                                    {{-- 矢印 --}}
                                    <svg class="flex-shrink-0 w-6 h-6 text-gray-400 transition-colors group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- 検索結果が0件の場合 --}}
                    @if($hasSearched && count($suggestions) === 0 && count($dbProducts) === 0 && mb_strlen($searchQuery) >= 2)
                        <div class="p-8 mt-6 text-center border-2 border-gray-200 border-dashed bg-gray-50 rounded-xl">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mb-4 text-gray-600">「{{ $searchQuery }}」に一致する製品が見つかりませんでした</p>
                            <button 
                                wire:click="skipToManualEntry"
                                class="inline-flex items-center px-6 py-3 font-medium text-white transition-colors bg-gray-800 rounded-xl hover:bg-gray-700"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                手動で入力する
                            </button>
                        </div>
                    @endif
                </div>

                {{-- 楽天クレジット --}}
                @if($this->isRakutenConfigured())
                    <div class="flex items-center justify-center gap-2 mt-12 text-xs text-gray-400">
                        <span>Supported by</span>
                        <a href="https://webservice.rakuten.co.jp/" target="_blank" rel="noopener noreferrer" class="font-medium text-red-600 hover:underline">
                            Rakuten Web Service
                        </a>
                    </div>
                @endif

                {{-- 手動入力への直接遷移リンク --}}
                <div class="mt-6">
                    <button 
                        wire:click="skipToManualEntry"
                        class="text-sm text-gray-500 underline hover:text-gray-700"
                    >
                        検索せずに手動で登録する
                    </button>
                </div>
            </div>
        @endif

        {{-- ======================================== --}}
        {{-- ステップ2: 詳細入力 --}}
        {{-- ======================================== --}}
        @if($step === 2)
            <div class="overflow-visible bg-white shadow-xl rounded-2xl">
                {{-- 戻るボタン + ヘッダー --}}
                <div class="flex items-center gap-4 px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                    <button 
                        wire:click="backToSearch"
                        class="inline-flex items-center justify-center w-10 h-10 text-gray-600 transition-all bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">製品情報の入力</h3>
                        <p class="text-sm text-gray-500">
                            @if($selectedSuggestion)
                                楽天APIから取得したデータを確認・補完してください
                            @elseif($selectedDbProduct)
                                既存製品の情報をコピーしました
                            @else
                                製品情報を入力してください
                            @endif
                        </p>
                    </div>
                </div>

                {{-- 選択された製品のプレビュー（楽天API） --}}
                @if($selectedSuggestion)
                    <div class="px-6 py-4 border-b border-green-100 bg-green-50">
                        <div class="flex items-center gap-4">
                            @if(!empty($selectedSuggestion['_display_image']))
                                <img 
                                    src="{{ $selectedSuggestion['_display_image'] }}" 
                                    alt="" 
                                    class="object-contain w-16 h-16 bg-white rounded-lg shadow-sm"
                                >
                            @endif
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="font-semibold text-green-800">{{ $selectedSuggestion['product_name'] }}</span>
                                </div>
                                <p class="mt-1 text-sm text-green-600">楽天APIからデータを取得しました</p>
                            </div>
                        </div>
                    </div>
                @elseif($selectedDbProduct)
                    {{-- 選択された製品のプレビュー（DB） --}}
                    <div class="px-6 py-4 border-b border-blue-100 bg-blue-50">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="font-semibold text-blue-800">{{ $selectedDbProduct->name }}</span>
                                </div>
                                <p class="mt-1 text-sm text-blue-600">既存製品の情報をコピーしました（新しい製品として登録されます）</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 登録フォーム --}}
                <form wire:submit="save" class="p-6 space-y-8">
                    {{-- 製品基本情報 --}}
                    <div>
                        <h4 class="flex items-center gap-2 mb-4 text-lg font-bold text-gray-800">
                            <span class="inline-flex items-center justify-center text-sm text-white bg-gray-800 rounded-lg w-7 h-7">1</span>
                            製品基本情報
                        </h4>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 pl-9">
                            {{-- 製品名 --}}
                            <div class="md:col-span-2">
                                <x-input-label for="name" :value="__('製品名')" class="font-medium" />
                                <x-text-input 
                                    id="name" 
                                    wire:model="name"
                                    class="block w-full mt-1.5 {{ $selectedSuggestion ? 'bg-green-50 border-green-300 focus:border-green-500 focus:ring-green-200' : ($selectedDbProduct ? 'bg-blue-50 border-blue-300 focus:border-blue-500 focus:ring-blue-200' : '') }}" 
                                    type="text" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- 型番 --}}
                            <div>
                                <x-input-label for="model_number" :value="__('型番')" class="font-medium" />
                                <x-text-input 
                                    id="model_number" 
                                    wire:model="model_number"
                                    class="block w-full mt-1.5 {{ ($selectedSuggestion && !empty($selectedSuggestion['model_number'])) ? 'bg-green-50 border-green-300' : ($selectedDbProduct && $selectedDbProduct->model_number ? 'bg-blue-50 border-blue-300' : '') }}" 
                                    type="text" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('model_number')" class="mt-2" />
                            </div>

                            {{-- メーカー --}}
                            <div>
                                <x-input-label for="manufacturer" :value="__('メーカー')" class="font-medium" />
                                <x-text-input 
                                    id="manufacturer" 
                                    wire:model="manufacturer"
                                    class="block w-full mt-1.5 {{ ($selectedSuggestion && !empty($selectedSuggestion['maker_name'])) ? 'bg-green-50 border-green-300' : ($selectedDbProduct && $selectedDbProduct->manufacturer ? 'bg-blue-50 border-blue-300' : '') }}" 
                                    type="text" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('manufacturer')" class="mt-2" />
                            </div>

                            {{-- ジャンル --}}
                            <div class="min-h-[90px] relative" x-data="{ open: false, value: @entangle('genre_name').defer }" @click.outside="open = false">
                                <x-input-label for="genre_name" :value="__('ジャンル')" class="font-medium" />
                                <button
                                    type="button"
                                    class="flex items-center justify-between w-full mt-1.5 px-3 py-2 min-h-[42px] border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ ($selectedSuggestion && !empty($selectedSuggestion['genre_name'])) ? 'bg-green-50 border-green-300' : ($selectedDbProduct && $selectedDbProduct->genre_name ? 'bg-blue-50 border-blue-300' : 'bg-white border-gray-300') }}"
                                    @click="open = !open"
                                    :aria-expanded="open.toString()"
                                    aria-controls="genre-options"
                                >
                                    <span class="text-left truncate" x-text="value || '選択してください'"></span>
                                    <svg class="flex-shrink-0 w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <input type="hidden" id="genre_name" wire:model.blur="genre_name" :value="value" required>

                                <div
                                    x-show="open"
                                    x-transition
                                    id="genre-options"
                                    class="absolute z-50 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg max-h-60"
                                    role="listbox"
                                >
                                    <div class="px-2 py-1 text-sm text-gray-500">選択してください</div>
                                    @foreach($genreCategories as $category => $subcategories)
                                        <div class="sticky top-0 px-3 py-1 text-xs font-semibold text-gray-500 bg-gray-50">
                                            {{ $category }}
                                        </div>
                                        @foreach($subcategories as $subcategory)
                                            <button
                                                type="button"
                                                class="w-full px-3 py-2 text-sm text-left hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none"
                                                @click="value = '{{ $subcategory }}'; open = false"
                                                role="option"
                                                :aria-selected="value === '{{ $subcategory }}'"
                                            >
                                                {{ $subcategory }}
                                            </button>
                                        @endforeach
                                    @endforeach
                                </div>

                                <p class="h-4 mt-1 text-xs text-gray-400">{{ $genre_id ? 'ジャンルID: ' . $genre_id : '' }}</p>
                                <x-input-error :messages="$errors->get('genre_name')" class="mt-2" />
                            </div>

                            {{-- 楽天リンク --}}
                            @if($rakuten_url)
                                <div>
                                    <x-input-label :value="__('楽天リンク')" class="font-medium" />
                                    <a 
                                        href="{{ $rakuten_url }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 mt-1.5 px-4 py-2.5 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors font-medium"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        楽天で見る
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 購入情報 --}}
                    <div>
                        <h4 class="flex items-center gap-2 mb-4 text-lg font-bold text-gray-800">
                            <span class="inline-flex items-center justify-center text-sm text-white bg-gray-800 rounded-lg w-7 h-7">2</span>
                            購入情報
                        </h4>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 pl-9">
                            {{-- 購入日 --}}
                            <div>
                                <x-input-label for="purchase_date" :value="__('購入日')" class="font-medium" />
                                <x-text-input id="purchase_date" wire:model="purchase_date" class="block w-full mt-1.5" type="date" required />
                                <x-input-error :messages="$errors->get('purchase_date')" class="mt-2" />
                            </div>

                            {{-- 保証終了日 --}}
                            <div>
                                <x-input-label for="warranty_expires_on" :value="__('保証終了日')" class="font-medium" />
                                <x-text-input id="warranty_expires_on" wire:model="warranty_expires_on" class="block w-full mt-1.5" type="date" />
                                <x-input-error :messages="$errors->get('warranty_expires_on')" class="mt-2" />
                            </div>

                            {{-- 購入金額 --}}
                            <div>
                                <x-input-label for="price" :value="__('購入金額')" class="font-medium" />
                                <div class="relative mt-1.5">
                                    <span class="absolute inset-y-0 flex items-center text-gray-500 left-3">¥</span>
                                    <x-text-input id="price" wire:model="price" class="block w-full pl-8" type="number" min="0" />
                                </div>
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            {{-- 購入状態 --}}
                            <div>
                                <x-input-label for="purchase_condition" :value="__('購入状態')" class="font-medium" />
                                <select id="purchase_condition" wire:model="purchase_condition" class="block w-full mt-1.5 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">選択してください</option>
                                    <option value="新品">新品</option>
                                    <option value="中古">中古</option>
                                    <option value="再生品">再生品</option>
                                    <option value="不明">不明</option>
                                </select>
                                <x-input-error :messages="$errors->get('purchase_condition')" class="mt-2" />
                            </div>

                            {{-- 耐用年数 --}}
                            <div>
                                <x-input-label for="useful_life" :value="__('耐用年数 (年)')" class="font-medium" />
                                <x-text-input id="useful_life" wire:model="useful_life" class="block w-full mt-1.5" type="number" min="0" step="1" placeholder="例: 5" />
                                <x-input-error :messages="$errors->get('useful_life')" class="mt-2" />
                            </div>

                            {{-- ステータス --}}
                            <div>
                                <x-input-label for="status" :value="__('ステータス')" class="font-medium" />
                                <select id="status" wire:model="status" class="block w-full mt-1.5 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">選択してください</option>
                                    <option value="active">使用中</option>
                                    <option value="in_storage">保管中</option>
                                    <option value="in_repair">修理中</option>
                                    <option value="disposed">廃棄済み</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- 備考 --}}
                    <div>
                        <h4 class="flex items-center gap-2 mb-4 text-lg font-bold text-gray-800">
                            <span class="inline-flex items-center justify-center text-sm text-white bg-gray-800 rounded-lg w-7 h-7">3</span>
                            その他
                        </h4>
                        <div class="pl-9">
                            <x-input-label for="notes" :value="__('備考')" class="font-medium" />
                            <textarea id="notes" wire:model="notes" class="block w-full mt-1.5 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4" placeholder="メモや特記事項があれば入力してください"></textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    {{-- 送信ボタン --}}
                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700">
                            キャンセル
                        </a>
                        <x-primary-button class="px-8 py-3 text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('登録する') }}
                        </x-primary-button>
                    </div>
                </form>

                {{-- 楽天クレジット --}}
                @if($rakuten_url)
                    <div class="flex items-center justify-center gap-2 px-6 py-4 text-xs text-gray-400 border-t bg-gray-50">
                        <span>Supported by</span>
                        <a href="https://webservice.rakuten.co.jp/" target="_blank" rel="noopener noreferrer" class="font-medium text-red-600 hover:underline">
                            Rakuten Web Service
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
