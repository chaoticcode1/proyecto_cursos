<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $universidad_procedencia = $_POST['universidad_procedencia'] ?? null;
    $estado = isset($_POST['estado']) ? 1 : 0; // 1 para activo, 0 para inactivo
    $admin_id = $_SESSION['user_id']; // ID del administrador que está registrando al estudiante

    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;

    require_once '../conf/conne.php';

    try {
        // Verificar si ya existe un estudiante con la misma cédula, correo o teléfono
        $sql = "SELECT COUNT(*) FROM estudiantes WHERE cedula = :cedula OR correo = :correo OR telefono = :telefono";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Si existe, muestra el mensaje de error
            echo "<script>alert('Error: Ya existe un estudiante con la misma cédula, correo o teléfono.');</script>";
        } else {
            // Si no existe, proceder con el registro del estudiante
            $sql = "INSERT INTO estudiantes (nombre, apellido, cedula, correo, telefono, fecha_nacimiento, edad, universidad_procedencia, estado)
                    VALUES (:nombre, :apellido, :cedula, :correo, :telefono, :fecha_nacimiento, :edad, :universidad_procedencia, :estado)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':edad', $edad);
            $stmt->bindParam(':universidad_procedencia', $universidad_procedencia);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->execute();
            
            // REGISTRAR LA ACCIÓN EN LA BITÁCORA
            $accion = "Registró al estudiante: $nombre $apellido, Cédula: $cedula";
            $sql_bitacora = "INSERT INTO bitacora (usuario_id, accion) VALUES (:usuario_id, :accion)";
            $stmt_bitacora = $conn->prepare($sql_bitacora);
            $stmt_bitacora->bindParam(':usuario_id', $admin_id);
            $stmt_bitacora->bindParam(':accion', $accion);
            $stmt_bitacora->execute();
            
            // Si se registra correctamente, mostrar mensaje de éxito y redirigir
            echo "<script>
                    alert('Estudiante registrado exitosamente.');
                    window.location.href = 'mostrar_estudiantes.php';
                  </script>";
        }
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
    <title>Registrar Estudiante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style_inicio.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
        }
        .form-container {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
    <script>
        // Validación para el campo Cédula (solo números y longitud de 7 a 8 dígitos)
        function validarCedula(event) {
            const regex = /^[0-9]{0,8}$/;  // Permite hasta 8 dígitos
            const cedula = document.getElementById('cedula').value;
            if (!regex.test(cedula + event.key)) {
                event.preventDefault(); // Si el valor no es válido, previene la entrada
            }
        }

        // Validación para el campo Teléfono (solo números y longitud de 11 dígitos)
        function validarTelefono(event) {
            const regex = /^[0-9]{0,11}$/;  // Permite solo 11 dígitos
            const telefono = document.getElementById('telefono').value;
            if (!regex.test(telefono + event.key)) {
                event.preventDefault(); // Si el valor no es válido, previene la entrada
            }
        }

        // Validación para los campos de Nombre y Apellido (solo letras y "ó")
        function validarLetrasConO(event) {
            const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑó\s]*$/;  // Permite letras y "ó"
            if (!regex.test(event.key)) {
                event.preventDefault(); // Si el valor no es válido, previene la entrada
            }
        }

        // Validación para el campo Correo (debe contener '@' y '.com')
        function validarCorreo() {
            const correo = document.getElementById('correo').value;
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com)$/; // Solo permite correos que terminen en .com
            if (!regex.test(correo)) {
                alert("Por favor ingrese un correo válido que termine en .com");
                return false; // Prevenir el envío del formulario si la validación falla
            }
            return true; // Permitir el envío del formulario si la validación pasa
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
            <h2>Registrar Estudiante</h2>
            <form method="POST" action="" onsubmit="return validarCorreo()">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required
                        onkeypress="validarLetrasConO(event)">
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" required
                        onkeypress="validarLetrasConO(event)">
                </div>
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" required 
                        onkeypress="validarCedula(event)" maxlength="8" minlength="7">
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" required
                        onblur="validarCorreo()">
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" 
                        onkeypress="validarTelefono(event)" maxlength="11" minlength="11">
                </div>
                <div class="mb-3">
                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                </div>
                <div class="mb-3">
                    <label for="universidad_procedencia" class="form-label">Universidad de Procedencia</label>
                    <input type="text" class="form-control" id="universidad_procedencia" name="universidad_procedencia">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="estado" name="estado" checked>
                    <label class="form-check-label" for="estado">Estudiante Activo</label>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="submit">Registrar</button>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>
</body>
</html>