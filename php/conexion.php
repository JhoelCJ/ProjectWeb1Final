<?php
$servername = "localhost";
$username = "admin";
$password = "admin";
$database = "proyecto_29797";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión");
}
?>
