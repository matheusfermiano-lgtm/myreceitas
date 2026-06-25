<?php
require_once dirname(dirname(__DIR__)) . '/base.php';
require_once dirname(dirname(__DIR__)) . '/models/model/recipe.php'; 
require_once dirname(dirname(__DIR__)) . '/models/dao/recipeDAO.php';

$dao = new recipeDAO();
$receita = null;

if (isset($_GET['id'])) {
    $receita = $dao->read($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receitaAtualizada = new Recipe($_POST['name'], $_POST['ingredients'], $_POST['description'], $_POST['preparation_time'], $_POST['category'], 0.00);
    $receitaAtualizada->setId($_POST['id']);
    $dao->update($receitaAtualizada);
    echo "<div class='container'><p style='color: green; font-weight: bold;'>Receita atualizada com sucesso!</p></div>";
    $receita = $dao->read($_POST['id']); 
}
?>
<style>
    /* Reutilizando as classes de estilo do formulário de criação */
    .form-wrapper { background-color: #fbeceb; padding: 40px; border-radius: 12px; max-width: 800px; margin: 40px auto; }
    .form-group { display: flex; flex-direction: column; margin-bottom: 20px; }
    .form-group label { color: #d37e42; font-weight: 600; margin-bottom: 8px; font-size: 16px; }
    .form-group input, .form-group textarea, .form-group select { 
        background-color: #fefce5; border: 1px solid #e0d9b5; border-radius: 8px; padding: 12px; font-size: 15px; outline: none; color: #444; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }
    .btn-submit { 
        background: linear-gradient(to bottom, #ff9e22, #e57300); color: white; font-size: 20px; font-weight: bold; padding: 15px 40px; border: none; border-radius: 8px; cursor: pointer; display: block; margin: 40px auto 0;
    }
</style>

<div class="container">
    <?php if ($receita): ?>
    <div class="form-wrapper">
        <h2 style="color: #8b2538; text-align: center; margin-top: 0;">Editar Receita</h2>
        <form method="POST" action="recipe_edit.php">
            <input type="hidden" name="id" value="<?= $receita->getId() ?>">

            <div class="form-group">
                <label>Nome da receita:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($receita->getName()) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Tempo de Preparo (minutos):</label>
                <input type="text" name="preparation_time" value="<?= htmlspecialchars($receita->getPreparationTime()) ?>">
            </div>

            <div class="form-group">
                <label>Ingredientes:</label>
                <textarea name="ingredients" rows="4" required><?= htmlspecialchars($receita->getIngredients()) ?></textarea>
            </div>

            <div class="form-group">
                <label>Descrição de preparo da receita:</label>
                <textarea name="description" rows="5"><?= htmlspecialchars($receita->getDescription()) ?></textarea>
            </div>

            <div class="form-group">
                <label>Categoria:</label>
                <select name="category">
                    <option value="">Selecione...</option>
                    <option value="Doces e Sobremesas" <?= $receita->getCategory() == 'Doces e Sobremesas' ? 'selected' : '' ?>>Doces e Sobremesas</option>
                    <option value="Carnes" <?= $receita->getCategory() == 'Carnes' ? 'selected' : '' ?>>Carnes</option>
                    <option value="Massas" <?= $receita->getCategory() == 'Massas' ? 'selected' : '' ?>>Massas</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Salvar Alterações</button>
        </form>
    </div>
    <?php else: ?>
        <p>Receita não encontrada.</p>
    <?php endif; ?>
</div>
</body>
</html>