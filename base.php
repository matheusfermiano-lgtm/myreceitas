<?php
// Linha mágica da navbar: detecta automaticamente a pasta do projeto no XAMPP
$base_path = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', str_replace('\\', '/', __DIR__));
$base_path = '/' . trim($base_path, '/') . '/';
if ($base_path === '//') { $base_path = '/'; }

// CORREÇÃO DA LINHA 3 (Busca o banco direto na raiz do projeto):
require_once __DIR__ . '/config/database.php'; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyReceitas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos Globais */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #fcfcfc; 
        }
        
        /* Navbar inspirada no v0.app */
        .navbar { 
            background-color: #8b2538; /* Vermelho/Bordô da imagem */
            padding: 12px 30px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            color: white; 
        }
        .logo-container { 
            display: flex; 
            align-items: center; 
            justify-content: center;
            background-color: #dcb382; /* Cor de fundo da logo */
            border-radius: 50%; 
            width: 45px; 
            height: 45px; 
            text-decoration: none; 
            color: #8b2538; 
            font-size: 22px;
        }
        .search-bar { 
            flex: 1; 
            max-width: 800px; 
            margin: 0 30px; 
            position: relative; 
        }
        .search-bar input { 
            width: 100%; 
            padding: 12px 20px 12px 45px; 
            border-radius: 25px; 
            border: none; 
            outline: none; 
            font-size: 15px;
            box-sizing: border-box;
        }
        .search-bar i { 
            position: absolute; 
            left: 18px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #a0a0a0; 
            font-size: 16px;
        }
        .nav-icons { 
            display: flex; 
            gap: 20px; 
            align-items: center; 
        }
        .nav-icons a { 
            color: white; 
            text-decoration: none; 
            font-size: 22px;
            transition: 0.3s;
        }
        .nav-icons a:hover { color: #dcb382; }

        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="<?php echo $base_path; ?>index.php" class="logo-container">
            <i class="fa-solid fa-utensils"></i>
        </a>
        
        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Pesquisar receitas, ingredientes...">
        </div>
        
        <div class="nav-icons">
            <a href="<?php echo $base_path; ?>views/recipes/recipes_list.php" title="Todas as Receitas"><i class="fa-solid fa-book-open"></i></a>
            <a href="<?php echo $base_path; ?>views/users/users_list.php" title="Lista de Usuários"><i class="fa-solid fa-users"></i></a>
            <a href="<?php echo $base_path; ?>views/users/user_edit.php" title="Meu Perfil / Informações"><i class="fa-regular fa-user"></i></a>
        </div>
    </header>