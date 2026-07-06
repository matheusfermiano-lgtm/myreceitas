<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/models/model/recipe.php';
require_once ROOT_PATH . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();
$ranking = $dao->getRanking(10); // Pega o top 10

require_once ROOT_PATH . '/base.php';
?>

<style>
    .rank-container { max-width: 800px; margin: 40px auto; }
    .rank-item { display: flex; align-items: center; background: white; padding: 20px; border-radius: 15px; margin-bottom: 15px; border: 1px solid #e1dacb; transition: 0.3s; }
    .rank-item:hover { transform: scale(1.02); border-color: #dcb382; }
    .pos { font-size: 32px; font-weight: 800; color: #dcb382; width: 60px; }
    .rank-content { flex-grow: 1; }
    .rank-content h3 { margin: 0; color: #8b2538; }
    .rank-stats { text-align: right; font-weight: bold; }
    .medal { font-size: 24px; margin-right: 10px; }
</style>

<div class="container">
    <div class="rank-container">
        <h1 style="text-align: center; color: #8b2538; margin-bottom: 40px;">🏆 Ranking de Receitas</h1>
        
        <?php foreach($ranking as $index => $r): ?>
            <div class="rank-item">
                <div class="pos">
                    <?php 
                    if($index == 0) echo '<span class="medal">🥇</span>';
                    elseif($index == 1) echo '<span class="medal">🥈</span>';
                    elseif($index == 2) echo '<span class="medal">🥉</span>';
                    else echo ($index + 1) . '°';
                    ?>
                </div>
                <div class="rank-content">
                    <h3><?= htmlspecialchars($r['name']) ?></h3>
                    <small><?= htmlspecialchars($r['category']) ?></small>
                </div>
                <div class="rank-stats">
                    <span style="color: #e74c3c;">❤️ <?= $r['total_likes'] ?></span><br>
                    <span style="color: #ffc107;">⭐ <?= number_format($r['avg_rating'], 1) ?></span>
                </div>
                <a href="recipe_view.php?id=<?= $r['id'] ?>" class="btn-filter" style="margin-left: 20px; text-decoration: none;">Ver</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>