<div class="configuracion-container">
    <div class="page-header">
        <div class="header-content">
            <h2>Datos del Comercio</h2>
            <p>Configura la información básica de tu restaurante</p>
        </div>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo BASE_URL; ?>configuracion'">
            ← Volver
        </button>
    </div>

    <form id="comercioForm" enctype="multipart/form-data">
        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">🏪</span>
                Información Básica
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="comercio_nombre">Nombre del Comercio *</label>
                    <input type="text" id="comercio_nombre" name="comercio_nombre" 
                           value="<?php echo htmlspecialchars($comercio['comercio_nombre']); ?>" 
                           required placeholder="Ej: Mi Restaurante">
                </div>
                
                <div class="form-group">
                    <label for="comercio_logo">Logo del Comercio</label>
                    <div class="file-upload-container">
                        <div class="logo-preview" id="logoPreviewContainer">
                            <?php if (!empty($comercio['comercio_logo'])): ?>
                                <img src="<?php echo BASE_URL; ?>assets/images/logos/<?php echo htmlspecialchars($comercio['comercio_logo']); ?>?t=<?php echo time(); ?>" 
                                     alt="Logo actual" class="current-logo"
                                     id="currentLogo">
                                <div class="logo-overlay" onclick="eliminarLogo()">
                                    <span class="delete-text">🗑️ Eliminar</span>
                                </div>
                            <?php else: ?>
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <i class="upload-icon">📷</i>
                                    <span>Haz clic para subir logo</span>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="comercio_logo" name="comercio_logo" 
                                   accept="image/jpeg,image/png,image/gif,image/webp" 
                                   class="file-input">
                            <input type="hidden" id="eliminar_logo" name="eliminar_logo" value="0">
                        </div>
                        <small class="file-info">Formatos: JPG, PNG, GIF, WebP. Máx: 2MB</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resto del formulario igual que antes -->
        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">📄</span>
                Documentos Fiscales
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="comercio_tipo_documento">Tipo de Documento *</label>
                    <select id="comercio_tipo_documento" name="comercio_tipo_documento" required>
                        <option value="NIT" <?php echo $comercio['comercio_tipo_documento'] == 'NIT' ? 'selected' : ''; ?>>NIT</option>
                        <option value="RUT" <?php echo $comercio['comercio_tipo_documento'] == 'RUT' ? 'selected' : ''; ?>>RUT</option>
                        <option value="DNI" <?php echo $comercio['comercio_tipo_documento'] == 'DNI' ? 'selected' : ''; ?>>DNI</option>
                        <option value="RUC" <?php echo $comercio['comercio_tipo_documento'] == 'RUC' ? 'selected' : ''; ?>>RUC</option>
                        <option value="CEDULA" <?php echo $comercio['comercio_tipo_documento'] == 'CEDULA' ? 'selected' : ''; ?>>Cédula</option>
                        <option value="PASAPORTE" <?php echo $comercio['comercio_tipo_documento'] == 'PASAPORTE' ? 'selected' : ''; ?>>Pasaporte</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="comercio_numero_documento">Número de Documento *</label>
                    <input type="text" id="comercio_numero_documento" name="comercio_numero_documento" 
                           value="<?php echo htmlspecialchars($comercio['comercio_numero_documento']); ?>" 
                           required placeholder="Ej: 123456789-0">
                </div>
            </div>
        </div>

        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">📍</span>
                Información de Contacto
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="comercio_telefono">Teléfono</label>
                    <input type="tel" id="comercio_telefono" name="comercio_telefono" 
                           value="<?php echo htmlspecialchars($comercio['comercio_telefono']); ?>" 
                           placeholder="Ej: +57 300 123 4567">
                </div>
                
                <div class="form-group">
                    <label for="comercio_email">Email</label>
                    <input type="email" id="comercio_email" name="comercio_email" 
                           value="<?php echo htmlspecialchars($comercio['comercio_email']); ?>" 
                           placeholder="Ej: info@restaurante.com">
                </div>
            </div>
            
            <div class="form-group">
                <label for="comercio_direccion">Dirección</label>
                <textarea id="comercio_direccion" name="comercio_direccion" 
                          placeholder="Ingrese la dirección completa"><?php echo htmlspecialchars($comercio['comercio_direccion']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="comercio_ciudad">Ciudad</label>
                    <input type="text" id="comercio_ciudad" name="comercio_ciudad" 
                           value="<?php echo htmlspecialchars($comercio['comercio_ciudad']); ?>" 
                           placeholder="Ej: Bogotá">
                </div>
                
                <div class="form-group">
                    <label for="comercio_pais">País</label>
                    <input type="text" id="comercio_pais" name="comercio_pais" 
                           value="<?php echo htmlspecialchars($comercio['comercio_pais']); ?>" 
                           placeholder="Ej: Colombia">
                </div>
            </div>
        </div>

        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">💰</span>
                Configuración Monetaria
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="comercio_moneda">Tipo de Moneda *</label>
                    <select id="comercio_moneda" name="comercio_moneda" required>
                        <option value="COP" <?php echo $comercio['comercio_moneda'] == 'COP' ? 'selected' : ''; ?>>Peso Colombiano (COP)</option>
                        <option value="USD" <?php echo $comercio['comercio_moneda'] == 'USD' ? 'selected' : ''; ?>>Dólar Americano (USD)</option>
                        <option value="EUR" <?php echo $comercio['comercio_moneda'] == 'EUR' ? 'selected' : ''; ?>>Euro (EUR)</option>
                        <option value="MXN" <?php echo $comercio['comercio_moneda'] == 'MXN' ? 'selected' : ''; ?>>Peso Mexicano (MXN)</option>
                        <option value="PEN" <?php echo $comercio['comercio_moneda'] == 'PEN' ? 'selected' : ''; ?>>Sol Peruano (PEN)</option>
                        <option value="ARS" <?php echo $comercio['comercio_moneda'] == 'ARS' ? 'selected' : ''; ?>>Peso Argentino (ARS)</option>
                        <option value="BRL" <?php echo $comercio['comercio_moneda'] == 'BRL' ? 'selected' : ''; ?>>Real Brasileño (BRL)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="comercio_simbolo_moneda">Símbolo de Moneda *</label>
                    <input type="text" id="comercio_simbolo_moneda" name="comercio_simbolo_moneda" 
                           value="<?php echo htmlspecialchars($comercio['comercio_simbolo_moneda']); ?>" 
                           required placeholder="Ej: $, €, S/." maxlength="3">
                </div>
            </div>
        </div>

        <div class="config-section">
            <h3 class="section-title">
                <span class="section-icon">⏰</span>
                Configuración de Seguridad
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sesion_tiempo_inactividad">Tiempo de Inactividad (minutos) *</label>
                    <input type="number" id="sesion_tiempo_inactividad" name="sesion_tiempo_inactividad" 
                           value="<?php echo htmlspecialchars($comercio['sesion_tiempo_inactividad'] ?? '30'); ?>" 
                           min="5" max="480" required>
                    <small>Tiempo en minutos después del cual la sesión se cierra automáticamente por inactividad</small>
                </div>
                
                <div class="form-group">
                    <label for="sesion_recordatorio">Recordatorio de Sesión (minutos)</label>
                    <input type="number" id="sesion_recordatorio" name="sesion_recordatorio" 
                           value="<?php echo htmlspecialchars($comercio['sesion_recordatorio'] ?? '5'); ?>" 
                           min="1" max="60">
                    <small>Tiempo antes del cierre para mostrar advertencia (0 para desactivar)</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="sesion_multidispositivo">Sesiones Múltiples</label>
                <select id="sesion_multidispositivo" name="sesion_multidispositivo">
                    <option value="1" <?php echo ($comercio['sesion_multidispositivo'] ?? '1') == '1' ? 'selected' : ''; ?>>Permitir múltiples dispositivos</option>
                    <option value="0" <?php echo ($comercio['sesion_multidispositivo'] ?? '1') == '0' ? 'selected' : ''; ?>>Una sola sesión activa</option>
                </select>
                <small>Permitir que el usuario inicie sesión desde múltiples dispositivos simultáneamente</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo BASE_URL; ?>configuracion'">
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                💾 Guardar Configuración
            </button>
        </div>
    </form>
</div>

<!-- Notificación -->
<div id="notification" class="notification"></div>

<script src="<?php echo BASE_URL; ?>assets/js/configuracion.js"></script>