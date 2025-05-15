<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../conf/conne.php';

if (isset($_GET['id'])) {
    $instructor_id = $_GET['id'];

    try {
        // Eliminar los cursos relacionados con el instructor
        $sql = "DELETE FROM cursos WHERE instructor_id = :instructor_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->execute();

        // Eliminar el instructor de la base de datos
        $sql = "DELETE FROM instructores WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $instructor_id);
        $stmt->execute();

        // Redirigir con mensaje de éxito
        echo "<script>
                alert('Instructor eliminado exitosamente');
                window.location.href = '../vistas/mostrar_instructores.php';
              </script>";
        exit;

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-warning mt-3'>No se ha especificado un instructor a eliminar.</div>";
}
?>
