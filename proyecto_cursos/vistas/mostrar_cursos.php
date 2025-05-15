<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../conf/conne.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Cursos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style_inicio.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <div class="sidebar" id="sidebar">
        <a href="inicio.php">Dashboard</a>
        <div class="dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#registerMenu">Registrar</a>
            <div id="registerMenu" class="collapse">
                <a href="registrar_instructor.php">Instructor</a>
                <a href="registrar_estudiantes.php">Estudiante</a>
                <a href="registrar_usuarios.php">Usuario</a>
                <a href="registrar_cursos.php">Curso</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#showMenu">Mostrar</a>
            <div id="showMenu" class="collapse">
                <a href="mostrar_instructores.php">Instructores</a>
                <a href="mostrar_estudiantes.php">Estudiantes</a>
                <a href="mostrar_usuarios.php">Usuarios</a>
                <a href="mostrar_cursos.php">Cursos</a>
            </div>
        </div>
        <a href="uso.php">Información sobre el Sistema y su Uso</a>
        <a href="mostrar_bitacora.php">Ver bitacora</a>
        <a href="../fun/logout.php" class="text-danger">Cerrar Sesión</a>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Lista de Cursos</h2>
        <?php
        // Obtener la suma de todos los precios totales de los cursos
        try {
            $sql_total = "SELECT SUM(precio_total) AS total_generado FROM cursos";
            $stmt_total = $conn->prepare($sql_total);
            $stmt_total->execute();
            $resultado = $stmt_total->fetch(PDO::FETCH_ASSOC);
            $total_generado = $resultado['total_generado'] ?? 0;

            echo "<div class='alert alert-info mt-3'><strong>Total generado por todos los cursos:</strong> $ {$total_generado} USD</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger mt-3'>Error al calcular el total: {$e->getMessage()}</div>";
        }
        ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Precio Total</th>
                <th>Fecha de inicio</th>
                <th>Precio de finalización</th>
                <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            try {
                $sql = "SELECT c.id, c.titulo, c.descripcion, c.precio_total, c.fecha_inicio, c.fecha_finalizacion FROM cursos c";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($cursos as $curso) {
                    echo "<tr>
                            <td>{$curso['titulo']}</td>
                            <td>{$curso['descripcion']}</td>
                            <td>{$curso['precio_total']} USD</td>
                            <td>{$curso['fecha_inicio']}</td>
                            <td>{$curso['fecha_finalizacion']}</td>
                            <td>
                                <a href='detalles_curso.php?id={$curso['id']}' class='btn btn-primary btn-sm'>Ver Información</a>
                                <a href='editar_curso.php?id={$curso['id']}' class='btn btn-warning btn-sm'>Editar</a>
                            </td>
                          </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='4' class='text-danger'>Error al cargar cursos: {$e->getMessage()}</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>
</body>
</html>
