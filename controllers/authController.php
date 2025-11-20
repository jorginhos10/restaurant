<?php
require_once 'models/userModel.php';
require_once 'config/permissions.php'; // Agregar esta línea

class AuthController {
    public function login() {
        if ($_POST) {
            $database = new Database();
            $db = $database->getConnection();
            $userModel = new UserModel($db);

            $usuario = $_POST['usuario'];
            $password = $_POST['password'];

            $user = $userModel->login($usuario, $password);

            if ($user === 'inactive') {
                $error = "Usuario desactivado. Contacte al administrador.";
            } elseif ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_fullname'] = $user['nombre'] . ' ' . $user['apellido'];
                $_SESSION['user_photo'] = $user['foto'];
                $_SESSION['user_direccion'] = $user['direccion'];
                $_SESSION['last_activity'] = time();
                
                header('Location: ' . BASE_URL . 'dashboard');
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos";
            }
        }
        
        require_once 'views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login');
        exit();
    }
}
?>