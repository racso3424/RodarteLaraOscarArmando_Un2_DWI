<?php
// ==============================================================================
// Componente Reutilizable: Barra de Navegación Principal (Navbar)
// ==============================================================================
$activePage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-wrench-adjustable-circle me-2"></i>TechFix
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($activePage == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house-door me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($activePage == 'clientes.php') ? 'active' : ''; ?>" href="clientes.php">
                        <i class="bi bi-people me-1"></i>Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($activePage == 'servicios.php') ? 'active' : ''; ?>" href="servicios.php">
                        <i class="bi bi-tools me-1"></i>Servicios
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
