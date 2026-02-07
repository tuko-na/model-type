<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('製品統計 - 集合知ダッシュボード') }}
        </h2>
        <div class="flex p-1 bg-gray-200 rounded-lg shadow-inner">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-500 transition-all rounded-md hover:text-gray-900">
                マイダッシュボード
            </a>
            <a href="{{ route('public.dashboard') }}" class="px-4 py-2 text-sm font-medium text-white transition-all rounded-md shadow bg-indigo-600">
                集合知
            </a>
        </div>
    </div>
</x-slot>

<div class="min-h-screen py-8 bg-gradient-to-br from-slate-50 via-gray-50 to-indigo-50">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- 検索セクション --}}
        <div class="mb-8">
            {{-- カテゴリフィルター --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <button 
                    wire:click="setCategory(null)" 
                    class="px-4 py-2 text-sm font-medium rounded-full transition-all duration-300 {{ !$selectedCategory ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}"
                >
                    すべて
                </button>
                @foreach($this->categories as $key => $label)
                    <button 
                        wire:click="setCategory('{{ $key }}')" 
                        class="px-4 py-2 text-sm font-medium rounded-full transition-all duration-300 {{ $selectedCategory === $key ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- 検索入力 --}}
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="型番で検索 (例: iPhone 15 Pro, WH-1000XM5)" 
                    class="w-full px-6 py-4 pl-12 text-lg text-gray-800 placeholder-gray-400 transition-all duration-300 bg-white border-0 shadow-lg rounded-2xl shadow-gray-200/50 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                <svg class="absolute w-6 h-6 text-gray-400 left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute text-gray-400 transition-colors right-4 top-4 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- 検索結果ドロップダウン --}}
            @if($this->searchResults->count() > 0)
                <div class="mt-3 overflow-y-auto bg-white border border-gray-100 divide-y divide-gray-100 shadow-xl rounded-2xl max-h-80">
                    @foreach($this->searchResults as $result)
                        <div class="flex items-center justify-between p-4 transition-all duration-200 cursor-pointer hover:bg-indigo-50/50" wire:click="selectProduct({{ $result['product_ids'][0] }})">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ $result['model_number'] }}</span>
                                    <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $result['manufacturer'] }}</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">{{ $result['name'] }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-800">¥{{ number_format($result['avg_price']) }}</p>
                                    <p class="text-xs text-gray-400">平均価格</p>
                                </div>
                                <span class="px-3 py-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 rounded-full">
                                    {{ $result['sample_count'] }}件
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(strlen($search) >= 2)
                <div class="py-12 mt-3 text-center bg-white shadow-lg rounded-2xl">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">「{{ $search }}」に一致する製品が見つかりません</p>
                </div>
            @endif
        </div>

        {{-- 製品が選択されている場合：Oura風ダッシュボード --}}
        @if($this->productAnalytics)
            @php $analytics = $this->productAnalytics; @endphp

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                {{-- 左カラム - 製品情報 --}}
                <div class="space-y-6 lg:col-span-4">
                    {{-- 製品カード + 総合スコア統合 --}}
                    @php
                        $cpdScore = max(0, min(100, 100 - ($analytics['avg_cpd'] / 2)));
                        $lifespanScore = min(100, ($analytics['avg_lifespan_years'] / $analytics['category_life_years']) * 100);
                        $overallScore = round(($analytics['reliability_score'] * 0.4) + ($cpdScore * 0.3) + ($lifespanScore * 0.3));
                    @endphp
                    <div class="relative overflow-hidden bg-white shadow-xl rounded-3xl shadow-gray-200/50">
                        {{-- 総合スコアヘッダー（グラデーション） --}}
                        <div class="relative px-8 pt-8 pb-12 text-center bg-gradient-to-br from-indigo-500 to-indigo-700">
                            {{-- アニメーション背景 --}}
                            <div class="absolute inset-0 opacity-30">
                                <div class="absolute top-0 left-0 w-40 h-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/20 blur-3xl animate-pulse"></div>
                                <div class="absolute bottom-0 right-0 w-32 h-32 translate-x-1/2 translate-y-1/2 rounded-full bg-indigo-400/20 blur-3xl"></div>
                            </div>
                            
                            {{-- 総合スコアリング --}}
                            <div class="relative z-10">
                                <div class="relative w-32 h-32 mx-auto mb-4">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="15" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3"></circle>
                                        <circle 
                                            cx="18" cy="18" r="15" fill="none" 
                                            stroke="white"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ $overallScore * 0.94 }} 94"
                                            style="transition: stroke-dasharray 1s ease-out;"
                                        ></circle>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-4xl font-bold text-white">{{ $overallScore }}</span>
                                </div>
                                <div class="text-lg font-medium text-white/90">総合スコア</div>
                            </div>
                        </div>

                        {{-- 製品情報 --}}
                        <div class="px-8 pt-6 pb-8 text-center bg-white rounded-t-3xl">
                            <h2 class="mb-1 text-xl font-bold text-gray-900">{{ $analytics['product']['model_number'] }}</h2>
                            <p class="mb-1 text-sm text-gray-500">{{ $analytics['product']['manufacturer'] }} {{ $analytics['product']['name'] }}</p>
                            <p class="text-xs font-medium text-indigo-600">{{ $analytics['sample_count'] }}人のユーザーデータに基づく</p>

                            {{-- Oura風リングスコア --}}
                            <div class="grid grid-cols-3 gap-3 mt-6">
                            {{-- 信頼性リング --}}
                            <div class="text-center">
                                <div class="relative w-16 h-16 mx-auto mb-2">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="14" fill="none" stroke="#E5E7EB" stroke-width="3"></circle>
                                        <circle 
                                            cx="18" cy="18" r="14" fill="none" 
                                            class="stroke-emerald-500"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ $analytics['reliability_score'] * 0.88 }} 88"
                                            style="transition: stroke-dasharray 1s ease-out;"
                                        ></circle>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-base font-bold text-gray-900">{{ $analytics['reliability_score'] }}</span>
                                </div>
                                <p class="text-xs font-medium text-gray-600">信頼性</p>
                            </div>

                            {{-- コスパリング --}}
                            <div class="text-center">
                                <div class="relative w-16 h-16 mx-auto mb-2">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="14" fill="none" stroke="#E5E7EB" stroke-width="3"></circle>
                                        <circle 
                                            cx="18" cy="18" r="14" fill="none" 
                                            class="stroke-blue-500"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ $cpdScore * 0.88 }} 88"
                                            style="transition: stroke-dasharray 1s ease-out;"
                                        ></circle>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-gray-900">¥{{ $analytics['avg_cpd'] }}</span>
                                </div>
                                <p class="text-xs font-medium text-gray-600">コスパ</p>
                            </div>

                            {{-- 寿命リング --}}
                            <div class="text-center">
                                <div class="relative w-16 h-16 mx-auto mb-2">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="14" fill="none" stroke="#E5E7EB" stroke-width="3"></circle>
                                        <circle 
                                            cx="18" cy="18" r="14" fill="none" 
                                            class="stroke-amber-500"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ $lifespanScore * 0.88 }} 88"
                                            style="transition: stroke-dasharray 1s ease-out;"
                                        ></circle>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-gray-900">{{ $analytics['avg_lifespan_years'] }}y</span>
                                </div>
                                <p class="text-xs font-medium text-gray-600">寿命</p>
                            </div>
                            </div>

                            <button wire:click="clearSelection" class="w-full px-6 py-3 mt-6 text-sm font-medium text-gray-700 transition-all duration-200 bg-gray-100 hover:bg-gray-200 rounded-xl">
                                ← 検索に戻る
                            </button>
                        </div>
                    </div>

                    {{-- スペック表 --}}
                    <div class="p-6 bg-white shadow-xl rounded-3xl shadow-gray-200/50">
                        <h3 class="mb-4 text-lg font-bold text-gray-900">基本情報</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-500">カテゴリ</span>
                                <span class="text-sm font-medium text-gray-900">{{ $analytics['product']['category_label'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-500">メーカー</span>
                                <span class="text-sm font-medium text-gray-900">{{ $analytics['product']['manufacturer'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-500">平均購入価格</span>
                                <span class="text-sm font-medium text-gray-900">¥{{ number_format($analytics['price']['avg']) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-500">価格帯</span>
                                <span class="text-sm font-medium text-gray-900">¥{{ number_format($analytics['price']['min']) }} 〜 ¥{{ number_format($analytics['price']['max']) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <span class="text-sm text-gray-500">サンプル数</span>
                                <span class="text-sm font-medium text-gray-900">{{ $analytics['sample_count'] }}件</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 右カラム - データビジュアライゼーション --}}
                <div class="space-y-6 lg:col-span-8">
                    {{-- メトリクスグリッド --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- 実質コスト --}}
                        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-2xl shadow-gray-200/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-500">実質コスト (CPD)</span>
                                @if($analytics['avg_cpd'] < $this->globalStats['global_avg_cpd'])
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                        -{{ round((1 - $analytics['avg_cpd'] / max(1, $this->globalStats['global_avg_cpd'])) * 100) }}%
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                        +{{ round(($analytics['avg_cpd'] / max(1, $this->globalStats['global_avg_cpd']) - 1) * 100) }}%
                                    </span>
                                @endif
                            </div>
                            <div class="mb-1 text-3xl font-bold text-gray-900">¥{{ number_format($analytics['avg_cpd']) }}</div>
                            <p class="text-xs text-gray-400">全体平均 <span class="font-semibold text-indigo-600">¥{{ number_format($this->globalStats['global_avg_cpd']) }}/日</span></p>
                        </div>

                        {{-- 平均寿命 --}}
                        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-2xl shadow-gray-200/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-500">平均寿命</span>
                                @if($analytics['avg_lifespan_years'] >= $analytics['category_life_years'])
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                        +{{ round(($analytics['avg_lifespan_years'] / max(1, $analytics['category_life_years']) - 1) * 100) }}%
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">
                                        -{{ round((1 - $analytics['avg_lifespan_years'] / max(1, $analytics['category_life_years'])) * 100) }}%
                                    </span>
                                @endif
                            </div>
                            <div class="mb-1 text-3xl font-bold text-gray-900">{{ $analytics['avg_lifespan_years'] }}年</div>
                            <p class="text-xs text-gray-400">カテゴリ平均 <span class="font-semibold text-indigo-600">{{ $analytics['category_life_years'] }}年</span></p>
                        </div>

                        {{-- インシデント発生率 --}}
                        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-2xl shadow-gray-200/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-500">インシデント発生率</span>
                                @if($analytics['incident_rate'] < $this->globalStats['global_incident_rate'])
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                        -{{ round(($this->globalStats['global_incident_rate'] - $analytics['incident_rate'])) }}pt
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                        +{{ round(($analytics['incident_rate'] - $this->globalStats['global_incident_rate'])) }}pt
                                    </span>
                                @endif
                            </div>
                            <div class="mb-1 text-3xl font-bold text-gray-900">{{ $analytics['incident_rate'] }}%</div>
                            <p class="text-xs text-gray-400">全体平均 <span class="font-semibold text-indigo-600">{{ $this->globalStats['global_incident_rate'] }}%</span></p>
                        </div>

                        {{-- 修理コスト --}}
                        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-2xl shadow-gray-200/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-500">平均修理費用</span>
                            </div>
                            <div class="mb-1 text-3xl font-bold text-gray-900">¥{{ number_format($analytics['avg_repair_cost']) }}</div>
                            <p class="text-xs text-gray-400">インシデント発生時の平均費用</p>
                        </div>
                    </div>

                    {{-- ライフサイクルコスト予測 --}}
                    <div class="p-6 bg-white shadow-xl rounded-3xl shadow-gray-200/50">
                        <h3 class="mb-6 text-lg font-bold text-gray-900">
                            <svg class="inline-block w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            ライフサイクルコスト予測
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <p class="mb-1 text-sm text-gray-500">購入価格</p>
                                <p class="text-xl font-bold text-gray-900">¥{{ number_format($analytics['price']['avg']) }}</p>
                            </div>
                            <div class="p-4 bg-amber-50 rounded-2xl">
                                <p class="mb-1 text-sm text-gray-500">予想メンテナンス</p>
                                <p class="text-xl font-bold text-amber-600">+¥{{ number_format($analytics['lifecycle_cost'] - $analytics['price']['avg']) }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <p class="mb-1 text-sm text-gray-500">予想寿命</p>
                                <p class="text-xl font-bold text-gray-900">{{ $analytics['category_life_years'] }}年</p>
                            </div>
                            <div class="p-4 bg-indigo-600 rounded-2xl">
                                <p class="mb-1 text-sm text-white/80">総コスト</p>
                                <p class="text-xl font-bold text-white">¥{{ number_format($analytics['lifecycle_cost']) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- インシデント集約ブロック --}}
                    <div class="p-6 bg-white shadow-xl rounded-3xl shadow-gray-200/50" wire:ignore>
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <h3 class="text-lg font-bold text-gray-900">インシデント分析</h3>
                            <div class="inline-flex p-1 bg-gray-100 rounded-2xl" data-incident-tabs>
                                <button type="button" class="px-4 py-2 text-sm font-semibold text-indigo-600 transition rounded-xl bg-indigo-50 js-incident-tab" data-tab="incident-pattern">
                                    発生パターン
                                </button>
                                <button type="button" class="px-4 py-2 text-sm font-semibold text-gray-500 transition rounded-xl hover:text-gray-700 js-incident-tab" data-tab="top-incident">
                                    主要インシデント
                                </button>
                                <button type="button" class="px-4 py-2 text-sm font-semibold text-gray-500 transition rounded-xl hover:text-gray-700 js-incident-tab" data-tab="incident-distribution">
                                    種別・深刻度
                                </button>
                            </div>
                        </div>

                        <div data-incident-panel="incident-pattern">
                            <h4 class="mb-4 text-sm font-semibold text-gray-700">購入後の月数別インシデント件数</h4>
                            <div class="h-64">
                                <canvas id="incidentTimelineChart" data-time-patterns='@json($analytics['time_patterns'])'></canvas>
                            </div>
                            <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-2">
                                <div>
                                    <h4 class="mb-3 text-sm font-semibold text-gray-700">購入からの経過年数分布</h4>
                                    <div class="h-48">
                                        <canvas id="lifespanDistributionChart" data-lifespan-distribution='@json($analytics['lifespan_distribution'])'></canvas>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-3 text-sm font-semibold text-gray-700">購入後のメンテナンス費用推移</h4>
                                    <div class="h-48">
                                        <canvas id="costTrendChart" data-cost-trend='@json($analytics['cost_trend'])'></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden" data-incident-panel="top-incident">
                            <h4 class="mb-4 text-sm font-semibold text-gray-700">よくあるインシデント（深刻度順）</h4>
                            @if(!empty($analytics['top_problems']))
                                <div class="space-y-4">
                                    @foreach($analytics['top_problems'] as $index => $problem)
                                        @php
                                            $severityClass = match(true) {
                                                $problem['avg_cost'] > 10000 => 'border-red-400 bg-red-50/50',
                                                $problem['avg_cost'] > 5000 => 'border-amber-400 bg-amber-50/50',
                                                default => 'border-emerald-400 bg-emerald-50/50'
                                            };
                                        @endphp
                                        <div class="flex items-center gap-4 p-4 rounded-2xl border-l-4 {{ $severityClass }}">
                                            <div class="min-w-[72px] text-xs text-gray-400">{{ $problem['count'] }}件</div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">{{ $problem['label'] }}</div>
                                                <div class="text-sm text-gray-500">報告件数ベースの代表的インシデント</div>
                                            </div>
                                            @if($problem['avg_cost'] > 0)
                                                <div class="text-right">
                                                    <div class="text-sm font-semibold text-gray-900">¥{{ number_format($problem['avg_cost']) }}</div>
                                                    <div class="text-xs text-gray-400">平均費用</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-12 text-center text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>問題報告なし - この製品は良好な状態です！</p>
                                </div>
                            @endif
                        </div>

                        <div class="hidden" data-incident-panel="incident-distribution">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                {{-- インシデント種別分布 --}}
                                <div>
                                    <h4 class="mb-4 text-sm font-semibold text-gray-700">インシデント種別</h4>
                                    @if(!empty($analytics['incident_type_distribution']))
                                        <div class="space-y-4">
                                            @php
                                                $total = array_sum($analytics['incident_type_distribution']);
                                                $typeColors = [
                                                    'failure' => 'bg-red-500',
                                                    'maintenance' => 'bg-amber-500',
                                                    'damage' => 'bg-blue-500',
                                                    'loss' => 'bg-gray-500'
                                                ];
                                            @endphp
                                            @foreach($analytics['incident_type_distribution'] as $type => $count)
                                                @php $percentage = $total > 0 ? round(($count / $total) * 100) : 0; @endphp
                                                <div>
                                                    <div class="flex justify-between mb-2 text-sm">
                                                        <span class="font-medium text-gray-700">{{ \App\Models\Incident::INCIDENT_TYPES[$type] ?? $type }}</span>
                                                        <span class="text-gray-500">{{ $count }}件 ({{ $percentage }}%)</span>
                                                    </div>
                                                    <div class="h-2 overflow-hidden bg-gray-100 rounded-full">
                                                        <div class="h-full {{ $typeColors[$type] ?? 'bg-gray-500' }} rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="py-8 text-center text-gray-400">
                                            <p>データなし</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- 深刻度分布 --}}
                                <div>
                                    <h4 class="mb-4 text-sm font-semibold text-gray-700">深刻度分布</h4>
                                    @if(!empty($analytics['severity_distribution']))
                                        <div class="grid grid-cols-4 gap-3">
                                            @php
                                                $total = array_sum($analytics['severity_distribution']);
                                                $severityConfig = [
                                                    'low' => ['color' => 'emerald', 'label' => '軽微'],
                                                    'medium' => ['color' => 'amber', 'label' => '中程度'],
                                                    'high' => ['color' => 'orange', 'label' => '高'],
                                                    'critical' => ['color' => 'red', 'label' => '重大'],
                                                ];
                                            @endphp
                                            @foreach(['low', 'medium', 'high', 'critical'] as $severity)
                                                @php 
                                                    $count = $analytics['severity_distribution'][$severity] ?? 0;
                                                    $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                                                    $config = $severityConfig[$severity];
                                                @endphp
                                                <div class="text-center">
                                                    <div class="relative w-16 h-16 mx-auto mb-2">
                                                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                                            <circle cx="18" cy="18" r="14" fill="none" stroke="#E5E7EB" stroke-width="3"></circle>
                                                            <circle 
                                                                cx="18" cy="18" r="14" fill="none" 
                                                                class="stroke-{{ $config['color'] }}-500"
                                                                stroke-width="3"
                                                                stroke-linecap="round"
                                                                stroke-dasharray="{{ $percentage * 0.88 }} 88"
                                                            ></circle>
                                                        </svg>
                                                        <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-gray-900">{{ $count }}</span>
                                                    </div>
                                                    <p class="text-xs font-medium text-{{ $config['color'] }}-600">{{ $config['label'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="py-8 text-center text-gray-400">
                                            <p>データなし</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 比較レーダーチャート --}}
                    <div class="p-6 bg-white shadow-xl rounded-3xl shadow-gray-200/50" wire:ignore>
                        <h3 class="mb-6 text-lg font-bold text-gray-900">全体平均との差</h3>
                        <div class="h-72">
                            <canvas id="radarComparisonChart" data-radar='@json($analytics['radar_comparison'])'></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- 製品未選択時：全体統計を表示 --}}
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
                <div class="p-8 text-center bg-white shadow-xl rounded-3xl shadow-gray-200/50">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 shadow-lg bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-blue-200">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <p class="mb-2 text-sm text-gray-500">登録製品数</p>
                    <p class="text-4xl font-bold text-gray-900">{{ number_format($this->globalStats['total_products']) }}</p>
                </div>

                <div class="p-8 text-center bg-white shadow-xl rounded-3xl shadow-gray-200/50">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 shadow-lg bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl shadow-red-200">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <p class="mb-2 text-sm text-gray-500">報告インシデント数</p>
                    <p class="text-4xl font-bold text-gray-900">{{ number_format($this->globalStats['total_incidents']) }}</p>
                </div>

                <div class="p-8 text-center bg-white shadow-xl rounded-3xl shadow-gray-200/50">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 shadow-lg bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-emerald-200">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="mb-2 text-sm text-gray-500">全体平均CPD</p>
                    <p class="text-4xl font-bold text-gray-900">¥{{ number_format($this->globalStats['global_avg_cpd']) }}</p>
                </div>
            </div>

            <div class="p-16 text-center bg-white shadow-xl rounded-3xl shadow-gray-200/50">
                <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="mb-3 text-2xl font-bold text-gray-800">製品を検索してください</h3>
                <p class="max-w-md mx-auto text-gray-500">型番、製品名、またはメーカー名で検索すると、集合知に基づいた詳細な分析結果を表示します。</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', initCharts);
    document.addEventListener('DOMContentLoaded', initCharts);
    
    Livewire.on('product-selected', (event) => {
        setTimeout(() => initCharts(event?.data ?? null), 100);
    });

    function parseChartData(value) {
        if (!value) return null;
        if (typeof value === 'object') return value;
        try {
            return JSON.parse(value);
        } catch (error) {
            return null;
        }
    }

    function destroyChart(canvas) {
        if (!canvas) return;
        const existingChart = Chart.getChart(canvas);
        if (existingChart) {
            existingChart.destroy();
        }
    }

    function initTabs() {
        const tabRoot = document.querySelector('[data-chart-tabs]');
        if (!tabRoot) return;

        const tabs = Array.from(tabRoot.querySelectorAll('.js-chart-tab'));
        const panels = Array.from(document.querySelectorAll('[data-chart-panel]'));
        if (tabs.length === 0 || panels.length === 0) return;

        const setActive = (tabName) => {
            tabRoot.dataset.activeTab = tabName;
            tabs.forEach((tab) => {
                const isActive = tab.dataset.tab === tabName;
                tab.classList.toggle('bg-indigo-50', isActive);
                tab.classList.toggle('text-indigo-600', isActive);
                tab.classList.toggle('text-gray-500', !isActive);
            });
            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.chartPanel !== tabName);
            });
        };

        if (!tabRoot.dataset.initialized) {
            tabs.forEach((tab) => {
                tab.addEventListener('click', () => setActive(tab.dataset.tab));
            });
            tabRoot.dataset.initialized = 'true';
        }

        const activeTab = tabRoot.dataset.activeTab || tabs[0]?.dataset.tab;
        if (activeTab) {
            setActive(activeTab);
        }
    }

    function initIncidentTabs() {
        const tabRoot = document.querySelector('[data-incident-tabs]');
        if (!tabRoot) return;

        const tabs = Array.from(tabRoot.querySelectorAll('.js-incident-tab'));
        const panels = Array.from(document.querySelectorAll('[data-incident-panel]'));
        if (tabs.length === 0 || panels.length === 0) return;

        const setActive = (tabName) => {
            tabRoot.dataset.activeTab = tabName;
            tabs.forEach((tab) => {
                const isActive = tab.dataset.tab === tabName;
                tab.classList.toggle('bg-indigo-50', isActive);
                tab.classList.toggle('text-indigo-600', isActive);
                tab.classList.toggle('text-gray-500', !isActive);
            });
            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.incidentPanel !== tabName);
            });
        };

        if (!tabRoot.dataset.initialized) {
            tabs.forEach((tab) => {
                tab.addEventListener('click', () => setActive(tab.dataset.tab));
            });
            tabRoot.dataset.initialized = 'true';
        }

        const activeTab = tabRoot.dataset.activeTab || tabs[0]?.dataset.tab;
        if (activeTab) {
            setActive(activeTab);
        }
    }

    function initCharts(productAnalytics = null) {
        if (typeof Chart === 'undefined') {
            return;
        }
        const incidentCanvas = document.getElementById('incidentTimelineChart');
        const lifespanCanvas = document.getElementById('lifespanDistributionChart');
        const costCanvas = document.getElementById('costTrendChart');
        const radarCanvas = document.getElementById('radarComparisonChart');

        if (!incidentCanvas) return;

        const timePatterns = productAnalytics?.time_patterns ?? parseChartData(incidentCanvas.dataset.timePatterns);
        const lifespanDistribution = productAnalytics?.lifespan_distribution ?? parseChartData(lifespanCanvas?.dataset.lifespanDistribution);
        const costTrend = productAnalytics?.cost_trend ?? parseChartData(costCanvas?.dataset.costTrend);
        const radarData = productAnalytics?.radar_comparison ?? parseChartData(radarCanvas?.dataset.radar);

        const incidentPeriods = ['0-3ヶ月', '3-6ヶ月', '6-12ヶ月', '1-2年', '2-3年', '3年以上'];
        const lifespanPeriods = ['0-1年', '1-2年', '2-3年', '3-4年', '4年以上'];

        if (timePatterns && Object.keys(timePatterns).length > 0) {
            destroyChart(incidentCanvas);
            const incidentValues = incidentPeriods.map((period) => timePatterns[period] ?? 0);
            new Chart(incidentCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: incidentPeriods,
                    datasets: [{
                        label: 'インシデント件数',
                        data: incidentValues,
                        borderColor: 'rgba(99, 102, 241, 1)',
                        backgroundColor: 'rgba(99, 102, 241, 0.15)',
                        tension: 0.35,
                        fill: true,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                precision: 0,
                                color: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                }
            });
        }

        if (lifespanCanvas && lifespanDistribution && Object.keys(lifespanDistribution).length > 0) {
            destroyChart(lifespanCanvas);
            const lifespanValues = lifespanPeriods.map((period) => lifespanDistribution[period] ?? 0);
            new Chart(lifespanCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: lifespanPeriods,
                    datasets: [{
                        label: '製品数',
                        data: lifespanValues,
                        backgroundColor: 'rgba(14, 116, 144, 0.3)',
                        borderColor: 'rgba(14, 116, 144, 0.9)',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                precision: 0,
                                color: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                }
            });
        }

        if (costCanvas && costTrend && Object.keys(costTrend).length > 0) {
            destroyChart(costCanvas);
            const costValues = incidentPeriods.map((period) => costTrend[period] ?? 0);
            new Chart(costCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: incidentPeriods,
                    datasets: [{
                        label: 'メンテナンス費用',
                        data: costValues,
                        borderColor: 'rgba(234, 88, 12, 1)',
                        backgroundColor: 'rgba(234, 88, 12, 0.12)',
                        tension: 0.35,
                        fill: true,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: (value) => `¥${value}`,
                                color: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                }
            });
        }

        if (radarCanvas && radarData && radarData.labels && radarData.product) {
            destroyChart(radarCanvas);
            new Chart(radarCanvas.getContext('2d'), {
                type: 'radar',
                data: {
                    labels: radarData.labels,
                    datasets: [
                        {
                            label: '選択製品',
                            data: radarData.product,
                            borderColor: 'rgba(99, 102, 241, 1)',
                            backgroundColor: 'rgba(99, 102, 241, 0.2)',
                            borderWidth: 2
                        },
                        {
                            label: '全体平均',
                            data: radarData.baseline ?? [],
                            borderColor: 'rgba(148, 163, 184, 0.8)',
                            backgroundColor: 'rgba(148, 163, 184, 0.15)',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'rgba(0, 0, 0, 0.6)'
                            }
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.08)'
                            },
                            angleLines: {
                                color: 'rgba(0, 0, 0, 0.08)'
                            },
                            pointLabels: {
                                color: 'rgba(0, 0, 0, 0.6)'
                            },
                            ticks: {
                                color: 'rgba(0, 0, 0, 0.45)',
                                backdropColor: 'transparent'
                            }
                        }
                    }
                }
            });
        }

        initTabs();
        initIncidentTabs();
    }
</script>
@endpush
