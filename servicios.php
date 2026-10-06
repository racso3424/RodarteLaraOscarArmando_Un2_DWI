<?php
// ==============================================================================
// Módulo de Ordenes de Servicio (CRUD Completo con MySQL)
// ==============================================================================

require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "TechFix - Ordenes de Servicio";

// ------------------------------------------------------------------------------
// Procesamiento de Formulario (POST Actions)
// ------------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $pdo) {
    $action = $_POST['action'];

    try {
        if ($action === 'crear') {
            $cliente_id    = (int)($_POST['cliente_id'] ?? 0);
            $equipo        = trim($_POST['equipo'] ?? '');
            $servicio      = trim($_POST['servicio'] ?? '');
            $precio        = (float)($_POST['precio'] ?? 0.00);
            $estado        = trim($_POST['estado'] ?? 'Pendiente');
            $fecha_ingreso = trim($_POST['fecha_ingreso'] ?? date('Y-m-d'));

            if ($cliente_id > 0 && !empty($equipo) && !empty($servicio)) {
                $stmt = $pdo->prepare("
                    INSERT INTO servicios (cliente_id, equipo, servicio, precio, estado, fecha_ingreso) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$cliente_id, $equipo, $servicio, $precio, $estado, $fecha_ingreso]);
                $newId = $pdo->lastInsertId();

                $_SESSION['flash_message'] = "¡Orden de Servicio #{$newId} registrada con éxito!";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "Debe seleccionar un cliente y especificar el equipo y detalle del servicio.";
                $_SESSION['flash_type'] = "warning";
            }
        } elseif ($action === 'editar') {
            $id            = (int)($_POST['id'] ?? 0);
            $cliente_id    = (int)($_POST['cliente_id'] ?? 0);
            $equipo        = trim($_POST['equipo'] ?? '');
            $servicio      = trim($_POST['servicio'] ?? '');
            $precio        = (float)($_POST['precio'] ?? 0.00);
            $estado        = trim($_POST['estado'] ?? 'Pendiente');
            $fecha_ingreso = trim($_POST['fecha_ingreso'] ?? date('Y-m-d'));

            if ($id > 0 && $cliente_id > 0 && !empty($equipo)) {
                $stmt = $pdo->prepare("
                    UPDATE servicios 
                    SET cliente_id = ?, equipo = ?, servicio = ?, precio = ?, estado = ?, fecha_ingreso = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$cliente_id, $equipo, $servicio, $precio, $estado, $fecha_ingreso, $id]);

                $_SESSION['flash_message'] = "Orden de Servicio #{$id} actualizada correctamente.";
                $_SESSION['flash_type'] = "info";
            }
        } elseif ($action === 'eliminar') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id > 0) {
                $stmt = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
                $stmt->execute([$id]);

                $_SESSION['flash_message'] = "Orden de servicio #{$id} eliminada.";
                $_SESSION['flash_type'] = "danger";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Error en la base de datos: " . $e->getMessage();
        $_SESSION['flash_type'] = "danger";
    }

    header("Location: servicios.php");
    exit;
}

// ------------------------------------------------------------------------------
// Obtención de Datos de la BD
// ------------------------------------------------------------------------------
$filterEstado = trim($_GET['estado'] ?? '');
$search       = trim($_GET['search'] ?? '');
$servicios    = [];
$listaClientes= [];

