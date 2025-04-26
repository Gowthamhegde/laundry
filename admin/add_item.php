<?php
include 'db.php';

$name = $_POST['name'];
$category_id = $_POST['category'];
$price_wash_fold = $_POST['price_wash_fold'] ?? 0;
$price_wash_iron = $_POST['price_wash_iron'] ?? 0;
$price_steam_iron = $_POST['price_steam_iron'] ?? 0;
$price_dry_clean = $_POST['price_dry_clean'] ?? 0;

$stmt = $conn->prepare("INSERT INTO price_list (name, category_id, price_wash_fold, price_wash_iron, price_steam_iron, price_dry_clean) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sidddd", $name, $category_id, $price_wash_fold, $price_wash_iron, $price_steam_iron, $price_dry_clean);

if ($stmt->execute()) {
  echo "item added successfully";
} else {
  echo "Error: " . $conn->error;
}
?>
