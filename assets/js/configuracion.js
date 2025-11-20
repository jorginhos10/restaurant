// Configuración del sistema - VERSIÓN FUNCIONAL
console.log('✅ configuracion.js cargado - VERSIÓN FUNCIONAL');

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Inicializando formulario de comercio...');
    
    // Configurar logo preview
    const logoPreview = document.getElementById('logoPreviewContainer');
    const fileInput = document.getElementById('comercio_logo');
    
    if (logoPreview && fileInput) {
        logoPreview.addEventListener('click', function(e) {
            if (!e.target.closest('.logo-overlay')) {
                fileInput.click();
            }
        });
        
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                previewLogo(this.files[0]);
            }
        });
    }
    
    // Configurar formulario
    const form = document.getElementById('comercioForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            guardarComercio();
        });
    }
});

// Previsualización del logo - FUNCIONAL
function previewLogo(file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const logoContainer = document.getElementById('logoPreviewContainer');
        const placeholder = document.getElementById('uploadPlaceholder');
        
        // Ocultar placeholder
        if (placeholder) placeholder.style.display = 'none';
        
        // Remover logo existente
        const oldLogo = logoContainer.querySelector('.current-logo');
        const oldOverlay = logoContainer.querySelector('.logo-overlay');
        if (oldLogo) oldLogo.remove();
        if (oldOverlay) oldOverlay.remove();
        
        // Crear nuevo logo
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'current-logo';
        img.alt = 'Logo preview';
        
        // Crear overlay
        const overlay = document.createElement('div');
        overlay.className = 'logo-overlay';
        overlay.innerHTML = '<span class="delete-text">🗑️ Eliminar</span>';
        overlay.onclick = eliminarLogo;
        
        // Agregar al contenedor
        logoContainer.appendChild(img);
        logoContainer.appendChild(overlay);
        
        // Resetear flag de eliminar
        document.getElementById('eliminar_logo').value = '0';
        
        showNotification('✅ Logo cargado correctamente', 'success');
    };
    
    reader.readAsDataURL(file);
}

// Eliminar logo
function eliminarLogo() {
    const logoContainer = document.getElementById('logoPreviewContainer');
    const fileInput = document.getElementById('comercio_logo');
    
    // Remover elementos
    const logo = logoContainer.querySelector('.current-logo');
    const overlay = logoContainer.querySelector('.logo-overlay');
    if (logo) logo.remove();
    if (overlay) overlay.remove();
    
    // Mostrar placeholder
    let placeholder = document.getElementById('uploadPlaceholder');
    if (!placeholder) {
        placeholder = document.createElement('div');
        placeholder.id = 'uploadPlaceholder';
        placeholder.className = 'upload-placeholder';
        placeholder.innerHTML = '<i class="upload-icon">📷</i><span>Haz clic para subir logo</span>';
        logoContainer.appendChild(placeholder);
    }
    placeholder.style.display = 'flex';
    
    // Limpiar input file
    fileInput.value = '';
    
    // Marcar para eliminar
    document.getElementById('eliminar_logo').value = '1';
    
    showNotification('✅ Logo eliminado', 'success');
}

// Guardar configuración - FUNCIONAL
function guardarComercio() {
    const form = document.getElementById('comercioForm');
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Validar campos requeridos
    const required = form.querySelectorAll('[required]');
    let valid = true;
    
    required.forEach(field => {
        if (!field.value.trim()) {
            valid = false;
            field.style.borderColor = '#e74c3c';
        }
    });
    
    if (!valid) {
        showNotification('❌ Complete los campos obligatorios', 'error');
        return;
    }
    
    // Mostrar loading
    submitBtn.innerHTML = '<div class="icon-loading"></div> Guardando...';
    submitBtn.disabled = true;
    
    // Crear FormData
    const formData = new FormData(form);
    
    // Enviar datos
    fetch(`${BASE_URL}configuracion/guardarComercio`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // SIEMPRE restaurar el botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        showNotification('❌ Error de conexión', 'error');
    });
}

// Notificaciones
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.textContent = message;
        notification.className = `notification ${type} show`;
        setTimeout(() => notification.classList.remove('show'), 4000);
    }
}