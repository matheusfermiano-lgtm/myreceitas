<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/model/chef.php';

class chefDAO {
    private $conn; 

    public function __construct() {
        $this->conn = database::getConexao();
    }

    public function create(Chef $c) {
        try {
            $sql = "INSERT INTO chef (name, email, password, description, phone, address, professional_experience, services_offered, region_operation, photo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                $c->getName(), $c->getEmail(), $c->getPassword(),
                $c->getDescription(), $c->getPhone(), $c->getAddress(),
                $c->getProfessionalExperience(), $c->getServicesOffered(),
                $c->getRegionOperation(), $c->getPhoto() ?? 'default_chef.png'
            ]);
        } catch (PDOException $e) {
            die("Erro ChefDAO: " . $e->getMessage());
        }
    }

public function read($id) {
        $sql = "SELECT * FROM chef WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]); 
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        // Criamos um objeto anônimo seguro que simula os getters que a View (user_profile.php) exige.
        // Isso evita mexer na classe Chef e ignora qualquer bloqueio de propriedade privada!
return new class($row) {
            private $data;
            public function __construct($data) { $this->data = $data; }
            
            public function getName() { return $this->data['name'] ?? ''; }
            public function getEmail() { return $this->data['email'] ?? ''; }
            public function getPhone() { return $this->data['phone'] ?? ''; }
            public function getCreatedAt() { return $this->data['created_at'] ?? ''; }
            public function getAddress() { return $this->data['address'] ?? ''; }
            public function getProfessionalExperience() { return $this->data['professional_experience'] ?? ''; }
            public function getDescription() { return $this->data['description'] ?? ''; }
            public function getRegionOperation() { return $this->data['region_operation'] ?? ''; }
            public function getServicesOffered() { return $this->data['services_offered'] ?? ''; }
            public function getPhoto() { return $this->data['photo'] ?? 'default_chef.png'; }
        };
    }

    public function getProfileData($chefId) {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM recipe_likes rl 
                 JOIN recipes r ON rl.recipe_id = r.id 
                 WHERE r.chef_id = c.id) as total_likes
                FROM chef c WHERE c.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $chefId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}