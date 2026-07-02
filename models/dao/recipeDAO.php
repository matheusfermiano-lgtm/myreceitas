<?php
require_once dirname(__DIR__, 2) . '/config/database.php';

class recipeDAO {
    private $conn; 

    public function __construct() {
        $this->conn = database::getConexao();
    }

    // LISTAGEM GERAL (Apenas públicas e que NÃO são de restaurantes)
    public function readGeneral() {
        $sql = "SELECT * FROM recipes 
                WHERE is_public = 1 
                AND restaurant_id IS NULL 
                AND deleted_at IS NULL 
                ORDER BY created_at DESC";
        $stmt = $this->conn->query($sql);
        return $this->mapToArray($stmt);
    }

    // CARDÁPIO DO RESTAURANTE (Busca específica)
    public function getMenuByRestaurant($restaurantId) {
        $sql = "SELECT * FROM recipes 
                WHERE restaurant_id = :rid 
                AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':rid', $restaurantId);
        $stmt->execute();
        return $this->mapToArray($stmt);
    }

    // RANKING: TOP 10 RECEITAS (Baseado em Likes)
    public function getTopRecipes() {
        $sql = "SELECT r.*, COUNT(rl.id) as total_likes 
                FROM recipes r
                LEFT JOIN recipe_likes rl ON r.id = rl.recipe_id
                WHERE r.is_public = 1 AND r.deleted_at IS NULL
                GROUP BY r.id
                ORDER BY total_likes DESC
                LIMIT 10";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Helper para transformar resultados em Objetos Recipe
    private function mapToArray($stmt) {
        $receitas = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $receitas[] = new Recipe(
                $dados['name'], $dados['ingredients'], $dados['description'], 
                $dados['preparation_time'], $dados['category'], $dados['price'], 
                $dados['is_public'], $dados['user_id'], $dados['chef_id'], 
                $dados['restaurant_id'], $dados['id']
            );
        }
        return $receitas;
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
    // READ ALL — Retorna array de objetos Receita (Feed Global)
    public function readAll() {
        // A regra de negócio exige que receitas de restaurantes NÃO apareçam no feed geral.
        // Também filtramos apenas as receitas que são públicas (is_public = 1).
        $sql = "SELECT * FROM recipes WHERE restaurant_id IS NULL AND is_public = 1 ORDER BY created_at DESC";
        $stmt = $this->conn->query($sql);
        $receitas = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $receitas[] = new Recipe(
                $dados['name'], $dados['ingredients'], $dados['description'], 
                $dados['preparation_time'], $dados['category'], $dados['price'], 
                $dados['is_public'], $dados['user_id'], $dados['chef_id'], 
                $dados['restaurant_id'], $dados['id']
            );
        }
        return $receitas;
    }

    // Busca receitas exclusivas da página de um restaurante específico
    public function getRecipesByRestaurant($restaurantId) {
        $sql = "SELECT * FROM recipes WHERE restaurant_id = :restaurant_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':restaurant_id', $restaurantId, PDO::PARAM_INT);
        $stmt->execute();
        
        $receitas = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $receitas[] = new Recipe(
                $dados['name'], $dados['ingredients'], $dados['description'], 
                $dados['preparation_time'], $dados['category'], $dados['price'], 
                $dados['is_public'], $dados['user_id'], $dados['chef_id'], 
                $dados['restaurant_id'], $dados['id']
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