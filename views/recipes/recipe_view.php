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
// CORREÇÃO: Resgatando o tipo do usuário logado para enviar ao DAO
$userTypeLogado = $_SESSION['user_type'] ?? 'user';

// CORREÇÃO: Enviando o terceiro parâmetro ($userTypeLogado) exigido pelo método atualizado
$curtiu = ($userIdLogado) ? $dao->userLiked($r->getId(), $userIdLogado, $userTypeLogado) : false;
$totalLikes = $dao->getLikeCount($r->getId());

$isRestaurant = !empty($r->getRestaurantId());

$autorNome = '';
$autorLink = '';
$autorIcon = '';

if (!empty($r->getUserId())) {
    $autorNome = $r->getOwnerName();
    $autorLink = '../user_profile.php?id=' . $r->getUserId(); // tipo user padrão
    $autorIcon = 'fa-user';
} elseif (!empty($r->getChefId())) {
    $autorNome = $r->getOwnerName();
    $autorLink = '../user_profile.php?id=' . $r->getChefId() . '&type=chef';
    $autorIcon = 'fa-utensils';
} elseif (!empty($r->getRestaurantId())) {
    $autorNome = $r->getOwnerName();
    $autorLink = '../restaurant_profile.php?id=' . $r->getRestaurantId();
    $autorIcon = 'fa-store';
}
?>



<div class="container">
    <a href="recipe_list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Voltar para a lista</a>
    <div class="view-card">
        <div class="view-header">
            <h1><?= htmlspecialchars($r->getName()) ?></h1>
            <p>No site desde: <?= date('d/m/Y', strtotime($r->getCreatedAt())) ?></p>
            <div style="margin-top: 10px;">
                <span class="badge-info"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($r->getCategory()) ?></span>
                <span class="badge-info"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($r->getPreparationTime()) ?> min</span>
                
                <?php if ($autorNome && $autorLink): ?>
                    <a href="<?= $autorLink ?>" class="badge-info" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid <?= $autorIcon ?>"></i> <?= htmlspecialchars($autorNome) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-title">Ingredientes</div>
        <br>
        <div class="content-box"><?= nl2br(htmlspecialchars($r->getIngredients())) ?></div>

        <?php if ($isRestaurant): ?>
            <div style="text-align: right; margin-bottom: 20px;">
                <h2 style="color: #e57300; font-size: 30px;">Valor: R$ <?= number_format($r->getPrice(), 2, ',', '.') ?></h2>
            </div>
            <div class="content-box" style="background: #fdf5f6; border-color: #8b2538;">
                <i class="fa-solid fa-circle-info"></i> <strong>Aviso:</strong> O modo de preparo desta receita é exclusivo deste estabelecimento.
            </div>
        <?php else: ?>
            <div class="section-title">Modo de Preparo</div>
            <br>
            <div class="content-box"><?= nl2br(htmlspecialchars($r->getDescription())) ?></div>
        <?php endif; ?>

        <div class="section-title">Feedbacks e Avaliações</div>
        
        <div class="interaction-bar" style="display: flex; align-items: center; gap: 20px; margin: 20px 0;">
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

        <hr>

        <div class="reviews-section">
            <h3>Comentários e Avaliações</h3>
            
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
        
    </div> </div> </body>
</html>