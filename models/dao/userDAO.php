<?php
require_once "config/database.php";

class userDAO {
    private $conn; 

    // Construtor: obtém a conexão
    public function __construct() {
        $this->conn = database::getConexao();
    }

    //CREATE - insere uma Receita no banco
    public function create(user $u) {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getNome(),
            $u->getEmail(),
            $u->getSenha()
        ]);
        
        $u->setId($this->conn->lastInsertId());
        return $u; 
    }

    // READ — Busca Receita por ID
    public function read($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) return null;
            $u = new user($dados['nome'], $dados['email'], $dados['senha']);
            $u->setId($dados['id']);
            return $u;
    }

    // READ ALL — Retorna array de objetos Receita
    public function readAll() {
        $sql = "SELECT * FROM usuarios ORDER BY nome";
        $stmt = $this->conn->query($sql);
        $usuarios = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $u = new user(
                $dados['nome'],
                $dados['email'],
                $dados['senha']
            );

            $u->setId($dados['id']);
            $usuarios[] = $u;
        }
        
        return $usuarios;
    }

    // UPDATE — Atualiza dados de uma Receita
    public function update(user $u) {
        $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $u->getNome(),
            $u->getEmail(),
            $u->getSenha(),
            $u->getId()
        ]);
        return $u;
    }

    // DELETE — Remove uma Receita do banco
    
    public function delete(user $u) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$u->getId()]);
        return $u;
    }

    //  NOVO: DELETE por ID (sem precisar criar um objeto Pessoa)
    public function deleteById($id) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);  
    }
}