<?php
// ==============================================================================
// Componente Reutilizable: Pie de Página y Scripts (Footer)
// ==============================================================================
$currentYear = date("Y");
?>
    <!-- Pie de Página -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0 small">&copy; <?php echo $currentYear; ?> TechFix - Sistema de Gestión de Servicios Técnicos.</p>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
