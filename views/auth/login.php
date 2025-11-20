<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restaurante</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-form">
            <h1>RESTAURANTE</h1>
            <h2>Iniciar Sesión</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['expired'])): ?>
                <div class="alert alert-warning">Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente.</div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </form>
            
            <div class="session-info">
                <p>Sesión expira después de 30 minutos de inactividad</p>
            </div>
        </div>
    </div>
</body>
</html>