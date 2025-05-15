<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../conf/conne.php';

// Verificar si se proporcionó un ID de curso
if (isset($_GET['id'])) {
    $curso_id = $_GET['id'];

    // Obtener los detalles del curso
    try {
        $sql = "SELECT * FROM cursos WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $curso_id, PDO::PARAM_INT);
        $stmt->execute();
        $curso = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$curso) {
            echo "<p class='text-danger'>Curso no encontrado.</p>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<p class='text-danger'>Error al obtener los detalles del curso: {$e->getMessage()}</p>";
        exit;
    }

    // Procesar la actualización del curso
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $instructor_id = $_POST['instructor_id'];
        $estudiante_ids = isset($_POST['estudiante_ids']) ? $_POST['estudiante_ids'] : [];
        $precio_por_estudiante = $_POST['precio_por_estudiante'];

        $cantidad_estudiantes = count($estudiante_ids);
        $precio_total = $cantidad_estudiantes * $precio_por_estudiante;

        $estudiante_ids_str = implode(',', $estudiante_ids);

        try {
            $sql_update = "UPDATE cursos 
                           SET titulo = :titulo, descripcion = :descripcion, instructor_id = :instructor_id, 
                               estudiante_ids = :estudiante_ids, precio_por_estudiante = :precio_por_estudiante, 
                               precio_total = :precio_total 
                           WHERE id = :id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':titulo', $titulo);
            $stmt_update->bindParam(':descripcion', $descripcion);
            $stmt_update->bindParam(':instructor_id', $instructor_id);
            $stmt_update->bindParam(':estudiante_ids', $estudiante_ids_str);
            $stmt_update->bindParam(':precio_por_estudiante', $precio_por_estudiante);
            $stmt_update->bindParam(':precio_total', $precio_total);
            $stmt_update->bindParam(':id', $curso_id, PDO::PARAM_INT);
            $stmt_update->execute();

            echo "<script>
                    setTimeout(function() {
                        alert('Curso actualizado exitosamente.');
                        window.location.href = 'mostrar_cursos.php';
                    }, 500);
                  </script>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
        }
    }
} else {
    echo "<p class='text-danger'>ID del curso no proporcionado.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style_inicio.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<script>
        // Validación para el campo Título (solo letras y acentos)
        function validarTitulo(event) {
            const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;  // Solo letras y espacios
            if (!regex.test(event.key)) {
                event.preventDefault();  // Si no cumple con la validación, evita la entrada
            }
        }

        // Validación para el campo Precio (solo números positivos)
        function validarPrecio() {
            const precio = document.querySelector('[name="precio_por_estudiante"]').value;
            const regex = /^\d+(\.\d{1,2})?$/;  // Números positivos con hasta 2 decimales
            if (!regex.test(precio)) {
                alert("El precio debe ser un número positivo con hasta dos decimales.");
                return false;  // Previene el envío si no cumple con la validación
            }
            return true;  // Permite el envío si la validación pasa
        }

        // Esta función valida todo el formulario al enviar
        function validarFormulario() {
            return validarPrecio();  // Valida el precio
        }
    </script>
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
        <h2 class="mb-4">Editar Curso</h2>
        <form method="POST" action="" onsubmit="return validarFormulario()">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título del Curso</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($curso['titulo']); ?>" required onkeypress="validarTitulo(event)">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($curso['descripcion']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="instructor_id" class="form-label">Instructor</label>
                <select class="form-control" id="instructor_id" name="instructor_id" required>
                    <option value="" disabled>Seleccione un instructor</option>
                    <?php
                    $query_instructor = "SELECT id, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM instructores";
                    $stmt_instructor = $conn->prepare($query_instructor);
                    $stmt_instructor->execute();
                    foreach ($stmt_instructor->fetchAll(PDO::FETCH_ASSOC) as $instructor) {
                        $selected = ($curso['instructor_id'] == $instructor['id']) ? "selected" : "";
                        echo "<option value='{$instructor['id']}' {$selected}>{$instructor['nombre_completo']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Estudiantes</label>
                <div class="form-check">
                    <?php
                    $query_estudiantes = "SELECT id, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM estudiantes";
                    $stmt_estudiantes = $conn->prepare($query_estudiantes);
                    $stmt_estudiantes->execute();
                    $estudiantes_seleccionados = explode(',', $curso['estudiante_ids']);
                    foreach ($stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC) as $estudiante) {
                        $checked = in_array($estudiante['id'], $estudiantes_seleccionados) ? "checked" : "";
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='estudiante_ids[]' value='{$estudiante['id']}' id='estudiante_{$estudiante['id']}' {$checked}>
                                <label class='form-check-label' for='estudiante_{$estudiante['id']}'>{$estudiante['nombre_completo']}</label>
                              </div>";
                    }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="precio_por_estudiante" class="form-label">Precio por Estudiante</label>
                <input type="number" step="0.01" class="form-control" id="precio_por_estudiante" name="precio_por_estudiante" value="<?php echo htmlspecialchars($curso['precio_por_estudiante']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="submit">Actualizar Curso</button>
        </form>
    </div>

    <script>
        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }
    </script>
</body>
</html>
