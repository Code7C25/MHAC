<?php
session_start();
require_once 'conexion.php';
if (isset($_SESSION['usuario_id'], $_GET['id'])) {
    $pid = (int)$_GET['id'];
    $uid = $_SESSION['usuario_id'];
    $conn->query("DELETE FROM posts WHERE id=$pid AND usuario_id=$uid");
}
header("Location: comunidad.php");
exit;
