<?php
ob_start();
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/recipe.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';


if(!isset($_SESSION)) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type']; // 'user', 'chef' ou 'restaurant'

    // Atribuição dinâmica do dono
    $u_id = ($userType == 'user') ? $userId : null;
    $c_id = ($userType == 'chef') ? $userId : null;
    $r_id = ($userType == 'restaurant') ? $userId : null;

    $novaReceita = new Recipe(
        $_POST['name'], 
        $_POST['ingredients'], 
        $_POST['description'], 
        $_POST['preparation_time'], 
        $_POST['category'], 
        $_POST['price'] ?? 0.00, 
        $_POST['is_public'], 
        $u_id, $c_id, $r_id
    );

    $dao = new recipeDAO();
    $dao->create($novaReceita);
    header("Location: recipes_list.php?msg=sucesso");
    exit;
}
?>
<style>
    .form-wrapper { background-color: #fbeceb; padding: 40px; border-radius: 12px; max-width: 800px; margin: 40px auto; }
    .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
    .form-group { flex: 1; display: flex; flex-direction: column; }
    
    /* Estilização fiel ao PDF */
    .form-group label { color: #d37e42; font-weight: 600; margin-bottom: 8px; font-size: 16px; }
    .form-group input, .form-group textarea, .form-group select { 
        background-color: #fefce5; /* Amarelo claro da imagem */
        border: 1px solid #e0d9b5; 
        border-radius: 8px; 
        padding: 12px; 
        font-size: 15px; 
        outline: none;
        color: #444;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }
    .form-group input::placeholder, .form-group textarea::placeholder { color: #999; }
    
    .btn-submit { 
        background: linear-gradient(to bottom, #ff9e22, #e57300); /* Botão Laranja do PDF */
        color: white; 
        font-size: 20px; 
        font-weight: bold;
        padding: 15px 40px; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer; 
        display: block;
        margin: 40px auto 0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        transition: 0.2s;
    }
    .btn-submit:hover { filter: brightness(1.1); transform: translateY(-2px); }
</style>

<div class="container">
    <div class="form-wrapper">
        <form method="POST" action="recipe_form.php">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Nome da receita:</label>
                <input type="text" name="name" placeholder="Ex: Bolo de Cenoura com Chocolate" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Tempo de Preparo (minutos):</label>
                <input type="text" name="preparation_time" placeholder="Ex: 45">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Ingredientes:</label>
                <textarea name="ingredients" rows="4" placeholder="Ex: 2 xícaras de farinha, 3 ovos..." required></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Descrição de preparo da receita:</label>
                <textarea name="description" rows="5" placeholder="Passo a passo de como fazer..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Categoria:</label>
                    <select name="category">
                        <option value="">Selecione...</option>
                        <option value="entrada">Entrada</option>
                        <option value="prato principal">Prato Principal</option>
                        <option value="sobremesas">Sobremesas</option>
                        <option value="doces">Doces</option>
                        <option value="carnes">Carnes</option>
                        <option value="massas">Massas</option>
                        <option value="lanches">Lanches</option>
                        <option value="petiscos">Petiscos</option>
                        <option value="saladas">Saladas</option>
                        <option value="bolos">Bolos</option>
                        <option value="peixes">Peixes</option>
                        <option value="tortas">Tortas</option>
                        <option value="sopas">Sopas</option>
                       <option value="bebidas">Bebidas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_public">Visibilidade:</label>
                    <select name="is_public" id="is_public">
                        <option value="1">Pública (Todos podem ver)</option>
                        <option value="0">Privada (Só eu posso ver)</option>
                    </select>
                </div>
            </div>

            <?php if($_SESSION['user_type'] == 'restaurant'): ?>
                <div class="form-group">
                    <label>Preço no Cardápio (R$):</label>
                    <input type="number" step="0.01" name="price" placeholder="0,00">
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>
    </div>
</div>
</body>
</html>