<?php
require_once 'config/permissions.php';

class DashboardController {
    public function index() {
        // Verificar permiso para acceder al dashboard por ID (ID 1)
        $permissions = new Permissions();
        $permissions->requirePermission(1); // ID de Dashboard
        
        // Lógica del dashboard...
        require_once 'views/components/header.php';
        require_once 'views/components/sidebar.php';
        require_once 'views/dashboard/index.php';
        require_once 'views/components/footer.php';
    }
}
?>