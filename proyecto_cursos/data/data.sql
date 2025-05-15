CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    pregunta_seguridad_1 VARCHAR(255) NOT NULL,
    respuesta_seguridad_1_hash VARCHAR(255) NOT NULL,
    pregunta_seguridad_2 VARCHAR(255) NOT NULL,
    respuesta_seguridad_2_hash VARCHAR(255) NOT NULL,
    intentos_fallido INT DEFAULT 0, -- Contador de intentos fallidos
    bloqueo BOOLEAN DEFAULT FALSE, -- Estado de bloqueo
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Inserción de un usuario ejemplo
INSERT INTO usuarios (nombre, apellido, username, password_hash, pregunta_seguridad_1, respuesta_seguridad_1_hash, pregunta_seguridad_2, respuesta_seguridad_2_hash)
VALUES ('dixon', 'gonzalez', 'dixon', SHA2('zerom0510', 256), '¿Cuál es tu color favorito?', SHA2('azul', 256), '¿Nombre de tu primera mascota?', SHA2('firulais', 256));

CREATE TABLE instructores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15) UNIQUE,
    fecha_nacimiento DATE NOT NULL,
    edad INT NOT NULL
);

CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15) UNIQUE,
    fecha_nacimiento DATE NOT NULL,
    edad INT NOT NULL,
    universidad_procedencia VARCHAR(100),
    estado TINYINT(1) NULL DEFAULT 1 COMMENT '0=Inactivo, 1=Activo, NULL=No especificado'
);

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    instructor_id INT NOT NULL,
    estudiante_ids TEXT NOT NULL,
     precio_por_estudiante DECIMAL(10,2) NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_finalizacion DATE NOT NULL,
     FOREIGN KEY (instructor_id) REFERENCES instructores(id)
);


CREATE TABLE bitacora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(255) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);