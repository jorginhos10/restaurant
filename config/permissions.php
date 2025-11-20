<?php
class Permissions {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Verificar si el usuario tiene permiso para acceder a una sección por ID
     */
    public function checkPermission($seccion_id) {
        // Si no hay usuario logueado, redirigir al login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
        
        // El Super Admin (ID 1) tiene acceso completo
        if ($_SESSION['user_id'] == 1) {
            return true;
        }
        
        // Verificar si el usuario tiene permiso para esta sección por ID
        $query = "SELECT up.id 
                 FROM usuario_permisos up 
                 WHERE up.usuario_id = :user_id AND up.seccion_id = :seccion_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':seccion_id', $seccion_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Redirigir si no tiene permisos por ID
     */
    public function requirePermission($seccion_id) {
        if (!$this->checkPermission($seccion_id)) {
            $this->redirectNoPermission();
        }
    }
    
    /**
     * Obtener todos los IDs de permisos del usuario actual
     */
    public function getUserPermissionIds() {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        
        // Super Admin tiene todos los permisos
        if ($_SESSION['user_id'] == 1) {
            $query = "SELECT id_seccion FROM secciones";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        
        $query = "SELECT seccion_id FROM usuario_permisos WHERE usuario_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    /**
     * Redirección cuando no tiene permisos
     */
    private function redirectNoPermission() {
        $_SESSION['error'] = "No tienes permisos para acceder a esta sección";
        header('Location: ' . BASE_URL . 'error/403');
        exit();
    }
    
    /**
     * Verificar permiso y mostrar error (para AJAX/API) por ID
     */
    public function checkPermissionAPI($seccion_id) {
        if (!$this->checkPermission($seccion_id)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ]);
            exit();
        }
    }
    
    /**
     * Cargar menú según permisos del usuario (usando IDs)
     */
    public function getMenuItems() {
        $menuItems = [
            'dashboard' => [
                'nombre' => 'Dashboard',
                'icono' => '📊',
                'url_icon' => BASE_URL . 'assets/images/icons/dashboard.png',
                'url' => BASE_URL . 'dashboard',
                'requiere_permiso_id' => 1  // ID del permiso Dashboard
            ],
            'usuarios' => [
                'nombre' => 'Gestión de Usuarios',
                'icono' => '👥',
                'url_icon' => BASE_URL . 'assets/images/icons/users.png',
                'url' => BASE_URL . 'usuarios',
                'requiere_permiso_id' => 2  // ID del permiso Gestión de Usuarios
            ],
            'mesas' => [
                'nombre' => 'Gestión de Mesas',
                'icono' => '🍽️',
                'url_icon' => BASE_URL . 'assets/images/icons/tables.png',
                'url' => BASE_URL . 'mesas',
                'requiere_permiso_id' => 3  // ID del permiso Gestión de Mesas
            ],
            'menu' => [
                'nombre' => 'Gestión de Menú',
                'icono' => '📋',
                'url_icon' => BASE_URL . 'assets/images/icons/menu.png',
                'url' => BASE_URL . 'menu',
                'requiere_permiso_id' => 4  // ID del permiso Gestión de Menú
            ],
            'ventas' => [
                'nombre' => 'Gestión de Ventas',
                'icono' => '💳',
                'url_icon' => BASE_URL . 'assets/images/icons/sales.png',
                'url' => BASE_URL . 'ventas',
                'requiere_permiso_id' => 5  // ID del permiso Gestión de Ventas
            ],
            'inventario' => [
                'nombre' => 'Control de Inventario',
                'icono' => '📦',
                'url_icon' => BASE_URL . 'assets/images/icons/inventory.png',
                'url' => BASE_URL . 'inventario',
                'requiere_permiso_id' => 6  // ID del permiso Inventario
            ],
            'reportes' => [
                'nombre' => 'Reportes y Estadísticas',
                'icono' => '📈',
                'url_icon' => BASE_URL . 'assets/images/icons/reports.png',
                'url' => BASE_URL . 'reportes',
                'requiere_permiso_id' => 7  // ID del permiso Reportes y Estadísticas
            ],
            'configuracion' => [
                'nombre' => 'Configuración del Sistema',
                'icono' => '⚙️',
                'url_icon' => BASE_URL . 'assets/images/icons/settings.png',
                'url' => BASE_URL . 'configuracion',
                'requiere_permiso_id' => 8  // ID del permiso Configuración del Sistema
            ]
        ];
        
        $userPermissionIds = $this->getUserPermissionIds();
        $filteredMenu = [];
        
        foreach ($menuItems as $key => $item) {
            // Super Admin ve todo, otros usuarios solo lo que tienen permiso por ID
            if ($_SESSION['user_id'] == 1 || in_array($item['requiere_permiso_id'], $userPermissionIds)) {
                $filteredMenu[$key] = $item;
            }
        }
        
        return $filteredMenu;
    }
    
    /**
     * Obtener nombre de sección por ID (para logs o mensajes)
     */
    public function getSeccionNombre($seccion_id) {
        $query = "SELECT nombre FROM secciones WHERE id_seccion = :id_seccion";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_seccion', $seccion_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['nombre'];
        }
        
        return 'Sección Desconocida';
    }
}
?>