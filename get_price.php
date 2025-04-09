<?php
require 'db.php';

$sql = "SELECT price_list.id, category.category_name AS category, price_list.name, price_list.price
        FROM price_list
        JOIN category ON price_list.category_id = category.category_id
        ORDER BY category.category_name, price_list.name";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($data);
?>
