// Funcionalidades generales del sistema
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema Restaurante cargado');
    
    // Inicializar tooltips si es necesario
    initTooltips();
    
    // Configurar actividad del usuario para mantener sesión activa
    setupUserActivity();
});

function initTooltips() {
    // Tooltips para iconos de acciones
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.setAttribute('title', 'Opciones');
    });
}

function showTooltip(e) {
    // Implementar tooltips si es necesario
}

function hideTooltip(e) {
    // Implementar tooltips si es necesario
}

function setupUserActivity() {
    // Función para enviar actividad del usuario al servidor
    function sendActivity() {
        // Usar Fetch API para notificar actividad
        fetch('<?php echo BASE_URL; ?>api/activity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ activity: 'user_action' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'expired') {
                window.location.href = '<?php echo BASE_URL; ?>auth/login?expired=1';
            }
        })
        .catch(error => console.log('Error tracking activity:', error));
    }

    // Eventos que indican actividad del usuario
    const activityEvents = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'];
    
    // Enviar actividad periódicamente (cada 5 minutos)
    setInterval(sendActivity, 300000);
    
    // También enviar actividad en eventos importantes
    activityEvents.forEach(event => {
        document.addEventListener(event, function() {
            // Debounce para evitar muchas llamadas
            clearTimeout(window.activityTimeout);
            window.activityTimeout = setTimeout(sendActivity, 1000);
        });
    });
    
    // Enviar actividad inicial
    sendActivity();
}

// Función para verificar estado de sesión
function checkSessionStatus() {
    fetch('<?php echo BASE_URL; ?>api/session-status')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'expired') {
                window.location.href = '<?php echo BASE_URL; ?>auth/login?expired=1';
            }
        })
        .catch(error => console.log('Error checking session:', error));
}

// Verificar sesión cada 10 minutos
setInterval(checkSessionStatus, 600000);