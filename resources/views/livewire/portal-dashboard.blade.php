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
            {{-- 
                Improved x-data structure:
                - Listens for 'product-selected' event from Livewire to update data.
                - Uses wire:ignore on chart containers to prevent Livewire from DOM-diffing the canvas elements.
            --}}
            <div 
                x-data='focusMonitor(@json($this->focusMonitorData))' 
                @product-selected.window="updateCharts($event.detail.data)"
            >
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    {{-- Left: Lifespan Forecast --}}
                    <div class="p-4 rounded-lg bg-gray-50">
                        <h3 class="mb-4 text-sm font-medium text-gray-500">製品寿命の予測</h3>
                        {{-- wire:ignore prevents Livewire from re-rendering this div --}}
                        <div class="relative w-full h-48" wire:ignore>
                            <canvas x-ref="lifespanCanvas"></canvas>
                        </div>
                        <p class="mt-2 text-sm text-center text-gray-600">
                            カテゴリ平均: <span class="font-bold" x-text="data.category_life_years + ' 年'"></span>
                        </p>
                    </div>

                    {{-- Right: CPD Comparison --}}
                    <div class="p-4 rounded-lg bg-gray-50">
                        <h3 class="mb-4 text-sm font-medium text-gray-500">1日あたりのコスト (CPD) 効率</h3>
                        {{-- wire:ignore prevents Livewire from re-rendering this div --}}
                        <div class="relative w-full h-48" wire:ignore>
                             <canvas x-ref="cpdCanvas"></canvas>
                        </div>
                         <p class="mt-2 text-sm text-center text-gray-600">
                            自分のCPD: <span class="font-bold" x-text="'¥' + new Intl.NumberFormat().format(data.cpd)"></span> vs 平均: ¥<span x-text="new Intl.NumberFormat().format(data.avg_cpd)"></span>
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-4 mt-6">
                     <a href="{{ route('incidents.create', ['product_id' => $this->selectedProduct->id]) }}" class="px-4 py-2 text-sm text-white transition bg-red-600 rounded hover:bg-red-700">不具合を報告</a>
                     {{-- Placeholder for Note --}}
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
                lifespanChartInstance: null,
                cpdChartInstance: null,

                init() {
                    // console.log('Alpine Init with data:', this.data);
                    if (this.data) {
                        this.initCharts();
                    }
                },

                updateCharts(newData) {
                    // console.log('Data received:', newData);
                    this.data = newData;
                    // Instead of partial updates, we destroy and re-create.
                    // This ensures the chart is not lost and triggers the entrance animation every time.
                    this.initCharts();
                },

                initCharts() {
                    if (!window.Chart) {
                        console.warn('Chart.js not ready, retrying in 100ms...');
                        setTimeout(() => this.initCharts(), 100);
                        return;
                    }

                    // Destroy old instances just in case
                    if (this.lifespanChartInstance) {
                        this.lifespanChartInstance.destroy();
                    }
                    if (this.cpdChartInstance) {
                        this.cpdChartInstance.destroy();
                    }

                    // Lifespan Chart
                    if (this.$refs.lifespanCanvas) {
                        const ctx1 = this.$refs.lifespanCanvas.getContext('2d');
                        this.lifespanChartInstance = new window.Chart(ctx1, {
                            type: 'doughnut',
                            data: {
                                labels: ['使用済み', '残り'],
                                datasets: [{
                                    data: [this.data.lifespan_percentage, 100 - this.data.lifespan_percentage],
                                    backgroundColor: ['#4F46E5', '#E5E7EB'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { enabled: false }
                                }
                            },
                            plugins: [{
                                id: 'textCenter',
                                beforeDraw: function(chart) {
                                    var width = chart.width,
                                        height = chart.height,
                                        ctx = chart.ctx;
                                    ctx.restore();
                                    var fontSize = (height / 114).toFixed(2);
                                    ctx.font = "bold " + fontSize + "em sans-serif";
                                    ctx.textBaseline = "middle";
                                    var text = Math.round(chart.data.datasets[0].data[0]) + "%",
                                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                                        textY = height / 2;
                                    ctx.fillStyle = "#374151";
                                    ctx.fillText(text, textX, textY);
                                    ctx.save();
                                }
                            }]
                        });
                    }

                    // CPD Chart
                    if (this.$refs.cpdCanvas) {
                        const ctx2 = this.$refs.cpdCanvas.getContext('2d');
                        this.cpdChartInstance = new window.Chart(ctx2, {
                            type: 'bar',
                            data: {
                                labels: ['自分の製品', 'カテゴリ平均'],
                                datasets: [{
                                    label: '1日あたりのコスト (¥)',
                                    data: [this.data.cpd, this.data.avg_cpd],
                                    backgroundColor: [
                                        this.data.cpd > this.data.avg_cpd ? '#EF4444' : '#10B981',
                                        '#9CA3AF'
                                    ],
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    x: { beginAtZero: true }
                                }
                            }
                        });
                    }
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