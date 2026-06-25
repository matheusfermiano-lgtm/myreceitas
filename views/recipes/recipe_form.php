<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/recipe.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

// Simulação de usuário logado (Em um sistema real, isso vem do login)
if(!isset($_SESSION)) session_start();
$_SESSION['user_id'] = 1; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    
    // Criando o objeto receita com os novos campos (incluindo is_public e user_id)
    $novaReceita = new Recipe(
        $_POST['name'], 
        $_POST['ingredients'], 
        $_POST['description'], 
        $_POST['preparation_time'], 
        $_POST['category'], 
        0.00, // Preço (pode ser 0 para usuários comuns)
        $_POST['is_public'], 
        $userId, // Aqui ligamos ao usuário!
        null,    // chef_id
        null     // restaurant_id
    );

    $dao = new recipeDAO();
    $dao->create($novaReceita);
    echo "<div class='container'><p style='color: green; font-weight: bold;'>Receita cadastrada com sucesso!</p></div>";
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
                        <option value="Doces e Sobremesas">Doces e Sobremesas</option>
                        <option value="Carnes">Carnes</option>
                        <option value="Massas">Massas</option>
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

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>
    </div>
</div>
</body>
</html>