<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                <!-- Period Selector -->
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <label for="period-selector" class="block text-sm font-medium text-gray-700">Select Period</label>
                        <select id="period-selector" 
                                class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                onchange="window.location.href = '?period=' + this.value">
                            <option value="year" <?php echo e(request('period') == 'year' ? 'selected' : ''); ?>>Last Year</option>
                            <option value="6months" <?php echo e(request('period') == '6months' ? 'selected' : ''); ?>>Last 6 Months</option>
                            <option value="month" <?php echo e(request('period') == 'month' ? 'selected' : ''); ?>>Last Month</option>
                            <option value="week" <?php echo e(request('period') == 'week' ? 'selected' : ''); ?>>Last Week</option>
                        </select>
                    </div>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox" id="toggleSales" checked>
                            <span class="ml-2">Sales</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox" id="toggleProfit" checked>
                            <span class="ml-2">Profit</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox" id="toggleTrendline">
                            <span class="ml-2">Trend Lines</span>
                        </label>
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h4 class="text-sm font-medium text-gray-500">Total Sales</h4>
                        <p class="text-2xl font-bold text-gray-900">$<?php echo e(number_format(array_sum($salesData['data'] ?? []), 2)); ?></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h4 class="text-sm font-medium text-gray-500">Total Profit</h4>
                        <p class="text-2xl font-bold text-gray-900">$<?php echo e(number_format(array_sum($profitData['data'] ?? []), 2)); ?></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h4 class="text-sm font-medium text-gray-500">Total Expenses</h4>
                        <p class="text-2xl font-bold text-gray-900">$<?php echo e(number_format(array_sum($expensesData['data'] ?? []), 2)); ?></p>
                    </div>
                </div>

                <!-- Combined Chart Container -->
                <div class="relative" style="height: 400px; width: 100%;">
                    <canvas id="combinedChart"></canvas>
                </div>

                <!-- Expenses Chart Container -->
                <div class="relative mt-8" style="height: 400px; width: 100%;">
                    <canvas id="expensesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/regression@2.0.1/dist/regression.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing combined chart code
            const ctx = document.getElementById('combinedChart');
            const labels = <?php echo json_encode($salesData['labels'] ?? [], 15, 512) ?>;
            const salesData = <?php echo json_encode($salesData['data'] ?? [], 15, 512) ?>;
            const profitData = <?php echo json_encode($profitData['data'] ?? [], 15, 512) ?>;

            // Calculate trendlines
            function calculateTrendline(data) {
                const points = data.map((y, x) => [x, y]);
                const result = regression.linear(points);
                return result.points.map(point => point[1]);
            }

            const salesTrendline = calculateTrendline(salesData);
            const profitTrendline = calculateTrendline(profitData);

            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Sales',
                            data: salesData,
                            borderColor: '#b71540',
                            backgroundColor: 'rgba(183, 21, 64, 0.1)',
                            tension: 0.1,
                            fill: true
                        },
                        {
                            label: 'Sales Trend',
                            data: salesTrendline,
                            borderColor: '#b71540',
                            borderDash: [5, 5],
                            borderWidth: 1,
                            pointRadius: 0,
                            fill: false,
                            hidden: true
                        },
                        {
                            label: 'Profit',
                            data: profitData,
                            borderColor: '#2ecc71',
                            backgroundColor: 'rgba(46, 204, 113, 0.1)',
                            tension: 0.1,
                            fill: true
                        },
                        {
                            label: 'Profit Trend',
                            data: profitTrendline,
                            borderColor: '#2ecc71',
                            borderDash: [5, 5],
                            borderWidth: 1,
                            pointRadius: 0,
                            fill: false,
                            hidden: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': $' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Expenses Chart
            const ctxExpenses = document.getElementById('expensesChart').getContext('2d');
            const expensesChart = new Chart(ctxExpenses, {
                type: 'line', // Match the combined chart type
                data: {
                    labels: <?php echo json_encode($expensesData['labels'], 15, 512) ?>,
                    datasets: [{
                        label: 'Expenses',
                        data: <?php echo json_encode($expensesData['data'], 15, 512) ?>,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>

<?php /**PATH /Users/jeremy/Desktop/SHOPBOOKS/SHOPBOOKS/shopkeeper/resources/views/dashboard.blade.php ENDPATH**/ ?>