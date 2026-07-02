<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/recipe.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();

if (isset($_GET['delete_id'])) {
    $dao->deleteById($_GET['delete_id']);
    echo "<div class='container'><p style='color: green; font-weight: bold;'>Receita excluída com sucesso!</p></div>";
}

// Sugestão: Use um método que traga apenas as públicas para a lista geral
// $receitas = $dao->readAll(); // removed: method may not exist on recipeDAO; using paginated read below

$limit = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$dao = new recipeDAO();
$receitas = $dao->readAllPaginated($limit, $offset);
$totalReceitas = $dao->countAllPublic();
$totalPages = ceil($totalReceitas / $limit);
?>
<style>
    .header-list { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header-list h2 { color: #8b2538; font-size: 28px; margin: 0; }
    .btn-add { background-color: #8b2538; color: white; padding: 10px 20px; text-decoration: none; border-radius: 20px; font-weight: bold; }
    
    .recipe-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
    .recipe-card { 
        background: white; 
        border: 1px solid #eaeaea; 
        border-radius: 10px; 
        padding: 20px; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        transition: transform 0.2s, box-shadow 0.2s;
    }
    /* Efeito de destaque ao passar o mouse */
    .recipe-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
    }

    .recipe-card h3 a { 
        text-decoration: none; 
        color: #8b2538; 
    }
    .recipe-card h3 a:hover { text-decoration: underline; }

    .recipe-card p { margin: 5px 0; color: #666; font-size: 14px; }
    .card-actions { margin-top: 15px; display: flex; gap: 8px; flex-wrap: wrap; }
    .card-actions a { padding: 6px 12px; border-radius: 5px; text-decoration: none; color: white; font-size: 12px; font-weight: bold; }
    
    .btn-view { background-color: #8b2538; } /* Botão Ver */
    .btn-edit { background-color: #f39c12; }
    .btn-delete { background-color: #e74c3c; }

    .pagination { display: flex; justify-content: center; align-items: center; margin: 40px 0; gap: 10px; font-family: sans-serif; }
    .pagination a { text-decoration: none; color: #8b2538; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
    .pagination a.active { background: #8b2538; color: white; border-color: #8b2538; }
    .pagination span { color: #8b2538; font-weight: bold; font-size: 20px; }

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
                <!-- Dentro do foreach($receitas as $r) -->
                <div class="card-actions">
                    <a href="recipe_view.php?id=<?= $r->getId() ?>" class="btn-view"><i class="fa-solid fa-eye"></i> Ver</a>
                    
                    <?php 
                    // Verifica se o usuário logado é o dono da receita
                    $userId = $_SESSION['user_id'] ?? null;
                    $userType = $_SESSION['user_type'] ?? null;
                    $isOwner = false;

                    if ($userType == 'user' && $r->getUserId() == $userId) $isOwner = true;
                    if ($userType == 'chef' && $r->getChefId() == $userId) $isOwner = true;
                    if ($userType == 'restaurant' && $r->getRestaurantId() == $userId) $isOwner = true;

                    if ($isOwner): ?>
                        <a href="recipe_edit.php?id=<?= $r->getId() ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Editar</a>
                        <a href="recipes_list.php?delete_id=<?= $r->getId() ?>" class="btn-delete" onclick="return confirm('Excluir?');"><i class="fa-solid fa-trash"></i></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- PAGINAÇÃO ESTILO GOOGLE -->
    <div class="pagination">
        <span>M<span style="color:red">y</span>Receit<span style="color:orange">aaaa</span>s</span>
        
        <?php if($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&lt; Anterior</a>
        <?php endif; ?>

        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Próximo &gt;</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>