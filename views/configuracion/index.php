<div class="configuracion-container">
    <div class="page-header">
        <h2>⚙️ Configuración del Sistema</h2>
        <p>Gestiona todas las configuraciones de tu restaurante</p>
    </div>

    <div class="config-mosaico">
        <!-- Botón Comercio -->
        <a href="<?php echo BASE_URL; ?>configuracion/comercio" class="config-boton">
            <div class="config-boton-icono">🏪</div>
            <div class="config-boton-contenido">
                <h3>Datos del Comercio</h3>
                <p>Información básica del restaurante</p>
                <ul class="config-features">
                    <li>Nombre y logo</li>
                    <li>Documentos fiscales</li>
                    <li>Información de contacto</li>
                    <li>Configuración monetaria</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </a>

        <!-- Botón Restaurant -->
        <a href="<?php echo BASE_URL; ?>configuracion/restaurant" class="config-boton">
            <div class="config-boton-icono">🍽️</div>
            <div class="config-boton-contenido">
                <h3>Configuración del Restaurant</h3>
                <p>Horarios, mesas y servicios</p>
                <ul class="config-features">
                    <li>Horarios de atención</li>
                    <li>Capacidad y mesas</li>
                    <li>Servicio de delivery</li>
                    <li>Impuestos y propinas</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </a>

        <!-- Botón Sistema -->
        <div class="config-boton" onclick="mostrarProximamente()">
            <div class="config-boton-icono">💻</div>
            <div class="config-boton-contenido">
                <h3>Configuración del Sistema</h3>
                <p>Ajustes generales del sistema</p>
                <ul class="config-features">
                    <li>Tiempos de sesión</li>
                    <li>Seguridad y accesos</li>
                    <li>Backup automático</li>
                    <li>Mantenimiento</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </div>

        <!-- Botón Impresión -->
        <div class="config-boton" onclick="mostrarProximamente()">
            <div class="config-boton-icono">🖨️</div>
            <div class="config-boton-contenido">
                <h3>Configuración de Impresión</h3>
                <p>Impresoras y formatos</p>
                <ul class="config-features">
                    <li>Impresoras de tickets</li>
                    <li>Formatos personalizados</li>
                    <li>Configuración de cortes</li>
                    <li>Reportes automáticos</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </div>

        <!-- Botón Notificaciones -->
        <div class="config-boton" onclick="mostrarProximamente()">
            <div class="config-boton-icono">🔔</div>
            <div class="config-boton-contenido">
                <h3>Notificaciones</h3>
                <p>Alertas y recordatorios</p>
                <ul class="config-features">
                    <li>Notificaciones por email</li>
                    <li>Alertas de stock</li>
                    <li>Recordatorios de pago</li>
                    <li>Notificaciones push</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </div>

        <!-- Botón Backup -->
        <div class="config-boton" onclick="mostrarProximamente()">
            <div class="config-boton-icono">💾</div>
            <div class="config-boton-contenido">
                <h3>Backup y Seguridad</h3>
                <p>Copias de seguridad</p>
                <ul class="config-features">
                    <li>Backup automático</li>
                    <li>Restauración de datos</li>
                    <li>Logs del sistema</li>
                    <li>Auditoría de seguridad</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </div>

        <!-- Botón Apariencia -->
        <div class="config-boton" onclick="mostrarProximamente()">
            <div class="config-boton-icono">🎨</div>
            <div class="config-boton-contenido">
                <h3>Apariencia</h3>
                <p>Temas y colores</p>
                <ul class="config-features">
                    <li>Temas personalizados</li>
                    <li>Colores del sistema</li>
                    <li>Logo personalizado</li>
                    <li>Interfaz adaptable</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </div>

        <!-- Botón Redes Sociales -->
        <div class="config-boton" onclick="mostrarProximamente()">
            <div class="config-boton-icono">📱</div>
            <div class="config-boton-contenido">
                <h3>Redes Sociales</h3>
                <p>Integración con redes</p>
                <ul class="config-features">
                    <li>Conexión con Facebook</li>
                    <li>Integración Instagram</li>
                    <li>WhatsApp Business</li>
                    <li>Publicación automática</li>
                </ul>
            </div>
            <div class="config-boton-flecha">→</div>
        </div>
    </div>
</div>

