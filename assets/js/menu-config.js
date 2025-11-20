// Sistema de Configuración de Menús - Página Principal
console.log('✅ menu-config.js cargado');

// Variables globales
let menus = [];
let menuAEliminar = null;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando sistema de menús...');
    cargarMenus();
});

// Cargar menús existentes
function cargarMenus() {
    const container = document.getElementById('menusContainer');
    if (!container) return;
    
    container.innerHTML = '<div class="loading-state">Cargando menús...</div>';
    
    fetch(`${BASE_URL}menu/getMenus`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                menus = data.menus;
                mostrarMenus();
            } else {
                container.innerHTML = '<div class="empty-state">Error al cargar menús</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="empty-state">Error de conexión</div>';
        });
}

// Mostrar menús en el grid
function mostrarMenus() {
    const container = document.getElementById('menusContainer');
    
    if (!menus || menus.length === 0) {
        container.innerHTML = `
            <div class="empty-menus">
                <span class="icon">📋</span>
                <h3>No hay menús creados</h3>
                <p>Crea tu primer menú para organizar tus productos</p>
                <button class="btn btn-primary" onclick="crearNuevoMenu()">
                    ➕ Crear Primer Menú
                </button>
            </div>
        `;
        return;
    }
    
    let html = '';
    menus.forEach(menu => {
        const totalProductos = calcularTotalProductos(menu.categorias);
        const totalCategorias = menu.categorias ? menu.categorias.length : 0;
        
        html += `
            <div class="menu-card ${!menu.estado ? 'menu-inactivo' : ''}">
                <div class="menu-header">
                    <h3 class="menu-nombre">${menu.nombre}</h3>
                    ${menu.descripcion ? `<p class="menu-descripcion">${menu.descripcion}</p>` : ''}
                    <div class="menu-estado ${menu.estado ? 'estado-activo' : 'estado-inactivo'}">
                        ${menu.estado ? 'ACTIVO' : 'INACTIVO'}
                    </div>
                </div>
                <div class="menu-content">
                    <div class="menu-stats">
                        <div class="stat-item">
                            <span class="stat-number">${totalCategorias}</span>
                            <span class="stat-label">Categorías</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">${totalProductos}</span>
                            <span class="stat-label">Productos</span>
                        </div>
                    </div>
                    
                    <div class="categorias-lista">
                        ${generarHTMLCategorias(menu.categorias)}
                    </div>
                    
                    <div class="menu-acciones">
                        <button class="btn btn-warning btn-sm" onclick="editarMenu(${menu.id})">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="solicitarEliminarMenu(${menu.id})">
                            🗑️ Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function calcularTotalProductos(categorias) {
    if (!categorias) return 0;
    return categorias.reduce((total, categoria) => total + (categoria.productos ? categoria.productos.length : 0), 0);
}

function generarHTMLCategorias(categorias) {
    if (!categorias || categorias.length === 0) {
        return '<div class="empty-categoria">No hay categorías en este menú</div>';
    }
    
    let html = '';
    
    // Mostrar solo las primeras 3 categorías
    const categoriasMostrar = categorias.slice(0, 3);
    
    categoriasMostrar.forEach(categoria => {
        const productosCount = categoria.productos ? categoria.productos.length : 0;
        
        html += `
            <div class="categoria-item">
                <div class="categoria-nombre">
                    <span>${categoria.nombre}</span>
                    <span class="productos-count">${productosCount}</span>
                </div>
                <div class="productos-lista">
                    ${generarHTMLProductos(categoria.productos)}
                </div>
            </div>
        `;
    });
    
    // Mostrar indicador si hay más categorías
    if (categorias.length > 3) {
        html += `<div class="empty-categoria">+ ${categorias.length - 3} categorías más...</div>`;
    }
    
    return html;
}

function generarHTMLProductos(productos) {
    if (!productos || productos.length === 0) {
        return '<div style="font-size: 0.8rem; color: #6c757d; font-style: italic;">Sin productos</div>';
    }
    
    let html = '';
    
    // Mostrar solo los primeros 3 productos
    const productosMostrar = productos.slice(0, 3);
    
    productosMostrar.forEach(producto => {
        html += `
            <div class="producto-item">
                <span class="producto-nombre" title="${producto.nombre}">${producto.nombre}</span>
                <span class="producto-precio">${MONEDA_SIMBOLO}${producto.precio}</span>
            </div>
        `;
    });
    
    // Mostrar indicador si hay más productos
    if (productos.length > 3) {
        html += `<div style="font-size: 0.7rem; color: #6c757d; text-align: center; padding: 0.2rem;">+ ${productos.length - 3} más</div>`;
    }
    
    return html;
}

// Navegación
function crearNuevoMenu() {
    window.location.href = `${BASE_URL}configuracion/crear-menu`;
}

function editarMenu(menuId) {
    window.location.href = `${BASE_URL}configuracion/editar-menu/${menuId}`;
}

// Eliminación de menús
function solicitarEliminarMenu(menuId) {
    const menu = menus.find(m => m.id == menuId);
    if (!menu) return;
    
    menuAEliminar = menuId;
    
    const modal = document.getElementById('confirmModal');
    const message = document.getElementById('confirmMessage');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    message.textContent = `¿Estás seguro de eliminar el menú "${menu.nombre}"? Esta acción no se puede deshacer y se eliminarán todas sus categorías y productos.`;
    
    confirmBtn.onclick = eliminarMenuConfirmado;
    modal.style.display = 'block';
}

function cerrarConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    menuAEliminar = null;
}

function eliminarMenuConfirmado() {
    if (!menuAEliminar) return;
    
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const originalText = confirmBtn.innerHTML;
    
    confirmBtn.innerHTML = '<div class="icon-loading"></div> Eliminando...';
    confirmBtn.disabled = true;
    
    fetch(`${BASE_URL}menu/eliminarMenu`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: menuAEliminar })
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            cerrarConfirmModal();
            cargarMenus(); // Recargar la lista
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Error de conexión', 'error');
    })
    .finally(() => {
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
        menuAEliminar = null;
    });
}

// Utilidades
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.textContent = message;
        notification.className = `notification ${type} show`;
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
}

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
    const modal = document.getElementById('confirmModal');
    if (event.target === modal) {
        cerrarConfirmModal();
    }
});