<?php
// ==============================================================================
// Módulo de Ordenes de Servicio (Vista)
// ==============================================================================

$pageTitle = "TechFix - Ordenes de Servicio";

// Datos simulados de servicios (Fase 2)
$servicios = [
    [
        'folio' => 101,
        'cliente' => 'Carlos Mendoza',
        'equipo' => 'Laptop HP Pavilion',
        'servicio' => 'Mantenimiento preventivo y formateo',
        'fecha' => '2026-09-28',
        'estado' => 'En proceso',
        'badge' => 'bg-warning text-dark'
    ],
    [
        'folio' => 102,
        'cliente' => 'María Fernández',
        'equipo' => 'PC Escritorio Ryzen 5',
        'servicio' => 'Cambio de fuente de poder',
        'fecha' => '2026-09-25',
        'estado' => 'Completado',
        'badge' => 'bg-success'
    ],
    [
        'folio' => 103,
        'cliente' => 'Roberto Gómez',
        'equipo' => 'Impresora Epson L3150',
        'servicio' => 'Limpieza de cabezales',
        'fecha' => '2026-09-29',
        'estado' => 'Pendiente',
        'badge' => 'bg-secondary'
    ]
];

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tools me-2 text-primary"></i>Ordenes de Servicio</h2>
            <p class="text-muted mb-0">Administración y seguimiento de servicios técnicos.</p>
        </div>
        <button class="btn btn-primary shadow-sm" disabled>
            <i class="bi bi-plus-lg me-2"></i>Nueva Orden
        </button>
    </div>

    <!-- Tabla de Ordenes de Servicio -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Equipo / Dispositivo</th>
                            <th>Servicio Solicitado</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $srv): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $srv['folio']; ?></td>
                                <td><?php echo $srv['cliente']; ?></td>
                                <td><?php echo $srv['equipo']; ?></td>
                                <td><?php echo $srv['servicio']; ?></td>
                                <td><?php echo $srv['fecha']; ?></td>
                                <td>
                                    <span class="badge <?php echo $srv['badge']; ?>">
                                        <?php echo $srv['estado']; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1" disabled><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-pencil"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
