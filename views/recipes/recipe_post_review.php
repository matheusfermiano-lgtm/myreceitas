<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/dao/recipeDAO.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Segurança: Se não estiver logado ou não for POST, volta para a home
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../index.php");
    exit;
}

$recipeId = $_POST['recipe_id'];
$userId = $_SESSION['user_id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

$dao = new recipeDAO();
$success = $dao->addReview($recipeId, $userId, $rating, $comment);

if ($success) {
    header("Location: recipe_view.php?id=$recipeId&msg=review_success");
} else {
    header("Location: recipe_view.php?id=$recipeId&msg=error");
}
exit;