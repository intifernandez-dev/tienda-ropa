<?php
require_once 'config/database.php';
$conexion = conectarDB();

// Preparar la consulta con prepared statement
$consulta = "SELECT * FROM ropa ORDER BY id DESC";
$resultado = mysqli_query($conexion, $consulta);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Tienda de Ropa</title>
    
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
                        <a class="nav-link active" href="listar.php">
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
                <h1><i class="bi bi-card-list"></i> Inventario de Prendas</h1>
                <p class="text-muted">Listado completo de prendas en stock</p>
            </div>

            <?php if (mysqli_num_rows($resultado) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Prenda</th>
                                <th width="15%">Marca</th>
                                <th width="10%">Talle</th>
                                <th width="10%">Precio</th>
                                <th width="15%">Imagen</th>
                                <th width="15%" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reg = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reg['id']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['prenda']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['marca']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['talle']); ?></td>
                                    <td>$<?php echo number_format($reg['precio'], 2); ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($reg['imagen']) && file_exists("imagenes/" . $reg['imagen'])): ?>
                                            <img src="imagenes/<?php echo htmlspecialchars($reg['imagen']); ?>" 
                                                 alt="<?php echo htmlspecialchars($reg['prenda']); ?>" 
                                                 width="80" height="80" 
                                                 style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center actions-cell">
                                        <a href="modificar.php?id=<?php echo $reg['id']; ?>" 
                                           class="btn btn-sm btn-warning btn-action"
                                           title="Editar">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="borrar.php?id=<?php echo $reg['id']; ?>" 
                                           class="btn btn-sm btn-danger btn-action"
                                           title="Eliminar"
                                           onclick="return confirm('¿Estás seguro de eliminar esta prenda?');">
                                            <i class="bi bi-trash"></i> Borrar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Total de prendas: <strong><?php echo mysqli_num_rows($resultado); ?></strong>
                    </p>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">No hay prendas registradas</h4>
                    <p>Comienza agregando tu primera prenda al inventario</p>
                    <a href="agregar.html" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Agregar Prenda
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
cerrarDB($conexion);
?>
