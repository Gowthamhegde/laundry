<?php
include 'db.php';

$service = $_GET['service'] ?? 'wash_fold';

$column_map = [
  'wash_fold' => 'price_wash_fold',
  'wash_iron' => 'price_wash_iron',
  'steam_iron' => 'price_steam_iron',
  'dry_clean' => 'price_dry_clean',
];

$price_column = $column_map[$service] ?? 'price_wash_fold';

$query = "SELECT pl.id, pl.name, c.category_name AS category, pl.$price_column AS price
          FROM price_list pl
          LEFT JOIN category c ON pl.category_id = c.category_id
          ORDER BY pl.id ASC";
$result = mysqli_query($conn, $query);

// Table Header
echo '<table class="w-full text-left border-collapse">';
echo '<thead><tr>
  <th class="border-b-2 p-2">ID</th>
  <th class="border-b-2 p-2">Item</th>
  <th class="border-b-2 p-2">Category</th>
  <th class="border-b-2 p-2">Price</th>
  <th class="border-b-2 p-2">Actions</th>
</tr></thead><tbody>';

// Table Rows
while ($row = mysqli_fetch_assoc($result)) {
  echo '<tr>';
  echo '<td class="p-2 border-b">'. $row['id'] .'</td>';
  echo '<td class="p-2 border-b">'. htmlspecialchars($row['name']) .'</td>';
  echo '<td class="p-2 border-b">'. htmlspecialchars($row['category']) .'</td>';
  echo '<td class="p-2 border-b">₹'. htmlspecialchars($row['price']) .'</td>';
  echo '<td class="p-2 border-b">
          <button class="edit-btn text-blue-600" data-id="'.$row['id'].'" data-service="'.$service.'">Edit</button>
          |
          <button class="delete-btn text-red-600" data-id="'.$row['id'].'">Delete</button>
        </td>';
  echo '</tr>';
}
echo '</tbody></table>';
