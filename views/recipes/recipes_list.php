<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/recipe.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();

if (isset($_GET['delete_id'])) {
    $dao->deleteById($_GET['delete_id']);
    echo "<div class='container'><p style='color: green; font-weight: bold;'>Receita excluída com sucesso!</p></div>";
}

$receitas = $dao->readAll();
?>
<style>
    .header-list { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header-list h2 { color: #8b2538; font-size: 28px; margin: 0; }
    .btn-add { background-color: #8b2538; color: white; padding: 10px 20px; text-decoration: none; border-radius: 20px; font-weight: bold; }
    
    .recipe-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
    .recipe-card { background: white; border: 1px solid #eaeaea; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .recipe-card h3 { margin: 0 0 10px 0; color: #333; }
    .recipe-card p { margin: 5px 0; color: #666; font-size: 14px; }
    .card-actions { margin-top: 15px; display: flex; gap: 10px; }
    .card-actions a { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; font-size: 13px; font-weight: bold; }
    .btn-edit { background-color: #f39c12; }
    .btn-delete { background-color: #e74c3c; }
</style>

<div class="container">
    <div class="header-list">
        <h2>Todas as Receitas</h2>
        <a href="recipe_form.php" class="btn-add">+ Nova Receita</a>
    </div>

    <div class="recipe-grid">
        <?php if(empty($receitas)): ?>
            <p>Nenhuma receita cadastrada.</p>
        <?php else: ?>
            <?php foreach($receitas as $r): ?>
                <div class="recipe-card">
                    <h3><?= htmlspecialchars($r->getName()) ?></h3>
                    <p><strong>Categoria:</strong> <?= htmlspecialchars($r->getCategory() ?: 'N/A') ?></p>
                    <p><strong>Tempo:</strong> <?= htmlspecialchars($r->getPreparationTime() ?: '--') ?> min</p>
                    <div class="card-actions">
                        <a href="recipe_edit.php?id=<?= $r->getId() ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Editar</a>
                        <a href="recipes_list.php?delete_id=<?= $r->getId() ?>" class="btn-delete" onclick="return confirm('Excluir esta receita?');"><i class="fa-solid fa-trash"></i> Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>