<?php

// Include database connection
require_once 'config.php';
require_once 'auth.php';

// Fetch dashboard statistics
$stats = [];
try {
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $stats['total_orders'] = $stmt->fetchColumn();

    // Total revenue
    $stmt = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'completed'");
    $stats['total_revenue'] = $stmt->fetchColumn() ?: 0;

    // Active customers (customers with orders in last 30 days)
    $stmt = $pdo->query("SELECT COUNT(DISTINCT customer_email) FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['active_customers'] = $stmt->fetchColumn();

    // Pending orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetchColumn();

    // Recent orders (last 5)
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent messages (last 3)
    $stmt = $pdo->query("SELECT c.name,f.comments,f.created_at from feedback f  join  customers c on c.customer_id=f.customer_id order BY f.created_at DESC limit 3");
    $recent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monthly revenue data for chart
    $stmt = $pdo->query("SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month, 
        SUM(total_price) AS revenue 
        FROM orders 
        WHERE status = 'completed' 
        GROUP BY month 
        ORDER BY month DESC 
        LIMIT 6");
    $revenue_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle error
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-processing {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        .status-completed {
            background-color: #D1FAE5;
            color: #065F46;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                    <p class="text-gray-600">Welcome back, <?= $_SESSION['admin_name'] ?? 'Admin' ?></p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-bell text-gray-500 text-xl"></i>
                        <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <span class="hidden md:inline font-medium"><?= $_SESSION['admin_name'] ?? 'Admin' ?></span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500 transition-all duration-300 card-hover">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-500">Total Orders</p>
                            <h3 class="text-2xl font-bold mt-2"><?= number_format($stats['total_orders']) ?></h3>
                            <p class="text-sm text-green-500 mt-2"><i class="fas fa-arrow-up"></i> 12% from last month</p>
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
                            <h3 class="text-2xl font-bold mt-2">₹<?= number_format($stats['total_revenue'], 2) ?></h3>
                            <p class="text-sm text-green-500 mt-2"><i class="fas fa-arrow-up"></i> 8% from last month</p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500 transition-all duration-300 card-hover">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-500">Active Customers</p>
                            <h3 class="text-2xl font-bold mt-2"><?= number_format($stats['active_customers']) ?></h3>
                            <p class="text-sm text-red-500 mt-2"><i class="fas fa-arrow-down"></i> 3% from last month</p>
                        </div>
                        <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-purple-500 transition-all duration-300 card-hover">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-500">Pending Orders</p>
                            <h3 class="text-2xl font-bold mt-2"><?= number_format($stats['pending_orders']) ?></h3>
                            <p class="text-sm text-green-500 mt-2"><i class="fas fa-arrow-down"></i> 15% from last month</p>
                        </div>
                        <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
    </div>
            </div>

            <!-- Recent Orders and Messages -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-md lg:col-span-2">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Orders</h3>
                            <a href="orders.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $order['id'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></div>
                                                <div class="text-sm text-gray-500"><?= htmlspecialchars($order['customer_phone']) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹<?= number_format($order['total_price'], 2) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="status-badge status-<?= $order['status'] ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Messages</h3>
                            <a href="messages.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                        </div>
                        <div class="space-y-4">
                            <?php foreach ($recent_messages as $message): ?>
                                <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex-shrink-0 flex items-center justify-center">
                                        <i class="fas fa-envelope text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium"><?= htmlspecialchars($message['name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars(substr($message['comments'], 0, 50)) ?>...</p>
                                        <p class="text-xs text-gray-400 mt-1"><?= date('M j, g:i A', strtotime($message['created_at'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column(array_reverse($revenue_data), 'month')) ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?= json_encode(array_column(array_reverse($revenue_data), 'revenue')) ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
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
    </script>
</body>
</html>