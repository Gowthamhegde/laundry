<?php
include 'db.php';

$customer_phone= isset($_GET['customer_phone']) ? intval($_GET['customer_phone']) : 0;

$query = "
    SELECT o.id as order_id, o.pickup_date, o.pickup_time, o.status, o.customer_phone,
           i.item_name, i.quantity
    FROM orders o
    JOIN order_items i ON o.id = i.order_id
    WHERE o.customer_phone = '$customer_phone'
    ORDER BY o.pickup_date DESC, o.pickup_time DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px 16px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .back-link {
            display: block;
            margin: 20px auto;
            text-align: center;
        }
        .back-link a {
            color: #007BFF;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>Order Details for Customer </h2>

<?php if(mysqli_num_rows($result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Pickup Date</th>
                <th>Pickup Time</th>
                <th>Phone</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['pickup_date'] ?></td>
                <td><?= $row['pickup_time'] ?></td>
                <td><?= $row['customer_phone'] ?></td>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['status'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center;">No orders found for this customer.</p>
<?php endif; ?>

<div class="back-link">
    <a href="customers.php">&larr; Back to Customer List</a>
</div>

</body>
</html>
