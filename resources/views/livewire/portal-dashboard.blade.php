    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex bg-gray-200 rounded-lg p-1 shadow-inner">
                @php
                    $isPublic = session('view_mode') === 'public';
                @endphp
                <a href="{{ route('mode.switch', 'private') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ !$isPublic ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
                    Private
                </a>
                <a href="{{ route('mode.switch', 'public') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ $isPublic ? 'bg-emerald-600 shadow text-white' : 'text-gray-500 hover:text-gray-700' }}">
                    Public
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

<div class="space-y-6">
    {{-- Header / KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm font-medium">Avg Lifespan</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $this->globalKpis['avg_lifespan'] }} <span class="text-sm font-normal text-gray-400">years</span></p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <h3 class="text-gray-500 text-sm font-medium">Annual Maint. Cost</h3>
            <p class="text-2xl font-bold text-gray-800">¥{{ number_format($this->globalKpis['annual_maintenance_cost']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <h3 class="text-gray-500 text-sm font-medium">Incident Rate</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $this->globalKpis['incident_rate'] }}%</p>
        </div>
    </div>

    {{-- Focus Monitor --}}
    <div class="bg-white rounded-lg shadow p-6" x-data="focusMonitor(@json($this->focusMonitorData))">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Focus Monitor</h2>
            <div class="w-full md:w-1/3">
                 <select wire:change="selectProduct($event.target.value)" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($this->allProducts as $p)
                        <option value="{{ $p->id }}" @selected($selectedProductId == $p->id)>{{ $p->name }} ({{ $p->model_number }})</option>
                    @endforeach
                 </select>
            </div>
        </div>

        @if($this->selectedProduct)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8" wire:key="charts-{{ $selectedProductId }}">
                {{-- Left: Lifespan Forecast --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Lifespan Forecast</h3>
                    <div class="relative h-48 w-full">
                        <canvas x-ref="lifespanCanvas"></canvas>
                    </div>
                    <p class="text-center mt-2 text-sm text-gray-600">
                        Category Avg: <span class="font-bold">{{ $this->focusMonitorData['category_life_years'] }} years</span>
                    </p>
                </div>

                {{-- Right: CPD Comparison --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Cost Per Day (CPD) Efficiency</h3>
                    <div class="relative h-48 w-full">
                         <canvas x-ref="cpdCanvas"></canvas>
                    </div>
                     <p class="text-center mt-2 text-sm text-gray-600">
                        My CPD: <span class="font-bold">¥{{ number_format($this->focusMonitorData['cpd']) }}</span> vs Avg: ¥{{ number_format($this->focusMonitorData['avg_cpd']) }}
                    </p>
                </div>
            </div>
            
            <div class="mt-6 flex gap-4">
                 <a href="{{ route('incidents.create', ['product_id' => $this->selectedProduct->id]) }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm transition">Report Issue</a>
                 {{-- Placeholder for Note --}}
                 <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm transition">Add Note</button>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No product selected.</p>
        @endif
    </div>

    {{-- Discovery --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Worst CPD --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Worst CPD (Efficiency)</h3>
            <ul class="space-y-3">
                @foreach($this->discovery['worst_cpd'] as $item)
                    <li class="flex justify-between items-center text-sm">
                        <span class="truncate w-2/3">{{ $item['product']->name }}</span>
                        <span class="font-mono text-red-600">¥{{ number_format($item['cpd'], 1) }}/day</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Hall of Fame --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Hall of Fame (Longest Life)</h3>
            <ul class="space-y-3">
                 @foreach($this->discovery['hall_of_fame'] as $p)
                    <li class="flex justify-between items-center text-sm">
                        <span class="truncate w-2/3">{{ $p->name }}</span>
                        <span class="font-mono text-green-600">{{ \Carbon\Carbon::parse($p->purchase_date)->diffInYears(now()) }} yrs</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Alerts --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-800 mb-3 border-b pb-2">Alerts</h3>
            @if($this->discovery['alerts']->isEmpty())
                <p class="text-sm text-gray-400">No active alerts.</p>
            @else
                <ul class="space-y-3">
                    @foreach($this->discovery['alerts'] as $p)
                        <li class="flex items-start gap-2 text-sm">
                            <span class="text-yellow-500 mt-0.5">⚠️</span>
                            <div>
                                <div class="font-medium">{{ $p->name }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($p->status == 'repairing') In Repair
                                    @else Warranty Expiring
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('focusMonitor', (initialData) => ({
            data: initialData,
            lifespanChartInstance: null,
            cpdChartInstance: null,

            init() {
                if (this.data) {
                    this.initCharts();
                }
            },

            initCharts() {
                // Lifespan Chart
                if (this.$refs.lifespanCanvas) {
                    const ctx1 = this.$refs.lifespanCanvas.getContext('2d');
                    this.lifespanChartInstance = new Chart(ctx1, {
                        type: 'doughnut',
                        data: {
                            labels: ['Used', 'Remaining'],
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
                    this.cpdChartInstance = new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: ['My Product', 'Category Avg'],
                            datasets: [{
                                label: 'Cost Per Day (¥)',
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
        }))
    })
</script>