<?php
class UserModel {
    private $conn;
    private $tableName = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($usuario, $password) {
        $query = "SELECT id, nombre, apellido, usuario, password, foto, estado, direccion 
                  FROM " . $this->tableName . " 
                  WHERE usuario = :usuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$row['estado']) {
                return 'inactive';
            }
            
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function getAllUsers() {
        $query = "SELECT id, nombre, apellido, usuario, telefono, direccion, fecha_registro, estado, foto 
                  FROM " . $this->tableName . " 
                  ORDER BY fecha_registro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function createUser($data) {
        $query = "INSERT INTO " . $this->tableName . " 
                  (nombre, apellido, usuario, password, telefono, direccion, estado, fecha_registro) 
                  VALUES (:nombre, :apellido, :usuario, :password, :telefono, :direccion, 1, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellido', $data['apellido']);
        $stmt->bindParam(':usuario', $data['usuario']);
        
        // Hashear la contraseña
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':direccion', $data['direccion']);
        
        return $stmt->execute();
    }

    public function updateUser($data) {
        $query = "UPDATE " . $this->tableName . " 
                  SET nombre = :nombre, apellido = :apellido, usuario = :usuario, 
                      telefono = :telefono, direccion = :direccion 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellido', $data['apellido']);
        $stmt->bindParam(':usuario', $data['usuario']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':direccion', $data['direccion']);
        
        return $stmt->execute();
    }

    public function updateUserStatus($id, $estado) {
        $query = "UPDATE " . $this->tableName . " SET estado = :estado WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function changePassword($id, $newPassword) {
        $query = "UPDATE " . $this->tableName . " SET password = :password WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Hashear la nueva contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':password', $hashedPassword);
        
        return $stmt->execute();
    }

    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>