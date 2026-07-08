<?php
ob_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
// Ensure ROOT_PATH is defined (config.php may define it; fallback to project root)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/models/model/recipe.php'; 
require_once ROOT_PATH . '/models/dao/recipeDAO.php';

if(!isset($_SESSION)) session_start();

$dao = new recipeDAO();
$receita = null;

if (isset($_GET['id'])) {
    $receita = $dao->read($_GET['id']);
}

// Bloqueio de Segurança: Apenas o dono edita
$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];
$isOwner = false;

if ($receita) {
    if ($userType == 'user' && $receita->getUserId() == $userId) $isOwner = true;
    if ($userType == 'chef' && $receita->getChefId() == $userId) $isOwner = true;
    if ($userType == 'restaurant' && $receita->getRestaurantId() == $userId) $isOwner = true;
}

if (!$isOwner) {
    header("Location: recipe_list.php?msg=permissao_negada");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mantém o preço se for restaurante, senão 0.00
    $price = ($userType === 'restaurant') ? $_POST['price'] : 0.00;

    $receitaAtualizada = new Recipe(
        $_POST['name'], 
        $_POST['ingredients'], 
        $_POST['description'], 
        $_POST['preparation_time'], 
        $_POST['category'], 
        $price,
        $_POST['is_public']
    );
    $receitaAtualizada->setId($_POST['id']);
    
    // Importante: Passar os IDs de dono no Update para não perder a referência
    $receitaAtualizada->setUserId($receita->getUserId());
    $receitaAtualizada->setChefId($receita->getChefId());
    $receitaAtualizada->setRestaurantId($receita->getRestaurantId());

    if ($dao->update($receitaAtualizada)) {
        header("Location: recipe_view.php?id=" . $_POST['id'] . "&msg=edit_success");
        exit;
    }
}

require_once ROOT_PATH . '/base.php';
?>

<div class="container">
    <div class="form-wrapper">
        <h2 style="color: #8b2538; text-align: center;">Editar Receita</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $receita->getId() ?>">

            <div class="form-group">
                <label>Nome:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($receita->getName()) ?>" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Tempo (min):</label>
                    <input type="number" name="preparation_time" value="<?= $receita->getPreparationTime() ?>">
                </div>
                <div class="form-group">
                    <label>Categoria:</label>
                    <select name="category">
                        <option value="entrada" <?= $receita->getCategory() == 'entradas' ? 'selected' : '' ?>>Entradas</option>
                        <option value="prato principal" <?= $receita->getCategory() == 'pratos principais' ? 'selected' : '' ?>>Pratos Principais</option>
                        <option value="sobremesas" <?= $receita->getCategory() == 'sobremesas' ? 'selected' : '' ?>>Sobremesas</option>
                        <option value="doces" <?= $receita->getCategory() == 'doces' ? 'selected' : '' ?>>Doces</option>
                        <option value="carnes" <?= $receita->getCategory() == 'carnes' ? 'selected' : '' ?>>Carnes</option>
                        <option value="massas" <?= $receita->getCategory() == 'massas' ? 'selected' : '' ?>>Massas</option>
                        <option value="lanches" <?= $receita->getCategory() == 'lanches' ? 'selected' : '' ?>>Lanches</option>
                        <option value="petiscos" <?= $receita->getCategory() == 'petiscos' ? 'selected' : '' ?>>Petiscos</option>
                        <option value="saladas" <?= $receita->getCategory() == 'saladas' ? 'selected' : '' ?>>Saladas</option>
                        <option value="bolos" <?= $receita->getCategory() == 'bolos' ? 'selected' : '' ?>>Bolos</option>
                        <option value="peixes" <?= $receita->getCategory() == 'peixes' ? 'selected' : '' ?>>Peixes</option>
                        <option value="tortas" <?= $receita->getCategory() == 'tortas' ? 'selected' : '' ?>>Tortas</option>
                        <option value="sopas" <?= $receita->getCategory() == 'sopas' ? 'selected' : '' ?>>Sopas</option>
                       <option value="bebidas" <?= $receita->getCategory() == 'bebidas' ? 'selected' : '' ?>>Bebidas</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Ingredientes:</label>
                <textarea name="ingredients" rows="4" required><?= htmlspecialchars($receita->getIngredients()) ?></textarea>
            </div>

            <div class="form-group">
                <label>Modo de Preparo:</label>
                <textarea name="description" rows="5"><?= htmlspecialchars($receita->getDescription()) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Visibilidade:</label>
                    <select name="is_public">
                        <option value="1" <?= $receita->getIsPublic() == 1 ? 'selected' : '' ?>>Pública</option>
                        <option value="0" <?= $receita->getIsPublic() == 0 ? 'selected' : '' ?>>Privada</option>
                    </select>
                </div>

                <?php if ($userType === 'restaurant'): ?>
                <div class="form-group">
                    <label>Preço (R$):</label>
                    <input type="number" step="0.01" name="price" value="<?= $receita->getPrice() ?>" required>
                </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Salvar Alterações</button>
        </form>
    </div>
</div>