<?php
// ==============================================================================
// Caso Práctico: Sistema Web de Gestión de Servicios Técnicos y Clientes (TechFix)
// Fase 3: Integración con Base de Datos MySQL y Gestión Completa (CRUD)
// ==============================================================================

require_once 'includes/db.php';

$pageTitle = "TechFix - Panel Principal";

$totalClientes = 0;
$serviciosPendientes = 0;
$serviciosCompletados = 0;
$ingresosTotales = 0.00;
$serviciosRecientes = [];
$dbConnected = ($pdo !== null);

if ($dbConnected) {
    try {
        // 1. Total Clientes Registrados
        $stmt = $pdo->query("SELECT COUNT(*) FROM clientes");
        $totalClientes = $stmt->fetchColumn();

        // 2. Servicios Pendientes
        $stmt = $pdo->query("SELECT COUNT(*) FROM servicios WHERE estado = 'Pendiente'");
        $serviciosPendientes = $stmt->fetchColumn();

        // 3. Servicios Completados
        $stmt = $pdo->query("SELECT COUNT(*) FROM servicios WHERE estado = 'Completado'");
        $serviciosCompletados = $stmt->fetchColumn();

        // 4. Ingresos Totales de Servicios Completados
        $stmt = $pdo->query("SELECT COALESCE(SUM(precio), 0) FROM servicios WHERE estado = 'Completado'");
        $ingresosTotales = (float)$stmt->fetchColumn();

        // 5. Servicios Recientes (Últimos 5)
        $stmt = $pdo->query("
            SELECT s.*, c.nombre AS cliente_nombre 
            FROM servicios s 
            JOIN clientes c ON s.cliente_id = c.id 
            ORDER BY s.id DESC 
            LIMIT 5
        ");
        $serviciosRecientes = $stmt->fetchAll();
    } catch (PDOException $e) {
        $dbConnected = false;
        $dbErrorMsg = $e->getMessage();
    }
}

$metrics = [
    [
        'titulo' => 'Clientes Registrados',
        'valor' => $totalClientes,
        'icono' => 'bi-people-fill',
        'color' => 'primary'
    ],
    [
        'titulo' => 'Servicios Pendientes',
        'valor' => $serviciosPendientes,
        'icono' => 'bi-tools',
        'color' => 'warning'
    ],
    [
        'titulo' => 'Servicios Completados',
        'valor' => $serviciosCompletados,
        'icono' => 'bi-check-circle-fill',
        'color' => 'success'
    ],
    [
        'titulo' => 'Ingresos Totales',
        'valor' => '$' . number_format($ingresosTotales, 2),
        'icono' => 'bi-currency-dollar',
        'color' => 'info'
    ]
];

// Función para obtener clase de badge según estado
function getBadgeClass($estado) {
    switch ($estado) {
        case 'Completado':
            return 'bg-success';
        case 'En proceso':
            return 'bg-warning text-dark';
        case 'Pendiente':
            return 'bg-secondary';
        case 'Cancelado':
            return 'bg-danger';
        default:
            return 'bg-light text-dark';
    }
}

// Incluir Encabezado y Navegación Modular
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Header / Banner de Bienvenida -->
<header class="bg-primary text-white py-5 shadow-sm mb-4">
    <div class="container text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Panel Principal de Control</h1>
                <p class="fs-5 opacity-75 mb-0">Bienvenido al sistema de control de órdenes de servicio e inventario técnico.</p>
            </div>
            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                <a href="servicios.php" class="btn btn-light btn-lg px-4 shadow-sm fw-semibold me-2 mb-2 mb-sm-0">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
                </a>
                <a href="clientes.php" class="btn btn-outline-light btn-lg px-4 shadow-sm fw-semibold">
                    <i class="bi bi-person-plus me-2"></i>Nuevo Cliente
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Contenido Principal -->
<main class="container my-4">

    <?php if (!$dbConnected): ?>
        <div class="alert alert-warning shadow-sm border-0 mb-4" role="alert">
            <h4 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Base de datos MySQL no conectada</h4>
            <p class="mb-2">No se pudo establecer conexión con la base de datos MySQL (<code>techfix_db</code>).</p>
            <hr>
            <p class="mb-0 small"><strong>Instrucciones:</strong> Asegúrate de tener MySQL ejecutándose (ej. XAMPP/WAMP/Docker) e importar el archivo <code>database.sql</code> en phpMyAdmin, o configura las variables de entorno de conexión.</p>
            <?php if (isset($dbErrorMsg)): ?>
                <div class="mt-2 text-danger font-monospace small">Detalle del error: <?php echo htmlspecialchars($dbErrorMsg); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

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

    <!-- Tabla de Muestra de Servicios Recientes -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Servicios Recientes</h5>
            <a href="servicios.php" class="btn btn-sm btn-outline-primary fw-semibold">Ver Todos <i class="bi bi-arrow-right ms-1"></i></a>
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
                            <th>Precio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($serviciosRecientes)): ?>
                            <?php foreach ($serviciosRecientes as $servicio): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $servicio['id']; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($servicio['cliente_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($servicio['equipo']); ?></td>
                                    <td><?php echo htmlspecialchars($servicio['servicio']); ?></td>
                                    <td class="fw-bold text-success">$<?php echo number_format($servicio['precio'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo getBadgeClass($servicio['estado']); ?>">
                                            <?php echo htmlspecialchars($servicio['estado']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No hay servicios registrados en la base de datos.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<?php
// Incluir Pie de Página
require_once 'includes/footer.php';
?>
