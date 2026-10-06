<?php
// ==============================================================================
// Módulo de Gestión de Clientes (CRUD Completo con MySQL)
// ==============================================================================

require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "TechFix - Gestión de Clientes";

// ------------------------------------------------------------------------------
// Procesamiento de Formulario (POST Actions)
// ------------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $pdo) {
    $action = $_POST['action'];

    try {
        if ($action === 'crear') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $ciudad   = trim($_POST['ciudad'] ?? '');

            if (!empty($nombre) && !empty($email)) {
                $stmt = $pdo->prepare("INSERT INTO clientes (nombre, email, telefono, ciudad) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $email, $telefono, $ciudad]);

                $_SESSION['flash_message'] = "¡Cliente '{$nombre}' registrado exitosamente!";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "El nombre y el correo electrónico son obligatorios.";
                $_SESSION['flash_type'] = "warning";
            }
        } elseif ($action === 'editar') {
            $id       = (int)($_POST['id'] ?? 0);
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $ciudad   = trim($_POST['ciudad'] ?? '');

            if ($id > 0 && !empty($nombre) && !empty($email)) {
                $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ?, ciudad = ? WHERE id = ?");
                $stmt->execute([$nombre, $email, $telefono, $ciudad, $id]);

                $_SESSION['flash_message'] = "Información del cliente actualizada correctamente.";
                $_SESSION['flash_type'] = "info";
            }
        } elseif ($action === 'eliminar') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id > 0) {
                $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
                $stmt->execute([$id]);

                $_SESSION['flash_message'] = "Cliente eliminado del sistema.";
                $_SESSION['flash_type'] = "danger";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Error en la base de datos: " . $e->getMessage();
        $_SESSION['flash_type'] = "danger";
    }

    header("Location: clientes.php");
    exit;
}

// ------------------------------------------------------------------------------
// Obtención de Clientes desde la BD
// ------------------------------------------------------------------------------
$search = trim($_GET['search'] ?? '');
$clientes = [];

if ($pdo) {
    try {
        if (!empty($search)) {
            $sql = "
                SELECT c.*, COUNT(CASE WHEN s.estado IN ('Pendiente', 'En proceso') THEN 1 END) AS servicios_activos
                FROM clientes c
                LEFT JOIN servicios s ON c.id = s.cliente_id
                WHERE c.nombre LIKE ? OR c.email LIKE ? OR c.ciudad LIKE ?
                GROUP BY c.id
                ORDER BY c.id DESC
            ";
            $searchTerm = "%{$search}%";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        } else {
            $sql = "
                SELECT c.*, COUNT(CASE WHEN s.estado IN ('Pendiente', 'En proceso') THEN 1 END) AS servicios_activos
                FROM clientes c
                LEFT JOIN servicios s ON c.id = s.cliente_id
                GROUP BY c.id
                ORDER BY c.id DESC
            ";
            $stmt = $pdo->query($sql);
        }
        $clientes = $stmt->fetchAll();
    } catch (PDOException $e) {
        $dbErrorMsg = $e->getMessage();
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container my-4">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-people me-2 text-primary"></i>Gestión de Clientes</h2>
            <p class="text-muted mb-0">Directorio, registro y edición de clientes en la base de datos.</p>
        </div>
        <button class="btn btn-primary shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCrearCliente" <?php echo !$pdo ? 'disabled' : ''; ?>>
            <i class="bi bi-person-plus me-2"></i>Registrar Cliente
        </button>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="clientes.php" class="row g-2 align-items-center">
                <div class="col-md-9 col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por nombre, correo o ciudad..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-3 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100 fw-semibold">Buscar</button>
                    <?php if (!empty($search)): ?>
                        <a href="clientes.php" class="btn btn-outline-secondary" title="Limpiar filtro"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
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
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clientes)): ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?php echo $cliente['id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($cliente['email']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($cliente['email']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['ciudad']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $cliente['servicios_activos'] > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border'; ?>">
                                            <?php echo $cliente['servicios_activos']; ?> Activo(s)
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button 
                                            class="btn btn-sm btn-outline-primary me-1 btn-edit-cliente" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarCliente"
                                            data-id="<?php echo $cliente['id']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($cliente['nombre']); ?>"
                                            data-email="<?php echo htmlspecialchars($cliente['email']); ?>"
                                            data-telefono="<?php echo htmlspecialchars($cliente['telefono']); ?>"
                                            data-ciudad="<?php echo htmlspecialchars($cliente['ciudad']); ?>"
                                            title="Editar cliente">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button 
                                            class="btn btn-sm btn-outline-danger btn-delete-cliente" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarCliente"
                                            data-id="<?php echo $cliente['id']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($cliente['nombre']); ?>"
                                            title="Eliminar cliente">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    <?php echo !empty($search) ? 'No se encontraron clientes que coincidan con la búsqueda.' : 'No hay clientes registrados en el sistema.'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================== -->
<!-- MODALES (Crear, Editar, Eliminar) -->
<!-- ============================================================================== -->

<!-- Modal Crear Cliente -->
<div class="modal fade" id="modalCrearCliente" tabindex="-1" aria-labelledby="modalCrearClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="clientes.php">
                <input type="hidden" name="action" value="crear">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalCrearClienteLabel"><i class="bi bi-person-plus me-2"></i>Registrar Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo *</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico *</label>
                        <input type="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Ej. 555-0199">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" placeholder="Ej. Ciudad de México">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-save me-1"></i> Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-labelledby="modalEditarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="clientes.php">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="modalEditarClienteLabel"><i class="bi bi-pencil-square me-2"></i>Editar Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo *</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico *</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" id="edit_telefono" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="ciudad" id="edit_ciudad" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold"><i class="bi bi-check-lg me-1"></i> Actualizar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Cliente -->
<div class="modal fade" id="modalEliminarCliente" tabindex="-1" aria-labelledby="modalEliminarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="clientes.php">
                <input type="hidden" name="action" value="eliminar">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="modalEliminarClienteLabel"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2 fs-5">¿Estás seguro de que deseas eliminar al cliente <strong id="delete_nombre"></strong>?</p>
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Esta acción también eliminará todas las órdenes de servicio asociadas a este cliente.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-semibold"><i class="bi bi-trash me-1"></i> Sí, Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Escuchar click en botones de editar cliente para poblar el modal
    document.querySelectorAll('.btn-edit-cliente').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_nombre').value = this.getAttribute('data-nombre');
            document.getElementById('edit_email').value = this.getAttribute('data-email');
            document.getElementById('edit_telefono').value = this.getAttribute('data-telefono');
            document.getElementById('edit_ciudad').value = this.getAttribute('data-ciudad');
        });
    });

    // Escuchar click en botones de eliminar cliente
    document.querySelectorAll('.btn-delete-cliente').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('delete_id').value = this.getAttribute('data-id');
            document.getElementById('delete_nombre').textContent = this.getAttribute('data-nombre');
        });
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>
