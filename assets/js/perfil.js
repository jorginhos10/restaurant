// Funcionalidad para la página de perfil - Versión Completa con Gestión de Fotos
console.log('✅ perfil.js cargado correctamente');

// Verificar que BASE_URL esté definido
if (typeof BASE_URL === 'undefined') {
    console.error('❌ BASE_URL no está definido. Definiendo valor por defecto...');
    const baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -2).join('/') + '/';
    window.BASE_URL = baseUrl.replace(/\/$/, '') + '/';
    console.log('🔧 BASE_URL auto-detectado:', window.BASE_URL);
} else {
    console.log('🌐 BASE_URL:', BASE_URL);
}

// Variables globales
let fotoSeleccionada = null;

// Funcionalidad de pestañas
function openTab(tabName) {
    console.log('Cambiando a pestaña:', tabName);
    
    // Ocultar todas las pestañas
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
    }
    
    // Desactivar todos los botones
    const tabButtons = document.getElementsByClassName('tab-button');
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }
    
    // Activar la pestaña seleccionada
    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active');
}

// Actualizar perfil - Versión Completa
function updateProfile(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    console.log('🔄 Iniciando updateProfile...');
    
    const form = document.getElementById('profileForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validar formulario antes de enviar
    if (!validarFormularioPerfil(form)) {
        showNotification('❌ Por favor, completa todos los campos obligatorios', 'error');
        return false;
    }
    
    submitBtn.innerHTML = '<div class="icon-loading"></div> Guardando...';
    submitBtn.disabled = true;
    
    // Crear FormData directamente del formulario
    const formData = new FormData(form);
    
    console.log('📤 Datos a enviar:');
    for (let [key, value] of formData.entries()) {
        if (key !== 'foto') {
            console.log(`  ${key}: ${value}`);
        } else {
            console.log(`  ${key}: [archivo]`);
        }
    }
    
    const url = `${BASE_URL}usuarios/updateProfile`;
    console.log('🌐 URL de petición:', url);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📥 Respuesta recibida, status:', response.status);
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Respuesta del servidor:', data);
        if (data.success) {
            let mensaje = '✅ ' + data.message;
            if (data.foto_cambiada && data.foto_anterior_eliminada) {
                mensaje += ' (foto anterior eliminada del servidor)';
            }
            showNotification(mensaje, 'success');
            
            // Actualizar información en la interfaz
            if (data.user) {
                actualizarInfoUsuario(data.user);
            }
            
            // Recargar la página después de 1 segundo para ver los cambios
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error en fetch:', error);
        showNotification('❌ Error de conexión: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
    
    return false;
}

// Cambiar contraseña
function changeProfilePassword(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    console.log('🔄 Iniciando changeProfilePassword...');
    
    const form = document.getElementById('changePasswordForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Validar contraseñas
    if (!validarFormularioContraseña(currentPassword, newPassword, confirmPassword)) {
        return false;
    }
    
    submitBtn.innerHTML = '<div class="icon-loading"></div> Cambiando...';
    submitBtn.disabled = true;
    
    const data = {
        current_password: currentPassword,
        new_password: newPassword
    };
    
    console.log('📤 Datos a enviar:', data);
    
    const url = `${BASE_URL}usuarios/changeProfilePassword`;
    console.log('🌐 URL de petición:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('📥 Respuesta recibida, status:', response.status);
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Respuesta del servidor:', data);
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            form.reset();
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error en fetch:', error);
        showNotification('❌ Error de conexión: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
    
    return false;
}

// Cambiar foto de perfil
function cambiarFotoPerfil(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validar tipo de archivo
        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!tiposPermitidos.includes(file.type)) {
            showNotification('❌ Tipo de archivo no permitido. Use JPEG, PNG, GIF o WebP.', 'error');
            input.value = '';
            return;
        }
        
        // Validar tamaño (5MB máximo)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('❌ El archivo es demasiado grande. Máximo 5MB.', 'error');
            input.value = '';
            return;
        }
        
        fotoSeleccionada = file;
        
        // Mostrar previsualización
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreviewModal').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Confirmar cambio de foto
function confirmPhotoChange() {
    if (!fotoSeleccionada) {
        showNotification('❌ No se ha seleccionado ninguna foto', 'error');
        return;
    }
    
    console.log('🔄 Confirmando cambio de foto...');
    
    const formData = new FormData();
    formData.append('foto', fotoSeleccionada);
    
    const url = `${BASE_URL}usuarios/updateProfilePhoto`;
    console.log('🌐 URL de petición:', url);
    
    // Mostrar loading
    const confirmBtn = document.querySelector('#photoPreviewModal .btn-primary');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<div class="photo-loading"></div> Procesando...';
    confirmBtn.disabled = true;
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📥 Respuesta recibida, status:', response.status);
        
        // Primero verificar si la respuesta es JSON válido
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // Si no es JSON, obtener el texto para debug
            return response.text().then(text => {
                console.error('❌ Respuesta no es JSON:', text);
                throw new Error('El servidor respondió con un formato incorrecto');
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('✅ Respuesta JSON del servidor:', data);
        
        if (data.success) {
            let mensaje = '✅ ' + data.message;
            if (data.foto_anterior_eliminada) {
                mensaje += ' (foto anterior eliminada del servidor)';
            }
            showNotification(mensaje, 'success');
            closePhotoPreview();
            
            // Usar la URL proporcionada por el servidor o crear una temporal
            const nuevaFotoUrl = data.foto_url || `${BASE_URL}assets/images/usuarios/${data.foto_nombre}`;
            console.log('🖼️ Nueva URL de foto:', nuevaFotoUrl);
            
            // Actualizar foto en la interfaz con timestamp para evitar cache
            actualizarFotoPerfil(nuevaFotoUrl + '?t=' + new Date().getTime());
            
        } else {
            showNotification('❌ ' + data.message, 'error');
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('❌ Error en fetch:', error);
        
        let mensajeError = '❌ Error de conexión: ' + error.message;
        
        // Si es error de JSON, sugerir recargar
        if (error.message.includes('JSON') || error.message.includes('formato')) {
            mensajeError += '. Intenta recargar la página.';
        }
        
        showNotification(mensajeError, 'error');
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}

// Eliminar foto actual
function eliminarFotoActual() {
    console.log('🔄 Solicitando eliminar foto actual...');
    document.getElementById('deletePhotoModal').style.display = 'block';
}

// Confirmar eliminación de foto
function confirmDeletePhoto() {
    console.log('🔄 Confirmando eliminación de foto...');
    
    const confirmBtn = document.querySelector('#deletePhotoModal .btn-danger');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<div class="photo-loading"></div> Eliminando...';
    confirmBtn.disabled = true;
    
    const url = `${BASE_URL}usuarios/deleteProfilePhoto`;
    console.log('🌐 URL de petición:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        console.log('📥 Respuesta recibida, status:', response.status);
        
        // Verificar si la respuesta es JSON válido
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('❌ Respuesta no es JSON:', text);
                throw new Error('El servidor respondió con un formato incorrecto');
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('✅ Respuesta del servidor:', data);
        if (data.success) {
            let mensaje = '✅ ' + data.message;
            if (data.foto_eliminada) {
                mensaje += ' (foto eliminada del servidor)';
            }
            showNotification(mensaje, 'success');
            closeDeletePhotoModal();
            
            // Actualizar interfaz con foto por defecto
            const fotoDefaultUrl = data.foto_url || `${BASE_URL}assets/images/usuarios/default.png`;
            actualizarFotoPerfil(fotoDefaultUrl + '?t=' + new Date().getTime());
            
        } else {
            showNotification('❌ ' + data.message, 'error');
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('❌ Error en fetch:', error);
        
        let mensajeError = '❌ Error de conexión: ' + error.message;
        if (error.message.includes('JSON') || error.message.includes('formato')) {
            mensajeError += '. Intenta recargar la página.';
        }
        
        showNotification(mensajeError, 'error');
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}

// Cerrar previsualización de foto
function closePhotoPreview() {
    document.getElementById('photoPreviewModal').style.display = 'none';
    document.getElementById('fotoInput').value = '';
    fotoSeleccionada = null;
}

// Cerrar modal de eliminación de foto
function closeDeletePhotoModal() {
    document.getElementById('deletePhotoModal').style.display = 'none';
}

// Función para actualizar la foto en el header
function actualizarFotoEnHeader(fotoUrl) {
    console.log('📢 Notificando cambio de foto al header:', fotoUrl);
    
    // Llamar a la función global del header si existe
    if (typeof updateHeaderPhoto === 'function') {
        updateHeaderPhoto(fotoUrl);
    }
    
    // Notificar a otras pestañas
    if (typeof notifyPhotoUpdate === 'function') {
        notifyPhotoUpdate(fotoUrl);
    } else {
        // Fallback: usar localStorage directamente
        localStorage.setItem('userPhotoUpdate', JSON.stringify({
            photoUrl: fotoUrl,
            timestamp: new Date().getTime()
        }));
    }
}

// Función para forzar recarga de imágenes sin cache
function forzarRecargaImagenes() {
    console.log('🔄 Forzando recarga de imágenes...');
    
    // Seleccionar todas las imágenes de perfil
    const imagenes = document.querySelectorAll('.avatar-image, .current-photo, .header-user-avatar, .dropdown-user-avatar');
    
    imagenes.forEach(img => {
        const srcOriginal = img.src.split('?')[0]; // Remover parámetros existentes
        img.src = srcOriginal + '?t=' + new Date().getTime();
    });
    
    console.log('✅ Imágenes recargadas sin cache');
}

// Actualizar foto en la interfaz
function actualizarFotoPerfil(fotoUrl) {
    console.log('🔄 Actualizando foto en toda la interfaz:', fotoUrl);
    
    // Asegurarse de que la URL no tenga parámetros duplicados
    let urlLimpia = fotoUrl.split('?')[0];
    let urlConTimestamp = urlLimpia + '?t=' + new Date().getTime();
    
    console.log('🖼️ URL final con timestamp:', urlConTimestamp);
    
    // Actualizar avatares en la página de perfil
    const avatarImages = document.querySelectorAll('.avatar-image, .current-photo');
    avatarImages.forEach(img => {
        img.src = urlConTimestamp;
        // Forzar recarga
        img.onload = function() {
            console.log('✅ Imagen de perfil recargada');
        };
        img.onerror = function() {
            console.error('❌ Error al cargar imagen:', urlConTimestamp);
            // Fallback a imagen por defecto
            this.src = `${BASE_URL}assets/images/usuarios/default.png?t=` + new Date().getTime();
        };
    });
    
    // Actualizar también en el header
    actualizarFotoEnHeader(urlConTimestamp);
    
    // Forzar recarga del navegador después de un tiempo si es necesario
    setTimeout(() => {
        forzarRecargaImagenes();
    }, 1000);
}

// Función para limpiar cache del navegador para imágenes
function limpiarCacheImagenes() {
    console.log('🧹 Limpiando cache de imágenes...');
    
    if ('caches' in window) {
        caches.keys().then(function(names) {
            for (let name of names) {
                if (name.includes('image-cache') || name.includes('assets/images')) {
                    caches.delete(name);
                    console.log('✅ Cache eliminado:', name);
                }
            }
        });
    }
    
    // Forzar recarga de todas las imágenes
    forzarRecargaImagenes();
}

// Funciones de validación
function validarFormularioPerfil(form) {
    const nombre = form.querySelector('#nombre').value.trim();
    const apellido = form.querySelector('#apellido').value.trim();
    
    if (!nombre || !apellido) {
        return false;
    }
    
    return true;
}

function validarFormularioContraseña(currentPassword, newPassword, confirmPassword) {
    if (!currentPassword || !newPassword || !confirmPassword) {
        showNotification('❌ Todos los campos de contraseña son obligatorios', 'error');
        return false;
    }
    
    if (newPassword !== confirmPassword) {
        showNotification('❌ Las contraseñas no coinciden', 'error');
        return false;
    }
    
    if (newPassword.length < 6) {
        showNotification('❌ La contraseña debe tener al menos 6 caracteres', 'error');
        return false;
    }
    
    return true;
}

// Actualizar información del usuario en la interfaz
function actualizarInfoUsuario(userData) {
    // Actualizar el nombre en el header si está disponible
    const userNameElements = document.querySelectorAll('.user-name, .user-fullname');
    userNameElements.forEach(element => {
        if (element.classList.contains('user-name')) {
            element.textContent = userData.nombre;
        } else if (element.classList.contains('user-fullname')) {
            element.textContent = userData.fullname;
        }
    });
    
    // Actualizar en el perfil header
    const profileHeader = document.querySelector('.profile-info h3');
    if (profileHeader) {
        profileHeader.textContent = userData.fullname;
    }
    
    // Actualizar foto si viene en la respuesta
    if (userData.foto) {
        const fotoUrl = `${BASE_URL}assets/images/usuarios/${userData.foto}`;
        actualizarFotoPerfil(fotoUrl);
    }
}

// Mostrar notificación mejorada
function showNotification(message, type = 'success') {
    console.log('📢 Notificación:', message);
    const notification = document.getElementById('notification');
    if (notification) {
        notification.textContent = message;
        notification.className = `notification ${type} show`;
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);
    } else {
        console.error('❌ Elemento de notificación no encontrado');
        crearNotificacionTemporal(message, type);
    }
}

// Crear notificación temporal si no existe el elemento
function crearNotificacionTemporal(message, type) {
    const tempNotification = document.createElement('div');
    tempNotification.className = `notification ${type} show`;
    tempNotification.textContent = message;
    tempNotification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 10000;
        padding: 1rem 1.5rem;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        max-width: 300px;
    `;
    
    if (type === 'success') {
        tempNotification.style.background = '#27ae60';
    } else if (type === 'error') {
        tempNotification.style.background = '#e74c3c';
    } else if (type === 'warning') {
        tempNotification.style.background = '#f39c12';
    }
    
    document.body.appendChild(tempNotification);
    
    setTimeout(() => {
        tempNotification.remove();
    }, 5000);
}

// Inicialización mejorada
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado, inicializando eventos del perfil...');
    
    const profileForm = document.getElementById('profileForm');
    const changePasswordForm = document.getElementById('changePasswordForm');
    const profileAvatar = document.querySelector('.profile-avatar');
    
    console.log('Formulario perfil encontrado:', !!profileForm);
    console.log('Formulario contraseña encontrado:', !!changePasswordForm);
    console.log('Avatar encontrado:', !!profileAvatar);
    
    // Configurar formulario de perfil
    if (profileForm) {
        profileForm.addEventListener('submit', updateProfile);
        console.log('✅ Evento submit agregado a profileForm');
        
        // Agregar validación en tiempo real
        profileForm.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '#dcdfe6';
                }
            });
        });
    }
    
    // Configurar formulario de contraseña
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', changeProfilePassword);
        console.log('✅ Evento submit agregado a changePasswordForm');
        
        // Validación en tiempo real para contraseñas
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        if (newPasswordInput && confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                if (newPasswordInput.value !== this.value && this.value.length > 0) {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '#dcdfe6';
                }
            });
        }
    }
    
    // Configurar funcionalidad de foto
    if (profileAvatar) {
        profileAvatar.addEventListener('click', function() {
            document.getElementById('fotoInput').click();
        });
        console.log('✅ Evento click agregado al avatar');
    }
    
    // Cerrar modales al hacer clic fuera
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'photoPreviewModal') {
                    closePhotoPreview();
                } else if (this.id === 'deletePhotoModal') {
                    closeDeletePhotoModal();
                }
            }
        });
    });
    
    // Agregar funcionalidad de "Enter" para enviar formularios
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const activeForm = document.querySelector('.tab-content.active form');
            if (activeForm) {
                const submitBtn = activeForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    e.preventDefault();
                    submitBtn.click();
                }
            }
        }
    });
    
    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhotoPreview();
            closeDeletePhotoModal();
        }
    });
    
    // Limpiar cache al cargar la página (opcional)
    setTimeout(() => {
        limpiarCacheImagenes();
    }, 2000);
});

// Hacer funciones globales
window.openTab = openTab;
window.updateProfile = updateProfile;
window.changeProfilePassword = changeProfilePassword;
window.cambiarFotoPerfil = cambiarFotoPerfil;
window.confirmPhotoChange = confirmPhotoChange;
window.closePhotoPreview = closePhotoPreview;
window.eliminarFotoActual = eliminarFotoActual;
window.confirmDeletePhoto = confirmDeletePhoto;
window.closeDeletePhotoModal = closeDeletePhotoModal;
window.forzarRecargaImagenes = forzarRecargaImagenes;
window.limpiarCacheImagenes = limpiarCacheImagenes;