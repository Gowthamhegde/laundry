<?php
include 'db.php';

$id = $_POST['id'];
$service = $_POST['service'];
$name = $_POST['name'];
$price = $_POST['price'];

$column_map = [
  'wash_fold' => 'price_wash_fold',
  'wash_iron' => 'price_wash_iron',
  'steam_iron' => 'price_steam_iron',
  'dry_clean' => 'price_dry_clean',
];
$price_column = $column_map[$service];

$query = "UPDATE price_list SET name = ?, $price_column = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sdi", $name, $price, $id);

if ($stmt->execute()) {
  echo "<script>alert('Updated successfully.'); window.location.href='price_list.php';</script>";
} else {
  echo "Failed to update.";
}
?>
