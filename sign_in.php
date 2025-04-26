<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "project_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate POST data exists
if (!isset($_POST["username"], $_POST["email"], $_POST["phone"], $_POST["password"], $_POST["confirmPassword"])) {
   die("All fields are required");
}

$name = $_POST["username"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$password = $_POST["password"];
$confirm_password = $_POST["confirmPassword"];

function validateUserInput($conn, $name, $email, $phone, $password, $confirm_password)
{
    // Trim inputs
    $name = trim($name);
    $email = trim($email);
    $phone = trim($phone);
    $password = trim($password);
    $confirm_password = trim($confirm_password);

    // Validate Name
    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) {
        return "Invalid name. Only letters and spaces are allowed.";
    }

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }

    // Validate Phone
    if (empty($phone) || !preg_match("/^\d{10}$/", $phone)) {
        return "Invalid phone number. It must be 10 digits long.";
    }

    // Check for existing phone number (using prepared statement)
    $stmt = $conn->prepare("SELECT * FROM customers WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return "Phone number already exists";
    }

    // Validate Password
    if (empty($password) ||!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password)) {
        return "Password must be at least 8 characters long, containing at least one letter and one number.";
    }

    // Validate Confirm Password
    if ($password !== $confirm_password) {
        return "Passwords do not match.";
    }

    return true;
}

$res = validateUserInput($conn, $name, $email, $phone, $password, $confirm_password);

if ($res === true) {
    // Secure password hashing
    $pass = password_hash($password, PASSWORD_DEFAULT);

    // Prepared statement for insertion
    $stmt = $conn->prepare("INSERT INTO customers (name, phone, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $email, $pass);

    if ($stmt->execute()) {
        echo "Registration successful";
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . htmlspecialchars($conn->error);
    }
} else {
    echo "Error: " . htmlspecialchars($res);
}

$conn->close();
?>
