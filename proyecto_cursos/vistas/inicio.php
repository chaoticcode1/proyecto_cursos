<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        
        /* Sidebar responsive */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            transition: all 0.3s;
            padding-top: 20px;
            z-index: 1000;
        }
        
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            transition: background-color 0.2s;
        }
        
        .sidebar a:hover {
            background-color: #495057;
        }
        
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            transition: margin 0.3s;
        }
        
        .toggle-btn {
            position: fixed;
            top: 15px;
            left: 260px;
            cursor: pointer;
            background-color: #343a40;
            border: none;
            color: white;
            padding: 10px;
            border-radius: 5px;
            z-index: 1001;
            transition: left 0.3s;
        }
        
        .toggle-btn:hover {
            background-color: #495057;
        }
        
        .sidebar.collapsed {
            width: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .sidebar.collapsed + .content {
            margin-left: 0;
        }
        
        .sidebar.collapsed ~ .toggle-btn {
            left: 10px;
        }
        
        /* Cards responsive */
        .card-container {
            margin-top: 30px;
        }
        
        .card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px 5px 0 0;
            transition: transform 0.3s;
        }
        
        .card-img:hover {
            transform: scale(1.03);
        }
        
        .card-body {
            padding: 15px;
        }
        
        /* Footer responsive */
        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px 0;
            width: 100%;
            font-size: 0.9rem;
        }
        
        .footer a {
            color: #f8f9fa;
            text-decoration: none;
            font-weight: bold;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .license-icon {
            height: 1.2rem;
            margin-left: 5px;
            vertical-align: text-bottom;
        }
        
        /* Media queries para diferentes tamaños de pantalla */
        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            
            .content {
                margin-left: 220px;
            }
            
            .toggle-btn {
                left: 230px;
            }
            
            .card-img {
                height: 180px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            
            .content {
                margin-left: 200px;
            }
            
            .toggle-btn {
                left: 210px;
            }
            
            .card-img {
                height: 150px;
            }
            
            .footer {
                font-size: 0.8rem;
                padding: 10px 0;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }
            
            .sidebar:not(.collapsed) {
                width: 180px;
            }
            
            .content {
                margin-left: 0;
            }
            
            .toggle-btn {
                left: 10px;
            }
            
            .sidebar:not(.collapsed) ~ .toggle-btn {
                left: 190px;
            }
            
            .card-container {
                margin-top: 20px;
            }
            
            .card-img {
                height: 120px;
            }
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
        <div class="container">
            <h1 class="text-center mb-4">Bienvenido al sistema de inventario</h1>
            <p class="text-center mb-5">Este sistema te permite gestionar instructores, estudiantes y cursos de manera eficiente.</p>
            
            <div class="row card-container">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <img src="../img/1.jpg" class="card-img" alt="Imagen 1">
                        <div class="card-body">
                            <h5 class="card-title text-center">Gestión de Instructores</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <img src="../img/2.png" class="card-img" alt="Imagen 2">
                        <div class="card-body">
                            <h5 class="card-title text-center">Gestión de Estudiantes</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <img src="../img/3.jpg" class="card-img" alt="Imagen 3">
                        <div class="card-body">
                            <h5 class="card-title text-center">Gestión de Cursos</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto">
        <p xmlns:cc="http://creativecommons.org/ns#">
            This work is licensed under 
            <a href="https://creativecommons.org/licenses/by-nc/4.0/?ref=chooser-v1" 
               target="_blank" rel="license noopener noreferrer">
                CC BY-NC 4.0
                <img class="license-icon" src="https://mirrors.creativecommons.org/presskit/icons/cc.svg?ref=chooser-v1" alt="">
                <img class="license-icon" src="https://mirrors.creativecommons.org/presskit/icons/by.svg?ref=chooser-v1" alt="">
                <img class="license-icon" src="https://mirrors.creativecommons.org/presskit/icons/nc.svg?ref=chooser-v1" alt="">
            </a>
        </p>
    </footer>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
        
        // Ajustar automáticamente el sidebar en pantallas pequeñas
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth < 576 && !sidebar.classList.contains('collapsed')) {
                sidebar.classList.add('collapsed');
            }
        }
        
        // Ejecutar al cargar y al cambiar el tamaño de la ventana
        window.addEventListener('load', handleResize);
        window.addEventListener('resize', handleResize);
    </script>
</body>
</html>