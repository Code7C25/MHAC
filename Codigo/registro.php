<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - MHAC</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>

<div class="registro-container">
  <div class="registro-header">
    <div class="registro-logo">🐾</div>
    <h1 class="registro-title">Regístrate</h1>
    <p class="registro-subtitle">¡Únete a MHAC y ayuda a más peluditos!</p>
  </div>

  <?php if (isset($_SESSION['registro_error'])): ?>
    <div class="error-message">
      <?= htmlspecialchars($_SESSION['registro_error']) ?>
    </div>
    <?php unset($_SESSION['registro_error']); ?>
  <?php endif; ?>

  <form action="procesar_registro.php" method="POST" class="registro-form">

    <div class="form-row">
      <div class="form-group nombre">
        <input type="text" name="nombre" class="form-input" placeholder="Nombre" required>
        <label class="form-label">Nombre</label>
      </div>
      <div class="form-group apellido">
        <input type="text" name="apellido" class="form-input" placeholder="Apellido" required>
        <label class="form-label">Apellido</label>
      </div>
    </div>

    <div class="form-group email full-width">
      <input type="email" name="email" class="form-input" placeholder="Correo electrónico" required>
      <label class="form-label">Email</label>
    </div>

    <div class="form-group telefono full-width">
      <input type="text" name="telefono" class="form-input" placeholder="Teléfono (opcional)">
      <label class="form-label">Teléfono</label>
    </div>

    <div class="form-row">
      <div class="form-group password">
        <input type="password" name="password" class="form-input" placeholder="Contraseña" required>
        <label class="form-label">Contraseña</label>
      </div>
      <div class="form-group rol">
        <select name="rol" class="form-select" required>
          <option value="" disabled selected>Selecciona un rol</option>
          <option value="adoptante">Adoptante</option>
          <option value="refugio">Refugio</option>
          <option value="voluntario">Voluntario</option>
          <option value="hogar_transito">Hogar de tránsito</option>
          <option value="veterinario">Veterinario</option>
          <option value="donante">Donante</option>
        </select>
        <label class="form-label">Rol</label>
      </div>
    </div>

    <button type="submit" class="registro-btn">Registrarse</button>
  </form>

  <div class="registro-footer">
    <p>¿Ya tenés cuenta? <a href="login.php" class="login-link">Iniciá sesión</a></p>
  </div>
</div>

</body>
</html>
