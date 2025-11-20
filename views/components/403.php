<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - Restaurante</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        .error-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
            text-align: center;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .error-title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
            max-width: 500px;
            line-height: 1.6;
        }
        
        .error-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #e67e22;
            margin-bottom: 2rem;
            max-width: 500px;
            text-align: left;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn-home {
            background: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .btn-home:hover {
            background: #2980b9;
        }
        
        .btn-support {
            background: #e67e22;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .btn-support:hover {
            background: #d35400;
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-details {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'views/components/header.php'; ?>
    
    <main class="main-content-2">
        <div class="error-page">
            <div class="error-code">403</div>
            <div class="error-icon">🚫</div>
            <h1 class="error-title">Acceso Denegado</h1>
            <p class="error-message">
                No tienes los permisos necesarios para acceder a esta sección del sistema.
            </p>
            

            
            <div class="error-actions">
                <a href="<?php echo BASE_URL; ?>dashboard" class="btn-home">
                    🏠 Ir al Dashboard
                </a>
            </div>
        </div>
    </main>
    
    <?php require_once 'views/components/footer.php'; ?>
</body>
</html>