<?php
// Linha mágica da navbar: detecta automaticamente a pasta do projeto no XAMPP
// No início do base.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Detecta se o usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
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
            /* NOVO: Fundo em off-white/creme */
            background-color: #f5f2eb; 
        }
        
        /* Navbar inspirada no v0.app */
        .navbar { 
            background-color: #8b2538;
            padding: 12px 30px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            color: white; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo-container { 
            display: flex; 
            align-items: center; 
            justify-content: center;
            background-color: #dcb382;
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
            /* NOVO: Fundo branco com borda creme para destacar do fundo da página */
            border: 1px solid #e1dacb; 
            background-color: #ffffff;
            outline: none; 
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s;
        }
        .search-bar input:focus {
            border-color: #8b2538;
            box-shadow: 0 0 8px rgba(139, 37, 56, 0.2);
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
            right: -320px;
            width: 300px;
            height: 100vh;
            /* NOVO: Sidebar em branco puro para contrastar com o fundo */
            background-color: #ffffff;
            border-left: 1px solid #e1dacb;
            box-shadow: -5px 0 25px rgba(0,0,0,0.08);
            transition: right 0.3s ease-in-out;
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }

        .user-sidebar.ativa {
            right: 0;
        }

        .sidebar-header {
            background-color: #8b2538;
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
            /* NOVO: Efeito de hover agora em tom bege */
            background-color: #f7f4ed;
            border-left-color: #8b2538;
            color: #8b2538;
        }

        /* Destaque especial para o botão de Login/Cadastro */
        .sidebar-item.highlight {
            /* NOVO: Destaque com fundo creme quente */
            background-color: #faf5ec; 
            font-weight: bold;
            border-bottom: 1px solid #eee2cc;
            margin-bottom: 10px;
        }

        /* Overlay (Fundo escurecido atrás da sidebar) */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(40, 30, 20, 0.4); /* Fundo um pouco mais quente e aconchegante */
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 1040;
        }

        .sidebar-overlay.ativa {
            opacity: 1;
            visibility: visible;
        }

 /* =========================================
   VARIÁVEIS DE TEMA (MODO CLARO / VERMELHO ESCURO)
   ========================================= */
:root {
    --bg-principal: #fcfcfc;
    --bg-navbar: #8b2538;
    --bg-sidebar: #ffffff;
    --texto-principal: #333333;
    --borda-item: #eee;
    --sombra: rgba(0,0,0,0.15);
    --bg-toggle-capsula: #FAF7F0; /* Creme original */
    --cor-toggle-elementos: #8b2538; /* Bordô original */
}

body.dark-theme {
    --bg-principal: #6d0000; /* Fundo geral: Vermelho bem escuro/Burgundy */
    --bg-navbar: #6d0000;    /* Navbar: Tom de vinho quase preto */
    --bg-sidebar: #6d0000;   /* Sidebar: Vermelho escuro para destacar do fundo */
    --texto-principal: #fceef0; /* Texto: Branco sutilmente rosado para leitura confortável */
    --borda-item: #6d0000;   /* Linhas divisórias em vermelho fosco */
    --sombra: rgba(0,0,0,0.4);
    --bg-toggle-capsula: #6d0000; /* Fundo da cápsula do botão no modo escuro */
    --cor-toggle-elementos: #dcb382; /* Elementos do botão viram dourado */

    
    color: #FAF7F0 !important; /* Cor off-white */
}

body.dark-theme .section-title h2 { 
    color: #FAF7F0 !important; /* Cor off-white */
}
/* Para o link "Ver todas" mudar no modo escuro */
body.dark-theme .section-title a { 
    color: #FAF7F0 !important; /* Cor off-white */
}

/* Para o texto "Nenhuma receita em destaque no momento" */
body.dark-theme .container p { 
    color: #FAF7F0 !important; /* Cor off-white */
}


/* Aplicando as variáveis nos elementos do seu site */
body { 
    background-color: var(--bg-principal) !important; 
    color: var(--texto-principal) !important;
    transition: background-color 0.3s, color 0.3s;
}
.navbar { background-color: var(--bg-navbar) !important; }
.user-sidebar { background-color: var(--bg-sidebar) !important; box-shadow: -4px 0 15px var(--sombra) !important; }
.sidebar-item { color: var(--texto-principal) !important; }
.sidebar-content { flex: 1; } 

