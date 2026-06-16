<?php
require_once "config/database.php";

class recipeDAO {
    private $conn; 

    // Construtor: obtém a conexão
    public function __construct() {
        $this->conn = database::getConexao();
    }

    //CREATE - insere uma Receita no banco
    public function create(recipe $r) {
        $sql = "INSERT INTO receitas (nome, descricao, instrucoes, tempo_preparo, dificuldade, imagem) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $r->getNome(),
            $r->getDescricao(),
            $r->getInstrucoes(),
            $r->getTempoPreparo(),
            $r->getDificuldade(),
            $r->getImagem()
        ]);
        
        $r->setId($this->conn->lastInsertId());
        return $r; 
    }

    // READ — Busca Receita por ID
    public function read($id) {
        $sql = "SELECT * FROM receitas WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) return null;
            $r = new recipe($dados['nome'], $dados['descricao'], $dados['instrucoes'], $dados['tempo_preparo'], $dados['dificuldade'], $dados['imagem']);
            $r->setId($dados['id']);
            return $r;
    }

    // READ ALL — Retorna array de objetos Receita
    public function readAll() {
        $sql = "SELECT * FROM receitas ORDER BY nome";
        $stmt = $this->conn->query($sql);
        $receitas = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $r = new recipe(
                $dados['nome'],
                $dados['descricao'],
                $dados['instrucoes'],
                $dados['tempo_preparo'],
                $dados['dificuldade'],
                $dados['imagem']
            );

            $r->setId($dados['id']);
            $receitas[] = $r;
        }
        
        return $receitas;
    }

    // UPDATE — Atualiza dados de uma Receita
    public function update(recipe $r) {
        $sql = "UPDATE receitas SET nome = ?, descricao = ?, instrucoes = ?, tempo_preparo = ?, dificuldade = ?, imagem = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $r->getNome(),
            $r->getDescricao(),
            $r->getInstrucoes(),
            $r->getTempoPreparo(),
            $r->getDificuldade(),
            $r->getImagem(),
            $r->getId()
        ]);
        return $r;
    }

    // DELETE — Remove uma Receita do banco
    
    public function delete(recipe $r) {
        $sql = "DELETE FROM receitas WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$r->getId()]);
        return $r;
    }

    //  NOVO: DELETE por ID (sem precisar criar um objeto Pessoa)
    public function deleteById($id) {
        $sql = "DELETE FROM receitas WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }

}
?>