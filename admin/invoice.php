<?php
include 'config.php';
include 'auth.php';

if (!isset($_GET['id'])) {
  echo "Order ID missing!";
  exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
  echo "Order not found!";
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Invoice #<?= $order['id'] ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .invoice-container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      font-family: Arial, sans-serif;
    }

    .invoice-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .invoice-details {
      margin-bottom: 30px;
    }

    .invoice-details p {
      margin: 5px 0;
    }

    .invoice-items {
      width: 100%;
      border-collapse: collapse;
    }

    .invoice-items th, .invoice-items td {
      padding: 10px;
      border: 1px solid #ddd;
    }

    .print-btn {
      display: block;
      margin: 30px auto 0;
      padding: 10px 20px;
      background: #007BFF;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    @media print {
      .print-btn {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="invoice-container">
    <div class="invoice-header">
      <h2>Laundry Service Invoice</h2>
      <p>Order #<?= $order['id'] ?></p>
    </div>

    <div class="invoice-details">
      <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
      <p><strong>Phone:</strong> <?= $order['phone'] ?></p>
      <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
      <p><strong>Placed At:</strong> <?= $order['created_at'] ?></p>
    </div>

    <table class="invoice-items">
      <thead>
        <tr>
          <th>Item</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $items = explode("\n", trim($order['items']));
          foreach ($items as $item):
        ?>
        <tr><td><?= htmlspecialchars($item) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h3 style="text-align:right;">Total: ₹<?= $order['total_price'] ?></h3>

    <button class="print-btn" onclick="window.print()">🖨️ Print Invoice</button>
  </div>
</body>
</html>