body.dark-theme .sidebar-item i { color: #dcb382 !important; }
body.dark-theme .sidebar-item:hover { color: #dcb382 !important; border-left-color: #dcb382 !important; }

/* Estilos estruturais do Switch (Garante o visual arredondado) */
.theme-toggle__switch {
    position: relative;
    width: 46px;
    height: 24px;
    background-color: #C1C3C6;
    border-radius: 24px;
    transition: background-color 0.3s;
}
.theme-toggle__switch::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background-color: var(--cor-toggle-elementos);
    border-radius: 50%;
    transition: transform 0.3s ease, background-color 0.3s;
}
.theme-toggle__checkbox:checked + .theme-toggle__container .theme-toggle__switch::after {
    transform: translateX(22px);
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
            
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo $base_path; ?>views/user_profile.php" class="sidebar-item highlight">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Meu Perfil</span>
                </a>
                <a href="<?php echo $base_path; ?>views/logout.php" class="sidebar-item" style="color: #8b2538;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Sair da Conta</span>
                </a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>views/login.php" class="sidebar-item highlight">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Entrar / Cadastrar</span>
                </a>
            <?php endif; ?>

            <hr style="border: 0; border-top: 1px solid #eee2cc; margin: 10px 20px;">

            <a href="<?php echo $base_path; ?>views/restaurant/restaurant_list.php" class="sidebar-item">
                <i class="fa-solid fa-store"></i>
                <span>Restaurantes</span>
            </a>
            
            <a href="<?php echo $base_path; ?>views/recipes/recipes_list.php" class="sidebar-item">
                <i class="fa-solid fa-book-open"></i>
                <span>Todas as Receitas</span>
            </a>

            <a href="<?php echo $base_path; ?>views/chefs/chef_list.php" class="sidebar-item">
                <i class="fa-solid fa-utensils"></i>
                <span>Nossos Chefs</span>
            </a>
            <div class="sidebar-toggle-container" style="padding: 20px 25px; margin-top: auto; border-top: 1px solid var(--borda-item);">
    <label class="theme-toggle" style="cursor: pointer; display: block; width: 100%;">
        <input type="checkbox" id="dark-mode-switch" class="theme-toggle__checkbox" style="display: none;" checked>
        <div class="theme-toggle__container" style="display: flex; align-items: center; justify-content: space-between; background-color: var(--bg-toggle-capsula); padding: 12px 20px; border-radius: 50px; transition: background-color 0.3s;">
            <div class="theme-toggle__info" style="display: flex; align-items: center; gap: 12px; color: var(--cor-toggle-elementos); transition: color 0.3s;">
                <i class="fa-regular fa-sun theme-toggle__icon" id="theme-icon" style="font-size: 1.2rem;"></i>
                <span class="theme-toggle__text" id="theme-text" style="font-size: 1rem; font-weight: 600;">Modo claro</span>
            </div>
            <div class="theme-toggle__switch"></div>
        </div>
    </label>
</div>
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
            e.preventDefault(); 
            sidebar.classList.add('ativa');
            overlay.classList.add('ativa');
            document.body.style.overflow = 'hidden'; 
        }

        // Função para fechar a sidebar
        function fecharSidebar() {
            sidebar.classList.remove('ativa');
            overlay.classList.remove('ativa');
            document.body.style.overflow = ''; 
        }

        // Atrelando os eventos aos botões e ao fundo
        if (btnPerfil) btnPerfil.addEventListener('click', abrirSidebar);
        if (fecharSidebarBtn) fecharSidebarBtn.addEventListener('click', fecharSidebar);
        if (overlay) overlay.addEventListener('click', fecharSidebar);

        // =========================================
// INTERAÇÃO DO MODO ESCURO
// =========================================
const darkSwitch = document.getElementById('dark-mode-switch');
const themeText = document.getElementById('theme-text');
const themeIcon = document.getElementById('theme-icon');    

// Verificar preferência salva ao carregar a página
if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-theme');
    darkSwitch.checked = false; // Move a bolinha para a esquerda
    themeText.textContent = 'Modo escuro';
    themeIcon.className = 'fa-regular fa-moon';
} else {
    document.body.classList.remove('dark-theme');
    darkSwitch.checked = true; // Move a bolinha para a direita
    themeText.textContent = 'Modo claro';
    themeIcon.className = 'fa-regular fa-sun';
}

// Ouvir o clique no botão
darkSwitch.addEventListener('change', function() {
    if (this.checked) {
        document.body.classList.remove('dark-theme');
        themeText.textContent = 'Modo claro';
        themeIcon.className = 'fa-regular fa-sun';
        localStorage.setItem('theme', 'light');
    } else {
        document.body.classList.add('dark-theme');
        themeText.textContent = 'Modo escuro';
        themeIcon.className = 'fa-regular fa-moon';
        localStorage.setItem('theme', 'dark');
    }
});
    });


    </script>

