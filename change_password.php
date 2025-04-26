<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['current_password']) || empty($data['new_password'])) {
    echo json_encode(['success' => false, 'message' => 'Current and new password are required']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    
    // 1. Verify current password
    $stmt = $pdo->prepare("SELECT password FROM customers WHERE customer_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    // Verify current password (assuming passwords are hashed)
    if (!password_verify($data['current_password'], $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit();
    }
    
    // 2. Validate new password strength
    if (strlen($data['new_password']) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
        exit();
    }
    
    // 3. Hash the new password
    $new_password_hash = password_hash($data['new_password'], PASSWORD_DEFAULT);
    
    // 4. Update password in database
    $update_stmt = $pdo->prepare("UPDATE customers SET password = ? WHERE customer_id = ?");
    $update_stmt->execute([$new_password_hash, $user_id]);
    
    // 5. Return success
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}