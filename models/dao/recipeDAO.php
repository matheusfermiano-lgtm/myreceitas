<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/model/recipe.php'; 
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
    // Retorna apenas as receitas PÚBLICAS de um usuário comum (Para quando visitantes virem o perfil)
    public function getPublicRecipesByUser($userId) {
        $sql = "SELECT * FROM recipes WHERE user_id = :user_id AND is_public = 1 AND deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $this->mapToArray($stmt);
    }

    // Retorna todas as receitas de um Chef (Dono do perfil vê tudo)
    public function getRecipesByChef($chefId) {
        $sql = "SELECT * FROM recipes WHERE chef_id = :chef_id AND deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':chef_id', $chefId);
        $stmt->execute();
        return $this->mapToArray($stmt);
    }

    // Retorna apenas as receitas PÚBLICAS de um Chef (Visitante vê apenas as públicas)
    public function getPublicRecipesByChef($chefId) {
        $sql = "SELECT * FROM recipes WHERE chef_id = :chef_id AND is_public = 1 AND deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':chef_id', $chefId);
        $stmt->execute();
        return $this->mapToArray($stmt);
    }

    // Helper central para transformar qualquer busca em array de Objetos Recipe
    private function mapToArray($stmt) {
        $receitas = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $receitas[] = new Recipe(
                $dados['name'], 
                $dados['ingredients'], 
                $dados['description'] ?? null, 
                $dados['preparation_time'] ?? null, 
                $dados['category'] ?? null, 
                $dados['price'] ?? 0.00, 
                $dados['is_public'] ?? 1, 
                $dados['user_id'] ?? null, 
                $dados['chef_id'] ?? null, 
                $dados['restaurant_id'] ?? null,
                $dados['id'],
                $dados['created_at'] ?? null
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

    // LISTAGEM GERAL (Com paginação e sem receitas de restaurantes)
    public function readAllPaginated($limit = 30, $offset = 0) {
        $sql = "SELECT * FROM recipes 
                WHERE is_public = 1 
                AND restaurant_id IS NULL 
                AND deleted_at IS NULL 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapToArray($stmt);
    }

    public function countAllPublic() {
        $sql = "SELECT COUNT(*) as total FROM recipes WHERE is_public = 1 AND deleted_at IS NULL AND restaurant_id IS NULL";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch()['total'];
    }

    public function read($id) {
        // Query especial para trazer o nome do dono junto
        $sql = "SELECT r.*, 
                COALESCE(u.name, c.name, res.name) as owner_name 
                FROM recipes r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN chef c ON r.chef_id = c.id
                LEFT JOIN restaurants res ON r.restaurant_id = res.id
                WHERE r.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) return null;
        
        $r = new Recipe(
            $dados['name'], $dados['ingredients'], $dados['description'], 
            $dados['preparation_time'], $dados['category'], $dados['price'], 
            $dados['is_public'], $dados['user_id'], $dados['chef_id'], 
            $dados['restaurant_id'], $dados['id'], $dados['created_at']
        );
        $r->setOwnerName($dados['owner_name']);
        return $r;
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

    // Busca receitas do dono do perfil (Usado no user_profile.php)
    public function getRecipesByUser($userId) {
        $sql = "SELECT * FROM recipes WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $this->mapToArray($stmt); // Agora retorna OBJETOS
    }

    // Busca receitas favoritadas (Usado no user_profile.php)
    public function getFavoriteRecipes($userId) {
        $sql = "SELECT r.* FROM recipes r
                JOIN user_favorites uf ON r.id = uf.recipe_id
                WHERE uf.user_id = :user_id AND r.deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $this->mapToArray($stmt); // Agora retorna OBJETOS
    }
}