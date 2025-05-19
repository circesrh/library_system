<?php
$servername = "localhost:8111";
$username = "root";
$password = ""; // leave blank if no password
$database = "library_db"; // or your actual database

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
