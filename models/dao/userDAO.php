<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
// CORREÇÃO: Incluindo a classe User para eliminar o erro de "Class not found"
require_once dirname(__DIR__) . '/model/user.php';

class userDAO {
    private $conn; 

    public function __construct() {
        $this->conn = database::getConexao();
    }

    public function create(User $u, $photo = 'default_user.png') {
        // Atualizado para salvar a foto de perfil do usuário comum
        $sql = "INSERT INTO users (name, email, password, phone, address, photo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getName(), $u->getEmail(), $u->getPassword(),
            $u->getPhone(), $u->getAddress(), $photo
        ]);
        $u->setId($this->conn->lastInsertId());
        return true; 
    }

    public function read($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dados) return null;

        $user = new User(
            $dados['name'],
            $dados['email'],
            $dados['password'],
            $dados['phone'] ?? null,
            $dados['address'] ?? null,
            $dados['photo'] ?? null,   // ← foto (6º parâmetro)
            $dados['id'],              // ← id (7º)
            $dados['created_at'] ?? null
        );
        return $user;
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
    public function update($user) {
        $sql = "UPDATE users SET 
                    name = ?,
                    email = ?,
                    password = ?,
                    phone = ?,
                    address = ?,
                    photo = ?
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getPhone(),
            $user->getAddress(),
            $user->getPhoto(),    // ← agora salva a foto
            $user->getId()
        ]);
    }

    public function getProfileData($userId) {
        $sql = "SELECT u.name, u.email, u.address, u.phone, u.created_at,
                (SELECT COUNT(*) FROM recipe_likes rl 
                 JOIN recipes r ON rl.recipe_id = r.id 
                 WHERE r.user_id = u.id) as total_likes
                FROM users u WHERE u.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteById($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);  
    }
}