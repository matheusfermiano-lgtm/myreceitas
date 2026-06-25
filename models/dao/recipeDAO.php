<?php
require_once dirname(__DIR__, 2) . '/config/database.php';

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
        
        return new Recipe(
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

    // UPDATE
    public function update(Recipe $r) {
        $sql = "UPDATE recipes SET name = ?, ingredients = ?, description = ?, preparation_time = ?, category = ?, price = ?, is_public = ?, user_id = ?, chef_id = ?, restaurant_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $r->getName(), $r->getIngredients(), $r->getDescription(),
            $r->getPreparationTime(), $r->getCategory(), $r->getPrice(),
            $r->getIsPublic(), $r->getUserId(), $r->getChefId(), $r->getRestaurantId(),
            $r->getId()
        ]);
        return $r;
    }

    // DELETE
    public function deleteById($id) {
        $sql = "DELETE FROM recipes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }

    // Busca receitas criadas pelo usuário (Públicas e Privadas)
    public function getRecipesByUser($userId) {
        $sql = "SELECT * FROM recipes WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca receitas que o usuário favoritou (Com nome do autor)
    public function getFavoriteRecipes($userId) {
        $sql = "SELECT r.*, u.name as author_name 
                FROM recipes r
                JOIN user_favorites uf ON r.id = uf.recipe_id
                LEFT JOIN users u ON r.user_id = u.id
                WHERE uf.user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}