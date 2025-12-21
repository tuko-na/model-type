<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('製品カタログ') }}
        </h2>
    </x-slot>

    <div class="py-12 h-full">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 h-full">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full flex flex-col">
                <div class="p-6 bg-white border-b border-gray-200 h-full flex flex-col">

                    <div class="flex items-center justify-between mb-4 flex-none">
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('products.catalog') }}" method="GET">
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
                                    <form action="{{ route('products.catalog') }}" method="GET" class="space-y-4">
                                        <h4 class="text-sm font-semibold">絞り込み</h4>

                                        <!-- カテゴリ -->
                                        <div x-data="{ searchTerm: '' }">
                                            <h5 class="mb-2 text-xs text-gray-500">カテゴリ</h5>
                                            <x-text-input type="text" x-model="searchTerm" placeholder="カテゴリ検索..." class="w-full mb-2" />
                                            <div class="space-y-1 overflow-y-auto max-h-40">
                                                @php
                                                    $all_categories = app(App\Models\ModelSuggestion::class)->pluck('category')->unique()->sort();
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
                                            <a href="{{ route('products.catalog') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">リセット</a>
                                            <x-primary-button type="submit">適用</x-primary-button>
                                        </div>
                                    </form>
                                </x-slot>
                            </x-filter-popover>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto border rounded-lg min-h-0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
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
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($models as $model)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ $model->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $model->model_number }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $model->category }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $model->manufacturer }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        製品が見つかりません。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $models->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
