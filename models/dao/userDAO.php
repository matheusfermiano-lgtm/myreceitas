<?php
// Correção definitiva: Volta 2 níveis para sair de 'dao' e 'models', chegando na raiz do projeto
require_once dirname(dirname(__DIR__)) . '/config/database.php';

class userDAO {
    private $conn; 

    public function __construct() {
        $this->conn = database::getConexao();
    }

    // CREATE - Insere um usuário no banco
    public function create(User $u) {
        $sql = "INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getName(),
            $u->getEmail(),
            $u->getPassword(),
            $u->getPhone(),
            $u->getAddress()
        ]);
        
        $u->setId($this->conn->lastInsertId());
        return $u; 
    }

    // READ — Busca usuário por ID
    public function read($id) {
        $sql = "SELECT * FROM users WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) return null;
        
        $u = new User(
            $dados['name'], 
            $dados['email'], 
            $dados['password'], 
            $dados['phone'], 
            $dados['address'], 
            $dados['id']
        );
        return $u;
    }

    // READ ALL — Retorna array de objetos User
    public function readAll() {
        $sql = "SELECT * FROM users ORDER BY name";
        $stmt = $this->conn->query($sql);
        $usuarios = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $u = new User(
                $dados['name'],
                $dados['email'],
                $dados['password'],
                $dados['phone'],
                $dados['address'],
                $dados['id']
            );
            $usuarios[] = $u;
        }
        
        return $usuarios;
    }

    // UPDATE — Atualiza dados do usuário
    public function update(User $u) {
        $sql = "UPDATE users SET name = ?, email = ?, password = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getName(),
            $u->getEmail(),
            $u->getPassword(),
            $u->getPhone(),
            $u->getAddress(),
            $u->getId()
        ]);
        return $u;
    }

    // DELETE — Remove um usuário do banco recebendo o objeto
    public function delete(User $u) {
        return $this->deleteById($u->getId());
    }

    // DELETE por ID
    public function deleteById($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);  
    }
}
?>