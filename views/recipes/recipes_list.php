<?php
ob_start();
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/recipe.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();

// Configuração da Paginação
$limit = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Busca dados
$receitas = $dao->readAllPaginated($limit, $offset);
$totalReceitas = $dao->countAllPublic();
$totalPages = ceil($totalReceitas / $limit);

// Tratamento de exclusão
if (isset($_GET['delete_id'])) {
    $dao->deleteById($_GET['delete_id']);
    header("Location: recipes_list.php?msg=excluido");
    exit;
}
?>

<style>
    .header-list { display: flex; justify-content: space-between; align-items: center; margin: 30px 0; }
    .header-list h2 { color: #8b2538; font-size: 32px; margin: 0; font-family: 'Segoe UI', sans-serif; }
    
    .btn-add { 
        background-color: #8b2538; color: white; padding: 12px 25px; 
        text-decoration: none; border-radius: 30px; font-weight: bold; 
        transition: 0.3s; box-shadow: 0 4px 10px rgba(139, 37, 56, 0.2);
    }
    .btn-add:hover { background-color: #dcb382; color: #8b2538; }

    /* Estilização dos Cards */
    .recipe-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
    
    .recipe-card { 
        background: white; border: 1px solid #e1dacb; border-radius: 15px; 
        padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
        transition: all 0.3s ease; display: flex; flex-direction: column; justify-content: space-between;
    }
    .recipe-card:hover { transform: translateY(-8px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); border-color: #dcb382; }

    .recipe-card h3 { margin: 0 0 10px 0; color: #333; font-size: 20px; line-height: 1.3; }
    .recipe-card .category { color: #8b2538; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; display: block; }
    .recipe-card .info { color: #777; font-size: 14px; margin-bottom: 20px; display: flex; gap: 15px; }
    .recipe-card .info i { color: #dcb382; }

    .card-actions { display: flex; gap: 10px; border-top: 1px solid #f5f2eb; padding-top: 15px; }
    .card-actions a { 
        padding: 10px 15px; border-radius: 8px; text-decoration: none; 
        font-size: 13px; font-weight: bold; flex: 1; text-align: center; transition: 0.2s;
    }
    
    .btn-view { background-color: #8b2538; color: white; }
    .btn-view:hover { opacity: 0.9; }
    .btn-edit { background-color: #f7f4ed; color: #8b2538; border: 1px solid #dcb382; }
    .btn-delete { background-color: #fff; color: #e74c3c; border: 1px solid #ffdada; }
    .btn-delete:hover { background-color: #e74c3c; color: white; }

    /* Paginação Estilo Google Customizada */
    .google-pagination { display: flex; flex-direction: column; align-items: center; margin: 60px 0; }
    
    .google-logo { font-family: 'Georgia', serif; font-size: 32px; font-weight: bold; margin-bottom: 15px; }
    .letter-bordo { color: #8b2538; }
    .letter-bege { color: #dcb382; }

    .page-numbers { display: flex; gap: 5px; align-items: center; }
    .page-link { 
        text-decoration: none; color: #8b2538; padding: 8px 16px; 
        border-radius: 4px; font-weight: 600; transition: 0.2s;
    }
    .page-link:hover { background-color: #f5f2eb; }
    .page-link.active { color: #333; cursor: default; pointer-events: none; }
    .page-link.active::after { 
        content: ''; display: block; width: 100%; height: 3px; 
        background: #8b2538; margin-top: 2px; border-radius: 2px;
    }
    .nav-btn { 
        color: #8b2538; font-weight: bold; text-decoration: none; 
        padding: 8px 15px; margin: 0 10px;
    }
</style>

<div class="container">
    <div class="header-list">
        <h2>Todas as Receitas</h2>
        <a href="recipe_form.php" class="btn-add"><i class="fa-solid fa-plus"></i> Nova Receita</a>
    </div>

    <div class="recipe-grid">
        <?php if(empty($receitas)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <p style="color: #888;">Nenhuma receita encontrada nesta página.</p>
            </div>
        <?php else: ?>
            <?php foreach($receitas as $r): ?>
                <div class="recipe-card">
                    <div>
                        <span class="category"><?= htmlspecialchars($r->getCategory()) ?></span>
                        <h3><?= htmlspecialchars($r->getName()) ?></h3>
                        <div class="info">
                            <span><i class="fa-regular fa-clock"></i> <?= $r->getPreparationTime() ?> min</span>
                            <span><i class="fa-regular fa-calendar"></i> <?= date('d/m', strtotime($r->getCreatedAt())) ?></span>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <a href="recipe_view.php?id=<?= $r->getId() ?>" class="btn-view"><i class="fa-solid fa-eye"></i> Ver</a>
                        
                        <?php 
                        $userId = $_SESSION['user_id'] ?? null;
                        $userType = $_SESSION['user_type'] ?? null;
                        $isOwner = false;
                        if ($userType == 'user' && $r->getUserId() == $userId) $isOwner = true;
                        if ($userType == 'chef' && $r->getChefId() == $userId) $isOwner = true;
                        if ($userType == 'restaurant' && $r->getRestaurantId() == $userId) $isOwner = true;

                        if ($isOwner): ?>
                            <a href="recipe_edit.php?id=<?= $r->getId() ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="recipes_list.php?delete_id=<?= $r->getId() ?>" class="btn-delete" onclick="return confirm('Excluir esta receita?');"><i class="fa-solid fa-trash"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- SISTEMA DE PAGINAÇÃO ESTILO GOOGLE -->
    <div class="google-pagination">
        <div class="google-logo">
            <span class="letter-bordo">M</span><span class="letter-bege">y</span><span class="letter-bordo">R</span><span class="letter-bege">e</span><span class="letter-bordo">c</span><span class="letter-bege">e</span><?php 
                for($i=1; $i<=$totalPages; $i++) {
                    echo "<span class='letter-bege'>i</span>";
                }
            ?><span class="letter-bordo">t</span><span class="letter-bege">a</span><span class="letter-bordo">s</span>
        </div>

        <div class="page-numbers">
            <?php if($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="nav-btn">&lt; Anterior</a>
            <?php endif; ?>

            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="page-link <?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="nav-btn">Próximo &gt;</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>