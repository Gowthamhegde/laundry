<?php
include 'db.php';

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if ($id) {
  $stmt = $conn->prepare("DELETE FROM price_list WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  echo "Item deleted successfully.";
} else {
  echo "Invalid ID.";
}
?>
