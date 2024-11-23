<?php

function reviewMessage($text)
{
    $apiKey = "";
    $prompt = "Eres un asistente especializado en corregir y mejorar textos generados por un software de traducción de señas. Tu tarea es:

    1. Corregir errores gramaticales y ortográficos.
    2. Eliminar palabras repetidas innecesariamente.
    3. Mejorar la fluidez y naturalidad del texto sin cambiar su significado esencial.
    4. Mantener un tono neutral y respetuoso.
    
    Reglas importantes:
    - No agregues información que no esté en el texto original.
    - No hagas cambios drásticos en la estructura o el significado del mensaje.
    - Responde ÚNICAMENTE con el texto corregido y mejorado, sin explicaciones ni comentarios adicionales.
    
    Texto a corregir: ";

    // Mensaje para la solicitud
    $messages = array(
        array(
            "role" => "user",
            "content" => array(
                array("type" => "text", "text" => $prompt . " " . $text)
            )
        )
    );

    // Cabeceras para la solicitud HTTP
    $headers = array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    );

    // Payload para la solicitud HTTP
    $data = array(
        "model" => "gpt-4-turbo",
        "messages" => $messages,
        "max_tokens" => 1000
    );

    // Convertir datos a formato JSON
    $dataString = json_encode($data);

    // Inicializar cURL
    $ch = curl_init("https://api.openai.com/v1/chat/completions");

    // Configurar opciones de cURL
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Deshabilitar verificación SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Ejecutar la solicitud HTTP
    $result = curl_exec($ch);

    // Manejar el resultado
    if ($result === false) {
        echo "Error en la solicitud: " . curl_error($ch);
    } else {
        // Decodificar la respuesta JSON
        $resultArray = json_decode($result, true);

        // Acceder al contenido de la respuesta del chatbot
        $chatbotContent = $resultArray['choices'][0]['message']['content'];

        // Devolver solo el contenido de la respuesta del chatbot
        return $chatbotContent;
    }

    // Cerrar la sesión cURL
    curl_close($ch);
}
