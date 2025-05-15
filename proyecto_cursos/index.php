<?php
require_once 'conf/conne.php'; // Incluir la conexión a la base de datos

session_start(); // Iniciar la sesión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("SELECT id, username, password_hash, intentos_fallido, bloqueo FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username); // Enlace del parámetro para la consulta

    $stmt->execute(); // Ejecutar la consulta
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener el resultado de la consulta

    if ($user) {
        // Verificar si la cuenta está bloqueada, pero NO bloquear si el usuario es el ID 1
        if ($user['bloqueo'] && $user['id'] != 1) {
            echo "<script>alert('La cuenta está bloqueada.'); window.location.href='index.php';</script>";
            exit();
        }

        // Generar el hash de la contraseña ingresada
        $password_hash = hash('sha256', $password);

        // Si el usuario es el ID 1, no incrementar intentos fallidos ni bloquear, solo mostrar mensaje de "contraseña incorrecta"
        if ($user['id'] == 1) {
            if ($password_hash !== $user['password_hash']) {
                echo "<script>alert('Su contraseña es incorrecta.'); window.location.href='index.php';</script>";
            } else {
                // Si la contraseña es correcta, iniciar sesión como cualquier otro usuario
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                echo "<script>alert('Bienvenido " . $user['username'] . "'); window.location.href='vistas/inicio.php';</script>";
            }
        } else {
            // Verificar si la contraseña ingresada coincide con la almacenada
            if ($password_hash === $user['password_hash']) {
                // Si la contraseña es correcta, reiniciar intentos fallidos y iniciar sesión
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallido = 0 WHERE id = :id");
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                echo "<script>alert('Bienvenido " . $user['username'] . "'); window.location.href='vistas/inicio.php';</script>";
                exit(); // Salir para evitar que se ejecute más código
            } else {
                // Incrementar el contador de intentos fallidos, pero NO para el usuario con ID 1
                if ($user['id'] != 1) {
                    $intentos_fallidos = $user['intentos_fallido'] + 1;

                    // Bloquear la cuenta si se alcanzan 3 intentos fallidos
                    if ($intentos_fallidos >= 3) {
                        $bloqueo = 1; // Establecer bloqueo
                        $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallido = :intentos_fallido, bloqueo = :bloqueo WHERE id = :id");
                        $stmt->bindParam(':intentos_fallidos', $intentos_fallidos);
                        $stmt->bindParam(':bloqueo', $bloqueo);
                        $stmt->bindParam(':id', $user['id']);
                        $stmt->execute();

                        echo "<script>alert('La cuenta ha sido bloqueada por demasiados intentos fallidos.'); window.location.href='index.php';</script>";
                    } else {
                        // Actualizar el contador de intentos fallidos
                        $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallido = :intentos_fallido WHERE id = :id");
                        $stmt->bindParam(':intentos_fallido', $intentos_fallidos);
                        $stmt->bindParam(':id', $user['id']);
                        $stmt->execute();

                        echo "<script>alert('Su contraseña es incorrecta. Intentos fallidos: " . $intentos_fallidos . "'); window.location.href='index.php';</script>";
                    }
                } else {
                    // Si el usuario con ID 1 tiene una contraseña incorrecta, solo mostrar mensaje sin modificar los intentos
                    echo "<script>alert('Su contraseña es incorrecta.'); window.location.href='index.php';</script>";
                }
            }
        }
    } else {
        echo "<script>alert('Usuario no existente.'); window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style/style_login.css">
</head>
<body>
    <div class="container">
        <!-- Contenedor de imágenes y botones -->
        <div class="image-left">
            <img src="img/image_login.jpeg" alt="Login Image">
            <button id="forgotPasswordButton" class="btn left-btn">¿Olvidaste tu contraseña?</button>
        </div>
        <div class="image-right">
            <img src="img/image_login2.jpg" alt="Recover Image">
            <button id="loginButton" class="btn right-btn">Deseo iniciar sesión</button>
        </div>

        <!-- Contenedor de formularios -->
        <div class="form-container">
            <!-- Formulario de Inicio de Sesión -->
            <form id="loginForm" method="POST">
                <h2>Iniciar Sesión</h2>
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Iniciar sesión</button>
            </form>

            <!-- Formulario de Recuperación de Contraseña -->
            <form id="recoverForm" method="POST" action="fun/recuperar_clave.php" class="hidden">
                <h2>Recuperar Contraseña</h2>
                <input type="text" id="username" name="username" placeholder="Usuario" required>
                <button type="submit" id="checkUser">Comprobar Usuario</button>
            </form>
        </div>
    </div>
    <script src="js/scripts.js"></script>
</body>
</html>