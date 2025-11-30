<?php
session_start();

// Si ya está logueado, redirige
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/login.css">

<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>
</head>
<body>

<div class="login-container">
  <div class="login-header">
    <div class="login-logo">🐾</div>
    <h1 class="login-title">Bienvenido</h1>
    <p class="login-subtitle">Iniciá sesión en MHAC</p>
  </div>

  <?php if (isset($_GET['error'])): ?>
    <div class="error-message">
      <?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

  <form action="procesar_login.php" method="post" class="login-form">
    <div class="form-group email">
      <input type="email" name="email" class="form-input" placeholder="Correo electrónico" required>
      <label class="form-label" for="email">Email</label>
    </div>

    <div class="form-group password">
      <input type="password" name="password" class="form-input" placeholder="Contraseña" required>
      <label class="form-label" for="password">Contraseña</label>
    </div>

    <button type="submit" class="login-btn">Ingresar</button>
  </form>

  <div class="login-footer">
    <p>¿No tenés cuenta? 
      <a href="registro.php" class="register-link">Registrate acá</a>
    </p>
  </div>
</div>

</body>
</html>
