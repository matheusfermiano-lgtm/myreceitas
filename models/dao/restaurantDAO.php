<?php
// models/dao/restaurantDAO.php
require_once dirname(dirname(__DIR__)) . '/config/database.php';
require_once dirname(__DIR__) . '/model/restaurant.php';

class RestaurantDAO {
    private $conn;

    public function __construct() {
        $this->conn = database::getConexao();
    }

    public function getAll() {
        $query = "SELECT * FROM restaurants ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $restaurants = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $restaurants[] = new Restaurant(
                $row['id'], $row['name'], $row['email'], $row['password'], 
                $row['address'], $row['phone'], $row['description'], $row['photo'], $row['menu_description']
            );
        }
        return $restaurants;
    }

    public function getById($id) {
        $query = "SELECT * FROM restaurants WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Restaurant(
                $row['id'], $row['name'], $row['email'], $row['password'], 
                $row['address'], $row['phone'], $row['description'], $row['photo'], $row['menu_description']
            );
        }
        return null;
    }

    public function create(Restaurant $restaurant) {
        // Agora incluindo email e password (com hash de segurança)
        $query = "INSERT INTO restaurants (name, email, password, address, phone, description, photo, menu_description) 
                  VALUES (:name, :email, :password, :address, :phone, :description, :photo, :menu_description)";
        $stmt = $this->conn->prepare($query);
        
        // Criptografando a senha antes de salvar no banco
        $hashedPassword = password_hash($restaurant->password, PASSWORD_DEFAULT);

        $stmt->bindParam(':name', $restaurant->name);
        $stmt->bindParam(':email', $restaurant->email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':address', $restaurant->address);
        $stmt->bindParam(':phone', $restaurant->phone);
        $stmt->bindParam(':description', $restaurant->description);
        $stmt->bindParam(':photo', $restaurant->photo);
        $stmt->bindParam(':menu_description', $restaurant->menu_description);
        
        return $stmt->execute();
    }

    public function addReview($restaurant_id, $user_id, $rating, $comment) {
        $query = "INSERT INTO restaurant_reviews (restaurant_id, user_id, rating, comment, created_at) 
                  VALUES (:restaurant_id, :user_id, :rating, :comment, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':restaurant_id', $restaurant_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);
        return $stmt->execute();
    }

    public function getReviews($restaurant_id) {
        $query = "SELECT r.*, u.name as user_name FROM restaurant_reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.restaurant_id = :restaurant_id ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':restaurant_id', $restaurant_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChefsByRestaurant($restaurant_id) {
        $query = "SELECT c.* FROM chef c 
                  JOIN restaurant_chefs rc ON c.id = rc.chef_id 
                  WHERE rc.restaurant_id = :restaurant_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':restaurant_id', $restaurant_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}