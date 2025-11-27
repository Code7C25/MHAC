<?php
require __DIR__ . '/moderacion.php'; // o la ruta correcta a tu moderacion.php

$texto = "Este es un mensaje de prueba.";

if (moderar_texto($texto)) {
    echo "⚠️ El texto fue marcado como inapropiado.";
} else {
    echo "✅ El texto pasó la moderación correctamente.";
}
