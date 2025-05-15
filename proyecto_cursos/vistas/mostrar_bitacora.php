<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../conf/conne.php';

try {
    // Consulta para obtener los registros de la bitácora con el nombre y apellido del usuario
    $sql = "SELECT b.fecha, u.nombre, u.apellido, b.accion 
            FROM bitacora b
            JOIN usuarios u ON b.usuario_id = u.id
            ORDER BY b.fecha DESC";  

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bitacora = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='text-danger'>Error al cargar la bitácora: {$e->getMessage()}</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            display: flex;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            width: 250px;
            height: 100%;
            position: fixed;
        }
        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            width: calc(100% - 270px);
        }
        .content h2 {
            margin-bottom: 30px;
        }
        .table th, .table td {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
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

    <!-- Contenido -->
    <div class="content">
        <h2>Bitácora de Actividades</h2>

        <div class="card p-4">
            <?php if ($bitacora): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bitacora as $registro): ?>
                            <tr>
                                <td><?php echo $registro['nombre'] . ' ' . $registro['apellido']; ?></td>
                                <td><?php echo $registro['accion']; ?></td>
                                <td><?php echo $registro['fecha']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay registros en la bitácora.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
