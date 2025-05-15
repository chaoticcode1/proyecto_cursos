<?php
// Datos de conexión a la base de datos
$host = 'localhost'; // Dirección del servidor
$dbname = 'proyecto_cursos'; // Nombre de la base de datos
$username = 'root'; // Nombre de usuario de la base de datos
$password = ''; // Contraseña de la base de datos

try {
    // Crear una nueva conexión a la base de datos usando PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Confirmación de conexión
    // echo "Conexión exitosa"; // Puedes usar esto para verificar que la conexión fue exitosa
} catch (PDOException $e) {
    // Si ocurre un error en la conexión, lo mostramos
    echo "Error de conexión: " . $e->getMessage();
    die(); // Detener la ejecución del script si no se puede conectar a la base de datos
}
?>
