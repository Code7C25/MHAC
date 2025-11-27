<?php
/**
 * MODERACIÓN COMPLETA – MHAC
 * Usa Sightengine (mejor IA gratuita actual)
 * Filtra texto e imágenes: sexo, violencia, armas, drogas, odio, grooming, etc.
 */

function se_request($params, $file = null) {
    $API_USER   = "46642407";   
    $API_SECRET = "ARTC3n6FgnXE9UPkn2KscVQtPEWBAGvC"; 

    $params['api_user']   = $API_USER;
    $params['api_secret'] = $API_SECRET;

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.sightengine.com/1.0/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true
    ]);

    // Si es una imagen
    if ($file) {
        $params['media'] = curl_file_create($file);
    }

    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        return ['error' => curl_error($curl)];
    }

    curl_close($curl);

    return json_decode($response, true);
}


/* ---------------------------------------------------------
   TEXTOS: insultos, grooming, odio, amenazas, política extrema
--------------------------------------------------------- */
function moderar_texto($texto) {
    $res = se_request([
        'text'  => $texto,
        'mode'  => 'standard' // modo completo
    ]);

    if (isset($res['error'])) {
        return ['ok' => false, 'error' => "Error al analizar el texto."];
    }

    if (!isset($res['text'])) {
        return ['ok' => true];
    }

    $t = $res['text'];

    // Alto nivel de riesgo
    if (
        $t['profanity']['matches'] > 0 ||
        $t['toxicity']['prob'] > 0.4 ||
        $t['sexual']['prob'] > 0.4 ||
        $t['violence']['prob'] > 0.4 ||
        $t['grooming']['prob'] > 0.1 ||
        $t['hate']['prob'] > 0.3
    ) {
        return [
            'ok' => false,
            'error' => "El texto contiene contenido inapropiado (odio, violencia, insultos o sexual)."
        ];
    }

    return ['ok' => true];
}


/* ---------------------------------------------------------
   IMÁGENES: desnudos, sangre, armas, drogas, violencia
--------------------------------------------------------- */
function moderar_imagen($file_path) {
    if (!file_exists($file_path)) {
        return ['ok' => false, 'error' => "No se encontró la imagen."];
    }

    $res = se_request(
        ['models' => 'nudity-2.0,weapon,violence,drug'],
        $file_path
    );

    if (isset($res['error'])) {
        return ['ok' => false, 'error' => "Error al analizar la imagen."];
    }

    /* ------------ Nudity --------------- */
    if ($res['nudity']['sexual_activity'] > 0.05 ||
        $res['nudity']['sexual_display'] > 0.05 ||
        $res['nudity']['erotica'] > 0.2) {

        return ['ok' => false, 'error' => "La imagen contiene contenido sexual."];
    }

    /* ------------ Violence --------------- */
    if ($res['violence']['prob'] > 0.25) {
        return ['ok' => false, 'error' => "La imagen contiene violencia o sangre."];
    }

    /* ------------ Weapons --------------- */
    if ($res['weapon']['prob'] > 0.2) {
        return ['ok' => false, 'error' => "La imagen contiene armas y no puede subirse."];
    }

    /* ------------ Drugs --------------- */
    if ($res['drug']['prob'] > 0.2) {
        return ['ok' => false, 'error' => "La imagen contiene drogas o parafernalia ilegal."];
    }

    return ['ok' => true];
}
?>
