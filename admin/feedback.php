<?php include 'config.php'; include 'auth.php';
$feedbacks = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC")->fetchAll();
?>
<h2>Customer Feedback</h2>
<?php foreach ($feedbacks as $fb): ?>
  <div>
    <strong><?= htmlspecialchars($fb['name']) ?></strong> (<?= $fb['email'] ?>)<br>
    <p><?= nl2br(htmlspecialchars($fb['message'])) ?></p>
    <hr>
  </div>
<?php endforeach; ?>