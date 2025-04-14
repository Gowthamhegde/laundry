<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // You can add email sending functionality here
    // For now, let's redirect back with a success message
    header("Location: contact.php?status=success");
    exit();
}
?>