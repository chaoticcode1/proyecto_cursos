<?php
require_once '../conf/conne.php'; // Conexión a la base de datos

$username = isset($_GET['username']) ? trim($_GET['username']) : null;

if ($username) {
    $stmt = $conn->prepare("SELECT pregunta_seguridad_1, pregunta_seguridad_2 FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $pregunta1 = $user['pregunta_seguridad_1'];
        $pregunta2 = $user['pregunta_seguridad_2'];
    } else {
        echo "<script>alert('Usuario no encontrado.'); window.location.href='../index.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Nombre de usuario no especificado.'); window.location.href='../index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style_login.css">
    <title>Preguntas de Seguridad</title>
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
            <h2>Responde una pregunta de seguridad</h2>
            <form method="POST" action="../fun/procesar_respuestas.php" id="formSeguridad" onsubmit="return validarFormulario()">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">

                <!-- Botones de selección de pregunta -->
                <div class="toggle-buttons">
                    <button type="button" id="btnPregunta1" class="active" onclick="mostrarPregunta(1)">Pregunta 1</button>
                    <button type="button" id="btnPregunta2" onclick="mostrarPregunta(2)">Pregunta 2</button>
                </div>

                <!-- Aquí se insertarán dinámicamente las preguntas y los campos de respuesta -->
                <div id="preguntaContainer"></div>

                <button type="submit" class="btn-submit" id="btnSubmit" disabled>Enviar Respuesta</button>
            </form>
        </div>
    </div>

    <script>
        // Datos de las preguntas
        const preguntas = {
            1: "<?php echo htmlspecialchars($pregunta1); ?>",
            2: "<?php echo htmlspecialchars($pregunta2); ?>"
        };

        // Función para mostrar la pregunta seleccionada
        function mostrarPregunta(pregunta) {
            // Limpiar el contenedor de la pregunta y respuesta
            const preguntaContainer = document.getElementById('preguntaContainer');
            preguntaContainer.innerHTML = '';

            // Crear el HTML de la pregunta y el input de la respuesta
            const preguntaHtml = `
                <label>${preguntas[pregunta]}</label>
                <input type="text" id="inputRespuesta" name="respuesta" placeholder="Ingresa tu respuesta" oninput="activarBotonEnviar()">
                <input type="hidden" name="pregunta" value="${preguntas[pregunta]}">
            `;
            preguntaContainer.innerHTML = preguntaHtml;

            // Cambiar el estado de los botones de selección
            document.getElementById('btnPregunta1').classList.remove('active');
            document.getElementById('btnPregunta2').classList.remove('active');
            if (pregunta === 1) {
                document.getElementById('btnPregunta1').classList.add('active');
            } else {
                document.getElementById('btnPregunta2').classList.add('active');
            }
        }

        // Función para habilitar el botón de envío cuando haya texto en el input
        function activarBotonEnviar() {
            const respuesta = document.getElementById('inputRespuesta').value.trim();
            if (respuesta) {
                document.getElementById('btnSubmit').disabled = false;
            } else {
                document.getElementById('btnSubmit').disabled = true;
            }
        }
    </script>
</body>
</html>
