<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acerca de</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style_inicio.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
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
        <h2 class="text-center mb-4">Acerca del Sistema</h2>
        
        <div class="mb-4">
            <h4><strong>Nombre del Proyecto:</strong></h4>
            <p>SISTEMA DE REGISTRO Y CONTROL DE LOS CURSOS QUE SE IMPARTEN EN CI-DILG CENTRO DE INFORMACIÓN Y DOCUMENTACIÓN “IBRAHIM LÓPEZ GARCÍA”</p>
        </div>

        <div class="mb-4">
            <h4><strong>Miembros del Proyecto:</strong></h4>
            <ul>
                <li>Capielo Joswar - C.I: 28.289.543</li>
                <li>Quero Elvismar - C.I: 30.193.431</li>
                <li>Hernández Yanahis - C.I: 29.600.628</li>
                <li>González Dixon - C.I: 27.237.997</li>
            </ul>
        </div>

        <div class="mb-4">
            <h4><strong>Descripción del Sistema:</strong></h4>
            <p>El Sistema de Registro y Control de los Cursos que se imparten en el CI-DILG tiene como objetivo facilitar el proceso de registro, administración y seguimiento de cursos ofrecidos en el centro de información y documentación. A través de este sistema, los administradores pueden registrar los detalles de los cursos, incluyendo los instructores, estudiantes, y el costo por estudiante. Además, se puede generar un control y seguimiento del progreso de cada curso de manera eficiente.</p>

            <p>Este sistema ha sido diseñado para proporcionar una interfaz fácil de usar, optimizando el tiempo de los administradores y permitiendo que los estudiantes y profesores accedan a la información relevante de manera rápida y eficiente. La base de datos está organizada para garantizar la integridad y disponibilidad de la información a lo largo del tiempo.</p>

            <p>Es una herramienta fundamental para la gestión académica y administrativa del CI-DILG, contribuyendo al desarrollo de la educación y capacitación en el centro.</p>
        </div>

        <div class="text-center">
            <a href="../archive/uso.pdf" class="btn btn-success" download="uso.pdf">Descargar Manual de Usuario</a>
        </div>
    </div>

    <script>
        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }
    </script>
</body>
</html>
