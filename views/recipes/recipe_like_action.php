<?php
// Pode manter as linhas de erro enquanto testa, depois pode apagá-las se quiser!

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$userId = $_SESSION['user_id'] ?? null;
// Pega o tipo de conta da sessão (se não houver, assume que é 'user' por padrão)
$userType = $_SESSION['user_type'] ?? 'user'; 
$recipeId = $_GET['id'] ?? null;

if (!$userId || !$recipeId) {
    header("Location: ../users/login.php");
    exit;
}

$dao = new recipeDAO();
// Agora passamos o tipo de conta (userType) como terceiro parâmetro!
$dao->toggleLike($recipeId, $userId, $userType);

// CORREÇÃO AQUI: Adicionado o ../ para voltar para a pasta /views/ onde receitas_curtidas está!
if (isset($_GET['from']) && $_GET['from'] === 'curtidas') {
    header("Location: ../receitas_curtidas.php");
} else {
    header("Location: recipe_view.php?id=$recipeId");
}
exit;