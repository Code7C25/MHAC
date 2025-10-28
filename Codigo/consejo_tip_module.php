<?php

$sql_tip = "SELECT contenido FROM consejos_comunidad WHERE verificado = TRUE ORDER BY RAND() LIMIT 1";
$result_tip = @$conn->query($sql_tip); 
$consejo_tip = "";

if ($result_tip && $result_tip->num_rows > 0) {
    $row = $result_tip->fetch_assoc();
    $consejo_tip = htmlspecialchars($row['contenido']);
} else {
    // Consejo de reserva si la tabla está vacía o hay error
    $consejo_tip = "¡Conéctate! Revisa nuestros consejos de adopción responsable.";
}

echo '
<aside id="consejoPopup" class="consejo-tip-box">
    <span class="cerrar-tip" onclick="cerrarConsejo()">✕</span> <div class="tip-header">
        <div class="tip-icon">✨</div>
        <p class="tip-title">Tip de la Comunidad</p>
    </div>
    <div class="tip-body">
        <p class="tip-content">' . $consejo_tip . '</p>
        <a href="info.php" class="tip-link">Ver más consejos →</a>
    </div>
</aside>
';
?>