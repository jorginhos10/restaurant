// Gestión de usuarios - Versión corregida para permisos
console.log('✅ userManagement.js cargado');

// Configuración global
const API_BASE = window.BASE_URL || '';

// Agregar estilos CSS para los switches mejorados
const switchStyles = document.createElement('style');
switchStyles.textContent = `
    .permission-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }
    
    .permission-item:hover {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px 10px;
    }
    
    .permission-item:last-child {
        border-bottom: none;
    }
    
    .permission-info {
        flex: 1;
    }
    
    .permission-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
        margin-bottom: 2px;
    }
    
    .permission-description {
        font-size: 0.85rem;
        color: #7f8c8d;
        line-height: 1.3;
    }
    
    /* Switch moderno */
    .permission-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }
    
    .permission-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .permission-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.3);
    }
    
    .permission-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    
    .permission-switch input:checked + .permission-slider {
        background-color: #2196F3;
    }
    
    .permission-switch input:checked + .permission-slider:before {
        transform: translateX(30px);
    }
    
    .permission-switch input:focus + .permission-slider {
        box-shadow: 0 0 1px #2196F3, 0 0 0 3px rgba(33, 150, 243, 0.2);
    }
    
    /* Estados de loading */
    .status-badge.loading {
        background: #95a5a6 !important;
        color: white !important;
        border: 1px solid #7f8c8d !important;
        cursor: wait !important;
    }
    
    .status-badge.super-admin {
        background: #27ae60 !important;
        color: white !important;
        border-color: #27ae60 !important;
        font-weight: 600;
    }
    
    /* Loading spinner */
    .icon-loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(switchStyles);

// Funciones básicas de modal
function openUserModal() {
    console.log('Abriendo modal de usuario');
    document.getElementById('userModal').style.display = 'block';
}

function closeUserModal() {
    document.getElementById('userModal').style.display = 'none';
}

function openEditModal(userId) {
    console.log('Abriendo edición para usuario:', userId);
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    const cells = userRow.querySelectorAll('td');
    
    document.getElementById('editUserId').value = userId;
    document.getElementById('editNombre').value = cells[2].textContent;
    document.getElementById('editApellido').value = cells[3].textContent;
    document.getElementById('editUsuario').value = cells[4].textContent;
    document.getElementById('editTelefono').value = cells[5].textContent;
    document.getElementById('editDireccion').value = cells[6].textContent;
    
    document.getElementById('editUserModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

function openChangePasswordModal(userId) {
    console.log('Abriendo cambio de contraseña para:', userId);
    document.getElementById('passwordUserId').value = userId;
    document.getElementById('changePasswordModal').style.display = 'block';
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
}

function openPermissionsModal(userId) {
    console.log('Abriendo permisos para:', userId);
    document.getElementById('permissionsUserId').value = userId;
    loadPermissions(userId);
    document.getElementById('permissionsModal').style.display = 'block';
}

function closePermissionsModal() {
    document.getElementById('permissionsModal').style.display = 'none';
}

// Cerrar modales
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});

// Buscador
function filterUsers() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('usersTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            if (cells[j] && cells[j].textContent.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

// Cambiar estado del usuario
function toggleUserStatus(userId, currentStatus) {
    console.log('Cambiando estado:', userId, currentStatus);
    
    const newStatus = currentStatus ? 0 : 1;
    const action = newStatus ? 'activar' : 'desactivar';
    
    if (confirm(`¿Estás seguro de ${action} este usuario?`)) {
        const statusElement = document.querySelector(`tr[data-user-id="${userId}"] .status-badge`);
        const originalText = statusElement.textContent;
        
        statusElement.textContent = 'Cargando...';
        statusElement.classList.add('loading');
        
        fetch(`${API_BASE}usuarios/toggleStatus`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: userId,
                estado: newStatus
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                statusElement.textContent = newStatus ? 'Activo' : 'Inactivo';
                statusElement.className = `status-badge ${newStatus ? 'active' : 'inactive'} status-toggle`;
                statusElement.classList.remove('loading');
                statusElement.setAttribute('onclick', `toggleUserStatus(${userId}, ${newStatus})`);
                showNotification('✅ ' + data.message, 'success');
            } else {
                statusElement.textContent = originalText;
                statusElement.classList.remove('loading');
                showNotification('❌ ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            statusElement.textContent = originalText;
            statusElement.classList.remove('loading');
            showNotification('❌ Error de conexión', 'error');
        });
    }
}

// Crear usuario
function saveUser(event) {
    event.preventDefault();
    console.log('Guardando usuario...');
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.innerHTML = '<div class="icon-loading"></div> Guardando...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch(`${API_BASE}usuarios/create`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            closeUserModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Actualizar usuario
function updateUser(event) {
    event.preventDefault();
    console.log('Actualizando usuario...');
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.innerHTML = '<div class="icon-loading"></div> Actualizando...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch(`${API_BASE}usuarios/update`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            closeEditModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Cambiar contraseña
function changePassword(event) {
    event.preventDefault();
    console.log('Cambiando contraseña...');
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.innerHTML = '<div class="icon-loading"></div> Cambiando...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch(`${API_BASE}usuarios/changePassword`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            closeChangePasswordModal();
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Eliminar usuario
function confirmDelete(userId) {
    console.log('Eliminando usuario:', userId);
    
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    const userName = userRow.querySelector('td:nth-child(3)').textContent + ' ' + 
                     userRow.querySelector('td:nth-child(4)').textContent;
    
    if (confirm(`¿Estás seguro de eliminar al usuario "${userName}"?`)) {
        const deleteBtn = document.querySelector(`tr[data-user-id="${userId}"] .delete-btn`);
        const originalHTML = deleteBtn.innerHTML;
        
        deleteBtn.innerHTML = '<div class="icon-loading"></div>';
        deleteBtn.disabled = true;
        
        fetch(`${API_BASE}usuarios/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: userId })
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('✅ ' + data.message, 'success');
                userRow.style.opacity = '0';
                setTimeout(() => userRow.remove(), 300);
            } else {
                showNotification('❌ ' + data.message, 'error');
                deleteBtn.innerHTML = originalHTML;
                deleteBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('❌ Error de conexión', 'error');
            deleteBtn.innerHTML = originalHTML;
            deleteBtn.disabled = false;
        });
    }
}

