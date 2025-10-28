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
    <style>
      .hidden { display: none; }
    </style>
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
      <div class="form-group apellido" id="apellido-group">
        <input type="text" name="apellido" class="form-input" placeholder="Apellido">
        <label class="form-label">Apellido</label>
      </div>
    </div>

    <div class="form-group email full-width">
      <input 
      type="email" 
      name="email" 
      class="form-input" 
      placeholder="Correo electrónico" 
      required 
      pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
      title="Ingresa una dirección de correo válida (ej: ejemplo@dominio.com)">
      <label class="form-label">Email</label>
    </div>

<div class="form-group telefono-completo full-width">
    
    <select name="pais_codigo" id="pais-codigo" class="form-select-telefono" required>
        <option value="" disabled selected>País</option>
        <option value="+54" data-pais="AR">🇦🇷 Argentina (+54)</option>
        <option value="+56" data-pais="CL">🇨🇱 Chile (+56)</option>
        <option value="+57" data-pais="CO">🇨🇴 Colombia (+57)</option>
        <option value="+52" data-pais="MX">🇲🇽 México (+52)</option>
        <option value="+51" data-pais="PE">🇵🇪 Perú (+51)</option>
        <option value="+598" data-pais="UY">🇺🇾 Uruguay (+598)</option>
        </select>
    
    <div class="input-telefono-compuesto">
        <span id="codigo-display" class="codigo-display">+XX</span>
        
        <input 
            type="tel" 
            name="telefono_numero" 
            id="telefono-numero"
            class="form-input-numero" 
            placeholder="Nro. de ciudad y teléfono" 
            required>
        <input type="hidden" name="telefono_completo" id="telefono-completo">
    </div>

    <label class="form-label">Teléfono</label>
</div>

    <div class="form-row">
      <div class="form-group password">
        <input type="password" name="password" class="form-input" placeholder="Contraseña" required>
        <label class="form-label">Contraseña</label>
      </div>
      <div class="form-group rol">
        <select name="rol" id="rol" class="form-select" required>
          <option value="" disabled selected>Selecciona un rol</option>
          <option value="adoptante">Adoptante</option>
          <option value="refugio">Refugio</option>
          <option value="voluntario">Voluntario</option>
          <option value="hogar_transito">Hogar de tránsito</option>
          <option value="veterinario">Veterinario</option>
          <option value="donante">Donante</option>
           <option value="dador">Dador</option>
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

<script>
  document.getElementById("rol").addEventListener("change", function() {
      let apellidoGroup = document.getElementById("apellido-group");
      if (this.value === "refugio") {
          apellidoGroup.classList.add("hidden");
          apellidoGroup.querySelector("input").value = ""; // limpiar
      } else {
          apellidoGroup.classList.remove("hidden");
      }
  });

    // ----------------------------------------------------
    // Lógica para Teléfono y Código de País
    // ----------------------------------------------------
    const selectPais = document.getElementById("pais-codigo");
    const codigoDisplay = document.getElementById("codigo-display");
    const inputNumero = document.getElementById("telefono-numero");
    const inputCompleto = document.getElementById("telefono-completo");

    // Función para actualizar el código visible y concatenar el número final
    function actualizarTelefono() {
        let codigo = selectPais.value || "+XX";
        let numero = inputNumero.value.trim();

        // 1. Actualiza el display
        codigoDisplay.textContent = codigo;
        
        // 2. Concatena el número completo y lo guarda en el campo oculto para PHP
        if (codigo && numero) {
            inputCompleto.value = codigo + numero;
        } else {
            inputCompleto.value = '';
        }
    }

    // Eventos para actualizar
    selectPais.addEventListener("change", actualizarTelefono);
    inputNumero.addEventListener("input", actualizarTelefono);

    // Inicializa el campo al cargar la página (por si se recarga con error)
    actualizarTelefono();

</script>

</body>
</html>