<!-- Notificación -->
<div id="notification" class="notification"></div>

<script>
function mostrarProximamente() {
    const notification = document.getElementById('notification');
    notification.textContent = '🔨 Esta funcionalidad estará disponible próximamente';
    notification.className = 'notification warning show';
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type} show`;
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}
</script>

<style>
.configuracion-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-header h2 {
    color: #2c3e50;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.page-header p {
    color: #7f8c8d;
    font-size: 1.1rem;
}

.config-mosaico {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.config-boton {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

.config-boton:hover {
    transform: translateY(-5px);
    border-color: #3498db;
    box-shadow: 0 10px 25px rgba(52, 152, 219, 0.15);
    text-decoration: none;
    color: inherit;
}

.config-boton::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(52, 152, 219, 0.1), transparent);
    transition: left 0.5s ease;
}

.config-boton:hover::before {
    left: 100%;
}

.config-boton-icono {
    font-size: 2.5rem;
    padding: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    min-width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.config-boton:hover .config-boton-icono {
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.config-boton-contenido {
    flex: 1;
    min-width: 0;
}

.config-boton-contenido h3 {
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
    font-weight: 600;
    line-height: 1.3;
}

.config-boton-contenido p {
    color: #7f8c8d;
    margin: 0 0 1rem 0;
    font-size: 0.9rem;
    line-height: 1.4;
}

.config-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.config-features li {
    color: #5d6d7e;
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
    padding-left: 1rem;
    position: relative;
    line-height: 1.3;
}

.config-features li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #27ae60;
    font-weight: bold;
    font-size: 0.7rem;
}

.config-boton-flecha {
    color: #bdc3c7;
    font-size: 1.5rem;
    font-weight: bold;
    transition: all 0.3s ease;
    align-self: center;
    flex-shrink: 0;
}

.config-boton:hover .config-boton-flecha {
    color: #3498db;
    transform: translateX(5px);
}

/* Colores específicos para cada botón */
.config-boton:nth-child(1):hover { border-color: #e74c3c; }
.config-boton:nth-child(1):hover .config-boton-icono { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); }

.config-boton:nth-child(2):hover { border-color: #27ae60; }
.config-boton:nth-child(2):hover .config-boton-icono { background: linear-gradient(135deg, #27ae60 0%, #229954 100%); }

.config-boton:nth-child(3):hover { border-color: #3498db; }
.config-boton:nth-child(3):hover .config-boton-icono { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }

.config-boton:nth-child(4):hover { border-color: #9b59b6; }
.config-boton:nth-child(4):hover .config-boton-icono { background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); }

.config-boton:nth-child(5):hover { border-color: #f39c12; }
.config-boton:nth-child(5):hover .config-boton-icono { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); }

.config-boton:nth-child(6):hover { border-color: #34495e; }
.config-boton:nth-child(6):hover .config-boton-icono { background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%); }

.config-boton:nth-child(7):hover { border-color: #e91e63; }
.config-boton:nth-child(7):hover .config-boton-icono { background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%); }

.config-boton:nth-child(8):hover { border-color: #00bcd4; }
.config-boton:nth-child(8):hover .config-boton-icono { background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%); }

/* Notificación personalizada */
.notification.warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* Responsive */
@media (max-width: 768px) {
    .configuracion-container {
        padding: 1rem;
    }
    
    .page-header h2 {
        font-size: 2rem;
    }
    
    .config-mosaico {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .config-boton {
        padding: 1.25rem;
    }
    
    .config-boton-icono {
        font-size: 2rem;
        min-width: 60px;
        height: 60px;
    }
    
    .config-boton-contenido h3 {
        font-size: 1.1rem;
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.config-boton {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

.config-boton:nth-child(1) { animation-delay: 0.1s; }
.config-boton:nth-child(2) { animation-delay: 0.2s; }
.config-boton:nth-child(3) { animation-delay: 0.3s; }
.config-boton:nth-child(4) { animation-delay: 0.4s; }
.config-boton:nth-child(5) { animation-delay: 0.5s; }
.config-boton:nth-child(6) { animation-delay: 0.6s; }
.config-boton:nth-child(7) { animation-delay: 0.7s; }
.config-boton:nth-child(8) { animation-delay: 0.8s; }

/* Estados de carga */
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
</style>