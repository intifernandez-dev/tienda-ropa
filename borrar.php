<?php
require_once 'config/database.php';

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];
$conexion = conectarDB();

// Obtener información de la imagen antes de borrar
$stmt = mysqli_prepare($conexion, "SELECT imagen FROM ropa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) > 0) {
    $datos = mysqli_fetch_assoc($resultado);
    $imagen = $datos['imagen'];
    
    mysqli_stmt_close($stmt);
    
    // Eliminar el registro con prepared statement
    $stmt_delete = mysqli_prepare($conexion, "DELETE FROM ropa WHERE id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    
    if (mysqli_stmt_execute($stmt_delete)) {
        // Si se eliminó correctamente, intentar borrar la imagen física
        if (!empty($imagen) && file_exists("imagenes/" . $imagen)) {
            unlink("imagenes/" . $imagen);
        }
        
        mysqli_stmt_close($stmt_delete);
        cerrarDB($conexion);
        header('Location: listar.php?deleted=1');
        exit;
    } else {
        mysqli_stmt_close($stmt_delete);
        cerrarDB($conexion);
        header('Location: listar.php?error=delete_failed');
        exit;
    }
} else {
    mysqli_stmt_close($stmt);
    cerrarDB($conexion);
    header('Location: listar.php?error=not_found');
    exit;
}
?>
