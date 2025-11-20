<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada - Restaurante</title>
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
            color: #e74c3c;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
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
        
        .btn-back {
            background: #95a5a6;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .btn-back:hover {
            background: #7f8c8d;
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'views/components/header.php'; ?>
    
    <main class="">
        <div class="error-page">
            <div class="error-code">404</div>
            <h1 class="error-title">Página No Encontrada</h1>
            <p class="error-message">
                Lo sentimos, la página que estás buscando no existe o ha sido movida.
                Verifica la URL e intenta nuevamente.
            </p>
            <div class="error-actions">
                <a href="<?php echo BASE_URL; ?>dashboard" class="btn-home">
                    🏠 Ir al Dashboard
                </a>
                <a href="javascript:history.back()" class="btn-back">
                    ↩️ Volver Atrás
                </a>
            </div>
        </div>
    </main>
    
    <?php require_once 'views/components/footer.php'; ?>
</body>
</html>