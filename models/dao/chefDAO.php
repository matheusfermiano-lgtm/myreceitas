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
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dados) return null;

    // Transforma o array em Objeto
    $c = new Chef($dados['name'], $dados['email'], $dados['password'], $dados['id']);
    $c->setPhone($dados['phone']);
    $c->setAddress($dados['address']);
    $c->setDescription($dados['description']);
    $c->setProfessionalExperience($dados['professional_experience']);
    $c->setServicesOffered($dados['services_offered']);
    $c->setRegionOperation($dados['region_operation']);
    $c->setPhoto($dados['photo']);
    $c->setCreatedAt($dados['created_at']);
    
    return $c;
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