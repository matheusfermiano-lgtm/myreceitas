<?php
require_once __DIR__ . '/base.php';
// Importamos o DAO e o Model para buscar os destaques
require_once __DIR__ . '/models/model/recipe.php';
require_once __DIR__ . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();
// Buscamos as 4 melhores receitas para o destaque
$destaques = $dao->getRanking(4); 
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Birthstone&family=Montserrat:wght@400;600;700&display=swap');

    .hero {
        background-image: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)), url('assets/img/fundo.png'); 
        background-size: cover;
        background-position: center;
        height: 540px; 
        display: flex;
        align-items: center;
        padding: 0 10% 80px 10%; 
        color: #f4ebd0;
        position: relative; 
        overflow: hidden;    
        box-sizing: border-box;
    }
    
    .hero-content { max-width: 500px; z-index: 2; } 
    .hero h1 { font-family: 'Birthstone', cursive; font-size: 85px; margin: 0 0 -5px 0; color: #f4ebd0; text-shadow: 2px 2px 10px rgba(0,0,0,0.7); }
    .hero p { font-family: 'Montserrat', sans-serif; font-size: 24px; margin: 0; font-weight: 700; color: #e6d8b8; text-shadow: 1px 1px 8px rgba(0,0,0,0.7); }

    /* ==========================================================================
       ESTILOS DA ONDA SIMÉTRICA (MÁXIMA PRECISÃO)
       ========================================================================== */
    .wave-container {
        position: absolute;
        bottom: -1px; /* Gruda perfeitamente na seção de baixo */
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
        z-index: 1;
    }

    .wave-container svg {
        position: relative;
        display: block;
        width: calc(100% + 1.3px); 
        height: 70px; /* Ajuste aqui a altura/profundidade das ondinhas */
    }

    .wave-container .wave-shape {
        fill: var(--bg-principal) !important; /* Mapeia a cor de fundo claro/escuro perfeitamente */
        transition: fill 0.3s ease; 
    }

    /* =========================================
       RESTO DOS SEUS ESTILOS ATUAIS
       ========================================= */
    .section-title { 
        display: flex; justify-content: space-between; align-items: baseline; 
        margin-top: 50px; border-bottom: 2px solid #e1dacb; padding-bottom: 10px;
    }
    .section-title h2 { margin: 0; color: #333; font-size: 24px;}
    .section-title a { color: #8b2538; text-decoration: none; font-weight: 600; font-size: 14px;}

    /* GRID DE DESTAQUES */
    .featured-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    .featured-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
        border: 1px solid #eee;
        display: flex;
        flex-direction: column;
    }

    .featured-card:hover { transform: translateY(-5px); }

    .card-body { padding: 20px; flex-grow: 1; }
    .card-category { color: #8b2538; font-size: 12px; font-weight: bold; text-transform: uppercase; }
    .card-title { font-size: 18px; margin: 10px 0; color: #333; }
    
    .card-footer { 
        padding: 15px 20px; 
        background: #fdfaf5; 
        border-top: 1px solid #f5f2eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-view-destaque {
        background: #8b2538;
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
    }
</style>

<div class="hero">
    <div class="hero-content">
        <h1 class="titulo-logo">MyReceitas</h1>
        <p>se a fome bateu,<br>podemos te ajudar!</p>
    </div>

    <div class="wave-container">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
            <path d="M0,60 Q90,30 180,60 T360,60 T540,60 T720,60 T900,60 T1080,60 T1260,60 T1440,60 L1440,100 L0,100 Z" class="wave-shape"></path>
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