<?php
session_start();

// Database config
$host = "localhost";
$db = "project_db";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Get user input
$phone = $_POST["phone"] ?? '';
$password = $_POST["password"] ?? '';



// Validate input
if (empty($phone) || empty($password)) {
    $_SESSION['login_error'] = "Phone and password are required.";
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM customers WHERE phone = :phone";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION["user_id"] = $user["customer_id"];
// Check password
if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user_id"] = $user["customer_id"];
    $_SESSION["phone"] = $user["phone"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["logged_in"] = true;
    header("Location: place_order.php");
    exit();
} else {
    $_SESSION['login_error'] = "Invalid phone number or password!";
    header("Location: login.php");
    exit();
}