if ($pdo) {
    try {
        // Obtener clientes para el desplegable del modal
        $stmtC = $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
        $listaClientes = $stmtC->fetchAll();

        // Construir consulta filtrada
        $sql = "
            SELECT s.*, c.nombre AS cliente_nombre, c.email AS cliente_email, c.telefono AS cliente_telefono
            FROM servicios s
            JOIN clientes c ON s.cliente_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filterEstado)) {
            $sql .= " AND s.estado = ?";
            $params[] = $filterEstado;
        }

        if (!empty($search)) {
            $sql .= " AND (s.equipo LIKE ? OR s.servicio LIKE ? OR c.nombre LIKE ? OR s.id = ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = is_numeric($search) ? (int)$search : 0;
        }

        $sql .= " ORDER BY s.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $servicios = $stmt->fetchAll();
    } catch (PDOException $e) {
        $dbErrorMsg = $e->getMessage();
    }
}

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

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container my-4">
    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tools me-2 text-primary"></i>Ordenes de Servicio</h2>
            <p class="text-muted mb-0">Administración, seguimiento y estado de servicios técnicos.</p>
        </div>
        <button class="btn btn-primary shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCrearServicio" <?php echo (empty($listaClientes) || !$pdo) ? 'disabled' : ''; ?>>
            <i class="bi bi-plus-lg me-2"></i>Nueva Orden
        </button>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="servicios.php" class="row g-2 align-items-center">
                <div class="col-md-5 col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por folio, cliente, equipo o detalle..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <select name="estado" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">-- Filtrar por Estado (Todos) --</option>
                        <option value="Pendiente" <?php echo $filterEstado === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="En proceso" <?php echo $filterEstado === 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
                        <option value="Completado" <?php echo $filterEstado === 'Completado' ? 'selected' : ''; ?>>Completado</option>
                        <option value="Cancelado" <?php echo $filterEstado === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100 fw-semibold">Filtrar</button>
                    <?php if (!empty($search) || !empty($filterEstado)): ?>
                        <a href="servicios.php" class="btn btn-outline-secondary" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
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
                            <th>Precio</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($servicios)): ?>
                            <?php foreach ($servicios as $srv): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $srv['id']; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($srv['cliente_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($srv['equipo']); ?></td>
                                    <td class="text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($srv['servicio']); ?></td>
                                    <td class="fw-bold text-success">$<?php echo number_format($srv['precio'], 2); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($srv['fecha_ingreso'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo getBadgeClass($srv['estado']); ?>">
                                            <?php echo htmlspecialchars($srv['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button 
                                            class="btn btn-sm btn-outline-info me-1 btn-ver-servicio" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalVerServicio"
                                            data-id="<?php echo $srv['id']; ?>"
                                            data-cliente="<?php echo htmlspecialchars($srv['cliente_nombre']); ?>"
                                            data-email="<?php echo htmlspecialchars($srv['cliente_email']); ?>"
                                            data-telefono="<?php echo htmlspecialchars($srv['cliente_telefono']); ?>"
                                            data-equipo="<?php echo htmlspecialchars($srv['equipo']); ?>"
                                            data-servicio="<?php echo htmlspecialchars($srv['servicio']); ?>"
                                            data-precio="$<?php echo number_format($srv['precio'], 2); ?>"
                                            data-fecha="<?php echo date('d/m/Y', strtotime($srv['fecha_ingreso'])); ?>"
                                            data-estado="<?php echo htmlspecialchars($srv['estado']); ?>"
                                            title="Ver detalle">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button 
                                            class="btn btn-sm btn-outline-primary me-1 btn-edit-servicio" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarServicio"
                                            data-id="<?php echo $srv['id']; ?>"
                                            data-cliente_id="<?php echo $srv['cliente_id']; ?>"
                                            data-equipo="<?php echo htmlspecialchars($srv['equipo']); ?>"
                                            data-servicio="<?php echo htmlspecialchars($srv['servicio']); ?>"
                                            data-precio="<?php echo $srv['precio']; ?>"
                                            data-estado="<?php echo htmlspecialchars($srv['estado']); ?>"
                                            data-fecha="<?php echo $srv['fecha_ingreso']; ?>"
                                            title="Editar orden">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button 
                                            class="btn btn-sm btn-outline-danger btn-delete-servicio" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarServicio"
                                            data-id="<?php echo $srv['id']; ?>"
                                            title="Eliminar orden">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-tools fs-1 d-block mb-2"></i>
                                    <?php echo (!empty($search) || !empty($filterEstado)) ? 'No se encontraron órdenes que coincidan con los filtros.' : 'No hay órdenes de servicio registradas.'; ?>
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
<!-- MODALES (Crear, Editar, Ver Detalle, Eliminar) -->
<!-- ============================================================================== -->

<!-- Modal Crear Servicio -->
<div class="modal fade" id="modalCrearServicio" tabindex="-1" aria-labelledby="modalCrearServicioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="servicios.php">
                <input type="hidden" name="action" value="crear">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalCrearServicioLabel"><i class="bi bi-plus-circle me-2"></i>Nueva Orden de Servicio</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cliente *</label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">-- Seleccionar Cliente --</option>
                                <?php foreach ($listaClientes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Equipo / Dispositivo *</label>
                            <input type="text" name="equipo" class="form-control" placeholder="Ej. Laptop Dell XPS 15" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción del Servicio *</label>
                        <textarea name="servicio" class="form-control" rows="3" placeholder="Detalla las fallas o mantenimientos solicitados..." required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Precio Estimado ($)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" placeholder="0.00" value="0.00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="Pendiente" selected>Pendiente</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Completado">Completado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Fecha de Ingreso</label>
                            <input type="date" name="fecha_ingreso" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-save me-1"></i> Crear Orden de Servicio</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Servicio -->
<div class="modal fade" id="modalEditarServicio" tabindex="-1" aria-labelledby="modalEditarServicioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="servicios.php">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" id="edit_srv_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="modalEditarServicioLabel"><i class="bi bi-pencil-square me-2"></i>Editar Orden de Servicio</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cliente *</label>
                            <select name="cliente_id" id="edit_srv_cliente_id" class="form-select" required>
                                <?php foreach ($listaClientes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Equipo / Dispositivo *</label>
                            <input type="text" name="equipo" id="edit_srv_equipo" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción del Servicio *</label>
                        <textarea name="servicio" id="edit_srv_servicio" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" id="edit_srv_precio" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" id="edit_srv_estado" class="form-select">
                                <option value="Pendiente">Pendiente</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Completado">Completado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Fecha de Ingreso</label>
                            <input type="date" name="fecha_ingreso" id="edit_srv_fecha" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold"><i class="bi bi-check-lg me-1"></i> Actualizar Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle / Ticket de Servicio -->
<div class="modal fade" id="modalVerServicio" tabindex="-1" aria-labelledby="modalVerServicioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered border-0">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="modalVerServicioLabel"><i class="bi bi-receipt me-2"></i>Ticket de Servicio <span id="ver_folio"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="border-bottom pb-3 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Cliente:</small>
                            <strong id="ver_cliente" class="fs-6"></strong>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Estado:</small>
                            <span id="ver_estado" class="badge"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Correo:</small>
                            <span id="ver_email" class="small"></span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Teléfono:</small>
                            <span id="ver_telefono" class="small"></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Equipo / Dispositivo:</small>
                    <div id="ver_equipo" class="fw-bold fs-5 text-dark"></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Detalle del Servicio:</small>
                    <div id="ver_servicio" class="p-3 bg-light rounded border text-secondary"></div>
                </div>
                <div class="row align-items-center">
                    <div class="col-6">
                        <small class="text-muted d-block">Fecha de Registro:</small>
                        <span id="ver_fecha" class="fw-semibold"></span>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block">Precio Total:</small>
                        <span id="ver_precio" class="fs-4 fw-bold text-success"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Servicio -->
<div class="modal fade" id="modalEliminarServicio" tabindex="-1" aria-labelledby="modalEliminarServicioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="servicios.php">
                <input type="hidden" name="action" value="eliminar">
                <input type="hidden" name="id" id="delete_srv_id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="modalEliminarServicioLabel"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar Orden</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0 fs-5">¿Estás seguro de que deseas eliminar la orden de servicio <strong id="delete_srv_folio"></strong>?</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-semibold"><i class="bi bi-trash me-1"></i> Eliminar Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Escuchar click en botones de editar servicio
    document.querySelectorAll('.btn-edit-servicio').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('edit_srv_id').value = this.getAttribute('data-id');
            document.getElementById('edit_srv_cliente_id').value = this.getAttribute('data-cliente_id');
            document.getElementById('edit_srv_equipo').value = this.getAttribute('data-equipo');
            document.getElementById('edit_srv_servicio').value = this.getAttribute('data-servicio');
            document.getElementById('edit_srv_precio').value = this.getAttribute('data-precio');
            document.getElementById('edit_srv_estado').value = this.getAttribute('data-estado');
            document.getElementById('edit_srv_fecha').value = this.getAttribute('data-fecha');
        });
    });

    // Escuchar click en botones de ver detalle de servicio
    document.querySelectorAll('.btn-ver-servicio').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('ver_folio').textContent = '#' + this.getAttribute('data-id');
            document.getElementById('ver_cliente').textContent = this.getAttribute('data-cliente');
            document.getElementById('ver_email').textContent = this.getAttribute('data-email');
            document.getElementById('ver_telefono').textContent = this.getAttribute('data-telefono');
            document.getElementById('ver_equipo').textContent = this.getAttribute('data-equipo');
            document.getElementById('ver_servicio').textContent = this.getAttribute('data-servicio');
            document.getElementById('ver_precio').textContent = this.getAttribute('data-precio');
            document.getElementById('ver_fecha').textContent = this.getAttribute('data-fecha');

            const estado = this.getAttribute('data-estado');
            const badgeEl = document.getElementById('ver_estado');
            badgeEl.textContent = estado;
            badgeEl.className = 'badge ';
            if (estado === 'Completado') badgeEl.className += 'bg-success';
            else if (estado === 'En proceso') badgeEl.className += 'bg-warning text-dark';
            else if (estado === 'Cancelado') badgeEl.className += 'bg-danger';
            else badgeEl.className += 'bg-secondary';
        });
    });

    // Escuchar click en botones de eliminar servicio
    document.querySelectorAll('.btn-delete-servicio').forEach(function(button) {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('delete_srv_id').value = id;
            document.getElementById('delete_srv_folio').textContent = '#' + id;
        });
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>
