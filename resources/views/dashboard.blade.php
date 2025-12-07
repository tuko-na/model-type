<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (!empty($stats))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- TCO by Category -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="font-semibold text-lg mb-4">カテゴリ別総所有コスト (TCO)</h3>
                            <canvas id="tcoChart"></canvas>
                        </div>
                    </div>

                    <!-- Incidents by Type -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="font-semibold text-lg mb-4">種類別インシデント数</h3>
                            <canvas id="incidentsChart"></canvas>
                        </div>
                    </div>

                    <!-- Products per Category -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="font-semibold text-lg mb-4">カテゴリ別製品数</h3>
                            <canvas id="productsPerCategoryChart"></canvas>
                        </div>
                    </div>
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
        });
    </script>
    @endpush
</x-app-layout>
