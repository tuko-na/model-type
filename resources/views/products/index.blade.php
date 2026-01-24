<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('保有製品一覧') }}
        </h2>
    </x-slot>

    <div class="py-12 h-full">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 h-full">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full flex flex-col">
                <div class="p-6 bg-white border-b border-gray-200 h-full flex flex-col">

                    <div class="flex items-center justify-between mb-4 flex-none">
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('products.index') }}" method="GET">
                                <div class="relative">
                                    <x-text-input type="search" name="search" placeholder="検索..." class="pl-8" value="{{ request('search') }}" />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </form>
                            <x-filter-popover align="left" width="96">
                                <x-slot name="trigger">
                                    <button class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
                                        <h4 class="text-sm font-semibold">絞り込み</h4>

                                        <!-- 在庫ステータス -->
                                        <div>
                                            <h5 class="mb-2 text-xs text-gray-500">在庫ステータス</h5>
                                            <div class="space-y-1">
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="status[]" value="active" @if(in_array('active', request('status', []))) checked @endif>
                                                    <span class="ml-2 text-sm">使用中</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="status[]" value="in_storage" @if(in_array('in_storage', request('status', []))) checked @endif>
                                                    <span class="ml-2 text-sm">保管中</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="status[]" value="in_repair" @if(in_array('in_repair', request('status', []))) checked @endif>
                                                    <span class="ml-2 text-sm">修理中</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="status[]" value="disposed" @if(in_array('disposed', request('status', []))) checked @endif>
                                                    <span class="ml-2 text-sm">廃棄済み</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- 購入時の状態 -->
                                        <div>
                                            <h5 class="mb-2 text-xs text-gray-500">購入時の状態</h5>
                                            <div class="space-y-1">
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="condition[]" value="新品" @if(in_array('新品', request('condition', []))) checked @endif>
                                                    <span class="ml-2 text-sm">新品</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="condition[]" value="中古" @if(in_array('中古', request('condition', []))) checked @endif>
                                                    <span class="ml-2 text-sm">中古</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="condition[]" value="再生品" @if(in_array('再生品', request('condition', []))) checked @endif>
                                                    <span class="ml-2 text-sm">再生品</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="condition[]" value="不明" @if(in_array('不明', request('condition', []))) checked @endif>
                                                    <span class="ml-2 text-sm">不明</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- カテゴリ -->
                                        <div x-data="{ searchTerm: '' }">
                                            <h5 class="mb-2 text-xs text-gray-500">カテゴリ</h5>
                                            <x-text-input type="text" x-model="searchTerm" placeholder="カテゴリ検索..." class="w-full mb-2" />
                                            <div class="space-y-1 overflow-y-auto max-h-40">
                                                @php
                                                    // This should be passed from the controller to avoid re-querying
                                                    $all_categories = app(App\Models\Product::class)->pluck('category')->unique()->sort();
                                                @endphp
                                                @foreach($all_categories as $category)
                                                <label class="flex items-center" x-show="!searchTerm || '{{ $category }}'.toLowerCase().includes(searchTerm.toLowerCase())">
                                                    <input type="checkbox" class="rounded form-checkbox" name="category[]" value="{{ $category }}" @if(in_array($category, request('category', []))) checked @endif>
                                                    <span class="ml-2 text-sm">{{ $category }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- 操作ボタン -->
                                        <div class="flex justify-end pt-4 space-x-2 border-t border-gray-200">
                                            <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">リセット</a>
                                            <x-primary-button type="submit">適用</x-primary-button>
                                        </div>
                                    </form>
                                </x-slot>
                            </x-filter-popover>
                        </div>

                        <a href="{{ route('products.create') }}">
                            <x-primary-button>
                                {{ __('新規登録') }}
                            </x-primary-button>
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 text-sm font-medium text-green-600 flex-none">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex-1 overflow-y-auto border rounded-lg min-h-0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        ステータス
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        製品名
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        型番
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        カテゴリ
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        メーカー
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        購入日
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">編集</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span @class([
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                'bg-green-100 text-green-800' => $product->status === 'active',
                                                'bg-gray-100 text-gray-800' => $product->status === 'in_storage',
                                                'bg-yellow-100 text-yellow-800' => $product->status === 'in_repair',
                                                'bg-red-100 text-red-800' => $product->status === 'disposed',
                                            ])>
                                                {{ match ($product->status) {
                                                    'active' => '使用中',
                                                    'in_storage' => '保管中',
                                                    'in_repair' => '修理中',
                                                    'disposed' => '廃棄済み',
                                                    default => '不明',
                                                } }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $product->model_number }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $product->category }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $product->manufacturer }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $product->purchase_date }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex flex-col items-end space-y-2">
                                                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">編集</a>
                                                <x-danger-button
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('open-modal', { name: 'confirm-product-deletion', action: '{{ route('products.destroy', $product) }}' })"
                                                >{{ __('削除') }}</x-danger-button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">

                                        製品が登録されていません。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <x-confirm-delete-modal />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
