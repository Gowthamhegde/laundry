<?php
session_start();
$host = 'localhost';
$db   = 'project_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
?>