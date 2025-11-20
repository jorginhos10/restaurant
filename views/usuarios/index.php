<div class="usuarios-container">
    <div class="page-header">
        <h2>Gestión de Usuarios</h2>
        <button class="btn btn-primary" onclick="openUserModal()">
            <i class="icon-plus"></i> Agregar Usuario
        </button>
    </div>

    <!-- Mostrar mensajes -->
    <?php if (isset($_GET['success'])): ?>
        <div class="notification success show" id="successNotification">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.getElementById('successNotification');
                if (notification) notification.classList.remove('show');
            }, 3000);
        </script>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="notification error show" id="errorNotification">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.getElementById('errorNotification');
                if (notification) notification.classList.remove('show');
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- Buscador -->
    <div class="search-container">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Buscar usuarios..." onkeyup="filterUsers()">
            <i class="icon-search"></i>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="users-table-container">
        <table class="users-table" id="usersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Usuario</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Fecha Registro</th>
                    <th>Estado</th>
                    <th class="actions-header">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): 
                    $isCurrentUser = ($row['id'] == $_SESSION['user_id']);
                    $isSuperAdmin = ($row['id'] == 1);
                ?>
                <tr data-user-id="<?php echo $row['id']; ?>">
                    <td class="user-id"><?php echo $row['id']; ?></td>
                    <td class="user-photo">
                        <div class="photo-container">
                            <img src="<?php echo BASE_URL; ?>assets/images/<?php echo !empty($row['foto']) ? $row['foto'] : 'default.png'; ?>" 
                                 alt="Foto" class="user-avatar"
                                 onerror="this.src='<?php echo BASE_URL; ?>assets/images/default.png'">
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_registro'])); ?></td>
                    <td>
                        <?php if ($isSuperAdmin): ?>
                            <span class="status-badge super-admin" style="background: #27ae60; color: white; border-color: #27ae60;">
                                Super Admin
                            </span>
                        <?php else: ?>
                            <span class="status-badge <?php echo $row['estado'] ? 'active' : 'inactive'; ?> status-toggle"
                                  onclick="toggleUserStatus(<?php echo $row['id']; ?>, <?php echo $row['estado']; ?>)"
                                  style="cursor: pointer; <?php echo $isCurrentUser ? 'pointer-events: none; opacity: 0.6;' : ''; ?>">
                                <?php echo $row['estado'] ? 'Activo' : 'Inactivo'; ?>
                                <?php if ($isCurrentUser): ?><br><small>(Tú)</small><?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions-buttons">
                            <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $row['id']; ?>)" title="Editar">
                                <i class="icon-edit"></i>
                            </button>

                            <button class="action-btn password-btn" 
                                    onclick="<?php echo !$isSuperAdmin ? 'openChangePasswordModal(' . $row['id'] . ')' : 'showSuperAdminWarning()'; ?>" 
                                    title="<?php echo $isSuperAdmin ? 'Super Admin' : 'Cambiar Contraseña'; ?>"
                                    <?php echo $isSuperAdmin ? 'style="opacity: 0.4; cursor: not-allowed;"' : ''; ?>>
                                <i class="icon-lock"></i>
                            </button>

                            <button class="action-btn permissions-btn" 
                                    onclick="<?php echo !$isSuperAdmin ? 'openPermissionsModal(' . $row['id'] . ')' : 'showSuperAdminWarning()'; ?>" 
                                    title="<?php echo $isSuperAdmin ? 'Super Admin' : 'Permisos'; ?>"
                                    <?php echo $isSuperAdmin ? 'style="opacity: 0.4; cursor: not-allowed;"' : ''; ?>>
                                <i class="icon-shield"></i>
                            </button>

                            <button class="action-btn delete-btn" 
                                    onclick="<?php echo (!$isSuperAdmin && !$isCurrentUser) ? 'confirmDelete(' . $row['id'] . ')' : 'showDeleteWarning()'; ?>" 
                                    title="<?php echo $isSuperAdmin ? 'Super Admin' : ($isCurrentUser ? 'No puedes eliminarte' : 'Eliminar'); ?>"
                                    <?php echo ($isSuperAdmin || $isCurrentUser) ? 'style="opacity: 0.4; cursor: not-allowed;"' : ''; ?>>
                                <i class="icon-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar usuario -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar Nuevo Usuario</h3>
            <span class="close" onclick="closeUserModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="userForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido *</label>
                        <input type="text" id="apellido" name="apellido" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="usuario">Usuario *</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" rows="3" placeholder="Ingrese la dirección completa"></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Usuario</h3>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="editNombre">Nombre *</label>
                        <input type="text" id="editNombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="editApellido">Apellido *</label>
                        <input type="text" id="editApellido" name="apellido" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="editUsuario">Usuario *</label>
                    <input type="text" id="editUsuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="editTelefono">Teléfono</label>
                    <input type="tel" id="editTelefono" name="telefono">
                </div>
                <div class="form-group">
                    <label for="editDireccion">Dirección</label>
                    <textarea id="editDireccion" name="direccion" rows="3" placeholder="Ingrese la dirección completa"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cambiar contraseña -->
<div id="changePasswordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Cambiar Contraseña</h3>
            <span class="close" onclick="closeChangePasswordModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="changePasswordForm">
                <input type="hidden" id="passwordUserId" name="id">
                <div class="form-group">
                    <label for="newPassword">Nueva Contraseña *</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeChangePasswordModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para permisos -->
<div id="permissionsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Gestionar Permisos</h3>
            <span class="close" onclick="closePermissionsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="permissionsForm">
                <input type="hidden" id="permissionsUserId" name="id">
                <div class="permissions-list">
                    <h4>Permisos del Sistema</h4>
                    <div id="permissionsContainer">
                        <!-- Los permisos se cargarán dinámicamente aquí -->
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closePermissionsModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Permisos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notificación dinámica -->
<div id="notification" class="notification"></div>

<script src="<?php echo BASE_URL; ?>assets/js/userManagement.js"></script>