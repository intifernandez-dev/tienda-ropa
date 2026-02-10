<?php
/**
 * Configuración de conexión a la base de datos
 * XAMPP - Configuración por defecto
 */

// Configuración de la base de datos
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tienda');

/**
 * Función para conectar a la base de datos
 * @return mysqli Objeto de conexión
 */
function conectarDB() {
    $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    // Configurar charset UTF-8
    mysqli_set_charset($conexion, "utf8");
    
    return $conexion;
}

/**
 * Función para cerrar la conexión
 * @param mysqli $conexion
 */
function cerrarDB($conexion) {
    if ($conexion) {
        mysqli_close($conexion);
    }
}
?>
