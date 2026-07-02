<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/recipe.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();
$r = null;
if (isset($_GET['id'])) { $r = $dao->read($_GET['id']); }

if (!$r) {
    echo "<div class='container'><p class='alert error'>Receita não encontrada!</p></div>";
    exit;
}

$isRestaurant = !empty($r->getRestaurantId());
?>

<style>
    .view-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 900px; margin: 40px auto; }
    .view-header { border-bottom: 2px solid #fbeceb; padding-bottom: 20px; margin-bottom: 30px; }
    .view-header h1 { color: #8b2538; margin: 0; font-size: 32px; }
    .badge-info { display: inline-block; background: #fdf5f6; color: #8b2538; padding: 5px 15px; border-radius: 20px; font-weight: 600; margin-right: 10px; font-size: 14px; }
    
    .section-title { color: #d37e42; font-size: 20px; font-weight: bold; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
    .content-box { background: #fefce5; padding: 20px; border-radius: 10px; border: 1px solid #e0d9b5; line-height: 1.6; color: #444; margin-bottom: 30px; }
    
    .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #8b2538; font-weight: bold; }
</style>

<div class="container">
    <a href="recipes_list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Voltar para a lista</a>
    <div class="view-card">
        <div class="view-header">
            <h1><?= htmlspecialchars($r->getName()) ?></h1>
            <p>No site desde: <?= date('d/m/Y', strtotime($r->getCreatedAt())) ?></p>
            <div style="margin-top: 10px;">
                <span class="badge-info"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($r->getCategory()) ?></span>
                <span class="badge-info"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($r->getPreparationTime()) ?> min</span>
                
                <?php if ($isRestaurant): ?>
                    <span class="badge-info" style="background:#8b2538; color:white;"><i class="fa-solid fa-shop"></i> <?= htmlspecialchars($r->getOwnerName()) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-title">Ingredientes</div>
        <div class="content-box"><?= nl2br(htmlspecialchars($r->getIngredients())) ?></div>

        <?php if ($isRestaurant): ?>
            <!-- Lógica para Restaurante -->
            <div style="text-align: right; margin-bottom: 20px;">
                <h2 style="color: #e57300; font-size: 30px;">Valor: R$ <?= number_format($r->getPrice(), 2, ',', '.') ?></h2>
            </div>
            <div class="content-box" style="background: #fdf5f6; border-color: #8b2538;">
                <i class="fa-solid fa-circle-info"></i> <strong>Aviso:</strong> O modo de preparo desta receita é exclusivo deste estabelecimento.
            </div>
        <?php else: ?>
            <!-- Lógica para Chef/Usuário -->
            <div class="section-title">Modo de Preparo</div>
            <div class="content-box"><?= nl2br(htmlspecialchars($r->getDescription())) ?></div>
        <?php endif; ?>

        <div class="section-title">Feedbacks e Avaliações</div>
        <p><em>Esta receita ainda não possui comentários.</em></p>
    </div>
</div>
</body>
</html>