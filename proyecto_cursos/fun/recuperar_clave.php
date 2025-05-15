<?php
require_once '../conf/conne.php'; // Incluir la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;

    if ($username) {
        // Preparar la consulta para verificar si el usuario existe
        $stmt = $conn->prepare("SELECT username FROM usuarios WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            // Redirigir a preguntas_seguridad.php con el nombre de usuario
            header("Location: ../vistas/preguntas_seguridad.php?username=" . urlencode($username));
            exit();
        } else {
            echo "<script>alert('Usuario no encontrado.'); window.location.href='../index.php';</script>";
        }
    } else {
        echo "<script>alert('El nombre de usuario no puede estar vacío.'); window.location.href='../index.php';</script>";
    }
}
?>
