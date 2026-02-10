<?php
require_once 'config/database.php';

// Verificar que se envió el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: agregar.html');
    exit;
}

$conexion = conectarDB();

// Validar y sanitizar datos
$prenda = trim($_POST['prenda'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$talle = trim($_POST['talle'] ?? '');
$precio = trim($_POST['precio'] ?? '');

// Validaciones básicas
$errores = [];

if (empty($prenda)) {
    $errores[] = "El tipo de prenda es obligatorio";
}

if (empty($marca)) {
    $errores[] = "La marca es obligatoria";
}

if (empty($talle)) {
    $errores[] = "El talle es obligatorio";
}

if (empty($precio) || !is_numeric($precio) || $precio < 0) {
    $errores[] = "El precio debe ser un número válido mayor a 0";
}

// Si hay errores, mostrarlos
if (!empty($errores)) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Error</title>";
    echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head>";
    echo "<body><div class='container mt-5'><div class='alert alert-danger'>";
    echo "<h4>Se encontraron los siguientes errores:</h4><ul>";
    foreach ($errores as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul><a href='agregar.html' class='btn btn-primary'>Volver</a></div></div></body></html>";
    exit;
}

// Procesar imagen
$nombre_imagen = "";
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $archivo_tmp = $_FILES['imagen']['tmp_name'];
    $nombre_original = $_FILES['imagen']['name'];
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    
    // Validar extensión
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $extensiones_permitidas)) {
        die("Error: Solo se permiten imágenes JPG, PNG o WEBP");
    }
    
    // Validar tamaño (máximo 2MB)
    if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) {
        die("Error: La imagen no debe superar los 2MB");
    }
    
    // Crear carpeta si no existe
    $carpeta = "imagenes/";
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0755, true);
    }
    
    // Generar nombre único
    $nombre_imagen = time() . "_" . uniqid() . "." . $extension;
    
    // Mover archivo
    if (!move_uploaded_file($archivo_tmp, $carpeta . $nombre_imagen)) {
        die("Error al guardar la imagen");
    }
}

// Preparar consulta con prepared statement (SEGURIDAD)
$stmt = mysqli_prepare($conexion, 
    "INSERT INTO ropa (prenda, marca, talle, precio, imagen) VALUES (?, ?, ?, ?, ?)"
);

if ($stmt === false) {
    die("Error en la preparación de la consulta: " . mysqli_error($conexion));
}

// Bind de parámetros
mysqli_stmt_bind_param($stmt, "sssds", $prenda, $marca, $talle, $precio, $nombre_imagen);

// Ejecutar
if (mysqli_stmt_execute($stmt)) {
    // Éxito - Redirigir al listado
    mysqli_stmt_close($stmt);
    cerrarDB($conexion);
    header('Location: listar.php?success=1');
    exit;
} else {
    // Error
    echo "Error al insertar: " . mysqli_error($conexion);
    mysqli_stmt_close($stmt);
    cerrarDB($conexion);
}
?>
