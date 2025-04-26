<?php
// Database connection settings
$servername = "localhost";
$username = "root";      // Default username for XAMPP/WAMP
$password = "";          // Default password is usually empty
$dbname = "project_db";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = $_POST['category'] ?? '1';
$service = $_POST['service'] ?? 'wash_fold';

// Map service to the right column
$service_columns = [
    'wash_fold' => 'price_wash_fold',
    'wash_iron' => 'price_wash_iron',
    'steam_iron' => 'price_steam_iron',
    'dry_clean' => 'price_dry_clean'
];

$price_column = $service_columns[$service] ?? 'price_wash_fold';


$stmt = $conn->prepare("SELECT name, $price_column AS price FROM price_list WHERE category_id = ?");
$stmt->bind_param("i", $category);


$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    $output .= '<div class="price-container">';
    while ($row = $result->fetch_assoc()) {
        $output .= '<div class="price-card">';
        $output .= '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        $output .= '<p>₹' . htmlspecialchars($row['price']) . '</p>';
        $output .= '</div>';
    }
    $output .= '</div>';
} else {
    $output = '<p>No items found.</p>';
}

echo $output;
