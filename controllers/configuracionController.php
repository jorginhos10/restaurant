<?php
require_once 'config/permissions.php';

class ConfiguracionController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        // Verificar permiso para configuración del sistema por ID (ID 8)
        $permissions = new Permissions();
        $permissions->requirePermission(8); // ID de Configuración del Sistema
        
        require_once 'views/components/header.php';
        require_once 'views/components/sidebar.php';
        require_once 'views/configuracion/index.php';
        require_once 'views/components/footer.php';
    }

    public function comercio() {
        // Verificar permiso para configuración del sistema por ID (ID 8)
        $permissions = new Permissions();
        $permissions->requirePermission(8); // ID de Configuración del Sistema
        
        // Obtener datos del comercio
        $comercio = $this->getDatosComercio();
        
        require_once 'views/components/header.php';
        require_once 'views/components/sidebar.php';
        require_once 'views/configuracion/comercio.php';
        require_once 'views/components/footer.php';
    }

    public function restaurant() {
        // Verificar permiso para configuración del sistema por ID (ID 8)
        $permissions = new Permissions();
        $permissions->requirePermission(8); // ID de Configuración del Sistema
        
        // Obtener datos del restaurant
        $restaurant = $this->getDatosRestaurant();
        
        require_once 'views/components/header.php';
        require_once 'views/components/sidebar.php';
        require_once 'views/configuracion/restaurant.php';
        require_once 'views/components/footer.php';
    }

    public function menu() {
        // Verificar permiso para configuración del sistema por ID (ID 8)
        $permissions = new Permissions();
        $permissions->requirePermission(8); // ID de Configuración del Sistema
        
        // Obtener datos del comercio para el símbolo monetario
        $comercio = $this->getDatosComercio();
        
        // Obtener configuración del menú
        $menuConfig = $this->getDatosMenu();
        
        require_once 'views/components/header.php';
        require_once 'views/components/sidebar.php';
        require_once 'views/configuracion/menu.php';
        require_once 'views/components/footer.php';
    }

    public function guardarComercio() {
        // Verificar permiso para API por ID (ID 8)
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(8); // ID de Configuración del Sistema
        
        header('Content-Type: application/json');
        
        try {
            // Verificar si la tabla existe
            if (!$this->verificarTabla('configuracion_comercio')) {
                echo json_encode(['success' => false, 'message' => 'Error: La tabla configuracion_comercio no existe']);
                return;
            }
            
            $this->db->beginTransaction();
            
            // Guardar cada campo en la tabla configuracion_comercio
            $campos = [
                'comercio_nombre', 'comercio_tipo_documento', 'comercio_numero_documento',
                'comercio_telefono', 'comercio_direccion', 'comercio_ciudad',
                'comercio_pais', 'comercio_moneda', 'comercio_simbolo_moneda',
                'comercio_email', 'comercio_website'
            ];
            
            foreach ($campos as $campo) {
                $valor = $_POST[$campo] ?? '';
                $this->guardarConfiguracionComercio($campo, $valor);
            }
            
            // Manejar la subida del logo
            if (isset($_FILES['comercio_logo']) && $_FILES['comercio_logo']['error'] === UPLOAD_ERR_OK) {
                $logoNombre = $this->guardarLogo($_FILES['comercio_logo']);
                if ($logoNombre) {
                    $this->guardarConfiguracionComercio('comercio_logo', $logoNombre);
                }
            }
            
            $this->db->commit();
            echo json_encode(['success' => true, 'message' => 'Datos del comercio guardados correctamente']);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function guardarRestaurant() {
        // Verificar permiso para API por ID (ID 8)
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(8); // ID de Configuración del Sistema
        
        header('Content-Type: application/json');
        
        try {
            // Verificar si la tabla existe
            if (!$this->verificarTabla('configuracion_restaurante')) {
                echo json_encode(['success' => false, 'message' => 'Error: La tabla configuracion_restaurante no existe']);
                return;
            }
            
            $this->db->beginTransaction();
            
            // Guardar cada campo en la tabla configuracion_restaurante
            $campos = [
                'restaurant_horario_apertura', 'restaurant_horario_cierre',
                'restaurant_dias_apertura', 'restaurant_capacidad_maxima',
                'restaurant_mesas_disponibles', 'restaurant_reservas_online',
                'restaurant_impuesto_venta', 'restaurant_propina_automatica',
                'restaurant_porcentaje_propina', 'restaurant_delivery',
                'restaurant_costo_delivery', 'restaurant_tiempo_entrega'
            ];
            
            foreach ($campos as $campo) {
                $valor = $_POST[$campo] ?? '';
                $this->guardarConfiguracionRestaurant($campo, $valor);
            }
            
            $this->db->commit();
            echo json_encode(['success' => true, 'message' => 'Configuración del restaurant guardada correctamente']);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function guardarMenu() {
        // Verificar permiso para API por ID (ID 8)
        $permissions = new Permissions();
        $permissions->checkPermissionAPI(8); // ID de Configuración del Sistema
        
        header('Content-Type: application/json');
        
        try {
            // Verificar si la tabla existe
            if (!$this->verificarTabla('configuracion_menu')) {
                // Crear tabla si no existe
                $this->crearTablaConfiguracionMenu();
            }
            
            $this->db->beginTransaction();
            
            // Guardar cada campo en la tabla configuracion_menu
            $campos = [
                'categorias_principales', 'max_productos_categoria', 'orden_categorias',
                'mostrar_precios', 'mostrar_iva', 'permitir_personalizacion',
                'max_ingredientes', 'opciones_tamanos', 'opciones_sabores',
                'opciones_adicionales', 'precio_adicionales', 'formato_impresion',
                'mostrar_codigos', 'mostrar_disponibilidad', 'mostrar_calorias',
                'activar_promociones', 'max_promociones', 'duracion_promociones',
                'mostrar_promociones_destacadas'
            ];
            
            foreach ($campos as $campo) {
                $valor = $_POST[$campo] ?? '';
                $this->guardarConfiguracionMenu($campo, $valor);
            }
            
            $this->db->commit();
            echo json_encode(['success' => true, 'message' => 'Configuración del menú guardada correctamente']);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function getDatosComercio() {
        // Verificar si la tabla existe antes de intentar consultar
        if (!$this->verificarTabla('configuracion_comercio')) {
            // Retornar valores por defecto si la tabla no existe
            return [
                'comercio_nombre' => 'Mi Restaurante',
                'comercio_tipo_documento' => 'NIT',
                'comercio_numero_documento' => '',
                'comercio_telefono' => '',
                'comercio_direccion' => '',
                'comercio_ciudad' => '',
                'comercio_pais' => 'Colombia',
                'comercio_moneda' => 'COP',
                'comercio_simbolo_moneda' => '$',
                'comercio_email' => '',
                'comercio_website' => '',
                'comercio_logo' => ''
            ];
        }

        $datos = [
            'comercio_nombre' => $this->getValorConfigComercio('comercio_nombre', 'Mi Restaurante'),
            'comercio_tipo_documento' => $this->getValorConfigComercio('comercio_tipo_documento', 'NIT'),
            'comercio_numero_documento' => $this->getValorConfigComercio('comercio_numero_documento', ''),
            'comercio_telefono' => $this->getValorConfigComercio('comercio_telefono', ''),
            'comercio_direccion' => $this->getValorConfigComercio('comercio_direccion', ''),
            'comercio_ciudad' => $this->getValorConfigComercio('comercio_ciudad', ''),
            'comercio_pais' => $this->getValorConfigComercio('comercio_pais', 'Colombia'),
            'comercio_moneda' => $this->getValorConfigComercio('comercio_moneda', 'COP'),
            'comercio_simbolo_moneda' => $this->getValorConfigComercio('comercio_simbolo_moneda', '$'),
            'comercio_email' => $this->getValorConfigComercio('comercio_email', ''),
            'comercio_website' => $this->getValorConfigComercio('comercio_website', ''),
            'comercio_logo' => $this->getValorConfigComercio('comercio_logo', '')
        ];

        return $datos;
    }

    private function getDatosRestaurant() {
        // Verificar si la tabla existe antes de intentar consultar
        if (!$this->verificarTabla('configuracion_restaurante')) {
            // Retornar valores por defecto si la tabla no existe
            return [
                'restaurant_horario_apertura' => '08:00',
                'restaurant_horario_cierre' => '22:00',
                'restaurant_dias_apertura' => 'Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
                'restaurant_capacidad_maxima' => '50',
                'restaurant_mesas_disponibles' => '20',
                'restaurant_reservas_online' => '1',
                'restaurant_impuesto_venta' => '18',
                'restaurant_propina_automatica' => '0',
                'restaurant_porcentaje_propina' => '10',
                'restaurant_delivery' => '1',
                'restaurant_costo_delivery' => '5',
                'restaurant_tiempo_entrega' => '45'
            ];
        }

        $datos = [
            'restaurant_horario_apertura' => $this->getValorConfigRestaurant('restaurant_horario_apertura', '08:00'),
            'restaurant_horario_cierre' => $this->getValorConfigRestaurant('restaurant_horario_cierre', '22:00'),
            'restaurant_dias_apertura' => $this->getValorConfigRestaurant('restaurant_dias_apertura', 'Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'),
            'restaurant_capacidad_maxima' => $this->getValorConfigRestaurant('restaurant_capacidad_maxima', '50'),
            'restaurant_mesas_disponibles' => $this->getValorConfigRestaurant('restaurant_mesas_disponibles', '20'),
            'restaurant_reservas_online' => $this->getValorConfigRestaurant('restaurant_reservas_online', '1'),
            'restaurant_impuesto_venta' => $this->getValorConfigRestaurant('restaurant_impuesto_venta', '18'),
            'restaurant_propina_automatica' => $this->getValorConfigRestaurant('restaurant_propina_automatica', '0'),
            'restaurant_porcentaje_propina' => $this->getValorConfigRestaurant('restaurant_porcentaje_propina', '10'),
            'restaurant_delivery' => $this->getValorConfigRestaurant('restaurant_delivery', '1'),
            'restaurant_costo_delivery' => $this->getValorConfigRestaurant('restaurant_costo_delivery', '5'),
            'restaurant_tiempo_entrega' => $this->getValorConfigRestaurant('restaurant_tiempo_entrega', '45')
        ];

        return $datos;
    }

    private function getDatosMenu() {
        // Verificar si la tabla existe antes de intentar consultar
        if (!$this->verificarTabla('configuracion_menu')) {
            // Retornar valores por defecto si la tabla no existe
            return [
                'categorias_principales' => 'Entradas, Platos Fuertes, Bebidas, Postres',
                'max_productos_categoria' => '20',
                'orden_categorias' => 'alfabetico',
                'mostrar_precios' => 'si',
                'mostrar_iva' => 'si',
                'permitir_personalizacion' => 'si',
                'max_ingredientes' => '10',
                'opciones_tamanos' => 'Pequeño, Mediano, Grande, Familiar',
                'opciones_sabores' => 'Original, Especial, Picante, Sin gluten',
                'opciones_adicionales' => 'Extra queso, Extra salsa, Sin cebolla, Doble carne',
                'precio_adicionales' => '2',
                'formato_impresion' => 'ticket',
                'mostrar_codigos' => 'si',
                'mostrar_disponibilidad' => 'si',
                'mostrar_calorias' => 'no',
                'activar_promociones' => 'si',
                'max_promociones' => '5',
                'duracion_promociones' => '30',
                'mostrar_promociones_destacadas' => 'si'
            ];
        }

        $datos = [
            'categorias_principales' => $this->getValorConfigMenu('categorias_principales', 'Entradas, Platos Fuertes, Bebidas, Postres'),
            'max_productos_categoria' => $this->getValorConfigMenu('max_productos_categoria', '20'),
            'orden_categorias' => $this->getValorConfigMenu('orden_categorias', 'alfabetico'),
            'mostrar_precios' => $this->getValorConfigMenu('mostrar_precios', 'si'),
            'mostrar_iva' => $this->getValorConfigMenu('mostrar_iva', 'si'),
            'permitir_personalizacion' => $this->getValorConfigMenu('permitir_personalizacion', 'si'),
            'max_ingredientes' => $this->getValorConfigMenu('max_ingredientes', '10'),
            'opciones_tamanos' => $this->getValorConfigMenu('opciones_tamanos', 'Pequeño, Mediano, Grande, Familiar'),
            'opciones_sabores' => $this->getValorConfigMenu('opciones_sabores', 'Original, Especial, Picante, Sin gluten'),
            'opciones_adicionales' => $this->getValorConfigMenu('opciones_adicionales', 'Extra queso, Extra salsa, Sin cebolla, Doble carne'),
            'precio_adicionales' => $this->getValorConfigMenu('precio_adicionales', '2'),
            'formato_impresion' => $this->getValorConfigMenu('formato_impresion', 'ticket'),
            'mostrar_codigos' => $this->getValorConfigMenu('mostrar_codigos', 'si'),
            'mostrar_disponibilidad' => $this->getValorConfigMenu('mostrar_disponibilidad', 'si'),
            'mostrar_calorias' => $this->getValorConfigMenu('mostrar_calorias', 'no'),
            'activar_promociones' => $this->getValorConfigMenu('activar_promociones', 'si'),
            'max_promociones' => $this->getValorConfigMenu('max_promociones', '5'),
            'duracion_promociones' => $this->getValorConfigMenu('duracion_promociones', '30'),
            'mostrar_promociones_destacadas' => $this->getValorConfigMenu('mostrar_promociones_destacadas', 'si')
        ];

        return $datos;
    }

    private function getValorConfigComercio($clave, $default = '') {
        try {
            $query = "SELECT valor FROM configuracion_comercio WHERE clave = :clave";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':clave', $clave);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['valor'];
            }
        } catch (PDOException $e) {
            error_log("Error al obtener configuración comercio: " . $e->getMessage());
        }
        
        return $default;
    }

    private function getValorConfigRestaurant($clave, $default = '') {
        try {
            $query = "SELECT valor FROM configuracion_restaurante WHERE clave = :clave";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':clave', $clave);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['valor'];
            }
        } catch (PDOException $e) {
            error_log("Error al obtener configuración restaurant: " . $e->getMessage());
        }
        
        return $default;
    }

    private function getValorConfigMenu($clave, $default = '') {
        try {
            $query = "SELECT valor FROM configuracion_menu WHERE clave = :clave";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':clave', $clave);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['valor'];
            }
        } catch (PDOException $e) {
            error_log("Error al obtener configuración menú: " . $e->getMessage());
        }
        
        return $default;
    }

    private function guardarConfiguracionComercio($clave, $valor) {
        try {
            // Verificar si ya existe
            $checkQuery = "SELECT id FROM configuracion_comercio WHERE clave = :clave";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':clave', $clave);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                // Actualizar
                $query = "UPDATE configuracion_comercio SET valor = :valor WHERE clave = :clave";
            } else {
                // Insertar
                $query = "INSERT INTO configuracion_comercio (clave, valor) VALUES (:clave, :valor)";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':clave', $clave);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar configuración comercio: " . $e->getMessage());
            throw new Exception('Error al guardar en la base de datos');
        }
    }

    private function guardarConfiguracionRestaurant($clave, $valor) {
        try {
            // Verificar si ya existe
            $checkQuery = "SELECT id FROM configuracion_restaurante WHERE clave = :clave";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':clave', $clave);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                // Actualizar
                $query = "UPDATE configuracion_restaurante SET valor = :valor WHERE clave = :clave";
            } else {
                // Insertar
                $query = "INSERT INTO configuracion_restaurante (clave, valor) VALUES (:clave, :valor)";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':clave', $clave);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar configuración restaurant: " . $e->getMessage());
            throw new Exception('Error al guardar en la base de datos');
        }
    }

    private function guardarConfiguracionMenu($clave, $valor) {
        try {
            // Verificar si ya existe
            $checkQuery = "SELECT id FROM configuracion_menu WHERE clave = :clave";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':clave', $clave);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                // Actualizar
                $query = "UPDATE configuracion_menu SET valor = :valor WHERE clave = :clave";
            } else {
                // Insertar
                $query = "INSERT INTO configuracion_menu (clave, valor) VALUES (:clave, :valor)";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':clave', $clave);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar configuración menú: " . $e->getMessage());
            throw new Exception('Error al guardar en la base de datos');
        }
    }

    private function guardarLogo($archivo) {
        $directorio = 'assets/images/logos/';
        
        // Crear directorio si no existe
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }
        
        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            throw new Exception('Tipo de archivo no permitido. Use JPEG, PNG, GIF o WebP.');
        }
        
        // Validar tamaño (máximo 2MB)
        if ($archivo['size'] > 2097152) {
            throw new Exception('El archivo es demasiado grande. Máximo 2MB.');
        }
        
        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'logo_' . uniqid() . '.' . $extension;
        $rutaCompleta = $directorio . $nombreArchivo;
        
        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return $nombreArchivo;
        }
        
        throw new Exception('Error al subir el archivo.');
    }

    /**
     * Verificar si una tabla existe
     */
    private function verificarTabla($nombreTabla) {
        try {
            $query = "SHOW TABLES LIKE :tabla";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':tabla', $nombreTabla);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Crear tabla de configuración de menú
     */
    private function crearTablaConfiguracionMenu() {
        try {
            $sqlMenu = "
                CREATE TABLE IF NOT EXISTS configuracion_menu (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    clave VARCHAR(100) NOT NULL UNIQUE,
                    valor TEXT,
                    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            $this->db->exec($sqlMenu);
            
            // Insertar datos iniciales para menú
            $datosMenu = [
                'categorias_principales' => 'Entradas, Platos Fuertes, Bebidas, Postres',
                'max_productos_categoria' => '20',
                'orden_categorias' => 'alfabetico',
                'mostrar_precios' => 'si',
                'mostrar_iva' => 'si',
                'permitir_personalizacion' => 'si',
                'max_ingredientes' => '10',
                'opciones_tamanos' => 'Pequeño, Mediano, Grande, Familiar',
                'opciones_sabores' => 'Original, Especial, Picante, Sin gluten',
                'opciones_adicionales' => 'Extra queso, Extra salsa, Sin cebolla, Doble carne',
                'precio_adicionales' => '2',
                'formato_impresion' => 'ticket',
                'mostrar_codigos' => 'si',
                'mostrar_disponibilidad' => 'si',
                'mostrar_calorias' => 'no',
                'activar_promociones' => 'si',
                'max_promociones' => '5',
                'duracion_promociones' => '30',
                'mostrar_promociones_destacadas' => 'si'
            ];
            
            foreach ($datosMenu as $clave => $valor) {
                $this->guardarConfiguracionMenu($clave, $valor);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error al crear tabla de configuración menú: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear las tablas si no existen
     */
    public function crearTablasConfiguracion() {
        try {
            // Crear tabla configuracion_comercio
            $sqlComercio = "
                CREATE TABLE IF NOT EXISTS configuracion_comercio (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    clave VARCHAR(100) NOT NULL UNIQUE,
                    valor TEXT,
                    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            $this->db->exec($sqlComercio);
            
            // Crear tabla configuracion_restaurante
            $sqlRestaurant = "
                CREATE TABLE IF NOT EXISTS configuracion_restaurante (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    clave VARCHAR(100) NOT NULL UNIQUE,
                    valor TEXT,
                    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            $this->db->exec($sqlRestaurant);
            
            // Crear tabla configuracion_menu
            $this->crearTablaConfiguracionMenu();
            
            // Insertar datos iniciales para comercio
            $datosComercio = [
                'comercio_nombre' => 'Mi Restaurante',
                'comercio_tipo_documento' => 'NIT',
                'comercio_numero_documento' => '',
                'comercio_telefono' => '',
                'comercio_direccion' => '',
                'comercio_ciudad' => '',
                'comercio_pais' => 'Colombia',
                'comercio_moneda' => 'COP',
                'comercio_simbolo_moneda' => '$',
                'comercio_email' => '',
                'comercio_website' => '',
                'comercio_logo' => ''
            ];
            
            foreach ($datosComercio as $clave => $valor) {
                $this->guardarConfiguracionComercio($clave, $valor);
            }
            
            // Insertar datos iniciales para restaurant
            $datosRestaurant = [
                'restaurant_horario_apertura' => '08:00',
                'restaurant_horario_cierre' => '22:00',
                'restaurant_dias_apertura' => 'Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
                'restaurant_capacidad_maxima' => '50',
                'restaurant_mesas_disponibles' => '20',
                'restaurant_reservas_online' => '1',
                'restaurant_impuesto_venta' => '18',
                'restaurant_propina_automatica' => '0',
                'restaurant_porcentaje_propina' => '10',
                'restaurant_delivery' => '1',
                'restaurant_costo_delivery' => '5',
                'restaurant_tiempo_entrega' => '45'
            ];
            
            foreach ($datosRestaurant as $clave => $valor) {
                $this->guardarConfiguracionRestaurant($clave, $valor);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error al crear tablas de configuración: " . $e->getMessage());
            return false;
        }
    }
}
?>