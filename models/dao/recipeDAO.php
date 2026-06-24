<?php
// Altere a linha de importação do banco para:
require_once dirname(dirname(__DIR__)) . '/config/database.php';

class recipeDAO {
    private $conn; 

    public function __construct() {
        $this->conn = database::getConexao();
    }

    // CREATE - Insere uma Receita no banco
    public function create(Recipe $r) {
        $sql = "INSERT INTO recipes (name, ingredients, description, preparation_time, category, price, is_public, user_id, chef_id, restaurant_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $r->getName(),
            $r->getIngredients(),
            $r->getDescription(),
            $r->getPreparationTime(),
            $r->getCategory(),
            $r->getPrice(),
            $r->getIsPublic(),
            $r->getUserId(),
            $r->getChefId(),
            $r->getRestaurantId()
        ]);
        
        $r->setId($this->conn->lastInsertId());
        return $r; 
    }

    // READ — Busca Receita por ID
    public function read($id) {
        $sql = "SELECT * FROM recipes WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) return null;
        
        $r = new Recipe(
            $dados['name'], 
            $dados['ingredients'], 
            $dados['description'], 
            $dados['preparation_time'], 
            $dados['category'], 
            $dados['price'], 
            $dados['is_public'], 
            $dados['user_id'], 
            $dados['chef_id'], 
            $dados['restaurant_id'],
            $dados['id']
        );
        return $r;
    }

    // READ ALL — Retorna array de objetos Receita
    public function readAll() {
        $sql = "SELECT * FROM recipes ORDER BY name";
        $stmt = $this->conn->query($sql);
        $receitas = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $receitas[] = new Recipe(
                $dados['name'], 
                $dados['ingredients'], 
                $dados['description'], 
                $dados['preparation_time'], 
                $dados['category'], 
                $dados['price'], 
                $dados['is_public'], 
                $dados['user_id'], 
                $dados['chef_id'], 
                $dados['restaurant_id'],
                $dados['id']
            );
        }
        
        return $receitas;
    }

    // UPDATE — Atualiza dados de uma Receita
    public function update(Recipe $r) {
        $sql = "UPDATE recipes SET name = ?, ingredients = ?, description = ?, preparation_time = ?, category = ?, price = ?, is_public = ?, user_id = ?, chef_id = ?, restaurant_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $r->getName(),
            $r->getIngredients(),
            $r->getDescription(),
            $r->getPreparationTime(),
            $r->getCategory(),
            $r->getPrice(),
            $r->getIsPublic(),
            $r->getUserId(),
            $r->getChefId(),
            $r->getRestaurantId(),
            $r->getId()
        ]);
        return $r;
    }

    // DELETE — Remove uma Receita do banco
    public function delete(Recipe $r) {
        return $this->deleteById($r->getId());
    }

    // DELETE por ID 
    public function deleteById($id) {
        $sql = "DELETE FROM recipes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}
?>