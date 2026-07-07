<?php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/models/dao/recipeDAO.php';

$userId = $_SESSION['user_id'] ?? null;
$userType = $_SESSION['user_type'] ?? 'user';

if (!$userId || $userType === 'restaurant' || $userType === 'restaurante') {
    echo "<script>window.location.href = '../index.php';</script>";
    exit;
}

$recipeDAO = new recipeDAO();
$recipes = $recipeDAO->getFavoriteRecipes($userId, $userType);

$base_path = "../"; 
?>



<div class="main-container">
    
    <div class="page-header">
        <i class="fa-solid fa-heart"></i>
        <h1>Minhas Receitas Curtidas</h1>
    </div>

    <?php if (!empty($recipes)): ?>
        <div class="recipes-grid">
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card" id="recipe-card-<?php echo $recipe->getId(); ?>">
                    
                    <?php 
                        // Trava de segurança para a imagem da receita
                        $imagemUrl = 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?q=80&w=600&auto=format&fit=cover';
                        if (method_exists($recipe, 'getPhoto') && !empty($recipe->getPhoto())) {
                            $imagemUrl = $base_path . htmlspecialchars($recipe->getPhoto());
                        } elseif (method_exists($recipe, 'getImage') && !empty($recipe->getImage())) {
                            $imagemUrl = $base_path . htmlspecialchars($recipe->getImage());
                        }
                    ?>
                    <img src="<?php echo $imagemUrl; ?>" alt="<?php echo htmlspecialchars($recipe->getName()); ?>" class="recipe-image">
                    
                    <div class="recipe-info">
                        <div>
                            <h2 class="recipe-title"><?php echo htmlspecialchars($recipe->getName()); ?></h2>
                            <p class="recipe-description">
                                <?php echo htmlspecialchars($recipe->getDescription() ?: 'Explore os detalhes para conferir os ingredientes e o modo de preparo completo deste prato.'); ?>
                            </p>
                        </div>
                        
                        <div class="card-footer">
                           <a href="recipes/recipe_view.php?id=<?php echo $recipe->getId(); ?>" class="btn-view">Ver Receita</a>
                            
                            <button class="btn-unlike" title="Remover dos favoritos" onclick="removerCurtida(<?php echo $recipe->getId(); ?>)">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-regular fa-heart"></i>
            <h2>Seu caderno de receitas está vazio</h2>
            <p>Você ainda não favoritou nenhuma receita. Navegue pelo site, encontre seus pratos prediletos e clique no ícone de coração para salvá-los aqui!</p>
            <a href="<?php echo $base_path; ?>views/recipe_list.php" class="btn-explore">Explorar Receitas</a>
        </div>
    <?php endif; ?>

</div>

<script>
function removerCurtida(recipeId) {
    if (confirm("Deseja mesmo remover esta receita do seu caderno de curtidas?")) {
        // CORREÇÃO: Adicionado 'recipes/' para achar o arquivo na pasta correta
        window.location.href = `recipes/recipe_like_action.php?id=${recipeId}&from=curtidas`;
    }
}
</script>

</body>
</html>