<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || empty($_POST['post_id'])) {
    echo json_encode(['ok'=>false]);
    exit;
}

$uid  = $_SESSION['usuario_id'];
$pid  = (int)$_POST['post_id'];

// si existe → eliminar; si no → insertar
$stmt = $conn->prepare("SELECT 1 FROM likes WHERE post_id=? AND usuario_id=?");
$stmt->bind_param("ii", $pid, $uid);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;

if ($exists) {
    $stmt = $conn->prepare("DELETE FROM likes WHERE post_id=? AND usuario_id=?");
    $stmt->bind_param("ii", $pid, $uid);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO likes (post_id, usuario_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $pid, $uid);
    $stmt->execute();
}

// contar de nuevo
$res = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE post_id=$pid");
$total = $res->fetch_assoc()['total'] ?? 0;

echo json_encode(['ok'=>true, 'total'=>$total, 'liked'=>!$exists]);
