<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MHAC - Mis Huellitas a Casa</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <h1 class="titulo-animado">MHAC - Mis Huellitas a Casa</h1>
    <p class="subtitulo">Conectamos corazones con patitas 🐾</p>

    <div class="user-session">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <span class="bienvenida">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>

            <div class="menu-usuario">
                <button class="menu-toggle">☰</button>
                <div class="menu-contenido">
                    <a href="perfil.php">Mi perfil</a>
                    <a href="nuevo_post.php">Nuevo post</a>
                    <a href="#">Mis publicaciones</a>
                    <a href="#">Configuración</a>
                    <form action="logout.php" method="post">
                        <button type="submit" class="logout-btn">Cerrar sesión</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <a href="login.php" class="boton">Iniciar sesión</a>
            <a href="registro.php" class="boton">Registrarse</a>
        <?php endif; ?>
    </div>

    <nav class="menu-principal">
        <a class="boton" href="#">Adopción</a>
        <a class="boton" href="#">Refugios</a>
        <a class="boton" href="#">Campañas</a>
    </nav>

    <?php if (!isset($_SESSION['usuario_id'])): ?>
        <section class="alerta-inicio">
            <h2>¿Todavía no iniciaste sesión?</h2>
            <a href="login.php" class="cta">Iniciar sesión</a>
        </section>
    <?php endif; ?>

    <main>
        <section class="destacado">
            <h2>¿Buscando un nuevo amigo?</h2>
            <p>Explorá las historias de cientos de animalitos que están esperando por vos.</p>
            <a href="#" class="cta">Ver mascotas</a>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <h3>MHAC - Mis Huellitas a Casa</h3>
                <p>Un puente entre peluditos y hogares llenos de amor.</p>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 MHAC. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

</body>
</html>
