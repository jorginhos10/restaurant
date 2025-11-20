<div class="configuracion-container">
    <div class="page-header">
        <div class="header-content">
            <h2>🍽️ Configuración del Restaurant</h2>
            <p>Configura los horarios, mesas y servicios de tu restaurant</p>
        </div>
        <button class="btn btn-secondary" onclick="window.location.href='<?php echo BASE_URL; ?>configuracion'">
            ← Volver
        </button>
    </div>

    <form id="restaurantForm">
        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">🕒</span>
                Horarios de Atención
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="restaurant_horario_apertura">Horario de Apertura *</label>
                    <input type="time" id="restaurant_horario_apertura" name="restaurant_horario_apertura" 
                           value="<?php echo htmlspecialchars($restaurant['restaurant_horario_apertura']); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="restaurant_horario_cierre">Horario de Cierre *</label>
                    <input type="time" id="restaurant_horario_cierre" name="restaurant_horario_cierre" 
                           value="<?php echo htmlspecialchars($restaurant['restaurant_horario_cierre']); ?>" 
                           required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="restaurant_dias_apertura">Días de Apertura *</label>
                <div class="checkbox-group">
                    <?php
                    $diasSeleccionados = explode(',', $restaurant['restaurant_dias_apertura']);
                    $diasSemana = [
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                        'Sábado' => 'Sábado',
                        'Domingo' => 'Domingo'
                    ];
                    
                    foreach ($diasSemana as $key => $dia): ?>
                    <label class="checkbox-container">
                        <input type="checkbox" name="restaurant_dias_apertura[]" value="<?php echo $dia; ?>" 
                               <?php echo in_array($dia, $diasSeleccionados) ? 'checked' : ''; ?>>
                        <span class="checkmark"></span>
                        <?php echo $dia; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">🪑</span>
                Capacidad y Mesas
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="restaurant_capacidad_maxima">Capacidad Máxima (personas) *</label>
                    <input type="number" id="restaurant_capacidad_maxima" name="restaurant_capacidad_maxima" 
                           value="<?php echo htmlspecialchars($restaurant['restaurant_capacidad_maxima']); ?>" 
                           min="10" max="1000" required>
                </div>
                
                <div class="form-group">
                    <label for="restaurant_mesas_disponibles">Mesas Disponibles *</label>
                    <input type="number" id="restaurant_mesas_disponibles" name="restaurant_mesas_disponibles" 
                           value="<?php echo htmlspecialchars($restaurant['restaurant_mesas_disponibles']); ?>" 
                           min="1" max="200" required>
                </div>
            </div>
        </div>

        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">💰</span>
                Configuración Financiera
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="restaurant_impuesto_venta">Impuesto de Venta (%) *</label>
                    <input type="number" id="restaurant_impuesto_venta" name="restaurant_impuesto_venta" 
                           value="<?php echo htmlspecialchars($restaurant['restaurant_impuesto_venta']); ?>" 
                           min="0" max="50" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="restaurant_propina_automatica">Propina Automática</label>
                    <select id="restaurant_propina_automatica" name="restaurant_propina_automatica">
                        <option value="1" <?php echo $restaurant['restaurant_propina_automatica'] == '1' ? 'selected' : ''; ?>>Activada</option>
                        <option value="0" <?php echo $restaurant['restaurant_propina_automatica'] == '0' ? 'selected' : ''; ?>>Desactivada</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="restaurant_porcentaje_propina">Porcentaje de Propina (%)</label>
                <input type="number" id="restaurant_porcentaje_propina" name="restaurant_porcentaje_propina" 
                       value="<?php echo htmlspecialchars($restaurant['restaurant_porcentaje_propina']); ?>" 
                       min="0" max="30" step="0.1">
                <small>Se aplicará automáticamente si está activada la propina</small>
            </div>
        </div>

        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">🚚</span>
                Servicio de Delivery
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="restaurant_delivery">Servicio de Delivery</label>
                    <select id="restaurant_delivery" name="restaurant_delivery">
                        <option value="1" <?php echo $restaurant['restaurant_delivery'] == '1' ? 'selected' : ''; ?>>Activado</option>
                        <option value="0" <?php echo $restaurant['restaurant_delivery'] == '0' ? 'selected' : ''; ?>>Desactivado</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="restaurant_costo_delivery">Costo de Delivery (<?php echo $comercio['comercio_simbolo_moneda']; ?>)</label>
                    <input type="number" id="restaurant_costo_delivery" name="restaurant_costo_delivery" 
                           value="<?php echo htmlspecialchars($restaurant['restaurant_costo_delivery']); ?>" 
                           min="0" max="50" step="0.01">
                </div>
            </div>
            
            <div class="form-group">
                <label for="restaurant_tiempo_entrega">Tiempo de Entrega Promedio (minutos)</label>
                <input type="number" id="restaurant_tiempo_entrega" name="restaurant_tiempo_entrega" 
                       value="<?php echo htmlspecialchars($restaurant['restaurant_tiempo_entrega']); ?>" 
                       min="15" max="120">
            </div>
        </div>

        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">📱</span>
                Reservas Online
            </h3>
            
            <div class="form-group">
                <label for="restaurant_reservas_online">Sistema de Reservas Online</label>
                <select id="restaurant_reservas_online" name="restaurant_reservas_online">
                    <option value="1" <?php echo $restaurant['restaurant_reservas_online'] == '1' ? 'selected' : ''; ?>>Activado</option>
                    <option value="0" <?php echo $restaurant['restaurant_reservas_online'] == '0' ? 'selected' : ''; ?>>Desactivado</option>
                </select>
                <small>Permite a los clientes hacer reservas desde tu sitio web</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo BASE_URL; ?>configuracion'">
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary">
                💾 Guardar Configuración
            </button>
        </div>
    </form>
</div>

<!-- Notificación -->
<div id="notification" class="notification"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const restaurantForm = document.getElementById('restaurantForm');
    
    if (restaurantForm) {
        restaurantForm.addEventListener('submit', function(e) {
            e.preventDefault();
            guardarRestaurant();
        });
    }
});

function guardarRestaurant() {
    const form = document.getElementById('restaurantForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Procesar checkboxes de días
    const diasCheckboxes = document.querySelectorAll('input[name="restaurant_dias_apertura[]"]:checked');
    const diasSeleccionados = Array.from(diasCheckboxes).map(cb => cb.value).join(',');
    
    // Crear FormData y agregar días
    const formData = new FormData();
    const formElements = form.elements;
    
    for (let element of formElements) {
        if (element.name && element.type !== 'checkbox') {
            formData.append(element.name, element.value);
        }
    }
    
    // Agregar días seleccionados
    formData.append('restaurant_dias_apertura', diasSeleccionados);
    
    submitBtn.innerHTML = '<div class="icon-loading"></div> Guardando...';
    submitBtn.disabled = true;
    
    fetch(`${BASE_URL}configuracion/guardarRestaurant`, {
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
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
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
.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.checkbox-container {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background 0.3s ease;
}

.checkbox-container:hover {
    background: #f8f9fa;
}

.checkbox-container input {
    display: none;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #dcdfe6;
    border-radius: 3px;
    margin-right: 0.5rem;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-container input:checked + .checkmark {
    background: #3498db;
    border-color: #3498db;
}

.checkbox-container input:checked + .checkmark:after {
    content: '';
    position: absolute;
    left: 4px;
    top: 1px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
</style>