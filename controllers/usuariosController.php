<?php
require_once 'config/permissions.php';

class UsuariosController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        $permissions = new Permissions();
        $permissions->requirePermission(2);
        
        $users = $this->getAllUsers();
        
        require_once 'views/components/header.php';
        require_once 'views/components/sidebar.php';
        require_once 'views/usuarios/index.php';
        require_once 'views/components/footer.php';
    }

    public function perfil() {
        // Cualquier usuario puede ver su propio perfil
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
        
        $user_id = $_SESSION['user_id'];
        $user = $this->getUserById($user_id);
        
        require_once 'views/components/header.php';
        require_once 'views/components/sidebar.php';
        require_once 'views/usuarios/perfil.php';
        require_once 'views/components/footer.php';
    }

    public function updateProfile() {
        error_log("🎯 updateProfile llamado - Método: " . $_SERVER['REQUEST_METHOD']);
        
        // Configurar headers primero
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        
        error_log("📝 Usuario ID: " . $user_id);
        error_log("📝 Datos POST recibidos: " . print_r($_POST, true));
        error_log("📝 Archivos recibidos: " . print_r($_FILES, true));
        
        // Verificar si es POST y tiene datos
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validar datos requeridos
                if (empty($_POST['nombre']) || empty($_POST['apellido'])) {
                    error_log("❌ Faltan datos obligatorios");
                    echo json_encode(['success' => false, 'message' => 'Nombre y apellido son obligatorios']);
                    exit();
                }

                // Obtener foto actual antes de cualquier cambio
                $fotoActual = $this->getFotoActual($user_id);
                $fotoNombre = $fotoActual; // Por defecto mantener la actual

                // Preparar consulta de actualización
                $query = "UPDATE usuarios SET 
                         nombre = :nombre, 
                         apellido = :apellido, 
                         telefono = :telefono, 
                         direccion = :direccion, 
                         correo = :correo";
                
                // Si hay una nueva foto, procesarla
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $nuevaFotoNombre = $this->guardarFotoPerfil($_FILES['foto']);
                    if ($nuevaFotoNombre) {
                        $fotoNombre = $nuevaFotoNombre;
                        $query .= ", foto = :foto";
                    }
                }
                
                $query .= " WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                
                // Asignar valores con valores por defecto si están vacíos
                $nombre = trim($_POST['nombre']);
                $apellido = trim($_POST['apellido']);
                $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : NULL;
                $direccion = !empty($_POST['direccion']) ? trim($_POST['direccion']) : NULL;
                $correo = !empty($_POST['correo']) ? trim($_POST['correo']) : NULL;
                
                $stmt->bindParam(':id', $user_id);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':correo', $correo);
                
                if (isset($nuevaFotoNombre)) {
                    $stmt->bindParam(':foto', $nuevaFotoNombre);
                }
                
                if ($stmt->execute()) {
                    // Si se cambió la foto y la anterior no era default, eliminarla
                    $fotoEliminada = false;
                    if (isset($nuevaFotoNombre) && $fotoActual && $fotoActual !== 'default.png') {
                        $fotoEliminada = $this->eliminarFotoAnterior($fotoActual);
                    }
                    
                    // Actualizar variables de sesión
                    $_SESSION['user_name'] = $nombre;
                    $_SESSION['user_fullname'] = $nombre . ' ' . $apellido;
                    $_SESSION['user_direccion'] = $direccion;
                    if (isset($nuevaFotoNombre)) {
                        $_SESSION['user_photo'] = $nuevaFotoNombre;
                    }
                    
                    error_log("✅ Perfil actualizado exitosamente para usuario ID: " . $user_id);
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Perfil actualizado correctamente' . (isset($nuevaFotoNombre) ? ' con nueva foto' : ''),
                        'user' => [
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'fullname' => $nombre . ' ' . $apellido,
                            'foto' => $fotoNombre
                        ],
                        'foto_cambiada' => isset($nuevaFotoNombre),
                        'foto_anterior_eliminada' => $fotoEliminada
                    ]);
                } else {
                    error_log("❌ Error en la ejecución de la consulta UPDATE");
                    $errorInfo = $stmt->errorInfo();
                    error_log("❌ Error SQL: " . print_r($errorInfo, true));
                    echo json_encode(['success' => false, 'message' => 'Error al ejecutar la actualización']);
                }
            } catch (Exception $e) {
                error_log("❌ Excepción en updateProfile: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        } else {
            error_log("❌ No se recibieron datos POST válidos");
            error_log("❌ Método: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['success' => false, 'message' => 'No se recibieron datos del formulario']);
        }
        exit();
    }

    public function changeProfilePassword() {
        error_log("🎯 changeProfilePassword llamado");
        
        // Configurar headers primero
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        
        // Obtener datos del cuerpo de la solicitud
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("📝 Datos JSON recibidos: " . print_r($input, true));
        
        if (!$input || !isset($input['current_password']) || !isset($input['new_password'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit();
        }

        $current_password = $input['current_password'];
        $new_password = $input['new_password'];

        try {
            // Verificar contraseña actual
            $query = "SELECT password FROM usuarios WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($current_password, $user['password'])) {
                    // Actualizar contraseña
                    $updateQuery = "UPDATE usuarios SET password = :password WHERE id = :id";
                    $updateStmt = $this->db->prepare($updateQuery);
                    
                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                    $updateStmt->bindParam(':id', $user_id);
                    $updateStmt->bindParam(':password', $hashedPassword);
                    
                    if ($updateStmt->execute()) {
                        error_log("✅ Contraseña cambiada exitosamente para usuario ID: " . $user_id);
                        echo json_encode(['success' => true, 'message' => 'Contraseña cambiada correctamente']);
                    } else {
                        error_log("❌ Error al cambiar contraseña en la base de datos");
                        echo json_encode(['success' => false, 'message' => 'Error al cambiar contraseña']);
                    }
                } else {
                    error_log("❌ Contraseña actual incorrecta para usuario ID: " . $user_id);
                    echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
                }
            } else {
                error_log("❌ Usuario no encontrado ID: " . $user_id);
                echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
            }
        } catch (Exception $e) {
            error_log("❌ Excepción en changeProfilePassword: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    public function updateProfilePhoto() {
        // Configurar headers PRIMERO - esto es crucial
        header('Content-Type: application/json');
        
        error_log("🎯 updateProfilePhoto llamado");
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        
        error_log("📝 Usuario ID: " . $user_id);
        
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }
        
        // Verificar que se recibió un archivo
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = 'No se recibió una imagen válida';
            if (isset($_FILES['foto'])) {
                $errorMsg .= ' - Error: ' . $this->getUploadError($_FILES['foto']['error']);
            }
            error_log("❌ " . $errorMsg);
            echo json_encode(['success' => false, 'message' => $errorMsg]);
            exit();
        }

        try {
            // Obtener foto actual del usuario antes de cambiarla
            $fotoActual = $this->getFotoActual($user_id);
            
            $fotoNombre = $this->guardarFotoPerfil($_FILES['foto']);
            
            if ($fotoNombre) {
                // Actualizar foto en la base de datos
                $query = "UPDATE usuarios SET foto = :foto WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':foto', $fotoNombre);
                $stmt->bindParam(':id', $user_id);
                
                if ($stmt->execute()) {
                    // Eliminar foto anterior si no es la default
                    $fotoEliminada = false;
                    if ($fotoActual && $fotoActual !== 'default.png') {
                        $fotoEliminada = $this->eliminarFotoAnterior($fotoActual);
                    }
                    
                    // Actualizar variable de sesión
                    $_SESSION['user_photo'] = $fotoNombre;
                    
                    error_log("✅ Foto de perfil actualizada exitosamente para usuario ID: " . $user_id);
                    
                    // Construir URL completa de la nueva foto
                    $fotoUrl = BASE_URL . 'assets/images/usuarios/' . $fotoNombre;
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Foto de perfil actualizada correctamente',
                        'foto_url' => $fotoUrl,
                        'foto_nombre' => $fotoNombre,
                        'foto_anterior_eliminada' => $fotoEliminada
                    ]);
                } else {
                    error_log("❌ Error al actualizar foto en la base de datos");
                    echo json_encode(['success' => false, 'message' => 'Error al guardar la foto en la base de datos']);
                }
            } else {
                error_log("❌ Error al guardar la foto");
                echo json_encode(['success' => false, 'message' => 'Error al procesar la imagen']);
            }
        } catch (Exception $e) {
            error_log("❌ Excepción en updateProfilePhoto: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        
        // Asegurarse de que no se envía nada más después del JSON
        exit();
    }

    /**
     * Eliminar foto de perfil del usuario actual
     */
    public function deleteProfilePhoto() {
        error_log("🎯 deleteProfilePhoto llamado");
        
        // Configurar headers primero
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        
        error_log("📝 Usuario ID: " . $user_id);
        
        try {
            // Obtener foto actual del usuario
            $fotoActual = $this->getFotoActual($user_id);
            
            // Si ya es la foto por defecto, no hacer nada
            if ($fotoActual === 'default.png') {
                echo json_encode(['success' => true, 'message' => 'La foto ya es la predeterminada']);
                exit();
            }
            
            // Actualizar base de datos para usar la foto por defecto
            $query = "UPDATE usuarios SET foto = 'default.png' WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            
            if ($stmt->execute()) {
                // Eliminar foto anterior del servidor
                $fotoEliminada = $this->eliminarFotoAnterior($fotoActual);
                
                // Actualizar variable de sesión
                $_SESSION['user_photo'] = 'default.png';
                
                error_log("✅ Foto de perfil eliminada exitosamente para usuario ID: " . $user_id);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Foto de perfil eliminada correctamente',
                    'foto_eliminada' => $fotoEliminada,
                    'foto_url' => BASE_URL . 'assets/images/usuarios/default.png'
                ]);
            } else {
                error_log("❌ Error al eliminar foto en la base de datos");
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la foto']);
            }
        } catch (Exception $e) {
            error_log("❌ Excepción en deleteProfilePhoto: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    /**
     * Obtener la foto actual del usuario
     */
    private function getFotoActual($user_id) {
        try {
            $query = "SELECT foto FROM usuarios WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['foto'] ?: 'default.png';
            }
            
            return 'default.png';
        } catch (Exception $e) {
            error_log("❌ Error al obtener foto actual: " . $e->getMessage());
            return 'default.png';
        }
    }

    /**
     * Eliminar foto anterior del sistema de archivos
     */
    private function eliminarFotoAnterior($fotoNombre) {
        try {
            $rutaFoto = 'assets/images/usuarios/' . $fotoNombre;
            
            // Verificar que el archivo existe y no es la default
            if ($fotoNombre !== 'default.png' && file_exists($rutaFoto) && is_file($rutaFoto)) {
                if (unlink($rutaFoto)) {
                    error_log("✅ Foto anterior eliminada del servidor: " . $fotoNombre);
                    return true;
                } else {
                    error_log("❌ No se pudo eliminar la foto anterior del servidor: " . $fotoNombre);
                    return false;
                }
            } else {
                error_log("ℹ️  Foto anterior no existe o es la default: " . $fotoNombre);
                return false;
            }
        } catch (Exception $e) {
            error_log("❌ Error al eliminar foto anterior: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpiar fotos huérfanas (fotos que no están asociadas a ningún usuario)
     */
    public function limpiarFotosHuerfanas() {
        try {
            // Obtener todas las fotos que están siendo usadas por usuarios
            $query = "SELECT foto FROM usuarios WHERE foto IS NOT NULL AND foto != 'default.png'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $fotosEnUso = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            
            // Obtener todas las fotos en el directorio
            $directorio = 'assets/images/usuarios/';
            $fotosEnDirectorio = [];
            
            if (is_dir($directorio)) {
                $archivos = scandir($directorio);
                foreach ($archivos as $archivo) {
                    if ($archivo !== '.' && $archivo !== '..' && $archivo !== 'default.png' && is_file($directorio . $archivo)) {
                        $fotosEnDirectorio[] = $archivo;
                    }
                }
            }
            
            // Encontrar fotos huérfanas (en directorio pero no en uso)
            $fotosHuerfanas = array_diff($fotosEnDirectorio, $fotosEnUso);
            $fotosEliminadas = 0;
            
            // Eliminar fotos huérfanas
            foreach ($fotosHuerfanas as $fotoHuerfana) {
                $rutaCompleta = $directorio . $fotoHuerfana;
                if (unlink($rutaCompleta)) {
                    error_log("✅ Foto huérfana eliminada: " . $fotoHuerfana);
                    $fotosEliminadas++;
                } else {
                    error_log("❌ No se pudo eliminar foto huérfana: " . $fotoHuerfana);
                }
            }
            
            return [
                'total_fotos_huerfanas' => count($fotosHuerfanas),
                'fotos_eliminadas' => $fotosEliminadas
            ];
            
        } catch (Exception $e) {
            error_log("❌ Error al limpiar fotos huérfanas: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function guardarFotoPerfil($archivo) {
        $directorio = 'assets/images/usuarios/';
        
        // Crear directorio si no existe
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0755, true)) {
                throw new Exception('No se pudo crear el directorio para las fotos');
            }
        }
        
        // Verificar que el directorio es escribible
        if (!is_writable($directorio)) {
            throw new Exception('El directorio de fotos no tiene permisos de escritura');
        }
        
        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            throw new Exception('Tipo de archivo no permitido. Use JPEG, PNG, GIF o WebP.');
        }
        
        // Validar tamaño (máximo 5MB)
        if ($archivo['size'] > 5 * 1024 * 1024) {
            throw new Exception('El archivo es demasiado grande. Máximo 5MB.');
        }
        
        // Validar que sea una imagen real
        $check = getimagesize($archivo['tmp_name']);
        if ($check === false) {
            throw new Exception('El archivo no es una imagen válida');
        }
        
        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'usuario_' . $_SESSION['user_id'] . '_' . uniqid() . '.' . $extension;
        $rutaCompleta = $directorio . $nombreArchivo;
        
        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            // Redimensionar imagen si es muy grande
            $this->redimensionarImagen($rutaCompleta, 500, 500);
            
            error_log("✅ Foto guardada: " . $nombreArchivo);
            return $nombreArchivo;
        } else {
            throw new Exception('Error al subir el archivo. Verifique los permisos del directorio.');
        }
    }

    private function redimensionarImagen($ruta, $anchoMaximo, $altoMaximo) {
        try {
            // Verificar que el archivo existe
            if (!file_exists($ruta)) {
                error_log("❌ Archivo no existe para redimensionar: " . $ruta);
                return false;
            }
            
            $info = getimagesize($ruta);
            if (!$info) {
                error_log("❌ No se pudo obtener información de la imagen: " . $ruta);
                return false;
            }

            list($anchoOriginal, $altoOriginal, $tipo) = $info;

            // Si la imagen es más pequeña que el máximo, no redimensionar
            if ($anchoOriginal <= $anchoMaximo && $altoOriginal <= $altoMaximo) {
                error_log("ℹ️  Imagen ya tiene tamaño adecuado, no se redimensiona");
                return true;
            }

            // Calcular nuevas dimensiones manteniendo proporción
            $ratio = $anchoOriginal / $altoOriginal;
            if ($anchoMaximo / $altoMaximo > $ratio) {
                $anchoMaximo = $altoMaximo * $ratio;
            } else {
                $altoMaximo = $anchoMaximo / $ratio;
            }

            // Crear imagen según el tipo
            switch ($tipo) {
                case IMAGETYPE_JPEG:
                    $imagen = imagecreatefromjpeg($ruta);
                    break;
                case IMAGETYPE_PNG:
                    $imagen = imagecreatefrompng($ruta);
                    break;
                case IMAGETYPE_GIF:
                    $imagen = imagecreatefromgif($ruta);
                    break;
                default:
                    error_log("❌ Tipo de imagen no soportado: " . $tipo);
                    return false;
            }

            if (!$imagen) {
                error_log("❌ No se pudo crear la imagen desde el archivo");
                return false;
            }

            // Crear nueva imagen redimensionada
            $nuevaImagen = imagecreatetruecolor($anchoMaximo, $altoMaximo);
            if (!$nuevaImagen) {
                imagedestroy($imagen);
                error_log("❌ No se pudo crear la nueva imagen");
                return false;
            }
            
            // Preservar transparencia para PNG y GIF
            if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
                imagecolortransparent($nuevaImagen, imagecolorallocatealpha($nuevaImagen, 0, 0, 0, 127));
                imagealphablending($nuevaImagen, false);
                imagesavealpha($nuevaImagen, true);
            }

            // Redimensionar
            $resultado = imagecopyresampled($nuevaImagen, $imagen, 0, 0, 0, 0, $anchoMaximo, $altoMaximo, $anchoOriginal, $altoOriginal);
            if (!$resultado) {
                imagedestroy($imagen);
                imagedestroy($nuevaImagen);
                error_log("❌ Error al redimensionar la imagen");
                return false;
            }

            // Guardar imagen
            $guardado = false;
            switch ($tipo) {
                case IMAGETYPE_JPEG:
                    $guardado = imagejpeg($nuevaImagen, $ruta, 90);
                    break;
                case IMAGETYPE_PNG:
                    $guardado = imagepng($nuevaImagen, $ruta, 9);
                    break;
                case IMAGETYPE_GIF:
                    $guardado = imagegif($nuevaImagen, $ruta);
                    break;
            }

            // Liberar memoria
            imagedestroy($imagen);
            imagedestroy($nuevaImagen);

            if ($guardado) {
                error_log("✅ Imagen redimensionada correctamente: " . $ruta);
                return true;
            } else {
                error_log("❌ Error al guardar la imagen redimensionada");
                return false;
            }
        } catch (Exception $e) {
            error_log("❌ Error al redimensionar imagen: " . $e->getMessage());
            return false;
        }
    }

    private function getUploadError($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir en el disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida'
        ];
        
        return $errors[$errorCode] ?? 'Error desconocido';
    }

    public function toggleStatus() {
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(2);
        
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id']) || !isset($input['estado'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit();
        }

        $id = intval($input['id']);
        $estado = intval($input['estado']);

        try {
            $query = "UPDATE usuarios SET estado = :estado WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':estado', $estado);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    public function create() {
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(2);
        
        header('Content-Type: application/json');
        
        if ($_POST) {
            try {
                if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['usuario']) || empty($_POST['password'])) {
                    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                    exit();
                }

                $checkQuery = "SELECT id FROM usuarios WHERE usuario = :usuario";
                $checkStmt = $this->db->prepare($checkQuery);
                $checkStmt->bindParam(':usuario', $_POST['usuario']);
                $checkStmt->execute();
                
                if ($checkStmt->rowCount() > 0) {
                    echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
                    exit();
                }

                $query = "INSERT INTO usuarios (nombre, apellido, usuario, password, telefono, direccion, correo, estado, fecha_registro) 
                         VALUES (:nombre, :apellido, :usuario, :password, :telefono, :direccion, :correo, 1, NOW())";
                
                $stmt = $this->db->prepare($query);
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                $stmt->bindParam(':nombre', $_POST['nombre']);
                $stmt->bindParam(':apellido', $_POST['apellido']);
                $stmt->bindParam(':usuario', $_POST['usuario']);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':telefono', $_POST['telefono']);
                $stmt->bindParam(':direccion', $_POST['direccion']);
                $stmt->bindParam(':correo', $_POST['correo']);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al crear usuario']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        exit();
    }

    public function update() {
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(2);
        
        header('Content-Type: application/json');
        
        if ($_POST && isset($_POST['id'])) {
            try {
                $query = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, usuario = :usuario, 
                         telefono = :telefono, direccion = :direccion, correo = :correo WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':nombre', $_POST['nombre']);
                $stmt->bindParam(':apellido', $_POST['apellido']);
                $stmt->bindParam(':usuario', $_POST['usuario']);
                $stmt->bindParam(':telefono', $_POST['telefono']);
                $stmt->bindParam(':direccion', $_POST['direccion']);
                $stmt->bindParam(':correo', $_POST['correo']);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        exit();
    }

    public function changePassword() {
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(2);
        
        header('Content-Type: application/json');
        
        if ($_POST && isset($_POST['id']) && isset($_POST['newPassword'])) {
            try {
                $query = "UPDATE usuarios SET password = :password WHERE id = :id";
                $stmt = $this->db->prepare($query);
                
                $hashedPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':password', $hashedPassword);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Contraseña cambiada correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al cambiar contraseña']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        exit();
    }

    public function delete() {
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(2);
        
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit();
        }

        $id = intval($input['id']);

        try {
            // Obtener foto del usuario antes de eliminarlo
            $fotoUsuario = $this->getFotoActual($id);
            
            $deletePermisos = "DELETE FROM usuario_permisos WHERE usuario_id = :id";
            $stmtPermisos = $this->db->prepare($deletePermisos);
            $stmtPermisos->bindParam(':id', $id);
            $stmtPermisos->execute();

            $deleteUser = "DELETE FROM usuarios WHERE id = :id";
            $stmtUser = $this->db->prepare($deleteUser);
            $stmtUser->bindParam(':id', $id);
            
            if ($stmtUser->execute()) {
                // Eliminar foto del usuario si no es la default
                if ($fotoUsuario && $fotoUsuario !== 'default.png') {
                    $this->eliminarFotoAnterior($fotoUsuario);
                }
                
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar usuario']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    public function getPermissions() {
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(2);
        
        header('Content-Type: application/json');
        
        if (!isset($_GET['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
            exit();
        }

        $user_id = intval($_GET['user_id']);

        try {
            $query = "SELECT id_seccion, nombre FROM secciones ORDER BY id_seccion";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $query = "SELECT seccion_id FROM usuario_permisos WHERE usuario_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $userPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            echo json_encode([
                'success' => true,
                'permissions' => $permissions,
                'userPermissions' => $userPermissions
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    public function savePermissions() {
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(2);
        
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['user_id']) || !isset($input['permisos'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit();
        }

        $user_id = intval($input['user_id']);
        $permisos = $input['permisos'];

        if ($user_id === 1) {
            echo json_encode(['success' => false, 'message' => 'No se pueden modificar los permisos del Super Admin']);
            exit();
        }

        try {
            $this->db->beginTransaction();

            $deleteQuery = "DELETE FROM usuario_permisos WHERE usuario_id = :user_id";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bindParam(':user_id', $user_id);
            $deleteStmt->execute();

            if (!empty($permisos) && is_array($permisos)) {
                $insertQuery = "INSERT INTO usuario_permisos (usuario_id, seccion_id) VALUES (:user_id, :seccion_id)";
                $insertStmt = $this->db->prepare($insertQuery);
                
                foreach ($permisos as $seccion_id) {
                    $seccion_id = intval($seccion_id);
                    $insertStmt->bindParam(':user_id', $user_id);
                    $insertStmt->bindParam(':seccion_id', $seccion_id);
                    $insertStmt->execute();
                }
            }

            $this->db->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Permisos actualizados correctamente'
            ]);

        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => 'Error al guardar permisos: ' . $e->getMessage()
            ]);
        }
        exit();
    }

    private function getAllUsers() {
        $query = "SELECT id, nombre, apellido, usuario, telefono, direccion, correo, fecha_registro, estado, foto 
                 FROM usuarios ORDER BY fecha_registro DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    private function getUserById($id) {
        $query = "SELECT id, nombre, apellido, usuario, telefono, direccion, correo, fecha_registro, estado, foto 
                  FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>