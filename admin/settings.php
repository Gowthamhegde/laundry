<?php


// Include database connection
require_once 'config.php';

// Fetch current settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $business_name = $_POST['business_name'];
        $business_email = $_POST['business_email'];
        $business_phone = $_POST['business_phone'];
        $business_address = $_POST['business_address'];
        $currency = $_POST['currency'];
        $tax_rate = $_POST['tax_rate'];
        $opening_hours = $_POST['opening_hours'];

        // Check if settings exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM settings");
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Update existing settings
            $stmt = $pdo->prepare("UPDATE settings SET 
                                  business_name = ?, 
                                  business_email = ?, 
                                  business_phone = ?, 
                                  business_address = ?, 
                                  currency = ?, 
                                  tax_rate = ?, 
                                  opening_hours = ?");
            $stmt->execute([$business_name, $business_email, $business_phone, $business_address, $currency, $tax_rate, $opening_hours]);
        } else {
            // Insert new settings
            $stmt = $pdo->prepare("INSERT INTO settings 
                                  (business_name, business_email, business_phone, business_address, currency, tax_rate, opening_hours) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$business_name, $business_email, $business_phone, $business_address, $currency, $tax_rate, $opening_hours]);
        }

        $success = "Settings updated successfully!";
    } catch (PDOException $e) {
        $error = "Error saving settings: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
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
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
                    <p class="text-gray-600">Manage your laundry business configuration</p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <form method="POST" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Business Information</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                            <input type="text" name="business_name" value="<?= htmlspecialchars($settings['business_name'] ?? '') ?>" 
                                   class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Email</label>
                            <input type="email" name="business_email" value="<?= htmlspecialchars($settings['business_email'] ?? '') ?>" 
                                   class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Phone</label>
                            <input type="text" name="business_phone" value="<?= htmlspecialchars($settings['business_phone'] ?? '') ?>" 
                                   class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Address</label>
                            <textarea name="business_address" rows="3"
                                   class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($settings['business_address'] ?? '') ?></textarea>
                        </div>

                        <!-- Business Settings -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Business Settings</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <select name="currency" class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="₹" <?= ($settings['currency'] ?? '₹') === '₹' ? 'selected' : '' ?>>Indian Rupee (₹)</option>
                                <option value="$" <?= ($settings['currency'] ?? '₹') === '$' ? 'selected' : '' ?>>US Dollar ($)</option>
                                <option value="€" <?= ($settings['currency'] ?? '₹') === '€' ? 'selected' : '' ?>>Euro (€)</option>
                                <option value="£" <?= ($settings['currency'] ?? '₹') === '£' ? 'selected' : '' ?>>Pound (£)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" step="0.01" value="<?= htmlspecialchars($settings['tax_rate'] ?? '18') ?>" 
                                   class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Hours</label>
                            <textarea name="opening_hours" rows="2"
                                   class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($settings['opening_hours'] ?? 'Monday-Friday: 9AM-8PM\nSaturday-Sunday: 10AM-6PM') ?></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="md:col-span-2 pt-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                                Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mt-8 border border-red-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-red-600">Danger Zone</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">Reset All Data</h4>
                                <p class="text-sm text-gray-600">This will delete all orders, customers, and settings</p>
                            </div>
                            <button onclick="confirmReset()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                                Reset System
                            </button>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">Export Database</h4>
                                <p class="text-sm text-gray-600">Create a backup of all your data</p>
                            </div>
                            <a href="export_data.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                Export Data
                            </a>
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

        // Confirm system reset
        function confirmReset() {
            if (confirm('Are you absolutely sure you want to reset all data? This cannot be undone!')) {
                window.location.href = 'reset_system.php';
            }
        }
    </script>
</body>
</html>