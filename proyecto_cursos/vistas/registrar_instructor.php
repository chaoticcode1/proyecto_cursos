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

    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;

    require_once '../conf/conne.php';

    try {
        // Verificar si ya existe un instructor con la misma cédula, correo o teléfono
        $sql = "SELECT COUNT(*) FROM instructores WHERE cedula = :cedula OR correo = :correo OR telefono = :telefono";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Si existe, muestra el mensaje de error
            echo "<script>alert('Error: Ya existe un instructor con la misma cédula, correo o teléfono.');</script>";
        } else {
            // Si no existe, proceder con el registro
            $sql = "INSERT INTO instructores (nombre, apellido, cedula, correo, telefono, fecha_nacimiento, edad)
                    VALUES (:nombre, :apellido, :cedula, :correo, :telefono, :fecha_nacimiento, :edad)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':edad', $edad);
            $stmt->execute();
            
            // Registrar la acción en la bitácora
            $usuario_id = $_SESSION['user_id'];  // El ID del usuario que realiza la acción
            $accion = "Registró un nuevo instructor: $nombre $apellido";  // Descripción de la acción
            
            // Insertar en la bitácora
            $sql_bitacora = "INSERT INTO bitacora (usuario_id, accion) VALUES (:usuario_id, :accion)";
            $stmt_bitacora = $conn->prepare($sql_bitacora);
            $stmt_bitacora->bindParam(':usuario_id', $usuario_id);
            $stmt_bitacora->bindParam(':accion', $accion);
            $stmt_bitacora->execute();
            
            // Si se registra correctamente, mostrar mensaje de éxito y redirigir
            echo "<script>
                    alert('Instructor registrado exitosamente.');
                    window.location.href = 'mostrar_instructores.php';
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
    <title>Registrar Instructor</title>
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
            <h2>Registrar Instructor</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required onkeypress="validarNombreApellido(event)">
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" required onkeypress="validarNombreApellido(event)">
                </div>
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" minlength="7" maxlength="8" required onkeypress="validarCedula(event)">
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" required onkeyup="validarCorreo(event)">
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" minlength="11" maxlength="11" required onkeypress="validarTelefono(event)">
                </div>
                <div class="mb-3">
                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="submit">Registrar</button>
            </form>
        </div>
    </div>

    <script>
        // Validación para el campo nombre y apellido (solo letras y caracteres con acentos)
        function validarNombreApellido(event) {
            const regex = /^[A-Za-záéíóúÁÉÍÓÚüÜ\s]*$/;
            if (!regex.test(event.key)) {
                event.preventDefault(); // Si el valor no es válido, previene la entrada
            }
        }

        // Validación para el campo Cédula (solo números y longitud de 7 a 8 dígitos)
        function validarCedula(event) {
            const regex = /^[0-9]{0,8}$/;  // Permite hasta 8 dígitos
            if (!regex.test(event.key)) {
                event.preventDefault(); // Si el valor no es válido, previene la entrada
            }
        }

        // Validación para el campo Correo (aseguramos que tenga '@' y '.com' mediante regex)
        function validarCorreo(event) {
            const correo = document.getElementById('correo').value;
            if (correo.includes('@') && correo.includes('.com')) {
                return;
            }
            // Si el correo no tiene un formato adecuado, no se permite continuar escribiendo.
            const regex = /^[a-zA-Z0-9._%+-@.]+$/; 
            if (!regex.test(event.key)) {
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

    </script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>
</body>
</html>
