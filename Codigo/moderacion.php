<?php
require __DIR__ . '/vendor/autoload.php';

function moderar_texto($texto) {

    // -------------------------
    // 1) FILTRO DE MALAS PALABRAS MANUAL
    // -------------------------
    $malas_palabras = [
        'puta', 'puto', 'mierda', 'pelotudo', 'pelotuda',
        'concha', 'pija', 'pito', 'garchar', 'coger',
        'imbecil', 'forro', 'forra', 'boludo', 'boluda',
        'trolo', 'trola', 'orto', 'idiota', 'mogolico', 'mogólica'
    ];

    $texto_limpio = strtolower($texto);

    foreach ($malas_palabras as $mala) {
        if (strpos($texto_limpio, $mala) !== false) {
            return true; // Bloquear inmediatamente
        }
    }

    // -------------------------
    // 2) OPENAI MODERATION
    // -------------------------

    // ⚠️ PONÉ TU API KEY REAL ACÁ
    $apiKey = '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';

    // Cliente OpenAI (versión que usa tu Composer)
    $client = OpenAI::client($apiKey);

    try {
        // Primer intento de moderación
        $response = $client->moderations()->create([
            'model' => 'omni-moderation-latest',
            'input' => $texto
        ]);

        return $response->results[0]['flagged'] ?? false;

    } catch (Exception $e) {

        // Si te pasaste del límite → esperar un poquito y reintentar una vez
        if (strpos($e->getMessage(), 'Too Many Requests') !== false) {

            usleep(300000); // 300ms

            try {
                $response = $client->moderations()->create([
                    'model' => 'omni-moderation-latest',
                    'input' => $texto
                ]);

                return $response->results[0]['flagged'] ?? false;

            } catch (Exception $e2) {
                // Si sigue fallando → no bloquear el sitio
                return false;
            }
        }

        // Cualquier otro error → no bloquee el sitio
        return false;
    }
}
