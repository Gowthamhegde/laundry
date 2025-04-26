<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laundry Price List</title>
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
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
  </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 text-gray-800 min-h-screen flex">
  <!-- Sidebar Navigation -->
  <?php include 'sidebar.php'; ?>

  <!-- Mobile Menu Button -->
  <button class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded" id="menu-toggle">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Main Content -->
  <div class="flex-1 ml-0 md:ml-64 p-6">
    <div class="max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Laundry Services</h1>
          <p class="text-gray-600">Manage your price list efficiently</p>
        </div>
        <button onclick="openAddModal()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-all shadow-md hover:shadow-lg">
          <i class="fas fa-plus"></i>
          Add New Item
        </button>
      </div>

      <!-- Service Buttons -->
      <div class="flex flex-wrap gap-3 mb-8">
        <button class="service-btn flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md" data-service="wash_fold">
          <i class="fas fa-tshirt"></i>
          Wash + Fold
        </button>
        <button class="service-btn flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-md" data-service="wash_iron">
          <i class="fas fa-iron"></i>
          Wash + Iron
        </button>
        <button class="service-btn flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all shadow-md" data-service="steam_iron">
          <i class="fas fa-fire"></i>
          Steam Iron
        </button>
        <button class="service-btn flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-md" data-service="dry_clean">
          <i class="fas fa-soap"></i>
          Dry Clean
        </button>
      </div>

      <!-- Edit Modal -->
      <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Edit Item</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div id="edit-form-content" class="mb-4">Loading...</div>
          <div class="flex justify-end gap-3">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</button>
          </div>
        </div>
      </div>

      <!-- Dynamic Price Table -->
      <div id="price-table" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
          <div class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-4 py-1">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="space-y-2">
                <div class="h-4 bg-gray-200 rounded"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Item Modal -->
      <div id="addItemModal" class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center hidden z-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
              <i class="fas fa-plus-circle text-indigo-600"></i>
              Add New Item
            </h2>
          </div>
          <form id="addItemForm" class="p-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                <input type="text" name="name" placeholder="e.g. T-Shirt, Jeans" required 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <input type="text" name="category" placeholder="e.g. Clothing, Bedding" required 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Wash + Fold</label>
                  <input type="number" name="price_wash_fold" placeholder="Price" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Wash + Iron</label>
                  <input type="number" name="price_wash_iron" placeholder="Price" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Steam Iron</label>
                  <input type="number" name="price_steam_iron" placeholder="Price" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Dry Clean</label>
                  <input type="number" name="price_dry_clean" placeholder="Price" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
              </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
              <button type="button" onclick="closeAddModal()" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <i class="fas fa-times"></i> Cancel
              </button>
              <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-save"></i> Add Item
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Toggle mobile menu
    document.getElementById('menu-toggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('active');
    });

    // Price list functionality
    document.addEventListener('DOMContentLoaded', () => {
      const buttons = document.querySelectorAll('.service-btn');

      const loadPrices = (service) => {
        document.getElementById('price-table').innerHTML = `
          <div class="p-6">
            <div class="animate-pulse flex space-x-4">
              <div class="flex-1 space-y-4 py-1">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="space-y-2">
                  <div class="h-4 bg-gray-200 rounded"></div>
                  <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                </div>
              </div>
            </div>
          </div>
        `;
        
        fetch(`load_price.php?service=${service}`)
          .then(res => res.text())
          .then(data => {
            document.getElementById('price-table').innerHTML = data;
          });
      };

      // Default load
      loadPrices('wash_fold');

      // Button click handler
      buttons.forEach(button => {
        button.addEventListener('click', () => {
          const service = button.getAttribute('data-service');
          buttons.forEach(btn => {
            btn.classList.remove('from-blue-500', 'to-blue-600');
            btn.classList.add('from-violet-500', 'to-violet-600');
          });
          button.classList.remove('from-violet-500', 'to-violet-600');
          button.classList.add('from-blue-500', 'to-blue-600');
          loadPrices(service);
        });
      });
    });

    // Modal functions
    function closeModal() {
      document.getElementById('edit-modal').classList.add('hidden');
    }

    function openAddModal() {
      document.getElementById('addItemModal').classList.remove('hidden');
    }

    function closeAddModal() {
      document.getElementById('addItemModal').classList.add('hidden');
    }

    // Edit button handler
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('edit-btn')) {
        const id = e.target.getAttribute('data-id');
        const service = e.target.getAttribute('data-service');

        fetch(`edit_item_form.php?id=${id}&service=${service}`)
          .then(res => res.text())
          .then(html => {
            document.getElementById('edit-form-content').innerHTML = html;
            document.getElementById('edit-modal').classList.remove('hidden');
          });
      }

      if (e.target.classList.contains('delete-btn')) {
        const id = e.target.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this item?')) {
          fetch(`delete_item.php?id=${id}`, {
              method: 'POST'
            })
            .then(res => res.text())
            .then(msg => {
              alert(msg);
              location.reload();
            });
        }
      }
    });

    // Form submission
    document.getElementById('addItemForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('add_item.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(response => {
        alert(response);
        closeAddModal();
        location.reload();
      })
      .catch(error => {
        console.error('Error:', error);
      });
    });
  </script>
</body>
</html>