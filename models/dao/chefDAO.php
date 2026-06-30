<?php
require_once dirname(__DIR__, 2) . '/config/database.php';

class chefDAO {
    private $conn; 

    public function __construct() {
        $this->conn = database::getConexao();
    }

    public function create(Chef $c) {
        $sql = "INSERT INTO chef (name, email, password, description, phone, address, professional_experience, services_offered, region_operation, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $c->getName(), $c->getEmail(), $c->getPassword(),
            $c->getDescription(), $c->getPhone(), $c->getAddress(),
            $c->getProfessionalExperience(), $c->getServicesOffered(),
            $c->getRegionOperation(), $c->getPhoto()
        ]);
        $c->setId($this->conn->lastInsertId());
        return $c;
    }

    public function read($id) {
        $sql = "SELECT * FROM chef WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dados) return null;

        // Retorna o objeto Chef (ajuste o construtor no Model Chef se necessário)
        return new Chef($dados['name'], $dados['email'], $dados['password'], $dados['id'], $dados['description'], $dados['professional_experience'], $dados['services_offered'], $dados['region_operation'], $dados['photo'], $dados['created_at']);
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