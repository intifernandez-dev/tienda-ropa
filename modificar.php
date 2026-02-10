<?php
require_once 'config/database.php';

// Verificar que se recibió un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar.php');
    exit;
}

$id = (int)$_GET['id'];
$conexion = conectarDB();

// Obtener datos actuales con prepared statement
$stmt = mysqli_prepare($conexion, "SELECT * FROM ropa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) === 0) {
    echo "Prenda no encontrada";
    exit;
}

$datos = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_cambios'])) {
    $prenda = trim($_POST['prenda'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $talle = trim($_POST['talle'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $imagen_actual = $_POST['imagen_actual'] ?? '';
    
    // Procesar nueva imagen si se subió
    $nombre_imagen = $imagen_actual;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($extension, $extensiones_permitidas)) {
            $carpeta = "imagenes/";
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0755, true);
            }
            
            $nombre_imagen = time() . "_" . uniqid() . "." . $extension;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombre_imagen)) {
                // Eliminar imagen anterior si existe
                if (!empty($imagen_actual) && file_exists($carpeta . $imagen_actual)) {
                    unlink($carpeta . $imagen_actual);
                }
            }
        }
    }
    
    // Actualizar con prepared statement
    $stmt = mysqli_prepare($conexion, 
        "UPDATE ropa SET prenda=?, marca=?, talle=?, precio=?, imagen=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "sssdsi", $prenda, $marca, $talle, $precio, $nombre_imagen, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        cerrarDB($conexion);
        header('Location: listar.php?updated=1');
        exit;
    } else {
        $error = "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Prenda - Tienda de Ropa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <i class="bi bi-shop"></i> Tienda de Ropa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">
                            <i class="bi bi-house-door"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listar.php">
                            <i class="bi bi-list-ul"></i> Ver Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agregar.html">
                            <i class="bi bi-plus-circle"></i> Agregar Prenda
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-4">
        <div class="main-container">
            <div class="page-header">
                <h1><i class="bi bi-pencil-square"></i> Modificar Prenda</h1>
                <p class="text-muted">Actualiza los datos de la prenda</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <!-- Mostrar imagen actual -->
                <?php if (!empty($datos['imagen']) && file_exists("imagenes/" . $datos['imagen'])): ?>
                    <div class="imagen-preview mb-4">
                        <strong><i class="bi bi-image"></i> Imagen actual:</strong><br>
                        <img src="imagenes/<?php echo htmlspecialchars($datos['imagen']); ?>" 
                             alt="Imagen actual" 
                             style="max-width: 200px; max-height: 200px; margin-top: 10px;">
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="prenda">
                            <i class="bi bi-tag"></i> Tipo de Prenda
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="prenda" 
                               name="prenda" 
                               value="<?php echo htmlspecialchars($datos['prenda']); ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="marca">
                            <i class="bi bi-award"></i> Marca
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="marca" 
                               name="marca" 
                               value="<?php echo htmlspecialchars($datos['marca']); ?>" 
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="talle">
                                    <i class="bi bi-rulers"></i> Talle
                                </label>
                                <select class="form-select" id="talle" name="talle" required>
                                    <option value="">Seleccione un talle</option>
                                    <?php 
                                    $talles = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                    foreach ($talles as $t): 
                                    ?>
                                        <option value="<?php echo $t; ?>" 
                                            <?php echo ($datos['talle'] === $t) ? 'selected' : ''; ?>>
                                            <?php echo $t; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio">
                                    <i class="bi bi-currency-dollar"></i> Precio
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="precio" 
                                       name="precio" 
                                       value="<?php echo htmlspecialchars($datos['precio']); ?>" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="imagen">
                            <i class="bi bi-image"></i> Nueva Imagen (opcional)
                        </label>
                        <input type="file" 
                               class="form-control" 
                               id="imagen" 
                               name="imagen" 
                               accept="image/jpeg,image/png,image/jpg,image/webp">
                        <small class="form-text text-muted">
                            Si no seleccionas una imagen, se mantendrá la actual
                        </small>
                    </div>

                    <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($datos['imagen']); ?>">

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="listar.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" name="guardar_cambios" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php cerrarDB($conexion); ?>
