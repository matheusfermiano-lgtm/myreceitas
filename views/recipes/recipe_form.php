<?php
ob_start();
// Primeiro carregamos as configurações
require_once dirname(__DIR__, 2) . '/config/config.php';

// Agora usamos a constante ROOT_PATH que foi criada no config.php
require_once ROOT_PATH . '/config/validation.php';
require_once ROOT_PATH . '/models/model/recipe.php'; 
require_once ROOT_PATH . '/models/dao/recipeDAO.php';

if(!isset($_SESSION)) session_start();

// Bloqueio de segurança
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "views/users/login.php");
    exit;
}

$recipeError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$nomeOk, $nomeErro] = validarNome($_POST['name'] ?? '');
    if (!$nomeOk) {
        $recipeError = $nomeErro;
    } else {
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];

    // Atribuição de Dono
    $u_id = ($userType == 'user') ? $userId : null;
    $c_id = ($userType == 'chef') ? $userId : null;
    $r_id = ($userType == 'restaurant') ? $userId : null;

    // Regra de Preço (Apenas Restaurante)
    $price = ($userType === 'restaurant') ? ($_POST['price'] ?? 0.00) : 0.00;

    $novaReceita = new Recipe(
        $_POST['name'], 
        $_POST['ingredients'], 
        $_POST['description'], 
        $_POST['preparation_time'], 
        $_POST['category'], 
        $price, 
        $_POST['is_public'], 
        $u_id, $c_id, $r_id
    );

    $dao = new recipeDAO();
    if ($dao->create($novaReceita)) {
        header("Location: recipe_list.php?msg=sucesso");
        exit;
    }
    } // fim else validação nome
}

// Carrega o visual base do site
require_once ROOT_PATH . '/base.php';
?>

<div class="container">
    <div class="form-wrapper">
        <h2 style="color: #8b2538; text-align: center; margin-bottom: 30px;">
            <i class="fa-solid fa-utensils"></i> Cadastrar Nova Receita
        </h2>
        <?php if (!empty($recipeError)): ?>
            <p style="color: red; font-weight: bold; text-align: center;"><?php echo htmlspecialchars($recipeError); ?></p>
        <?php endif; ?>
        
        <form method="POST" action="recipe_form.php" id="form-cadastro" novalidate>
            <div class="form-group">
                <label>Nome da receita:</label>
                <input type="text" name="name" placeholder="Ex: Ratatouille" required data-validate-nome maxlength="100" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                <small class="field-error" style="color:red; display:none;">Use apenas letras e espaços (sem números, emojis ou caracteres especiais).</small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Tempo de Preparo (min):</label>
                    <input type="number" name="preparation_time" placeholder="45" required>
                </div>
                <div class="form-group">
                    <label>Categoria:</label>
                    <select name="category" required>
                        <option value="entradas">Entradas</option>
                        <option value="pratos principais">Pratos Principais</option>
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
            </div>

            <div class="form-group">
                <label>Ingredientes:</label>
                <textarea name="ingredients" rows="4" placeholder="Liste os ingredientes aqui..." required></textarea>
            </div>

            <div class="form-group">
                <label>Modo de Preparo:</label>
                <textarea name="description" rows="5" placeholder="Passo a passo..."></textarea>
                <?php if($_SESSION['user_type'] === 'restaurant'): ?>
                    <small style="color: #888;">* O modo de preparo ficará oculto para visitantes (segredo comercial).</small>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Visibilidade:</label>
                    <select name="is_public">
                        <option value="1">Pública (Todos veem)</option>
                        <option value="0">Privada (Só eu vejo)</option>
                    </select>
                </div>

                <?php if ($_SESSION['user_type'] === 'restaurant'): ?>
                <div class="form-group">
                    <label>Preço no Cardápio (R$):</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" required>
                </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>
    </div>
</div>