// PERMISOS - Funciones corregidas
function loadPermissions(userId) {
    const container = document.getElementById('permissionsContainer');
    container.innerHTML = `
        <div style="text-align: center; padding: 30px;">
            <div class="icon-loading" style="margin: 0 auto 10px;"></div>
            <div>Cargando permisos...</div>
        </div>
    `;
    
    console.log(`📋 Cargando permisos para usuario ID: ${userId}`);
    
    fetch(`${API_BASE}usuarios/getPermissions?user_id=${userId}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            console.log('📊 Respuesta de permisos:', data);
            
            if (data.success) {
                displayPermissions(data.permissions, data.userPermissions);
            } else {
                container.innerHTML = `<div class="alert alert-error">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('❌ Error cargando permisos:', error);
            container.innerHTML = `
                <div class="alert alert-error">
                    Error al cargar los permisos. Verifique la conexión.
                </div>
            `;
        });
}

function displayPermissions(permissions, userPermissions) {
    const container = document.getElementById('permissionsContainer');
    
    if (!permissions || permissions.length === 0) {
        container.innerHTML = `
            <div class="alert alert-warning">
                No hay permisos configurados en el sistema.
            </div>
        `;
        return;
    }
    
    console.log(`🎯 Permisos disponibles: ${permissions.length}`);
    console.log(`🔐 Permisos activos del usuario:`, userPermissions);
    
    let html = '';
    permissions.forEach(perm => {
        // CORREGIDO: Convertir ambos a número para comparación correcta
        const permId = parseInt(perm.id_seccion);
        const isActive = Array.isArray(userPermissions) && userPermissions.some(activeId => parseInt(activeId) === permId);
        
        console.log(`Permiso ${perm.nombre} (ID: ${permId}): ${isActive ? 'ACTIVO' : 'INACTIVO'}`);
        
        // Crear una descripción basada en el nombre del permiso
        const description = getPermissionDescription(perm.nombre);
        
        html += `
            <div class="permission-item">
                <div class="permission-info">
                    <div class="permission-name">${perm.nombre}</div>
                    <div class="permission-description">${description}</div>
                </div>
                <label class="permission-switch" title="${isActive ? 'Desactivar' : 'Activar'} permiso">
                    <input type="checkbox" name="permisos[]" value="${perm.id_seccion}" ${isActive ? 'checked' : ''}>
                    <span class="permission-slider"></span>
                </label>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Debug: mostrar conteo de permisos activos
    const activeCount = Array.isArray(userPermissions) ? userPermissions.length : 0;
    console.log(`✅ Permisos activos cargados: ${activeCount}/${permissions.length}`);
}

function savePermissions(event) {
    event.preventDefault();
    
    const form = event.target;
    const userId = document.getElementById('permissionsUserId').value;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.innerHTML = '<div class="icon-loading"></div> Guardando...';
    submitBtn.disabled = true;
    
    // Obtener los permisos seleccionados (switches ACTIVOS)
    const selectedPermissions = [];
    const checkboxes = document.querySelectorAll('#permissionsForm input[type="checkbox"]:checked');
    checkboxes.forEach(checkbox => {
        selectedPermissions.push(checkbox.value);
    });
    
    console.log(`💾 Guardando permisos para usuario ${userId}:`, selectedPermissions);
    
    const requestData = {
        user_id: userId,
        permisos: selectedPermissions
    };
    
    fetch(`${API_BASE}usuarios/savePermissions`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        return response.json();
    })
    .then(data => {
        console.log('📨 Respuesta del guardado:', data);
        
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            if (data.debug) {
                console.log('🔍 Debug guardado:', data.debug);
            }
            closePermissionsModal();
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error guardando permisos:', error);
        showNotification('❌ Error de conexión al guardar permisos', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Función para generar descripciones de permisos
function getPermissionDescription(permissionName) {
    const descriptions = {
        'Gestión de Usuarios': 'Permite crear, editar y eliminar usuarios del sistema',
        'Gestión de Mesas': 'Control sobre la configuración y estado de las mesas',
        'Gestión de Menú': 'Administración de productos, categorías y precios',
        'Gestión de Ventas': 'Acceso a ventas, facturación y transacciones',
        'Reportes y Estadísticas': 'Visualización de reportes y análisis de datos',
        'Configuración del Sistema': 'Ajustes generales y configuración del restaurante',
        'Inventario': 'Control de stock y gestión de productos',
        'Cocina': 'Acceso al módulo de pedidos en cocina',
        'Caja': 'Operaciones de caja y cierre diario',
        'Delivery': 'Gestión de pedidos para delivery'
    };
    
    return descriptions[permissionName] || 'Permiso del sistema';
}

// Notificaciones
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type} show`;
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Funciones de ayuda
function showSuperAdminWarning() {
    alert('⚠️ El usuario Super Admin no puede ser modificado');
}

function showDeleteWarning() {
    const activeElement = document.activeElement;
    const userRow = activeElement.closest('tr');
    if (!userRow) return;
    
    const userId = parseInt(userRow.querySelector('.user-id').textContent);
    
    if (userId === 1) {
        alert('❌ El usuario Super Admin no puede ser eliminado');
    } else {
        alert('❌ No puedes eliminarte a ti mismo');
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ userManagement.js inicializado');
    
    // Agregar event listeners
    const userForm = document.getElementById('userForm');
    if (userForm) userForm.addEventListener('submit', saveUser);
    
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) editUserForm.addEventListener('submit', updateUser);
    
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) changePasswordForm.addEventListener('submit', changePassword);
    
    const permissionsForm = document.getElementById('permissionsForm');
    if (permissionsForm) permissionsForm.addEventListener('submit', savePermissions);
    
    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUserModal();
            closeEditModal();
            closeChangePasswordModal();
            closePermissionsModal();
        }
    });
});