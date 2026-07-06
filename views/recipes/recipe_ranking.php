<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();
$topRecipes = $dao->getRanking(10);
?>

<div class="container">
    <h1 style="color: #8b2538; text-align: center;">🏆 Top 10 MyReceitas</h1>
    <p style="text-align: center; color: #666;">As receitas mais amadas da nossa comunidade</p>

    <div class="ranking-grid" style="max-width: 800px; margin: 40px auto;">
        <?php foreach($topRecipes as $index => $recipe): ?>
            <div class="rank-card" style="display: flex; align-items: center; background: white; margin-bottom: 15px; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <div class="rank-pos" style="font-size: 32px; font-weight: bold; width: 60px; color: #dcb382;">
                    #<?= $index + 1 ?>
                </div>
                <div class="rank-info" style="flex-grow: 1;">
                    <h3 style="margin: 0;"><a href="recipe_view.php?id=<?= $recipe['id'] ?>" style="color: #8b2538; text-decoration: none;"><?= $recipe['name'] ?></a></h3>
                    <span style="font-size: 14px; color: #888;"><?= $recipe['category'] ?></span>
                </div>
                <div class="rank-stats" style="text-align: right;">
                    <div style="color: #e74c3c; font-weight: bold;">❤️ <?= $recipe['total_likes'] ?></div>
                    <div style="color: #ffc107;">⭐ <?= number_format($recipe['avg_rating'], 1) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>