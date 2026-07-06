<?php
session_start();
require_once dirname(__DIR__) . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verifica se há ALGUÉM logado (seja user normal ou restaurante)
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['restaurant_id'])) {
        die("<div style='padding:20px; text-align:center;'><h3>Erro!</h3><p>Precisa de ter sessão iniciada para deixar uma avaliação.</p><a href='login.php'>Fazer Login</a></div>");
    }

    // Se for o restaurante a avaliar, usamos o ID dele, senão usamos o do user
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['restaurant_id'];
    $rating = (int) $_POST['rating'];
    $comment = trim($_POST['comment']);
    $type = $_POST['type'] ?? '';

    $db = database::getConexao();
    
    // ... resto do código igual ao anterior ...

    // Se a avaliação for para um CHEF
    if ($type === 'chef' && !empty($_POST['chef_id'])) {
        $chef_id = (int) $_POST['chef_id'];
        
        $stmt = $db->prepare("INSERT INTO chef_reviews (chef_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        
        if($stmt->execute([$chef_id, $user_id, $rating, $comment])) {
            // Volta para a página do perfil do chef
            header("Location: user_profile.php?id=" . $chef_id . "&type=chef");
            exit;
        }
    } 
    // Se a avaliação for para um RESTAURANTE
    elseif ($type === 'restaurant' && !empty($_POST['restaurant_id'])) {
        $restaurant_id = (int) $_POST['restaurant_id'];
        
        $stmt = $db->prepare("INSERT INTO restaurant_reviews (restaurant_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        
        if($stmt->execute([$restaurant_id, $user_id, $rating, $comment])) {
            // Volta para a página do perfil do restaurante
            header("Location: restaurant_profile.php?id=" . $restaurant_id);
            exit;
        }
    }
}

// Em caso de acesso indevido, redireciona para a página principal
header("Location: ../index.php");
exit;