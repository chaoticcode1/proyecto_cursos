<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Curso</title>
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
        .btn-secondary {
            margin-top: 20px;
        }
        .card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

    <!-- Content -->
    <div class="content">
        <h2>Detalles del Curso</h2>
        <div class="card p-4">
            <?php
            if (isset($_GET['id'])) {
                $curso_id = $_GET['id'];
                require_once '../conf/conne.php';

                try {
                    $sql = "SELECT 
                                c.titulo, 
                                c.descripcion, 
                                c.precio_por_estudiante, 
                                c.precio_total, 
                                i.nombre AS instructor_nombre, 
                                i.apellido AS instructor_apellido, 
                                c.estudiante_ids,
                                c.fecha_inicio,
                                c.fecha_finalizacion
                            FROM cursos c
                            JOIN instructores i ON c.instructor_id = i.id
                            WHERE c.id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $curso_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($curso) {
                        echo "<p><strong>Título:</strong> {$curso['titulo']}</p>";
                        echo "<p><strong>Descripción:</strong> {$curso['descripcion']}</p>";
                        echo "<p><strong>Instructor:</strong> {$curso['instructor_nombre']} {$curso['instructor_apellido']}</p>";

                        $estudiante_ids = $curso['estudiante_ids'];
                        if (!empty($estudiante_ids)) {
                            $sql_estudiantes = "SELECT nombre, apellido FROM estudiantes WHERE id IN ($estudiante_ids)";
                            $stmt_estudiantes = $conn->prepare($sql_estudiantes);
                            $stmt_estudiantes->execute();
                            $estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);

                            echo "<p><strong>Estudiantes:</strong></p><ul>";
                            foreach ($estudiantes as $estudiante) {
                                echo "<li>{$estudiante['nombre']} {$estudiante['apellido']}</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p><strong>Estudiantes:</strong> No asignados</p>";
                        }

                        // Mostrar precios directamente desde la base de datos
                        echo "<p><strong>Precio por Estudiante:</strong> {$curso['precio_por_estudiante']} USD</p>";
                        echo "<p><strong>Precio Total:</strong> {$curso['precio_total']} USD</p>";
                        echo "<p><strong>Fecha de inicio:</strong> {$curso['fecha_inicio']}</p>";
                        echo "<p><strong>Fecha final:</strong> {$curso['fecha_finalizacion']}</p>";
                    } else {
                        echo "<p class='text-danger'>Curso no encontrado.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='text-danger'>Error al cargar los detalles: {$e->getMessage()}</p>";
                }
            } else {
                echo "<p class='text-danger'>ID del curso no proporcionado.</p>";
            }
            ?>
            <a href="mostrar_cursos.php" class="btn btn-secondary">Volver a la Lista de Cursos</a>
        </div>
    </div>

</body>
</html>
