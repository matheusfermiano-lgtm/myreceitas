<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$userId = $_SESSION['user_id'] ?? null;
$recipeId = $_GET['id'] ?? null;

if (!$userId || !$recipeId) {
    header("Location: ../users/login.php");
    exit;
}

$dao = new recipeDAO();
$dao->toggleLike($recipeId, $userId);

// Volta para a página da receita
header("Location: recipe_view.php?id=$recipeId");
exit;