<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../conf/conne.php';

$preguntas = [
    "¿Cuál es el nombre de tu primera mascota?",
    "¿En qué ciudad naciste?",
    "¿Cuál es tu comida favorita?",
    "¿Cuál es el nombre de tu mejor amigo de la infancia?",
    "¿Cuál es tu película favorita?"
];

$admin_id = $_SESSION['user_id']; // ID del usuario que está registrando

// Obtener el nombre de usuario del administrador
$sql_admin = "SELECT username FROM usuarios WHERE id = ?";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->execute([$admin_id]);
$admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
$admin_username = $admin ? $admin['username'] : 'Desconocido'; // Si no se encuentra el usuario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $pregunta1 = $_POST['pregunta_seguridad_1'];
    $respuesta1 = password_hash($_POST['respuesta_seguridad_1'], PASSWORD_BCRYPT);
    $pregunta2 = $_POST['pregunta_seguridad_2'];
    $respuesta2 = password_hash($_POST['respuesta_seguridad_2'], PASSWORD_BCRYPT);

    if ($pregunta1 === $pregunta2) {
        echo "<script>alert('Las preguntas de seguridad no pueden ser iguales.'); window.history.back();</script>";
        exit;
    }

    try {
        // Insertar el nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, apellido, username, password_hash, pregunta_seguridad_1, respuesta_seguridad_1_hash, pregunta_seguridad_2, respuesta_seguridad_2_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $apellido, $username, $password, $pregunta1, $respuesta1, $pregunta2, $respuesta2]);

        // Registrar en la bitácora quién registró al usuario
        $accion = "$admin_username registró al usuario con username $username";
        $sql_bitacora = "INSERT INTO bitacora (usuario_id, accion) VALUES (:usuario_id, :accion)";
        $stmt_bitacora = $conn->prepare($sql_bitacora);
        $stmt_bitacora->bindParam(':usuario_id', $admin_id);
        $stmt_bitacora->bindParam(':accion', $accion);
        $stmt_bitacora->execute();

        // Redirigir a la página de mostrar usuarios
        header("Location: ../vistas/mostrar_usuarios.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al registrar usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style_inicio.css">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function actualizarPreguntas() {
            let pregunta1 = document.getElementById('pregunta_seguridad_1');
            let pregunta2 = document.getElementById('pregunta_seguridad_2');
            let opciones = <?php echo json_encode($preguntas); ?>;
            
            pregunta2.innerHTML = '<option value="" disabled selected>Seleccione una pregunta</option>';
            opciones.forEach(pregunta => {
                if (pregunta !== pregunta1.value) {
                    let option = document.createElement('option');
                    option.value = pregunta;
                    option.textContent = pregunta;
                    pregunta2.appendChild(option);
                }
            });
        }
    </script>
        <script>
        // Validación para Nombre y Apellido (solo letras y espacios)
        function validarNombreApellido(event) {
            const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;  // Solo letras y espacios
            if (!regex.test(event.key)) {
                event.preventDefault();  // Si no cumple con la validación, evita la entrada
            }
        }

        // Validación para el campo Usuario (alfanumérico, mínimo 5 caracteres)
        function validarUsuario() {
            const usuario = document.querySelector('[name="username"]').value;
            const regex = /^[a-zA-Z0-9]{5,}$/;  // Alfanumérico, mínimo 5 caracteres
            if (!regex.test(usuario)) {
                alert("El nombre de usuario debe ser alfanumérico y tener al menos 5 caracteres.");
                return false;  // Previene el envío si no cumple con la validación
            }
            return true;  // Permite el envío si la validación pasa
        }

        // Validación para la contraseña (mínimo 8 caracteres, al menos una letra y un número)
        function validarContraseña() {
            const contraseña = document.querySelector('[name="password"]').value;
            const regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;  // Al menos 8 caracteres, una letra y un número
            if (!regex.test(contraseña)) {
                alert("La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra y un número.");
                return false;  // Previene el envío si no cumple con la validación
            }
            return true;  // Permite el envío si la validación pasa
        }

        // Validación para las respuestas de seguridad (solo letras y números)
        function validarRespuestaSeguridad(event) {
            const regex = /^[a-zA-Z0-9\s]*$/;  // Solo letras, números y espacios
            if (!regex.test(event.key)) {
                event.preventDefault();  // Si no cumple con la validación, evita la entrada
            }
        }

        // Esta función valida todo el formulario al enviar
        function validarFormulario() {
            return validarUsuario() && validarContraseña();  // Valida usuario y contraseña
        }
    </script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4 text-center">Registrar Usuario</h2>
                        <form method="POST" onsubmit="return validarFormulario()">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required onkeypress="validarNombreApellido(event)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" required onkeypress="validarNombreApellido(event)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pregunta de Seguridad 1</label>
                                <select id="pregunta_seguridad_1" name="pregunta_seguridad_1" class="form-control" required onchange="actualizarPreguntas()">
                                    <option value="" disabled selected>Seleccione una pregunta</option>
                                    <?php foreach ($preguntas as $pregunta) { echo "<option value='$pregunta'>$pregunta</option>"; } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Respuesta de Seguridad 1</label>
                                <input type="text" name="respuesta_seguridad_1" class="form-control" required onkeypress="validarRespuestaSeguridad(event)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pregunta de Seguridad 2</label>
                                <select id="pregunta_seguridad_2" name="pregunta_seguridad_2" class="form-control" required>
                                    <option value="" disabled selected>Seleccione una pregunta</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Respuesta de Seguridad 2</label>
                                <input type="text" name="respuesta_seguridad_2" class="form-control" required onkeypress="validarRespuestaSeguridad(event)">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Registrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
