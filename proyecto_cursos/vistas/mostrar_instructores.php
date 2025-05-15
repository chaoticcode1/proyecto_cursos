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
    <title>Lista de Instructores</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style_inicio.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Estás seguro que deseas eliminar a este instructor?")) {
                window.location.href = '../fun/eliminar_instructor.php?id=' + id;
            }
        }
    </script>
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
        <h2 class="mb-4">Lista de Instructores</h2>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cédula</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Fecha de Nacimiento</th>
                <th>Edad</th>
                <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            try {
                $sql = "SELECT * FROM instructores";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($instructores as $instructor) {
                    echo "<tr>
                            <td>{$instructor['nombre']}</td>
                            <td>{$instructor['apellido']}</td>
                            <td>{$instructor['cedula']}</td>
                            <td>{$instructor['correo']}</td>
                            <td>{$instructor['telefono']}</td>
                            <td>{$instructor['fecha_nacimiento']}</td>
                            <td>{$instructor['edad']}</td>
                            <td>
                                <a href='editar_instructor.php?id={$instructor['id']}' class='btn btn-warning btn-sm'>Editar</a>
                                <button onclick='confirmarEliminacion({$instructor['id']})' class='btn btn-danger btn-sm'>Eliminar</button>
                            </td>
                          </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='8' class='text-danger'>Error al cargar instructores: {$e->getMessage()}</td></tr>";
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
