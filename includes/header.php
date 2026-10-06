<?php
// ==============================================================================
// Componente Reutilizable: Encabezado HTML y recursos del sistema (Header)
// ==============================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pageTitle)) {
    $pageTitle = "TechFix - Gestión de Servicios Técnicos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .card {
            border-radius: 12px;
        }
        .btn {
            border-radius: 8px;
        }
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php 
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
    ?>
<?php endif; ?>
