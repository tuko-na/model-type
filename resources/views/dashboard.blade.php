<x-app-layout :headerColor="$isPublic ? 'bg-emerald-50' : 'bg-white'">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex bg-gray-200 rounded-lg p-1 shadow-inner">
                <a href="{{ route('mode.switch', 'private') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ !$isPublic ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
                    プライベート
                </a>
                <a href="{{ route('mode.switch', 'public') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ $isPublic ? 'bg-emerald-600 shadow text-white' : 'text-gray-500 hover:text-gray-700' }}">
                    パブリック
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (!empty($stats))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- TCO by Category -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="relative flex items-center mb-4">
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                                <h3 class="flex-shrink mx-4 font-semibold text-lg text-gray-600">カテゴリ別総所有コスト (TCO)</h3>
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                            </div>
                            <canvas id="tcoChart"></canvas>
                        </div>
                    </div>

                    <!-- Incidents by Type -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="relative flex items-center mb-4">
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                                <h3 class="flex-shrink mx-4 font-semibold text-lg text-gray-600">種類別インシデント数</h3>
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                            </div>
                            <canvas id="incidentsChart"></canvas>
                        </div>
                    </div>

                    <!-- Products per Category -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="relative flex items-center mb-4">
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                                <h3 class="flex-shrink mx-4 font-semibold text-lg text-gray-600">カテゴリ別製品数</h3>
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                            </div>
                            <canvas id="productsPerCategoryChart"></canvas>
                        </div>
                    </div>

                    <!-- Depreciation Chart -->
                    @if ($depreciationData)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="relative flex items-center mb-4">
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                                <h3 class="flex-shrink mx-4 font-semibold text-lg text-gray-600">減価償却グラフ</h3>
                                <div class="flex-grow border-t {{ $isPublic ? 'border-emerald-400' : 'border-gray-200' }}"></div>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-sm text-gray-500">{{ $depreciationData['product_name'] }} の簿価の推移</p>
                                <select id="productSelector" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" onchange="location = '{{ route('dashboard') }}?product_id=' + this.value;">
                                    @foreach ($depreciableProducts as $product)
                                        <option value="{{ $product->id }}" {{ $product->id == $depreciationData['product_id'] ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <canvas id="depreciationChart"></canvas>
                        </div>
                    </div>
                    @endif
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("ログインしました！まだ統計を表示するデータがありません。") }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (!empty($stats))
                // TCO Chart (Bar)
                const tcoCtx = document.getElementById('tcoChart').getContext('2d');
                new Chart(tcoCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($stats['tco_by_category']['labels']),
                        datasets: [{
                            label: '総所有コスト',
                            data: @json($stats['tco_by_category']['data']),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Incidents Chart (Pie)
                const incidentsCtx = document.getElementById('incidentsChart').getContext('2d');
                new Chart(incidentsCtx, {
                    type: 'pie',
                    data: {
                        labels: @json($stats['incidents_by_type']['labels']),
                        datasets: [{
                            data: @json($stats['incidents_by_type']['data']),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 206, 86, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                                'rgba(153, 102, 255, 0.5)',
                            ],
                        }]
                    }
                });

                // Products per Category (Doughnut)
                const productsPerCategoryCtx = document.getElementById('productsPerCategoryChart').getContext('2d');
                new Chart(productsPerCategoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($stats['products_per_category']['labels']),
                        datasets: [{
                            data: @json($stats['products_per_category']['data']),
                             backgroundColor: [
                                'rgba(255, 159, 64, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                                'rgba(153, 102, 255, 0.5)',
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                            ],
                        }]
                    }
                });
            @endif

            @if ($depreciationData)
                const depreciationCtx = document.getElementById('depreciationChart').getContext('2d');
                new Chart(depreciationCtx, {
                    type: 'line',
                    data: {
                        labels: @json($depreciationData['labels']),
                        datasets: [{
                            label: '簿価',
                            data: @json($depreciationData['data']),
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value, index, values) {
                                        return '¥' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += '¥' + context.parsed.y.toLocaleString();
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        });
    </script>
    @endpush
</x-app-layout>
