<?php
include 'config.php'; // DB connection using $pdo

$search = $_POST['query'] ?? '';

$stmt = $pdo->prepare("SELECT p.*, c.category_name 
                       FROM price_list p 
                       JOIN category c ON p.category_id = c.category_id 
                       WHERE p.name LIKE CONCAT('%', :query, '%') 
                       ORDER BY c.category_name, p.name");
$stmt->execute(['query' => $search]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $item):
?>
  <tr class="order-row">
    <td><?= htmlspecialchars($item['category_name']) ?></td>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td class="item-price"><?= $item['price'] ?></td>
    <td>
      <input type="number" class="item-qty" name="qty[<?= $item['id'] ?>]" min="0" value="0">
    </td>
  </tr>
<?php endforeach; ?>
