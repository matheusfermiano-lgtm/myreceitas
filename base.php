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

        /* =========================================
           ESTILOS DA SIDEBAR LATERAL DO USUÁRIO
           ========================================= */
        .user-sidebar {
            position: fixed;
            top: 0;
            right: -320px; /* Começa escondida fora da tela */
            width: 300px;
            height: 100vh;
            background-color: #ffffff;
            box-shadow: -4px 0 15px rgba(0,0,0,0.15);
            transition: right 0.3s ease-in-out;
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }

        .user-sidebar.ativa {
            right: 0; /* Desliza para dentro da tela */
        }

        .sidebar-header {
            background-color: #8b2538; /* Acompanha a cor da navbar */
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s, color 0.2s;
        }

        .close-btn:hover {
            color: #dcb382;
            transform: scale(1.1);
        }

        .sidebar-content {
            padding: 15px 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #333;
            text-decoration: none;
            font-size: 1.05rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .sidebar-item i {
            width: 35px; /* Trava a largura do ícone para alinhar o texto */
            font-size: 1.3rem;
            color: #8b2538;
            text-align: center;
            margin-right: 15px;
        }

        .sidebar-item:hover {
            background-color: #f9f9f9;
            border-left-color: #8b2538;
            color: #8b2538;
        }

        /* Destaque especial para o botão de Login/Cadastro */
        .sidebar-item.highlight {
            background-color: #fdf5f6; /* Fundo avermelhado bem claro */
            font-weight: bold;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }

        /* Overlay (Fundo escurecido atrás da sidebar) */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 1040;
        }

        .sidebar-overlay.ativa {
            opacity: 1;
            visibility: visible;
        }
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

            <a href="#" id="btnPerfil" title="Menu do Usuário">
                <i class="fa-regular fa-user"></i>
            </a>
        </div>
    </header>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div id="userSidebar" class="user-sidebar">
        <div class="sidebar-header">
            <h3>Minha Conta</h3>
            <button id="fecharSidebar" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="sidebar-content">
            <a href="<?php echo $base_path; ?>user/profile.php" class="sidebar-item highlight">
                <i class="fa-solid fa-user"></i>
                <span>Perfil</span>
            </a>

            <a href="<?php echo $base_path; ?>views/user/login.php" class="sidebar-item highlight">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Entrar / Cadastrar</span>
            </a>

            <a href="<?php echo $base_path; ?>views/restaurant/restaurant_list.php" class="sidebar-item">
                <i class="fa-solid fa-store"></i>
                <span>Restaurantes</span>
            </a>
            
            <a href="<?php echo $base_path; ?>views/recipes/recipes_list.php" class="sidebar-item">
                <i class="fa-solid fa-book-open"></i>
                <span>Todas as Receitas</span>
            </a>
            
            <a href="<?php echo $base_path; ?>views/users/users_list.php" class="sidebar-item">
                <i class="fa-solid fa-users"></i>
                <span>Lista de Usuários</span>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPerfil = document.getElementById('btnPerfil');
        const sidebar = document.getElementById('userSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const fecharSidebarBtn = document.getElementById('fecharSidebar');

        // Função para abrir a sidebar
        function abrirSidebar(e) {
            e.preventDefault(); // Evita que a página pule pro topo ao clicar no link "#"
            sidebar.classList.add('ativa');
            overlay.classList.add('ativa');
            document.body.style.overflow = 'hidden'; // Evita rolagem da página por baixo
        }

        // Função para fechar a sidebar
        function fecharSidebar() {
            sidebar.classList.remove('ativa');
            overlay.classList.remove('ativa');
            document.body.style.overflow = ''; // Devolve a rolagem normal
        }

        // Atrelando os eventos aos botões e ao fundo
        if (btnPerfil) btnPerfil.addEventListener('click', abrirSidebar);
        if (fecharSidebarBtn) fecharSidebarBtn.addEventListener('click', fecharSidebar);
        if (overlay) overlay.addEventListener('click', fecharSidebar);
    });
    </script>