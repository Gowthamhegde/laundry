<?php

// Include database connection
require_once 'config.php';
require_once 'auth.php';

// Pagination variables
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search and filter functionality
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$date_filter = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '';

// Build WHERE conditions
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(customer_name LIKE :search OR customer_email LIKE :search OR customer_phone LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($status_filter) {
    $where_conditions[] = "status = :status";
    $params[':status'] = $status_filter;
}

if ($date_filter) {
    $where_conditions[] = "DATE(created_at) = :date";
    $params[':date'] = $date_filter;
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Fetch total number of orders
$total_query = "SELECT COUNT(*) FROM orders $where_clause";
$total_stmt = $pdo->prepare($total_query);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $records_per_page);

// Fetch orders with pagination
$query = "SELECT * FROM orders $where_clause ORDER BY created_at DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
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
        .table-container {
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Orders Management</h1>
                <div class="flex space-x-2">
                    <a href="export_orders.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-file-export mr-2"></i> Export
                    </a>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="glass-card p-4 rounded-lg shadow mb-6">
                <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search orders..." 
                               value="<?= $search ?>" 
                               class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select name="status" class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= $status_filter == 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                    <input type="date" name="date" value="<?= $date_filter ?>" 
                           class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex-1">
                            Filter
                        </button>
                        <?php if ($search || $status_filter || $date_filter): ?>
                            <a href="orders.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 flex items-center">
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="glass-card rounded-lg shadow overflow-hidden">
                <div class="table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Booking</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($orders) > 0): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $order['id'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($order['pickup_address']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($order['customer_phone']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($order['customer_email']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₹<?= number_format($order['total_price'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><?= date('M j, Y', strtotime($order['pickup_date'])) ?></div>
                                            <div><?= $order['pickup_time'] ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <select class="status-select border rounded px-2 py-1 text-sm" 
                                                        data-order-id="<?= $order['id'] ?>">
                                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                </select>
                                                <button class="delete-btn text-red-600 hover:text-red-900" 
                                                        data-order-id="<?= $order['id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No orders found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>&date=<?= $date_filter ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="?page=<?= $page < $total_pages ? $page + 1 : $total_pages ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>&date=<?= $date_filter ?>" 
                               class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium"><?= $offset + 1 ?></span> to <span class="font-medium"><?= min($offset + $records_per_page, $total_rows) ?></span> of <span class="font-medium"><?= $total_rows ?></span> orders
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>&date=<?= $date_filter ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>&date=<?= $date_filter ?>" 
                                           class="<?= $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    <a href="?page=<?= $page < $total_pages ? $page + 1 : $total_pages ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>&date=<?= $date_filter ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 p-4 rounded-lg shadow-lg hidden z-50"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Status update handler
            $('.status-select').change(function() {
                const orderId = $(this).data('order-id');
                const newStatus = $(this).val();
                const row = $(this).closest('tr');

                $.ajax({
                    url: 'update_order.php',
                    type: 'POST',
                    data: {
                        order_id: orderId,
                        status: newStatus
                    },
                    success: function(response) {
                        // Update status badge
                        const statusBadge = row.find('.status-badge');
                        statusBadge.removeClass('status-pending status-processing status-completed')
                                   .addClass('status-' + newStatus)
                                   .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                        
                        showToast('Order status updated successfully', 'success');
                    },
                    error: function(xhr, status, error) {
                        showToast('Error updating status: ' + error, 'error');
                        // Revert select to original value
                        $(this).val(row.data('original-status'));
                    }
                });
            });

            // Delete handler
            $('.delete-btn').click(function() {
                if (!confirm('Are you sure you want to delete this order?')) {
                    return;
                }

                const orderId = $(this).data('order-id');
                const row = $(this).closest('tr');

                $.ajax({
                    url: 'delete_order.php',
                    type: 'POST',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                        showToast('Order deleted successfully', 'success');
                    },
                    error: function(xhr, status, error) {
                        showToast('Error deleting order: ' + error, 'error');
                    }
                });
            });

            // Toast notification function
            function showToast(message, type = 'success') {
                const toast = $('#toast');
                toast.text(message);
                toast.removeClass('hidden bg-green-500 bg-red-500')
                     .addClass(type === 'error' ? 'bg-red-500' : 'bg-green-500')
                     .fadeIn();
                
                setTimeout(() => toast.fadeOut(), 3000);
            }
        });
    </script>
</body>
</html>