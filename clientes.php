<?php
// ==============================================================================
// Módulo de Gestión de Clientes (Vista)
// ==============================================================================

$pageTitle = "TechFix - Gestión de Clientes";

// Datos simulados de clientes (Fase 2)
$clientes = [
    [
        'id' => 1,
        'nombre' => 'Carlos Mendoza',
        'email' => 'carlos.mendoza@email.com',
        'telefono' => '555-0192',
        'ciudad' => 'Ciudad de México',
        'servicios_activos' => 1
    ],
    [
        'id' => 2,
        'nombre' => 'María Fernández',
        'email' => 'maria.fernandez@email.com',
        'telefono' => '555-0143',
        'ciudad' => 'Guadalajara',
        'servicios_activos' => 0
    ],
    [
        'id' => 3,
        'nombre' => 'Roberto Gómez',
        'email' => 'roberto.gomez@email.com',
        'telefono' => '555-0188',
        'ciudad' => 'Monterrey',
        'servicios_activos' => 1
    ]
];

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-people me-2 text-primary"></i>Gestión de Clientes</h2>
            <p class="text-muted mb-0">Directorio y registro de clientes del sistema.</p>
        </div>
        <button class="btn btn-primary shadow-sm" disabled>
            <i class="bi bi-person-plus me-2"></i>Registrar Cliente
        </button>
    </div>

    <!-- Tabla de Clientes -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Ciudad</th>
                            <th>Servicios Activos</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo $cliente['id']; ?></td>
                                <td class="fw-bold"><?php echo $cliente['nombre']; ?></td>
                                <td><?php echo $cliente['email']; ?></td>
                                <td><?php echo $cliente['telefono']; ?></td>
                                <td><?php echo $cliente['ciudad']; ?></td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        <?php echo $cliente['servicios_activos']; ?> Activo(s)
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1" disabled><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" disabled><i class="bi bi-trash"></i></button>
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
