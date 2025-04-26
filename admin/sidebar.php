<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Navigation</title>
  <!-- Single CDN for Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .sidebar {
      transition: all 0.3s ease;
    }
    .nav-item.active {
      background-color: #3b82f6;
      color: white;
    }
    .nav-item.active:hover {
      background-color: #2563eb;
    }
    .mobile-menu-button {
      display: none;
    }
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: -100%;
        z-index: 50;
        height: 100vh;
      }
      .sidebar.active {
        left: 0;
      }
      .mobile-menu-button {
        display: block;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 60;
      }
      .content-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 40;
      }
      .content-overlay.active {
        display: block;
      }
    }
    @media (max-width: 768px) {
  .ml-64 {
    margin-left: 0;
  }
}
  </style>
</head>
<body>
  <!-- Mobile Menu Button -->
  <button class="mobile-menu-button p-2 rounded-lg bg-blue-500 text-white shadow-lg" id="menuToggle">
    <i class="fas fa-bars text-xl"></i>
  </button>

  <!-- Content Overlay (for mobile) -->
  <div class="content-overlay" id="contentOverlay"></div>

  <!-- Sidebar -->
  <div class="sidebar w-64 bg-gradient-to-b from-blue-50 to-blue-100 h-screen shadow-lg flex flex-col justify-between fixed" id="sidebar">
    <div>
      <!-- Brand/Logo Section -->
      <div class="px-6 py-4 border-b border-blue-200 flex items-center space-x-3">
        <div class="bg-blue-500 text-white p-2 rounded-lg">
          <i class="fas fa-user-tie text-xl"></i>
        </div>
        <div>
          <div class="text-xl font-bold text-blue-800">Admin Panel</div>
          <div class="text-xs text-blue-500">Administrator</div>
        </div>
      </div>

      <!-- Main Navigation -->
      <nav class="px-2 py-4 space-y-1">
        <a href="dashboard.php" class="nav-item flex items-center rounded-lg px-4 py-3 transition-all hover:bg-blue-200 text-blue-800">
          <i class="fas fa-home mr-3 w-5 text-center"></i>
          <span>Dashboard</span>
        </a>
        <a href="orders.php" class="nav-item flex items-center rounded-lg px-4 py-3 transition-all hover:bg-blue-200 text-blue-800">
          <i class="fas fa-shopping-basket mr-3 w-5 text-center"></i>
          <span>Orders</span>
        </a>
        <a href="customers.php" class="nav-item flex items-center rounded-lg px-4 py-3 transition-all hover:bg-blue-200 text-blue-800">
          <i class="fas fa-users mr-3 w-5 text-center"></i>
          <span>Customers</span>
        </a>
        <a href="price_list.php" class="nav-item flex items-center rounded-lg px-4 py-3 transition-all hover:bg-blue-200 text-blue-800">
          <i class="fas fa-tags mr-3 w-5 text-center"></i>
          <span>Pricelist</span>
        </a>
        <a href="reports.php" class="nav-item flex items-center rounded-lg px-4 py-3 transition-all hover:bg-blue-200 text-blue-800">
          <i class="fas fa-chart-bar mr-3 w-5 text-center"></i>
          <span>Reports</span>
        </a>
        <a href="settings.php" class="nav-item flex items-center rounded-lg px-4 py-3 transition-all hover:bg-blue-200 text-blue-800">
          <i class="fas fa-cog mr-3 w-5 text-center"></i>
          <span>Settings</span>
        </a>
      </nav>
    </div>

    <!-- Bottom Section -->
    <div class="p-4 border-t border-blue-200">
      <div class="flex items-center px-3 py-2 text-blue-800">
        <div class="relative">
          <img src="https://ui-avatars.com/api/?name=Admin&background=3b82f6&color=fff" 
               class="w-8 h-8 rounded-full mr-3" alt="Admin">
          <span class="absolute bottom-0 right-2 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
        </div>
        <div>
          <div class="font-medium">Admin User</div>
          <div class="text-xs text-blue-500">laundrytechadmin@gmail.com</div>
        </div>
      </div>
      <a href="logout.php" class="flex items-center rounded-lg px-4 py-3 transition-all hover:bg-red-100 text-red-500 mt-2">
        <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <script>
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const contentOverlay = document.getElementById('contentOverlay');

    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      contentOverlay.classList.toggle('active');
    });

    contentOverlay.addEventListener('click', () => {
      sidebar.classList.remove('active');
      contentOverlay.classList.remove('active');
    });

    // Set active nav item based on current page
    document.addEventListener('DOMContentLoaded', () => {
      const currentPage = window.location.pathname.split('/').pop();
      const navItems = document.querySelectorAll('.nav-item');
      
      navItems.forEach(item => {
        if (item.getAttribute('href') === currentPage) {
          item.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>