<?php
ob_start();
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/models/model/recipe.php'; 
require_once ROOT_PATH . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();

// Captura filtros
$filters = [
    'q' => $_GET['q'] ?? '',
    'category' => $_GET['category'] ?? '',
    'max_time' => $_GET['max_time'] ?? '' 
];

$limit = 28;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$queryParams = $_GET; // copia os parâmetros atuais (q, category, max_time)
$queryParams['page'] = $page - 1;
$prevUrl = '?' . http_build_query($queryParams);

// Usa o novo método de busca
$receitas = $dao->searchRecipes($filters, $limit, $offset);
$totalReceitas = $dao->countSearchRecipes($filters);
$totalPages = ceil($totalReceitas / $limit);

// Tratamento de exclusão
if (isset($_GET['delete_id'])) {
    $dao->deleteById($_GET['delete_id']);
    header("Location: recipes_list.php?msg=excluido");
    exit;
}
?>

<div class="container">
    <div class="header-list">
        <h2>Todas as Receitas</h2>
        <a href="recipe_form.php" class="btn-add"><i class="fa-solid fa-plus"></i> Nova Receita</a>
    </div>

    <!-- Barra de Filtros -->
    <form class="filter-section" method="GET">
        <input type="hidden" name="q" value="<?= htmlspecialchars($filters['q']) ?>">
        
        <div class="filter-group">
            <label>Categoria</label>
            <select name="category">
                <option value="">Todas</option>
                <option value="entradas" <?= $filters['category'] == 'entradas' ? 'selected' : '' ?>>Entradas</option>
                <option value="pratos principais" <?= $filters['category'] == 'pratos principais' ? 'selected' : '' ?>>Pratos Principais</option>
                <option value="sobremesas" <?= $filters['category'] == 'sobremesas' ? 'selected' : '' ?>>Sobremesas</option>
                <option value="doces" <?= $filters['category'] == 'doces' ? 'selected' : '' ?>>Doces</option>
                <option value="carnes" <?= $filters['category'] == 'carnes' ? 'selected' : '' ?>>Carnes</option>
                <option value="massas" <?= $filters['category'] == 'massas' ? 'selected' : '' ?>>Massas</option>
                <option value="lanches" <?= $filters['category'] == 'lanches' ? 'selected' : '' ?>>Lanches</option>
                <option value="petiscos" <?= $filters['category'] == 'petiscos' ? 'selected' : '' ?>>Petiscos</option>
                <option value="saladas" <?= $filters['category'] == 'saladas' ? 'selected' : '' ?>>Saladas</option>
                <option value="bolos" <?= $filters['category'] == 'bolos' ? 'selected' : '' ?>>Bolos</option>
                <option value="peixes" <?= $filters['category'] == 'peixes' ? 'selected' : '' ?>>Peixes</option>
                <option value="tortas" <?= $filters['category'] == 'tortas' ? 'selected' : '' ?>>Tortas</option>
                <option value="sopas" <?= $filters['category'] == 'sopas' ? 'selected' : '' ?>>Sopas</option>
                <option value="bebidas" <?= $filters['category'] == 'bebidas' ? 'selected' : '' ?>>Bebidas</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Tempo Máximo (min)</label>
            <input type="number" name="max_time" placeholder="Ex: 60" value="<?= $filters['max_time'] ?>">
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
            <a href="recipe_list.php" class="btn-clear">
                <i class="fa-solid fa-rotate-left"></i> Limpar
            </a>
        </div>
    </form>

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
            <span class="letter-bordo">M</span><span class="letter-bege">y</span>...
        </div>

        <div class="page-numbers">
            <?php if ($page > 1): ?>
                <?php
                $queryParams['page'] = $page - 1;
                $prevUrl = '?' . http_build_query($queryParams);
                ?>
                <a href="<?= $prevUrl ?>" class="nav-btn">&lt; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php
                $queryParams['page'] = $i;
                $linkUrl = '?' . http_build_query($queryParams);
                ?>
                <a href="<?= $linkUrl ?>" class="page-link <?= ($i == $page) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <?php
                $queryParams['page'] = $page + 1;
                $nextUrl = '?' . http_build_query($queryParams);
                ?>
                <a href="<?= $nextUrl ?>" class="nav-btn">Próximo &gt;</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>