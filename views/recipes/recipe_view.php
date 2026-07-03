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

$userIdLogado = $_SESSION['user_id'] ?? null;

$curtiu = ($userIdLogado) ? $dao->userLiked($r->getId(), $userIdLogado) : false;
$totalLikes = $dao->getLikeCount($r->getId());

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
    <a href="recipe_list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Voltar para a lista</a>
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
        <div class="interaction-bar" style="display: flex; align-items: center; gap: 20px; margin: 20px 0;">
        <!-- Botão de Like -->
        
        <div class="interaction-bar" style="display: flex; align-items: center; gap: 20px; margin: 20px 0;">
            <!-- Se estiver logado, o link funciona. Se não, manda para o login -->
            <?php if($userIdLogado): ?>
                <a href="recipe_like_action.php?id=<?= $r->getId() ?>" style="text-decoration: none; font-size: 24px;">
                    <?= $curtiu ? '❤️' : '🤍' ?> 
                </a>
            <?php else: ?>
                <a href="../users/login.php" style="text-decoration: none; font-size: 24px;" title="Faça login para curtir">
                    🤍 
                </a>
            <?php endif; ?>
            
            <span style="font-size: 18px; color: #333;"><?= $totalLikes ?> curtidas</span>
        </div>
    </div>

    <hr>

    <!-- Seção de Feedbacks -->
    <div class="reviews-section">
        <h3>Comentários e Avaliações</h3>
        
        <!-- Formulário de Feedback (Apenas para logados) -->
        <?php if(isset($_SESSION['user_id'])): ?>
        <form action="recipe_post_review.php" method="POST" style="background: #f9f9f9; padding: 20px; border-radius: 10px;">
            <input type="hidden" name="recipe_id" value="<?= $r->getId() ?>">
            <label>Sua nota (1 a 5):</label>
            <select name="rating" required>
                <option value="5">⭐⭐⭐⭐⭐ (Incrível)</option>
                <option value="4">⭐⭐⭐⭐ (Muito bom)</option>
                <option value="3">⭐⭐⭐ (Bom)</option>
                <option value="2">⭐⭐ (Pode melhorar)</option>
                <option value="1">⭐ (Não gostei)</option>
            </select>
            <textarea name="comment" placeholder="O que achou dessa receita?" required style="width: 100%; margin-top: 10px;"></textarea>
            <button type="submit" class="btn-add">Enviar Avaliação</button>
        </form>
        <?php endif; ?>

        <div class="reviews-list" style="margin-top: 30px;">
            <?php $reviews = $dao->getReviews($r->getId()); ?>
            <?php foreach($reviews as $rev): ?>
                <div class="review-card" style="border-bottom: 1px solid #eee; padding: 15px 0;">
                    <strong><?= htmlspecialchars($rev['user_name']) ?></strong> 
                    <span style="color: #ffc107;"><?= str_repeat('⭐', $rev['rating']) ?></span>
                    <p style="margin: 5px 0;"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                    <small style="color: #999;"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
        
    </div>

</div>
</body>
</html>