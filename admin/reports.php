<?php

// Include database connection
require_once 'config.php';

// Date range for reports (default: current month)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Fetch report data
try {
    // Sales Summary
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_orders,
        SUM(total_price) as total_revenue,
        AVG(total_price) as avg_order_value
        FROM orders 
        WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$start_date, $end_date]);
    $sales_summary = $stmt->fetch(PDO::FETCH_ASSOC);

    // Service Type Breakdown
    $stmt = $pdo->prepare("SELECT 
        service_type,
        COUNT(*) as order_count,
        SUM(total_price) as total_revenue
        FROM orders 
        WHERE created_at BETWEEN ? AND ?
        GROUP BY service_type");
    $stmt->execute([$start_date, $end_date]);
    $service_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monthly Revenue Data (last 6 months)
    $stmt = $pdo->query("SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        SUM(total_price) AS revenue
        FROM orders 
        WHERE status = 'completed'
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6");
    $monthly_revenue = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

    // Top Customers
    $stmt = $pdo->prepare("SELECT 
        customer_name,
        customer_phone,
        COUNT(*) as order_count,
        SUM(total_price) as total_spent
        FROM orders 
        GROUP BY customer_phone
        ORDER BY total_spent DESC
        LIMIT 5");
    $stmt->execute();
    $top_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Laundry Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                z-index: 50;
            }

            .sidebar.active {
                left: 0;
            }
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50 flex">
    <!-- Sidebar Navigation -->
    <?php include 'sidebar.php'; ?>
    <!-- Mobile Menu Button -->
    <button class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded" id="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <div class="flex-1 ml-0 md:ml-64 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Business Reports</h1>
                    <p class="text-gray-600">Analyze your laundry business performance</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="export_report.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-file-export mr-2"></i> Export
                    </a>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                <form method="get" class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="<?= $start_date ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="<?= $end_date ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg h-[42px]">
                            Apply Filter
                        </button>
                    </div>
                    <div>
                        <a href="reports.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg h-[42px] inline-flex items-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500 transition-all duration-300 card-hover">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-500">Total Orders</p>
                            <h3 class="text-2xl font-bold mt-2"><?= number_format($sales_summary['total_orders'] ?? 0) ?></h3>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500 transition-all duration-300 card-hover">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-500">Total Revenue</p>
                            <h3 class="text-2xl font-bold mt-2">₹<?= number_format($sales_summary['total_revenue'] ?? 0, 2) ?></h3>
                        </div>
                        <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-purple-500 transition-all duration-300 card-hover">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-500">Avg. Order Value</p>
                            <h3 class="text-2xl font-bold mt-2">₹<?= number_format($sales_summary['avg_order_value'] ?? 0, 2) ?></h3>
                        </div>
                        <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Monthly Revenue</h3>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Service Breakdown Chart -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Service Type Breakdown</h3>
                    <div class="h-64">
                        <canvas id="serviceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Reports -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Customers -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Top Customers</h3>
                            <span class="text-sm text-gray-500">By Total Spending</span>
                        </div>
                        <div class="space-y-4">
                            <?php if (!empty($top_customers)): ?>
                                <?php foreach ($top_customers as $customer): ?>
                                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium"><?= htmlspecialchars($customer['customer_name']) ?></h4>
                                            <p class="text-sm text-gray-500"><?= htmlspecialchars($customer['customer_phone']) ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium">₹<?= number_format($customer['total_spent'], 2) ?></p>
                                            <p class="text-sm text-gray-500"><?= $customer['order_count'] ?> orders</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-4">No customer data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Service Breakdown Table -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Service Performance</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (!empty($service_breakdown)): ?>
                                        <?php foreach ($service_breakdown as $service): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    <?= ucfirst(str_replace('_', ' ', $service['service_type'])) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?= number_format($service['order_count']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    ₹<?= number_format($service['total_revenue'], 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No service data available
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle mobile menu
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($monthly_revenue, 'month')) ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?= json_encode(array_column($monthly_revenue, 'revenue')) ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
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
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Service Chart
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        const serviceChart = new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_map(function ($s) {
                            return ucfirst(str_replace('_', ' ', $s['service_type']));
                        }, $service_breakdown)) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($service_breakdown, 'total_revenue')) ?>,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(139, 92, 246, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₹' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>