<div class="usuarios-container">
    <div class="page-header">
        <h2>👤 Mi Perfil</h2>
        <p>Gestiona tu información personal y configuración de cuenta</p>
    </div>

    <!-- Notificación -->
    <div id="notification" class="notification"></div>

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="<?php echo BASE_URL; ?>assets/images/usuarios/<?php echo !empty($user['foto']) ? $user['foto'] : 'default.png'; ?>" 
                         alt="Foto de perfil" class="avatar-image"
                         onerror="this.src='<?php echo BASE_URL; ?>assets/images/default.png'">
                    <div class="avatar-overlay">
                        <i class="camera-icon">📷</i>
                        <span>Cambiar foto</span>
                    </div>
                    <!-- Input oculto para subir foto -->
                    <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/gif,image/webp" 
                           style="display: none;" onchange="cambiarFotoPerfil(this)">
                </div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></h3>
                    <p class="profile-username">@<?php echo htmlspecialchars($user['usuario']); ?></p>
                    <p class="profile-role">Administrador del Sistema</p>
                    <p class="profile-member-since">
                        Miembro desde: <?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?>
                    </p>
                </div>
            </div>

            <div class="profile-tabs">
                <button class="tab-button active" onclick="openTab('personal-tab')">
                    📝 Información Personal
                </button>
                <button class="tab-button" onclick="openTab('security-tab')">
                    🔒 Seguridad
                </button>
            </div>

            <!-- Pestaña Información Personal -->
            <div id="personal-tab" class="tab-content active">
                <form id="profileForm" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" 
                                   value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido *</label>
                            <input type="text" id="apellido" name="apellido" 
                                   value="<?php echo htmlspecialchars($user['apellido']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="usuario">Usuario *</label>
                            <input type="text" id="usuario" name="usuario" 
                                   value="<?php echo htmlspecialchars($user['usuario']); ?>" required readonly
                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                            <small>El usuario no puede ser modificado</small>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo Electrónico</label>
                            <input type="email" id="correo" name="correo" 
                                   value="<?php echo htmlspecialchars($user['correo'] ?? ''); ?>"
                                   placeholder="ejemplo@correo.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" 
                                   value="<?php echo htmlspecialchars($user['telefono'] ?? ''); ?>"
                                   placeholder="+57 300 123 4567">
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" 
                                   value="<?php echo htmlspecialchars($user['direccion'] ?? ''); ?>"
                                   placeholder="Ingrese su dirección">
                        </div>
                    </div>

                    <!-- Campo oculto para foto en el formulario principal -->
                    <input type="file" id="fotoForm" name="foto" accept="image/jpeg,image/png,image/gif,image/webp" 
                           style="display: none;">

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            💾 Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pestaña Seguridad -->
            <div id="security-tab" class="tab-content">
                <form id="changePasswordForm">
                    <div class="security-info">
                        <h4>Cambiar Contraseña</h4>
                        <p>Para cambiar tu contraseña, ingresa tu contraseña actual y la nueva contraseña.</p>
                    </div>

                    <div class="form-group">
                        <label for="current_password">Contraseña Actual *</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña *</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Contraseña *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <h5>Requisitos de la contraseña:</h5>
                        <ul>
                            <li>Mínimo 6 caracteres</li>
                            <li>Recomendado: combinación de letras y números</li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            🔒 Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="profile-sidebar">
            <div class="sidebar-card">
                <h4>📊 Estadísticas</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?></span>
                        <span class="stat-label">Fecha de Registro</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">Activo</span>
                        <span class="stat-label">Estado</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">Administrador</span>
                        <span class="stat-label">Rol</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-card">
                <h4>⚡ Acciones Rápidas</h4>
                <div class="quick-actions">
                    <a href="<?php echo BASE_URL; ?>dashboard" class="quick-action">
                        🏠 Ir al Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>usuarios" class="quick-action">
                        👥 Gestión de Usuarios
                    </a>
                    <a href="<?php echo BASE_URL; ?>configuracion" class="quick-action">
                        ⚙️ Configuración
                    </a>
                </div>
            </div>

            <!-- Sección para cambiar foto individualmente -->
            <div class="sidebar-card">
                <h4>🖼️ Cambiar Foto</h4>
                <div class="photo-upload-container">
                    <div class="current-photo-preview">
                        <img src="<?php echo BASE_URL; ?>assets/images/usuarios/<?php echo !empty($user['foto']) ? $user['foto'] : 'default.png'; ?>" 
                             alt="Foto actual" class="current-photo"
                             onerror="this.src='<?php echo BASE_URL; ?>assets/images/default.png'">
                    </div>
                    <div class="photo-upload-actions">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('fotoInput').click()">
                            📷 Seleccionar Foto
                        </button>
                        <small class="photo-info">Formatos: JPG, PNG, GIF, WebP. Máx: 5MB</small>
                        <?php if (!empty($user['foto']) && $user['foto'] !== 'default.png'): ?>
                        <button type="button" class="btn btn-warning" onclick="eliminarFotoActual()" style="margin-top: 0.5rem;">
                            🗑️ Eliminar Foto Actual
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para previsualizar foto -->
<div id="photoPreviewModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Previsualizar Foto</h3>
            <span class="close" onclick="closePhotoPreview()">&times;</span>
        </div>
        <div class="modal-body" style="text-align: center;">
            <div id="photoPreviewContainer" style="margin-bottom: 1rem;">
                <img id="photoPreview" src="" alt="Vista previa" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closePhotoPreview()">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="confirmPhotoChange()">
                    ✅ Usar esta Foto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación de foto -->
<div id="deletePhotoModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Eliminar Foto</h3>
            <span class="close" onclick="closeDeletePhotoModal()">&times;</span>
        </div>
        <div class="modal-body" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🗑️</div>
            <h4>¿Estás seguro de eliminar tu foto de perfil?</h4>
            <p>Tu foto actual será eliminada y se restaurará la foto por defecto.</p>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeDeletePhotoModal()">
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDeletePhoto()">
                    ✅ Sí, Eliminar Foto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Definir BASE_URL para JavaScript -->
<script>
    // Definir BASE_URL para JavaScript
    const BASE_URL = '<?php echo BASE_URL; ?>';
    console.log('🌐 BASE_URL definido:', BASE_URL);
    
    // Pasar información de la foto actual al JavaScript
    const FOTO_ACTUAL = '<?php echo !empty($user['foto']) ? $user['foto'] : 'default.png'; ?>';
    console.log('📸 Foto actual:', FOTO_ACTUAL);
</script>

<!-- Incluir CSS y JS separados -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/perfil.css">
<script src="<?php echo BASE_URL; ?>assets/js/perfil.js"></script>

<style>
.photo-upload-container {
    text-align: center;
}

.current-photo-preview {
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #3498db;
}

.current-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-upload-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.photo-info {
    color: #7f8c8d;
    font-size: 0.8rem;
}

.photo-loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.btn-warning {
    background: #f39c12;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-warning:hover {
    background: #e67e22;
}

.btn-danger {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-danger:hover {
    background: #c0392b;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>