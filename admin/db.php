<?php
$host = "localhost";
$user = "root";
$password = ""; // or your password
$db = "project_db"; // your actual DB name

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
