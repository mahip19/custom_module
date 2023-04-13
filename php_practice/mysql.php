<?php 

$servername = "localhost";
$username = "user";
$password = "user";

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "mysql database->{$username} Connected successfully";

$sql = "CREATE DATABASE myDB";
if (mysqli_query($conn, $sql)) echo "Database created";
else echo "database creation error";




?>