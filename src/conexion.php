<?php
$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "mhac_db";

$conn = new mysqli($host, $usuario, $password, $baseDatos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
