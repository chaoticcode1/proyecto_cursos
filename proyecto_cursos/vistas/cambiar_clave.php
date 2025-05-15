<?php
// Iniciar la sesión (si es necesario)
session_start();

// Conexión a la base de datos
include_once('../conf/conne.php'); // Asegúrate de que este archivo contenga la conexión a tu base de datos

// Si el formulario se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la nueva contraseña y la confirmación
    $clave_nueva = $_POST['clave_nueva'];
    $clave_confirmada = $_POST['clave_confirmada'];

    // Validar que las contraseñas coinciden
    if ($clave_nueva !== $clave_confirmada) {
        echo "<script>alert('Las contraseñas no coinciden.');</script>";
    } else {
        // Obtener el username desde la URL (parametro GET)
        $usuario = $_GET['username'];  // Esto asume que el username se pasa como parámetro en la URL (ej. ?username=juanp)

        // Verificar si el usuario existe en la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
            $stmt->bindParam(':username', $usuario);
            $stmt->execute();

            // Verificamos si el usuario existe
            if ($stmt->rowCount() > 0) {
                // Cifrar la nueva contraseña con SHA-256
                $clave_nueva_hash = hash('sha256', $clave_nueva); // Cifrado SHA-256

                // Actualizar la contraseña en la base de datos
                $updateStmt = $conn->prepare("UPDATE usuarios SET password_hash = :password_hash WHERE username = :username");
                $updateStmt->bindParam(':password_hash', $clave_nueva_hash);
                $updateStmt->bindParam(':username', $usuario);
                $updateStmt->execute();

                if ($updateStmt->rowCount() > 0) {
                    echo "<script>alert('Contraseña cambiada con éxito.'); window.location.href='../index.php';</script>";
                } else {
                    echo "<script>alert('No se pudo cambiar la contraseña. Inténtalo de nuevo.');</script>";
                }
            } else {
                echo "<script>alert('Usuario no encontrado.');</script>";
            }
        } catch (Exception $e) {
            $error = "Error al cambiar la contraseña: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style_login.css">
    <title>Cambiar Contraseña</title>
    <style>
        .toggle-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .toggle-buttons button {
            padding: 10px 20px;
            border: none;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        .toggle-buttons button.active {
            background-color: #0056b3;
        }
        #respuesta1, #respuesta2 {
            display: none;
        }
        #respuesta1.active, #respuesta2.active {
            display: block;
        }
        .btn-submit:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .btn {
            margin-right: 160px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-right">
            <img src="../img/image_login.jpeg" alt="Recover Image">
            <button id="loginButton" class="btn right-btn" onclick="window.location.href='../index.php';">Deseo iniciar sesión</button>
        </div>
        <div class="form-container">
            <h2>Cambiar Contraseña</h2>

            <!-- Mostrar mensaje de éxito -->
            <?php if (isset($exito)) { ?>
                <div style="color: green;"><?php echo $exito; ?></div>
            <?php } ?>

            <!-- Mostrar mensaje de error -->
            <?php if (isset($error)) { ?>
                <div style="color: red;"><?php echo $error; ?></div>
            <?php } ?>

            <!-- Formulario para cambiar contraseña -->
            <form method="POST" action="">
                <label for="clave_nueva">Nueva Contraseña</label>
                <input type="password" id="clave_nueva" name="clave_nueva" required>

                <br>

                <label for="clave_confirmada">Confirmar Nueva Contraseña</label>
                <input type="password" id="clave_confirmada" name="clave_confirmada" required>

                <br><br>

                <button type="submit">Cambiar Contraseña</button>
            </form>
        </div>
    </div>
</body>
</html>
