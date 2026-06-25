<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/dao/userDAO.php';
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

if(!isset($_SESSION)) session_start();
$userId = $_SESSION['user_id']; 

$uDAO = new userDAO();
$rDAO = new recipeDAO();

$profile = $uDAO->getProfileData($userId);
$minhasReceitas = $rDAO->getRecipesByUser($userId);
$favoritas = $rDAO->getFavoriteRecipes($userId);
?>

<style>
    .profile-header { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .recipe-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
    .recipe-card { background: #fefce5; border: 1px solid #e0d9b5; padding: 15px; border-radius: 8px; }
    .private-badge { background: #ff4444; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
    .public-badge { background: #2ecc71; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
</style>

<div class="container">
    <div class="profile-header">
        <h1>Perfil de <?php echo $profile['name']; ?></h1>
        <p><strong>Email:</strong> <?php echo $profile['email']; ?></p>
        <p><strong>❤️ Curtidas Totais:</strong> <?php echo $profile['total_likes']; ?></p>
    </div>

    <h2>Minhas Receitas (Criadas por mim)</h2>
    <div class="recipe-grid">
        <?php foreach($minhasReceitas as $r): ?>
            <div class="recipe-card">
                <h3><?php echo $r['name']; ?></h3>
                <p><?php echo $r['category']; ?> - <?php echo $r['preparation_time']; ?> min</p>
                <?php echo $r['is_public'] ? '<span class="public-badge">Pública</span>' : '<span class="private-badge">Privada</span>'; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 style="margin-top: 40px;">Receitas Favoritas ❤️</h2>
    <div class="recipe-grid">
        <?php foreach($favoritas as $f): ?>
            <div class="recipe-card">
                <h3><?php echo $f['name']; ?></h3>
                <p>Autor: <?php echo $f['author_name'] ?? 'Desconhecido'; ?></p>
                <a href="../recipes/recipe_view.php?id=<?php echo $f['id']; ?>">Ver Receita</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>