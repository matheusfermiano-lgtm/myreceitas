<?php
require_once __DIR__ . '/base.php';
// Importamos o DAO e o Model para buscar os destaques
require_once __DIR__ . '/models/model/recipe.php';
require_once __DIR__ . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();
// Buscamos as 4 melhores receitas para o destaque
$destaques = $dao->getRanking(4); 
?>

<div class="hero">
    <div class="hero-content">
        <h1 class="titulo-logo">MyReceitas</h1>
        <p>se a fome bateu,<br>podemos te ajudar!</p>
    </div>

    <div class="wave-container">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path class="wave-shape" d="M0,32L60,42.7C120,53,240,75,360,80C480,85,600,75,720,58.7C840,43,960,21,1080,16C1200,11,1320,21,1380,26.7L1440,32L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,120,120,60,120L0,120Z"></path>
        </svg>
    </div>
</div>

<div class="container">
    <div class="section-title">
        <h2>Receitas em destaque</h2>
        <a href="views/recipes/recipe_list.php">Ver todas</a>
    </div>
    
    <?php if(empty($destaques)): ?>
        <p style="margin-top: 20px; color: #667;">Nenhuma receita em destaque no momento.</p>
    <?php else: ?>
        <div class="featured-grid">
            <?php foreach($destaques as $d): ?>
                <div class="featured-card">
                    <div class="card-body">
                        <span class="card-category"><?= htmlspecialchars($d['category']) ?></span>
                        <h3 class="card-title"><?= htmlspecialchars($d['name']) ?></h3>
                        <div style="color: #ffc107; font-size: 14px;">
                            <?= str_repeat('⭐', round($d['avg_rating'])) ?> 
                            <span style="color: #888;">(<?= number_format($d['avg_rating'], 1) ?>)</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <span style="color: #e74c3c; font-weight: bold;">❤️ <?= $d['total_likes'] ?></span>
                        <a href="views/recipes/recipe_view.php?id=<?= $d['id'] ?>" class="btn-view-destaque">Ver Receita</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="container" style="margin-top: 60px; margin-bottom: 80px;">
    <div class="section-title">
        <h2>Top Chefs da Semana</h2>
    </div>
    <p style="margin-top: 20px; color: #667;">Em breve, conheça nossos chefs mais bem avaliados!</p>
</div>

</body>
</html>