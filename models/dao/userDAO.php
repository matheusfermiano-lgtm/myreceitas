<?php
require_once dirname(__DIR__, 2) . '/config/database.php';

class userDAO {
    private $conn; 

    public function __construct() {
        $this->conn = database::getConexao();
    }

    // CREATE
    public function create(User $u) {
        $sql = "INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getName(), $u->getEmail(), $u->getPassword(),
            $u->getPhone(), $u->getAddress()
        ]);
        $u->setId($this->conn->lastInsertId());
        return $u; 
    }

    // READ
    public function read($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dados) return null;
        
        return new User($dados['name'], $dados['email'], $dados['password'], $dados['phone'], $dados['address'], $dados['id']);
    }

    // READ ALL
    public function readAll() {
        $sql = "SELECT * FROM users ORDER BY name";
        $stmt = $this->conn->query($sql);
        $usuarios = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new User($dados['name'], $dados['email'], $dados['password'], $dados['phone'], $dados['address'], $dados['id']);
        }
        return $usuarios;
    }

    // UPDATE
    public function update(User $u) {
        $sql = "UPDATE users SET name = ?, email = ?, password = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getName(), $u->getEmail(), $u->getPassword(),
            $u->getPhone(), $u->getAddress(), $u->getId()
        ]);
        return $u;
    }

    // DELETE
    public function deleteById($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);  
    }

    // Busca dados consolidados do perfil
    public function getProfileData($userId) {
        $sql = "SELECT u.name, u.email, 
                (SELECT COUNT(*) FROM recipe_likes rl 
                 JOIN recipes r ON rl.recipe_id = r.id 
                 WHERE r.user_id = u.id) as total_likes
                FROM users u WHERE u.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}