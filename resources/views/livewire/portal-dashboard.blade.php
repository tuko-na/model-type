    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex p-1 bg-gray-200 rounded-lg shadow-inner">
                @php
                    $isPublic = session('view_mode') === 'public';
                @endphp
                <a href="{{ route('mode.switch', 'private') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ !$isPublic ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
                    プライベート
                </a>
                <a href="{{ route('mode.switch', 'public') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ $isPublic ? 'bg-emerald-600 shadow text-white' : 'text-gray-500 hover:text-gray-700' }}">
                    全体統計
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

<div class="space-y-6">
    {{-- Header / KPI --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="p-4 bg-white border-l-4 border-blue-500 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">平均寿命</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $this->globalKpis['avg_lifespan'] }} <span class="text-sm font-normal text-gray-400">年</span></p>
        </div>
        <div class="p-4 bg-white border-l-4 border-green-500 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">年間維持費</h3>
            <p class="text-2xl font-bold text-gray-800">¥{{ number_format($this->globalKpis['annual_maintenance_cost']) }}</p>
        </div>
        <div class="p-4 bg-white border-l-4 border-red-500 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">故障率</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $this->globalKpis['incident_rate'] }}%</p>
        </div>
    </div>

    {{-- Focus Monitor --}}
    <div class="p-6 bg-white rounded-lg shadow">
        <div class="flex flex-col items-center justify-between mb-6 md:flex-row">
            <h2 class="text-lg font-semibold text-gray-800">フォーカス・モニター</h2>
            <div class="w-full md:w-1/3">
                 <select wire:change="selectProduct($event.target.value)" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($this->allProducts as $p)
                        <option value="{{ $p->id }}" @selected($selectedProductId == $p->id)>{{ $p->name }} ({{ $p->model_number }})</option>
                    @endforeach
                 </select>
            </div>
        </div>

        @if($this->selectedProduct)
            <div 
                x-data='focusMonitor(@json($this->focusMonitorData))' 
                @product-selected.window="updateData($event.detail.data)"
            >
                {{-- 4-Panel Grid --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    
                    {{-- 1. 寿命 (Lifespan) --}}
                    <div class="p-4 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-600">寿命</h3>
                            <div class="p-1.5 bg-blue-100 rounded-full">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-3xl font-bold text-gray-800" x-text="data.years_owned"></span>
                            <span class="pb-1 text-sm text-gray-500">年経過</span>
                        </div>
                        <div class="mt-3">
                            <div class="flex justify-between mb-1 text-xs">
                                <span class="text-gray-500">カテゴリ平均寿命まで</span>
                                <span class="font-medium" x-text="data.lifespan_percentage + '%'"></span>
                            </div>
                            <div class="h-2 overflow-hidden bg-gray-200 rounded-full">
                                <div 
                                    class="h-full transition-all duration-500 rounded-full"
                                    :class="data.lifespan_percentage >= 80 ? 'bg-red-500' : data.lifespan_percentage >= 50 ? 'bg-yellow-500' : 'bg-blue-500'"
                                    :style="'width: ' + Math.min(100, data.lifespan_percentage) + '%'"
                                ></div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">平均: <span class="font-medium" x-text="data.category_life_years + '年'"></span></p>
                        </div>
                    </div>

                    {{-- 2. 安定スコア (Stability Score) --}}
                    <div class="p-4 rounded-lg bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-600">安定スコア</h3>
                            <div class="p-1.5 bg-green-100 rounded-full">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="relative w-16 h-16">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#E5E7EB" stroke-width="3"></circle>
                                    <circle 
                                        cx="18" cy="18" r="15.9" fill="none" 
                                        :stroke="data.stability_score >= 80 ? '#10B981' : data.stability_score >= 50 ? '#F59E0B' : '#EF4444'"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        :stroke-dasharray="(data.stability_score * 100 / 100) + ' 100'"
                                    ></circle>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-lg font-bold text-gray-800" x-text="data.stability_score"></span>
                            </div>
                            <div class="flex-1">
                                <div 
                                    class="text-sm font-semibold"
                                    :class="data.stability_score >= 80 ? 'text-green-600' : data.stability_score >= 50 ? 'text-yellow-600' : 'text-red-600'"
                                    x-text="data.stability_score >= 80 ? '優良' : data.stability_score >= 50 ? '普通' : '要注意'"
                                ></div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    インシデント: <span class="font-medium" x-text="data.incident_count + '件'"></span>
                                </p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">故障頻度と重大度から算出</p>
                    </div>

                    {{-- 3. 集合知との比較 (Public Stats) --}}
                    <div class="p-4 rounded-lg bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-600">集合知との比較</h3>
                            <div class="p-1.5 bg-purple-100 rounded-full">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        @if($this->isPublicDataEnabled ?? false)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">あなたのCPD</span>
                                    <span class="text-sm font-semibold" x-text="'¥' + new Intl.NumberFormat().format(data.cpd)"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">全体平均</span>
                                    <span class="text-sm font-medium text-gray-600" x-text="'¥' + new Intl.NumberFormat().format(data.public_avg_cpd || data.avg_cpd)"></span>
                                </div>
                                <div class="pt-2 mt-2 border-t border-purple-200">
                                    <div 
                                        class="text-sm font-semibold flex items-center gap-1"
                                        :class="data.cpd <= data.avg_cpd ? 'text-green-600' : 'text-red-600'"
                                    >
                                        <svg x-show="data.cpd <= data.avg_cpd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                        </svg>
                                        <svg x-show="data.cpd > data.avg_cpd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                        <span x-text="data.cpd <= data.avg_cpd ? '平均より効率的' : '平均より高コスト'"></span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-4 text-center">
                                <div class="p-3 mb-2 bg-purple-100 rounded-full">
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500">全体統計モードで</p>
                                <p class="text-xs text-gray-500">利用可能になります</p>
                            </div>
                        @endif
                    </div>

                    {{-- 4. タイムライン (Timeline) --}}
                    <div class="p-4 rounded-lg bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-600">タイムライン</h3>
                            <div class="p-1.5 bg-orange-100 rounded-full">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            <template x-if="data.timeline && data.timeline.length > 0">
                                <template x-for="(event, index) in data.timeline.slice(0, 5)" :key="index">
                                    <div class="flex items-start gap-2 text-xs">
                                        <div 
                                            class="w-2 h-2 mt-1 rounded-full flex-shrink-0"
                                            :class="{
                                                'bg-red-500': event.type === 'failure',
                                                'bg-yellow-500': event.type === 'maintenance',
                                                'bg-blue-500': event.type === 'damage',
                                                'bg-green-500': event.type === 'purchase',
                                                'bg-gray-400': !['failure', 'maintenance', 'damage', 'purchase'].includes(event.type)
                                            }"
                                        ></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-700 truncate" x-text="event.title"></p>
                                            <p class="text-gray-400" x-text="event.date"></p>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template x-if="!data.timeline || data.timeline.length === 0">
                                <div class="flex flex-col items-center justify-center py-2 text-center">
                                    <svg class="w-8 h-8 mb-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-xs text-gray-400">イベントなし</p>
                                </div>
                            </template>
                        </div>
                        <template x-if="data.timeline && data.timeline.length > 5">
                            <p class="mt-2 text-xs text-center text-orange-600 cursor-pointer hover:underline">
                                <span x-text="'他 ' + (data.timeline.length - 5) + ' 件'"></span>
                            </p>
                        </template>
                    </div>
                </div>
                
                <div class="flex gap-4 mt-6">
                     <a href="{{ route('incidents.create', ['product_id' => $this->selectedProduct->id]) }}" class="px-4 py-2 text-sm text-white transition bg-red-600 rounded hover:bg-red-700">不具合を報告</a>
                     <button class="px-4 py-2 text-sm text-gray-700 transition bg-gray-200 rounded hover:bg-gray-300">メモを追加</button>
                </div>
            </div>
        @else
            <p class="py-8 text-center text-gray-500">製品が選択されていません。</p>
        @endif
    </div>

    {{-- Discovery --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Worst CPD --}}
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="pb-2 mb-3 font-semibold text-gray-800 border-b">ワーストCPD (効率低下)</h3>
            <ul class="space-y-3">
                @foreach($this->discovery['worst_cpd'] as $item)
                    <li class="flex items-center justify-between text-sm">
                        <span class="w-2/3 truncate">{{ $item['product']->name }}</span>
                        <span class="font-mono text-red-600">¥{{ number_format($item['cpd'], 1) }}/日</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Hall of Fame --}}
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="pb-2 mb-3 font-semibold text-gray-800 border-b">殿堂入り (長寿命製品)</h3>
            <ul class="space-y-3">
                 @foreach($this->discovery['hall_of_fame'] as $p)
                    <li class="flex items-center justify-between text-sm">
                        <span class="w-2/3 truncate">{{ $p->name }}</span>
                        <span class="font-mono text-green-600">{{ \Carbon\Carbon::parse($p->purchase_date)->diffInYears(now()) }} 年</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Alerts --}}
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="pb-2 mb-3 font-semibold text-gray-800 border-b">アラート</h3>
            @if($this->discovery['alerts']->isEmpty())
                <p class="text-sm text-gray-400">現在のアラートはありません。</p>
            @else
                <ul class="space-y-3">
                    @foreach($this->discovery['alerts'] as $p)
                        <li class="flex items-start gap-2 text-sm">
                            <span class="text-yellow-500 mt-0.5">⚠️</span>
                            <div>
                                <div class="font-medium">{{ $p->name }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($p->status == 'repairing') 修理中
                                    @else 保証期間終了間近
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<script>
    (function() {
        const registerFocusMonitor = () => {
            Alpine.data('focusMonitor', (initialData) => ({
                data: initialData,

                init() {
                    // console.log('Alpine Init with data:', this.data);
                },

                updateData(newData) {
                    // console.log('Data received:', newData);
                    this.data = newData;
                }
            }));
        };

        if (typeof Alpine !== 'undefined') {
            registerFocusMonitor();
        } else {
            document.addEventListener('alpine:init', registerFocusMonitor);
        }
    })();
</script>