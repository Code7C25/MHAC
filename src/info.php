<?php
session_start();
require_once 'conexion.php';
include 'consejo_tip_module.php';

$sql_consejo_aleatorio = "SELECT contenido FROM consejos_comunidad WHERE verificado = TRUE ORDER BY RAND() LIMIT 1";
$result_consejo_aleatorio = $conn->query($sql_consejo_aleatorio);
$consejo_aleatorio = "";

if ($result_consejo_aleatorio && $result_consejo_aleatorio->num_rows > 0) {
    $row = $result_consejo_aleatorio->fetch_assoc();
    $consejo_aleatorio = "Consejo de la Comunidad: " . htmlspecialchars($row['contenido']);
} else {
    $consejo_aleatorio = "¡Sé el primero en subir un gran consejo! Tu aporte es vital para la comunidad.";
}

// Obtener la categoría seleccionada por el usuario (si existe)
$categoria_seleccionada = $_GET['cat'] ?? '';

// La base de la consulta SQL
$sql = "SELECT c.id, c.titulo, c.contenido, c.fecha, c.autor, c.categoria, u.nombre, u.apellido, u.rol, c.autor_id
        FROM cuidados c
        JOIN usuarios u ON c.autor_id = u.id";

// Agregar la cláusula WHERE si se seleccionó una categoría
if (!empty($categoria_seleccionada)) {
    // Escapar la variable para seguridad (aunque PDO es más seguro, en mysqli es un buen hábito)
    $cat_safe = $conn->real_escape_string($categoria_seleccionada);
    $sql .= " WHERE c.categoria = '$cat_safe'";
}

// Terminar la consulta con el ordenamiento
$sql .= " ORDER BY c.fecha DESC";

$result = $conn->query($sql);

// Lista de categorías para el filtro
$categorias = [
    "Perros: Salud", 
    "Perros: Comportamiento", 
    "Gatos: Salud", 
    "Gatos: Comportamiento",
    "Adopción Responsable",
    "Primeros Auxilios",
    "Varios/Legal"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/info.css">
    <link rel="stylesheet" href="css/consejo_module.css">
</head>

<body>
<a href="#" onclick="history.back(); return false;" class="volver-inicio">
    <span>←</span> Volver
</a>

<header>
  <h1>Cuidados y Consejos para Animales</h1>
</header>

<main>
    <section class="consejo-rapido-box">
        <p class="consejo-rapido-text"><?= $consejo_aleatorio ?></p>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="sugerir_consejo.php" class="btn-sugerir-rapido">Sugerir →</a>
        <?php endif; ?>
    </section>

    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['veterinario','refugio'])): ?>
      <a href="agregar_info.php" class="btn-nuevo">Agregar cuidado</a>
    <?php endif; ?>

    <section class="filtro-categorias">
        <h3>Filtrar por:</h3>
        <a href="info.php" class="<?= empty($categoria_seleccionada) ? 'active' : '' ?>">
            Todos los temas
        </a>
        
        <?php foreach ($categorias as $cat): ?>
            <a href="info.php?cat=<?= urlencode($cat) ?>" 
               class="<?= $categoria_seleccionada === $cat ? 'active' : '' ?>">
                <?= htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>
    </section>

    <section class="busqueda-externa-simulada">
        <h3>¿No encuentras lo que buscas? Usa nuestra Búsqueda Asistida:</h3>
        <form action="https://www.google.com/search" method="get" target="_blank">
            <input type="hidden" name="sitesearch" value="humaneworld.org"> 
            <input type="text" name="q" placeholder="Ej: 'Mi perro vomita espuma' o 'dieta de gatos'..." required>
            <button type="submit">Buscar en Fuentes Verificadas</button>
            <p class="nota-busqueda">La búsqueda se realiza en fuentes de información veterinaria y de bienestar animal, garantizando la calidad del contenido.</p>
        </form>
    </section>

    <section class="cuidados-lista">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($c = $result->fetch_assoc()): ?>
            <article class="cuidado-card">
              <h2><?= htmlspecialchars($c['titulo']) ?></h2>
              <p class="categoria-tag"><?= htmlspecialchars($c['categoria']) ?></p>
              
              <p><?= substr($c['contenido'], 0, 200) ?>...</p>
              <a href="detalle_cuidado.php?id=<?= $c['id'] ?>" class="btn-leer-mas">Leer más →</a>

              <div class="autor-info">
                <small>
                  Publicado por <?= htmlspecialchars($c['nombre'].' '.$c['apellido']) ?> 
                  (<?= ucfirst($c['rol']) ?>) 
                  <?php if (!empty($c['autor'])): ?>
                    | Créditos: <?= htmlspecialchars($c['autor']) ?>
                  <?php endif; ?>
                  <?= date("d/m/Y", strtotime($c['fecha'])) ?>
                </small>
              </div>

              <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $c['autor_id']): ?>
                <a href="editar_cuidado.php?id=<?= $c['id'] ?>" class="btn-editar">Editar</a>
              <?php endif; ?>
            </article>
          <?php endwhile; ?>
        <?php else: ?>
            <p>No hay cuidados publicados bajo la categoría <?= htmlspecialchars($categoria_seleccionada) ?> todavía.</p>
        <?php endif; ?>
    </section>

    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['veterinario','refugio'])): ?>
      <a href="verificar_consejos.php" class="btn-verificacion">Ir a Moderación de Consejos</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['veterinario','refugio'])): ?>
      <a href="agregar_info.php" class="btn-nuevo">Agregar artículo principal</a>
    <?php endif; ?>
</main>

  <script>
    // 1. Función para cerrar el consejo (llamada por el botón '✕')
    function cerrarConsejo() {
        const popup = document.getElementById("consejoPopup");
        if (popup) {
            popup.style.display = 'none'; // Oculta el elemento
        }
    }

    // 2. Lógica para hacer aparecer el popup al cargar la página
    window.addEventListener("DOMContentLoaded", () => {
        const popup = document.getElementById("consejoPopup");
        // Agrega la clase 'visible' para activar la animación CSS
        if (popup) {
            // Un pequeño retraso para que la animación se vea mejor
            setTimeout(() => {
                popup.classList.add('visible');
            }, 100); 

            // Opcional: desaparecerlo automáticamente después de 15 segundos
            setTimeout(() => {
                popup.style.display = 'none';
            }, 15000); 
        }
    });
</script>
</body>
</html>