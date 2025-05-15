<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['submit'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $instructor_id = $_POST['instructor_id'];
    $estudiante_ids = isset($_POST['estudiante_ids']) ? $_POST['estudiante_ids'] : [];
    $precio_por_estudiante = $_POST['precio_por_estudiante'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_finalizacion = $_POST['fecha_finalizacion'];

    // Validar que la fecha final no sea anterior a la fecha inicial
    if (strtotime($fecha_finalizacion) < strtotime($fecha_inicio)) {
        echo "<script>alert('Error: La fecha de finalización no puede ser anterior a la fecha de inicio.');</script>";
        exit;
    }

    $cantidad_estudiantes = count($estudiante_ids);
    $precio_total = $cantidad_estudiantes * $precio_por_estudiante;

    $estudiante_ids_str = implode(',', $estudiante_ids);
    $admin_id = $_SESSION['user_id'];

    require_once '../conf/conne.php';

    try {
        $sql = "INSERT INTO cursos (titulo, descripcion, instructor_id, estudiante_ids, precio_por_estudiante, precio_total, fecha_inicio, fecha_finalizacion)
                VALUES (:titulo, :descripcion, :instructor_id, :estudiante_ids, :precio_por_estudiante, :precio_total, :fecha_inicio, :fecha_finalizacion)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':estudiante_ids', $estudiante_ids_str);
        $stmt->bindParam(':precio_por_estudiante', $precio_por_estudiante);
        $stmt->bindParam(':precio_total', $precio_total);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_finalizacion', $fecha_finalizacion);
        $stmt->execute();

        echo "<script>
                setTimeout(function() {
                    alert('Curso registrado exitosamente.');
                    window.location.href = 'mostrar_cursos.php';
                }, 500);
              </script>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Curso</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style_inicio.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #f8f9fa; display: flex; }
        .form-container {
            max-width: 600px; background: white; padding: 20px; border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: auto;
        }
        .form-container h2 { text-align: center; margin-bottom: 20px; }
    </style>
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

    <div class="content">
        <div class="form-container mt-5">
            <h2>Registrar Curso</h2>
            <form method="POST" onsubmit="return validarFormulario()">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título del Curso</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required onkeypress="validarTitulo(event)">
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="instructor_id" class="form-label">Instructor</label>
                    <select class="form-control" id="instructor_id" name="instructor_id" required>
                        <option value="" disabled selected>Seleccione un instructor</option>
                        <?php
                        require_once '../conf/conne.php';
                        $query = "SELECT id, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM instructores";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $instructor) {
                            echo "<option value='{$instructor['id']}'>{$instructor['nombre_completo']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estudiantes</label>
                    <div class="form-check">
                        <?php
                        $query = "SELECT id, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM estudiantes";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $estudiante) {
                            echo "<div class='form-check'>
                                    <input class='form-check-input' type='checkbox' name='estudiante_ids[]' value='{$estudiante['id']}' id='estudiante_{$estudiante['id']}'>
                                    <label class='form-check-label' for='estudiante_{$estudiante['id']}'>{$estudiante['nombre_completo']}</label>
                                </div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="precio_por_estudiante" class="form-label">Precio por Estudiante</label>
                    <input type="number" step="0.01" class="form-control" id="precio_por_estudiante" name="precio_por_estudiante" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                
                <div class="mb-3">
                    <label for="fecha_finalizacion" class="form-label">Fecha de Finalización</label>
                    <input type="date" class="form-control" id="fecha_finalizacion" name="fecha_finalizacion" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="submit">Registrar Curso</button>
            </form>
        </div>
    </div>

    
    <script>
        // Validación en tiempo real de fechas
        document.getElementById('fecha_inicio').addEventListener('change', function() {
            const fechaInicio = this.value;
            document.getElementById('fecha_finalizacion').min = fechaInicio;
            
            // Si la fecha final actual es anterior a la nueva fecha inicio, resetearla
            const fechaFin = document.getElementById('fecha_finalizacion').value;
            if (fechaFin && new Date(fechaFin) < new Date(fechaInicio)) {
                document.getElementById('fecha_finalizacion').value = fechaInicio;
            }
        });
        
        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }
    </script>

    <script>
        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }
    </script>
</body>
</html>
