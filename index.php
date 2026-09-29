<?php
// ==============================================================================
// Caso Práctico: Sistema Web de Gestión de Servicios Técnicos y Clientes (TechFix)
// Estructura Inicial - Fase 1: Maquetación Base de Inicio
// ==============================================================================

// Variables principales del sistema
$appName = "TechFix - Gestión de Servicios";
$currentYear = date("Y");

// Datos simulados (Mock Data) para probar la renderización dinámica con PHP
$metrics = [
    [
        'titulo' => 'Clientes Registrados',
        'valor' => 128,
        'icono' => 'bi-people-fill',
        'color' => 'primary'
    ],
    [
        'titulo' => 'Servicios Pendientes',
        'valor' => 14,
        'icono' => 'bi-tools',
        'color' => 'warning'
    ],
    [
        'titulo' => 'Servicios Completados',
        'valor' => 89,
        'icono' => 'bi-check-circle-fill',
        'color' => 'success'
    ],
    [
        'titulo' => 'Ingresos del Mes',
        'valor' => '$45,200',
        'icono' => 'bi-currency-dollar',
        'color' => 'info'
    ]
];

$serviciosRecientes = [
    [
        'id' => 101,
        'cliente' => 'Carlos Mendoza',
        'equipo' => 'Laptop HP Pavilion',
        'servicio' => 'Mantenimiento preventivo y formateo',
        'estado' => 'En proceso',
        'badge' => 'bg-warning text-dark'
    ],
    [
        'id' => 102,
        'cliente' => 'María Fernández',
        'equipo' => 'PC Escritorio Ryzen 5',
        'servicio' => 'Cambio de fuente de poder',
        'estado' => 'Completado',
        'badge' => 'bg-success'
    ],
    [
        'id' => 103,
        'cliente' => 'Roberto Gómez',
        'equipo' => 'Impresora Epson L3150',
        'servicio' => 'Limpieza de cabezales',
        'estado' => 'Pendiente',
        'badge' => 'bg-secondary'
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $appName; ?></title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <!-- Navegación Principal -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-wrench-adjustable-circle me-2"></i><?php echo $appName; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="bi bi-house-door me-1"></i>Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#"><i class="bi bi-people me-1"></i>Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#"><i class="bi bi-receipt me-1"></i>Servicios</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header / Banner de Bienvenida -->
    <header class="bg-primary text-white py-5 shadow-sm mb-4">
        <div class="container text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">Panel Principal de Control</h1>
                    <p class="fs-5 opacity-75 mb-0">Bienvenido al sistema de control de ordenes de servicio e inventario técnico.</p>
                </div>
                <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                    <button class="btn btn-light btn-lg px-4 shadow-sm fw-semibold disabled">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="container my-4">

        <!-- Métricas Rápidas -->
        <div class="row g-4 mb-4">
            <?php foreach ($metrics as $metric): ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                            <div>
                                <h6 class="text-muted fw-normal mb-1"><?php echo $metric['titulo']; ?></h6>
                                <h3 class="fw-bold mb-0"><?php echo $metric['valor']; ?></h3>
                            </div>
                            <div class="fs-1 text-<?php echo $metric['color']; ?>">
                                <i class="bi <?php echo $metric['icono']; ?>"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Tabla de Muestra de Servicios -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Servicios Recientes</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Fase 1: Vista Previa</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Equipo / Dispositivo</th>
                                <th>Detalle del Servicio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($serviciosRecientes as $servicio): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $servicio['id']; ?></td>
                                    <td><?php echo $servicio['cliente']; ?></td>
                                    <td><?php echo $servicio['equipo']; ?></td>
                                    <td><?php echo $servicio['servicio']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $servicio['badge']; ?>">
                                            <?php echo $servicio['estado']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- Pie de Página -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0 small">&copy; <?php echo $currentYear; ?> <?php echo $appName; ?> - Desarrollo Web Integral.</p>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
