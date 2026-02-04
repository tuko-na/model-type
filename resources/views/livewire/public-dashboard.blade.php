<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('製品統計 - 集合知ダッシュボード') }}
        </h2>
        <div class="flex p-1 bg-gray-200 rounded-lg shadow-inner">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all text-gray-500 hover:text-gray-900">
                マイダッシュボード
            </a>
            <a href="{{ route('public.dashboard') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all bg-emerald-600 shadow text-white">
                集合知
            </a>
        </div>
    </div>
</x-slot>

<div class="py-8">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- 1. 製品検索セクション --}}
        <div class="p-6 mb-6 bg-white rounded-lg shadow-lg">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">
                <svg class="inline-block w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                型番で製品を検索
            </h3>
            
            {{-- カテゴリフィルター --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <button 
                    wire:click="setCategory(null)" 
                    class="px-3 py-1 text-sm rounded-full transition {{ !$selectedCategory ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    すべて
                </button>
                @foreach($this->categories as $key => $label)
                    <button 
                        wire:click="setCategory('{{ $key }}')" 
                        class="px-3 py-1 text-sm rounded-full transition {{ $selectedCategory === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
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
                    placeholder="型番、製品名、メーカー名を入力..." 
                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                <svg class="absolute w-5 h-5 text-gray-400 left-3 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute text-gray-400 right-3 top-4 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- 検索結果 --}}
            @if($this->searchResults->count() > 0)
                <div class="mt-4 border rounded-lg divide-y max-h-64 overflow-y-auto">
                    @foreach($this->searchResults as $result)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 cursor-pointer transition" wire:click="selectProduct({{ $result['product_ids'][0] }})">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">{{ $result['model_number'] }}</span>
                                    <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">{{ $result['manufacturer'] }}</span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $result['name'] }}</p>
                            </div>
                            <div class="flex items-center gap-4 text-right">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">¥{{ number_format($result['avg_price']) }}</p>
                                    <p class="text-xs text-gray-500">平均価格</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">
                                        {{ $result['sample_count'] }}件
                                    </span>
                                    <button 
                                        wire:click.stop="toggleCompare({{ $result['product_ids'][0] }})"
                                        class="p-1 rounded hover:bg-gray-200 {{ in_array($result['product_ids'][0], $compareProducts) ? 'text-indigo-600' : 'text-gray-400' }}"
                                        title="比較に追加"
                                    >
                                        <svg class="w-5 h-5" fill="{{ in_array($result['product_ids'][0], $compareProducts) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(strlen($search) >= 2)
                <div class="mt-4 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>「{{ $search }}」に一致する製品が見つかりません</p>
                </div>
            @endif
        </div>

        {{-- 2. 製品が選択されている場合：集合知ダッシュボード --}}
        @if($this->productAnalytics)
            @php $analytics = $this->productAnalytics; @endphp
            
            {{-- 製品情報ヘッダー --}}
            <div class="p-6 mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 text-sm bg-white/20 rounded-full">{{ $analytics['product']['category_label'] }}</span>
                            <span class="text-white/80">{{ $analytics['product']['manufacturer'] }}</span>
                        </div>
                        <h2 class="text-2xl font-bold">{{ $analytics['product']['model_number'] }}</h2>
                        <p class="text-white/90">{{ $analytics['product']['name'] }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-bold">{{ $analytics['sample_count'] }}</div>
                        <p class="text-white/80">サンプル数</p>
                    </div>
                </div>
                <button wire:click="clearSelection" class="mt-4 px-4 py-2 bg-white/20 rounded hover:bg-white/30 transition text-sm">
                    ← 検索に戻る
                </button>
            </div>

            {{-- 総合評価指標 --}}
            <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                {{-- 信頼性スコア --}}
                <div class="p-5 bg-white rounded-lg shadow border-l-4 border-emerald-500">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-500">信頼性スコア</h4>
                        <div class="p-2 bg-emerald-100 rounded-full">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold {{ $analytics['reliability_score'] >= 70 ? 'text-emerald-600' : ($analytics['reliability_score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $analytics['reliability_score'] }}
                        </span>
                        <span class="text-gray-500 pb-1">/100</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">インシデント発生率: {{ $analytics['incident_rate'] }}%</p>
                </div>

                {{-- 実質コストパフォーマンス --}}
                <div class="p-5 bg-white rounded-lg shadow border-l-4 border-blue-500">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-500">平均CPD（日額コスト）</h4>
                        <div class="p-2 bg-blue-100 rounded-full">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-end gap-1">
                        <span class="text-3xl font-bold text-gray-800">¥{{ number_format($analytics['avg_cpd']) }}</span>
                        <span class="text-gray-500 pb-1">/日</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">購入価格+維持費÷使用日数</p>
                </div>

                {{-- 平均使用期間 --}}
                <div class="p-5 bg-white rounded-lg shadow border-l-4 border-purple-500">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-500">平均使用期間</h4>
                        <div class="p-2 bg-purple-100 rounded-full">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-end gap-1">
                        <span class="text-3xl font-bold text-gray-800">{{ $analytics['avg_lifespan_years'] }}</span>
                        <span class="text-gray-500 pb-1">年</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">カテゴリ平均: {{ $analytics['category_life_years'] }}年</p>
                </div>

                {{-- 価格帯 --}}
                <div class="p-5 bg-white rounded-lg shadow border-l-4 border-orange-500">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-500">平均購入価格</h4>
                        <div class="p-2 bg-orange-100 rounded-full">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-end gap-1">
                        <span class="text-3xl font-bold text-gray-800">¥{{ number_format($analytics['price']['avg']) }}</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">¥{{ number_format($analytics['price']['min']) }} 〜 ¥{{ number_format($analytics['price']['max']) }}</p>
                </div>
            </div>

            {{-- インシデント分析 --}}
            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                {{-- インシデント種別分布 --}}
                <div class="p-6 bg-white rounded-lg shadow">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800">インシデント種別分布</h4>
                    @if(!empty($analytics['incident_type_distribution']))
                        <div class="space-y-3">
                            @php
                                $total = array_sum($analytics['incident_type_distribution']);
                                $colors = ['failure' => 'red', 'maintenance' => 'yellow', 'damage' => 'blue', 'loss' => 'gray'];
                            @endphp
                            @foreach($analytics['incident_type_distribution'] as $type => $count)
                                @php $percentage = $total > 0 ? round(($count / $total) * 100) : 0; @endphp
                                <div>
                                    <div class="flex justify-between mb-1 text-sm">
                                        <span class="font-medium text-gray-700">{{ \App\Models\Incident::INCIDENT_TYPES[$type] ?? $type }}</span>
                                        <span class="text-gray-500">{{ $count }}件 ({{ $percentage }}%)</span>
                                    </div>
                                    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-{{ $colors[$type] ?? 'gray' }}-500 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>インシデント報告なし</p>
                        </div>
                    @endif
                </div>

                {{-- 深刻度分布 --}}
                <div class="p-6 bg-white rounded-lg shadow">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800">深刻度分布</h4>
                    @if(!empty($analytics['severity_distribution']))
                        <div class="flex items-center justify-center gap-4">
                            @php
                                $total = array_sum($analytics['severity_distribution']);
                                $severityColors = ['low' => 'green', 'medium' => 'yellow', 'high' => 'orange', 'critical' => 'red'];
                            @endphp
                            @foreach(['low', 'medium', 'high', 'critical'] as $severity)
                                @php 
                                    $count = $analytics['severity_distribution'][$severity] ?? 0;
                                    $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                                @endphp
                                <div class="text-center">
                                    <div class="relative w-20 h-20 mx-auto mb-2">
                                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#E5E7EB" stroke-width="3"></circle>
                                            <circle 
                                                cx="18" cy="18" r="15.9" fill="none" 
                                                class="stroke-{{ $severityColors[$severity] }}-500"
                                                stroke-width="3"
                                                stroke-linecap="round"
                                                stroke-dasharray="{{ $percentage }} 100"
                                            ></circle>
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-gray-800">{{ $count }}</span>
                                    </div>
                                    <p class="text-xs font-medium text-{{ $severityColors[$severity] }}-600">
                                        {{ \App\Models\Incident::SEVERITY_LEVELS[$severity]['label'] ?? $severity }}
                                    </p>
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

            {{-- 時系列パターン & よくある問題 --}}
            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                {{-- 時系列発生パターン --}}
                <div class="p-6 bg-white rounded-lg shadow">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800">
                        <svg class="inline-block w-5 h-5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        時系列発生パターン
                    </h4>
                    <p class="mb-4 text-sm text-gray-500">購入後、いつ頃問題が起きやすいか</p>
                    @if(!empty($analytics['time_patterns']))
                        @php
                            $maxCount = max($analytics['time_patterns']);
                            $periods = ['0-3ヶ月', '3-6ヶ月', '6-12ヶ月', '1-2年', '2-3年', '3年以上'];
                        @endphp
                        <div class="flex items-end justify-between h-32 gap-2">
                            @foreach($periods as $period)
                                @php 
                                    $count = $analytics['time_patterns'][$period] ?? 0;
                                    $height = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                @endphp
                                <div class="flex-1 flex flex-col items-center">
                                    <span class="text-xs font-medium text-gray-700 mb-1">{{ $count }}</span>
                                    <div class="w-full bg-indigo-500 rounded-t transition-all" style="height: {{ max(4, $height) }}%"></div>
                                    <span class="text-xs text-gray-500 mt-2 text-center leading-tight">{{ $period }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400">
                            <p>時系列データなし</p>
                        </div>
                    @endif
                </div>

                {{-- よくある問題TOP5 --}}
                <div class="p-6 bg-white rounded-lg shadow">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800">
                        <svg class="inline-block w-5 h-5 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        よくある問題
                    </h4>
                    @if(!empty($analytics['top_problems']))
                        <div class="space-y-3">
                            @foreach($analytics['top_problems'] as $index => $problem)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <span class="flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-gray-600 rounded-full">{{ $index + 1 }}</span>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">{{ $problem['label'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $problem['count'] }}件報告</p>
                                    </div>
                                    @if($problem['avg_cost'] > 0)
                                        <span class="text-sm font-medium text-red-600">平均 ¥{{ number_format($problem['avg_cost']) }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>問題報告なし</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- リスク可視化 & ライフサイクルコスト予測 --}}
            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                {{-- リスク可視化 --}}
                <div class="p-6 bg-white rounded-lg shadow border-t-4 border-red-500">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800">
                        <svg class="inline-block w-5 h-5 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        リスク可視化
                    </h4>
                    <div class="space-y-4">
                        <div class="p-4 bg-red-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">購入後1年以内の故障確率</span>
                                <span class="text-xl font-bold {{ $analytics['one_year_failure_rate'] > 20 ? 'text-red-600' : ($analytics['one_year_failure_rate'] > 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $analytics['one_year_failure_rate'] }}%
                                </span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div 
                                    class="h-full rounded-full {{ $analytics['one_year_failure_rate'] > 20 ? 'bg-red-500' : ($analytics['one_year_failure_rate'] > 10 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                    style="width: {{ min(100, $analytics['one_year_failure_rate']) }}%"
                                ></div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">平均修理費用</span>
                                <span class="text-xl font-bold text-orange-600">
                                    ¥{{ number_format($analytics['avg_repair_cost']) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">保証期間後の修理費用リスク</p>
                        </div>
                    </div>
                </div>

                {{-- ライフサイクルコスト予測 --}}
                <div class="p-6 bg-white rounded-lg shadow border-t-4 border-indigo-500">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800">
                        <svg class="inline-block w-5 h-5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        ライフサイクルコスト予測
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">購入価格（平均）</span>
                            <span class="font-semibold">¥{{ number_format($analytics['price']['avg']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">予想メンテナンスコスト</span>
                            <span class="font-semibold text-orange-600">+¥{{ number_format($analytics['lifecycle_cost'] - $analytics['price']['avg']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">予想寿命</span>
                            <span class="font-semibold">{{ $analytics['category_life_years'] }}年</span>
                        </div>
                        <div class="flex justify-between items-center py-3 bg-indigo-50 rounded-lg px-3 -mx-1">
                            <span class="font-medium text-indigo-800">総ライフサイクルコスト</span>
                            <span class="text-xl font-bold text-indigo-600">¥{{ number_format($analytics['lifecycle_cost']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- 製品未選択時：全体統計を表示 --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">全体統計</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                        <p class="text-sm text-gray-500">登録製品数</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($this->globalStats['total_products']) }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-red-50 to-orange-50 rounded-lg border border-red-100">
                        <p class="text-sm text-gray-500">報告インシデント数</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($this->globalStats['total_incidents']) }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg border border-green-100">
                        <p class="text-sm text-gray-500">全体平均CPD</p>
                        <p class="text-3xl font-bold text-gray-800">¥{{ number_format($this->globalStats['global_avg_cpd']) }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-white rounded-lg shadow text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">製品を検索してください</h3>
                <p class="text-gray-500">型番、製品名、またはメーカー名で検索すると、集合知に基づいた詳細な分析結果を表示します。</p>
            </div>
        @endif

        {{-- 3. 比較ツール --}}
        @if(!empty($compareProducts))
            <div class="mt-6 p-6 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <svg class="inline-block w-5 h-5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        製品比較（{{ count($compareProducts) }}/3）
                    </h3>
                    <button wire:click="clearCompare" class="text-sm text-gray-500 hover:text-gray-700">
                        クリア
                    </button>
                </div>
                
                @if(!empty($this->comparisonData))
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 text-left font-medium text-gray-600">項目</th>
                                    @foreach($this->comparisonData as $product)
                                        <th class="py-3 text-center font-medium text-gray-800">
                                            {{ $product['model_number'] }}
                                            <br><span class="text-xs text-gray-500 font-normal">{{ $product['manufacturer'] }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr>
                                    <td class="py-3 text-gray-600">サンプル数</td>
                                    @foreach($this->comparisonData as $product)
                                        <td class="py-3 text-center font-medium">{{ $product['sample_count'] }}件</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600">信頼性スコア</td>
                                    @foreach($this->comparisonData as $product)
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-1 rounded-full text-sm font-medium {{ $product['reliability_score'] >= 70 ? 'bg-green-100 text-green-700' : ($product['reliability_score'] >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $product['reliability_score'] }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600">平均CPD</td>
                                    @foreach($this->comparisonData as $product)
                                        <td class="py-3 text-center font-medium">¥{{ number_format($product['avg_cpd']) }}/日</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600">平均価格</td>
                                    @foreach($this->comparisonData as $product)
                                        <td class="py-3 text-center font-medium">¥{{ number_format($product['avg_price']) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600">平均修理費用</td>
                                    @foreach($this->comparisonData as $product)
                                        <td class="py-3 text-center font-medium text-orange-600">¥{{ number_format($product['avg_repair_cost']) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600">インシデント発生率</td>
                                    @foreach($this->comparisonData as $product)
                                        <td class="py-3 text-center font-medium">{{ $product['incident_rate'] }}%</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
