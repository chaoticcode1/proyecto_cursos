<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $estudiante_id = $_GET['id'];

    require_once '../conf/conne.php';

    try {
        // Eliminar el estudiante de la base de datos
        $sql = "DELETE FROM estudiantes WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $estudiante_id);
        $stmt->execute();

        // Redirigir con mensaje de éxito
        echo "<script>
                alert('Estudiante eliminado exitosamente');
                window.location.href = '../vistas/mostrar_estudiantes.php';
              </script>";
        exit;

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>No se ha especificado un estudiante a eliminar.</div>";
}
?>
