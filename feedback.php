<?php
session_start();
require_once 'db.php';

// Initialize variables
$error = '';
$success = '';
$orders = [];

// Get customer's orders for dropdown
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id, created_at FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        if (empty($_POST['order_id']) || empty($_POST['rating']) || empty($_POST['comments'])) {
            throw new Exception("All fields are required");
        }

        // Check if this order belongs to the customer
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND customer_id = ?");
            $stmt->execute([$_POST['order_id'], $_SESSION['user_id']]);
            if (!$stmt->fetch()) {
                throw new Exception("Invalid order selected");
            }
        }

        // Insert feedback
        $stmt = $pdo->prepare("
            INSERT INTO feedback (
                order_id,
                customer_id,
                rating,
                comments,
                created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $_POST['order_id'],
            $_SESSION['user_id'] ?? null,
            $_POST['rating'],
            $_POST['comments']
        ]);

        $success = "Thank you for your feedback!";
        $_POST = []; // Clear form

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Techs - Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2575fc;
            --secondary: #6a11cb;
            --accent: #ff5e62;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }
        
        .feedback-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 40px auto;
        }
        
        .rating-stars {
            font-size: 2rem;
            color: #ffc107;
            cursor: pointer;
        }
        
        .rating-stars .star {
            transition: all 0.2s ease;
        }
        
        .rating-stars .star:hover,
        .rating-stars .star.active {
            transform: scale(1.2);
        }
        
        textarea {
            min-height: 150px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="feedback-container">
            <h2 class="mb-4"><i class="fas fa-comment-alt me-2"></i> Share Your Feedback</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="mb-4">
                    <label for="order_id" class="form-label">Select Order</label>
                    <select class="form-select" id="order_id" name="order_id" required>
                        <option value="">-- Select an order --</option>
                        <?php foreach ($orders as $order): ?>
                            <option value="<?= $order['id'] ?>" <?= isset($_POST['order_id']) && $_POST['order_id'] == $order['id'] ? 'selected' : '' ?>>
                                Order #<?= $order['id'] ?> - <?= date('M j, Y', strtotime($order['created_at'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Rating</label>
                    <div class="rating-stars mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= isset($_POST['rating']) && $_POST['rating'] >= $i ? 'active' : '' ?>" 
                                  data-value="<?= $i ?>">
                                <i class="fas fa-star"></i>
                            </span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="<?= $_POST['rating'] ?? '' ?>">
                </div>
                
                <div class="mb-4">
                    <label for="comments" class="form-label">Comments</label>
                    <textarea class="form-control" id="comments" name="comments" required><?= $_POST['comments'] ?? '' ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i> Submit Feedback
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Star rating functionality
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                document.getElementById('rating').value = value;
                
                // Update star display
                document.querySelectorAll('.star').forEach((s, index) => {
                    if (index < value) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    </script>
</body>
</html>