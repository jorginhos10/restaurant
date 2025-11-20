<?php
// Verificar permisos
$permissions = new Permissions();
$permissions->requirePermission(8); // ID de Configuración del Sistema
?>

<div class="configuracion-container">
    <div class="page-header">
        <div class="header-content">
            <h2>📋 Gestión de Menús</h2>
            <p>Crea y administra los menús de tu restaurante</p>
        </div>
        <button class="btn btn-primary" onclick="window.location.href='<?php echo BASE_URL; ?>configuracion/crear-menu'">
            ➕ Agregar Menú
        </button>
    </div>

    <!-- Notificación -->
    <div id="notification" class="notification"></div>

    <!-- Grid de Menús -->
    <div class="menus-grid" id="menusContainer">
        <div class="loading-state">Cargando menús...</div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div id="confirmModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Confirmar Eliminación</h3>
            <span class="close" onclick="cerrarConfirmModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p id="confirmMessage">¿Estás seguro de eliminar este menú?</p>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="cerrarConfirmModal()">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Sí, Eliminar</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/menu-config.css">
<script>
// Definir símbolo monetario global
const MONEDA_SIMBOLO = '<?php echo $comercio["comercio_simbolo_moneda"] ?? "$"; ?>';
</script>
<script src="<?php echo BASE_URL; ?>assets/js/menu-config.js"></script>