<?php
require_once '../conf/conne.php'; // Conexión a la base de datos

$username = trim($_POST['username']);
$pregunta_seleccionada = $_POST['pregunta'];
$respuesta = isset($_POST['respuesta']) ? trim($_POST['respuesta']) : null;

if (empty($username) || empty($pregunta_seleccionada) || empty($respuesta)) {
    echo "<script>alert('Por favor, completa todos los campos.'); window.history.back();</script>";
    exit();
}

$respuesta_hash = hash('sha256', $respuesta);

try {
    // Preparamos la consulta para obtener las preguntas y respuestas de seguridad del usuario
    $stmt = $conn->prepare("SELECT pregunta_seguridad_1, respuesta_seguridad_1_hash, pregunta_seguridad_2, respuesta_seguridad_2_hash FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Validamos si la respuesta es correcta en al menos una de las dos preguntas
        $respuesta_correcta = false;

        // Comprobamos si la respuesta de la pregunta seleccionada es correcta
        if (($pregunta_seleccionada === $user['pregunta_seguridad_1'] && $respuesta_hash === $user['respuesta_seguridad_1_hash']) ||
            ($pregunta_seleccionada === $user['pregunta_seguridad_2'] && $respuesta_hash === $user['respuesta_seguridad_2_hash'])) {
            $respuesta_correcta = true;
        }

        // Si la respuesta es correcta, dejamos al usuario cambiar la contraseña
        if ($respuesta_correcta) {
            echo "<script>alert('Respuesta correcta. Procede a cambiar tu contraseña.');window.location.href='../vistas/cambiar_clave.php?username=" . urlencode($username) . "';</script>";
        } else {
            // Si la respuesta es incorrecta, mostramos un mensaje de error
            echo "<script>alert('Respuesta incorrecta.'); window.history.back();</script>";
        }
    } else {
        // Si el usuario no existe en la base de datos
        echo "<script>alert('Usuario no encontrado.'); window.history.back();</script>";
    }
} catch (Exception $e) {
    // Si ocurre un error en la ejecución
    echo "<script>alert('Error al procesar la solicitud. Intenta de nuevo.'); window.history.back();</script>";
}
?>
