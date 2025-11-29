<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('インシデント一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-visible bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('incidents.index') }}" method="GET">
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
                                    <form action="{{ route('incidents.index') }}" method="GET" class="space-y-4">
                                        <h4 class="text-sm font-semibold">絞り込み</h4>

                                        <!-- 発生日 -->
                                        <div>
                                            <h5 class="mb-2 text-xs text-gray-500">発生日</h5>
                                            <div class="flex items-center space-x-2">
                                                <x-text-input type="date" name="start_date" class="w-full" value="{{ request('start_date') }}" />
                                                <span class="text-gray-500">-</span>
                                                <x-text-input type="date" name="end_date" class="w-full" value="{{ request('end_date') }}" />
                                            </div>
                                        </div>

                                        <!-- 費用 -->
                                        <div x-data="{
                                                minCost: parseInt('{{ request('min_cost', 0) }}'),
                                                maxCost: parseInt('{{ request('max_cost', 100000) }}'),
                                                minRange: 0,
                                                maxRange: 100000,
                                                step: 1000,
                                                updateMin() {
                                                    if (this.minCost > this.maxCost) {
                                                        this.maxCost = this.minCost;
                                                    }
                                                },
                                                updateMax() {
                                                    if (this.maxCost < this.minCost) {
                                                        this.minCost = this.maxCost;
                                                    }
                                                }
                                            }" class="space-y-3">
                                            <h5 class="text-xs text-gray-500">費用</h5>
                                            
                                            <div>
                                                <label for="min_cost_slider" class="flex justify-between text-sm">
                                                    <span>最小</span>
                                                    <span x-text="new Intl.NumberFormat('ja-JP').format(minCost) + ' 円'"></span>
                                                </label>
                                                <input id="min_cost_slider" type="range" :min="minRange" :max="maxRange" :step="step" x-model.number="minCost" @input="updateMin" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                            </div>

                                            <div>
                                                <label for="max_cost_slider" class="flex justify-between text-sm">
                                                    <span>最大</span>
                                                    <span x-text="new Intl.NumberFormat('ja-JP').format(maxCost) + ' 円'"></span>
                                                </label>
                                                <input id="max_cost_slider" type="range" :min="minRange" :max="maxRange" :step="step" x-model.number="maxCost" @input="updateMax" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <x-text-input type="number" name="min_cost" x-model.number.debounce.500ms="minCost" @change="updateMin" class="w-full" />
                                                <span class="text-gray-500">-</span>
                                                <x-text-input type="number" name="max_cost" x-model.number.debounce.500ms="maxCost" @change="updateMax" class="w-full" />
                                            </div>
                                        </div>

                                        <!-- インシデント種別 -->
                                        <div>
                                            <h5 class="mb-2 text-xs text-gray-500">インシデント種別</h5>
                                            <div class="space-y-1 overflow-y-auto max-h-32">
                                                @foreach(App\Models\Incident::INCIDENT_TYPES as $key => $label)
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="incident_type[]" value="{{ $key }}" {{ in_array($key, request('incident_type', [])) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm">{{ $label }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- 対応種別 -->
                                        <div>
                                            <h5 class="mb-2 text-xs text-gray-500">対応種別</h5>
                                            <div class="space-y-1 overflow-y-auto max-h-32">
                                                @foreach(App\Models\Incident::RESOLUTION_TYPES as $key => $label)
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="resolution_type[]" value="{{ $key }}" {{ in_array($key, request('resolution_type', [])) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm">{{ $label }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- 症状タグ -->
                                        <div>
                                            <h5 class="mb-2 text-xs text-gray-500">症状タグ</h5>
                                            <div class="space-y-1 overflow-y-auto max-h-40">
                                                @foreach(App\Models\Incident::SYMPTOM_TAGS as $key => $label)
                                                <label class="flex items-center">
                                                    <input type="checkbox" class="rounded form-checkbox" name="symptom_tags[]" value="{{ $key }}" {{ in_array($key, request('symptom_tags', [])) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm">{{ $label }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- 操作ボタン -->
                                        <div class="flex justify-end pt-4 space-x-2 border-t border-gray-200">
                                            <a href="{{ route('incidents.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">リセット</a>
                                            <x-primary-button>適用</x-primary-button>
                                        </div>
                                    </form>
                                </x-slot>
                            </x-filter-popover>
                        </div>

                        <a href="{{ route('incidents.create') }}">
                            <x-primary-button>
                                {{ __('新規登録') }}
                            </x-primary-button>
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 text-sm font-medium text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">タイトル</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">関連製品</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">発生日</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">種別</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">対応</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">詳細</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($incidents as $incident)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $incident->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        @if ($incident->product)
                                            <a href="{{ route('products.show', $incident->product) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $incident->product->name }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $incident->occurred_at }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ App\Models\Incident::INCIDENT_TYPES[$incident->incident_type] ?? $incident->incident_type }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ App\Models\Incident::RESOLUTION_TYPES[$incident->resolution_type] ?? $incident->resolution_type }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        @if ($incident->product)
                                            <a href="{{ route('products.show', $incident->product) }}" class="text-indigo-600 hover:text-indigo-900">詳細を見る</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        インシデントはまだ登録されていません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
