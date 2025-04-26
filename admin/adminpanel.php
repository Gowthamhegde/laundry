<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-50 text-gray-800">
  <div class="flex">
    <?php include 'sidebar.php'; ?>

    <div class="flex-1 p-8">
      <h1 class="text-2xl font-bold mb-6"></h1>
      
      <!-- Add dashboard widgets or summary here -->
    </div>
  </div>
  <!--<script>
    $(document).ready(function() {
      // Load dashboard content by default
      loadDashboard();
      
      function loadDashboard() {
        $.ajax({
          url: 'load_dashboard.php', // Create this file to handle dashboard data
          type: 'GET',
          success: function(response) {
            $('#dashboard-content').html(response);
          },
          error: function(xhr, status, error) {
            $('#dashboard-content').html('<p class="text-red-500">Error loading dashboard. Please try again.</p>');
            console.error(error);
          }
        });
      }
    });-->
  </script>
</body>
</html>
