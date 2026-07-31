<?php
session_start(); // লগিন সেশন মনে রাখার জন্য এই লাইনটি সবচেয়ে জরুরি!

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>