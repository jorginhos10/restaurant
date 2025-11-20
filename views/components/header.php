<?php
// Obtener datos del comercio para el header sin depender del controller
function getDatosComercioHeader() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Verificar si la tabla existe
        $query = "SHOW TABLES LIKE 'configuracion_comercio'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $tablaExiste = $stmt->rowCount() > 0;
        
        if (!$tablaExiste) {
            return [
                'comercio_nombre' => 'Mi Restaurante',
                'comercio_logo' => ''
            ];
        }
        
        // Obtener datos desde la tabla
        $datos = [
            'comercio_nombre' => 'Mi Restaurante',
            'comercio_logo' => ''
        ];
        
        $query = "SELECT clave, valor FROM configuracion_comercio WHERE clave IN ('comercio_nombre', 'comercio_logo')";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['clave'] === 'comercio_nombre') {
                $datos['comercio_nombre'] = $row['valor'] ?: 'Mi Restaurante';
            }
            if ($row['clave'] === 'comercio_logo') {
                $datos['comercio_logo'] = $row['valor'] ?: '';
            }
        }
        
        return $datos;
    } catch (Exception $e) {
        return [
            'comercio_nombre' => 'Mi Restaurante',
            'comercio_logo' => ''
        ];
    }
}

$datosComercio = getDatosComercioHeader();

// Obtener la foto actual del usuario para evitar cache
$fotoUsuario = !empty($_SESSION['user_photo']) ? $_SESSION['user_photo'] : 'default.png';
$fotoConTimestamp = $fotoUsuario . '?t=' . time(); // Agregar timestamp para evitar cache
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($datosComercio['comercio_nombre']); ?> - Sistema</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sidebar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/usuarios.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/configuracion.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <!-- Logo y nombre del restaurante -->
            <div class="header-brand">
                <?php if (!empty($datosComercio['comercio_logo'])): ?>
                    <img src="<?php echo BASE_URL; ?>assets/images/logos/<?php echo htmlspecialchars($datosComercio['comercio_logo']); ?>" 
                         alt="<?php echo htmlspecialchars($datosComercio['comercio_nombre']); ?>" 
                         class="header-logo"
                         onerror="this.style.display='none'">
                <?php endif; ?>
                <div class="header-title">
                    <a href="<?php echo BASE_URL; ?>dashboard" style="text-decoration: none; color: inherit;">
                        <h1><?php echo htmlspecialchars($datosComercio['comercio_nombre']); ?></h1>
                    </a>
                    <span class="header-subtitle">Sistema de Gestión</span>
                </div>
            </div>

            <!-- Información del usuario con menú desplegable -->
            <div class="user-menu-container">
                <div class="user-info" id="userMenuTrigger">
                    <div class="user-avatar-h">
                        <img src="<?php echo BASE_URL; ?>assets/images/usuarios/<?php echo $fotoConTimestamp; ?>" 
                             alt="<?php echo $_SESSION['user_name']; ?>"
                             class="header-user-avatar"
                             onerror="this.src='<?php echo BASE_URL; ?>assets/images/default.png?t=<?php echo time(); ?>'"
                             id="headerUserAvatar">
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?php echo $_SESSION['user_fullname']; ?></span>
                        <span class="user-role">Administrador</span>
                    </div>
                    <div class="user-dropdown-arrow">▼</div>
                </div>

                <!-- Menú desplegable -->
                <div class="user-dropdown-menu" id="userDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            <img src="<?php echo BASE_URL; ?>assets/images/usuarios/<?php echo $fotoConTimestamp; ?>" 
                                 alt="<?php echo $_SESSION['user_name']; ?>"
                                 class="dropdown-user-avatar"
                                 onerror="this.src='<?php echo BASE_URL; ?>assets/images/default.png?t=<?php echo time(); ?>'"
                                 id="dropdownUserAvatar">
                        </div>
                        <div class="dropdown-user-info">
                            <strong><?php echo $_SESSION['user_fullname']; ?></strong>
                            <span><?php echo $_SESSION['user_name']; ?></span>
                        </div>
                    </div>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="<?php echo BASE_URL; ?>usuarios/perfil" class="dropdown-item">
                        <span class="dropdown-icon">👤</span>
                        <span>Mi Perfil</span>
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>configuracion" class="dropdown-item">
                        <span class="dropdown-icon">⚙️</span>
                        <span>Configuración</span>
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="<?php echo BASE_URL; ?>auth/logout" class="dropdown-item logout-item">
                        <span class="dropdown-icon">🚪</span>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <script>
    // Funcionalidad del menú desplegable del usuario
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuTrigger = document.getElementById('userMenuTrigger');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        
        if (userMenuTrigger && userDropdownMenu) {
            // Alternar menú al hacer clic
            userMenuTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('show');
            });
            
            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', function() {
                userDropdownMenu.classList.remove('show');
            });
            
            // Prevenir que el clic dentro del menú lo cierre
            userDropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Función global para actualizar la foto en el header
        window.updateHeaderPhoto = function(newPhotoUrl) {
            console.log('🔄 Actualizando foto en header:', newPhotoUrl);
            
            // Actualizar avatar principal en el header
            const headerAvatar = document.getElementById('headerUserAvatar');
            if (headerAvatar) {
                headerAvatar.src = newPhotoUrl + '?t=' + new Date().getTime();
            }
            
            // Actualizar avatar en el dropdown
            const dropdownAvatar = document.getElementById('dropdownUserAvatar');
            if (dropdownAvatar) {
                dropdownAvatar.src = newPhotoUrl + '?t=' + new Date().getTime();
            }
        };

        // Escuchar eventos personalizados para actualizar la foto
        document.addEventListener('photoUpdated', function(e) {
            if (e.detail && e.detail.photoUrl) {
                window.updateHeaderPhoto(e.detail.photoUrl);
            }
        });

        // También escuchar cambios en el almacenamiento local (para sincronización entre pestañas)
        window.addEventListener('storage', function(e) {
            if (e.key === 'userPhotoUpdate' && e.newValue) {
                const photoData = JSON.parse(e.newValue);
                if (photoData.photoUrl) {
                    window.updateHeaderPhoto(photoData.photoUrl);
                }
            }
        });
    });

    // Función para notificar a otras pestañas sobre el cambio de foto
    function notifyPhotoUpdate(photoUrl) {
        // Disparar evento personalizado
        const event = new CustomEvent('photoUpdated', {
            detail: { photoUrl: photoUrl }
        });
        document.dispatchEvent(event);
        
        // Usar localStorage para sincronizar entre pestañas
        localStorage.setItem('userPhotoUpdate', JSON.stringify({
            photoUrl: photoUrl,
            timestamp: new Date().getTime()
        }));
        
        // Limpiar después de un tiempo
        setTimeout(() => {
            localStorage.removeItem('userPhotoUpdate');
        }, 1000);
    }
    </script>
</body>
</html>