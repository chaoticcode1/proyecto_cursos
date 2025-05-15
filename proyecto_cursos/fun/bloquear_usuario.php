<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../conf/conne.php';

// Verificar si se pasa un id de usuario
if (isset($_GET['id'])) {
    $usuario_id = $_GET['id'];
    $admin_id = $_SESSION['user_id']; // ID del administrador

    try {
        // Obtener el estado actual del usuario y su username
        $sql = "SELECT username, bloqueo FROM usuarios WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $username = $usuario['username']; // Obtener el username del usuario
            // Alternar el estado de bloqueo
            $nuevo_estado = $usuario['bloqueo'] ? 0 : 1; // Si está bloqueado, desbloquear, y viceversa

            // Actualizar el estado de bloqueo del usuario
            $sql_update = "UPDATE usuarios SET bloqueo = :bloqueo WHERE id = :id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':bloqueo', $nuevo_estado);
            $stmt_update->bindParam(':id', $usuario_id);
            $stmt_update->execute();

            // Registrar la acción en la bitácora
            $accion = $nuevo_estado ? "Bloqueó al usuario '$username'" : "Desbloqueó al usuario '$username'";
            $sql_bitacora = "INSERT INTO bitacora (usuario_id, accion) VALUES (:usuario_id, :accion)";
            $stmt_bitacora = $conn->prepare($sql_bitacora);
            $stmt_bitacora->bindParam(':usuario_id', $admin_id);
            $stmt_bitacora->bindParam(':accion', $accion);
            $stmt_bitacora->execute();

            // Redirigir de nuevo a la lista de usuarios
            header("Location: ../vistas/mostrar_usuarios.php");
            exit;
        } else {
            echo "Usuario no encontrado.";
        }
    } catch (PDOException $e) {
        echo "Error al bloquear/desbloquear usuario: " . $e->getMessage();
    }
} else {
    echo "ID de usuario no especificado.";
}
?>
