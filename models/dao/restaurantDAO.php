<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/model/restaurant.php';

class RestaurantDAO {
    private $conn;

    public function __construct() {
        $this->conn = database::getConexao();
    }

    public function create(Restaurant $r) {
        try {
            $query = "INSERT INTO restaurants (name, email, password, address, phone, location_map_link, description, photo, opening_hours, services_offered, menu_description) 
                      VALUES (:name, :email, :password, :address, :phone, :map, :desc, :photo, :hours, :serv, :menu)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindValue(':name', $r->getName());
            $stmt->bindValue(':email', $r->getEmail());
            $stmt->bindValue(':password', $r->getPassword());
            $stmt->bindValue(':address', $r->getAddress());
            $stmt->bindValue(':phone', $r->getPhone());
            $stmt->bindValue(':map', $r->getLocationMapLink());
            $stmt->bindValue(':desc', $r->getDescription());
            $stmt->bindValue(':photo', $r->getPhoto() ?? 'default_restaurant.png');
            $stmt->bindValue(':hours', $r->getOpeningHours());
            $stmt->bindValue(':serv', $r->getServicesOffered());
            $stmt->bindValue(':menu', $r->getMenuDescription());
            
            if($stmt->execute()) {
                $r->setId($this->conn->lastInsertId());
                return true;
            }
            return false;

        } catch (PDOException $e) {
            echo "<div style='background:red; color:white; padding:20px;'>";
            echo "<h3>Erro no Banco de Dados:</h3>";
            echo "Mensagem: " . $e->getMessage();
            echo "</div>";
            die(); 
        }
    }

    public function getById($id) {
        $query = "SELECT * FROM restaurants WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        $query = "SELECT * FROM restaurants ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}