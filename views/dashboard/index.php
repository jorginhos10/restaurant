<div class="dashboard-container">
    <h2>Dashboard Temporal</h2>
    <div class="dashboard-content">
        <div class="welcome-card">
            <h3>Bienvenido al Sistema</h3>
            <p>Has iniciado sesión correctamente en el sistema del restaurante.</p>
            <p>Desde aquí puedes acceder a la gestión de usuarios y otras funcionalidades.</p>
        </div>
        
        <div class="session-info-card">
            <h4>Información de Sesión</h4>
            <p><strong>Usuario:</strong> <?php echo $_SESSION['user_fullname']; ?></p>
            <p><strong>Tiempo de sesión:</strong> 30 minutos</p>
            <p class="session-note">La sesión se mantendrá activa por 30 minutos de inactividad</p>
        </div>
    </div>
</div>