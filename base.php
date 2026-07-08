<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);

// Detecta o caminho base para não quebrar links em subpastas
$base_path = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', str_replace('\\', '/', __DIR__));
$base_path = '/' . trim($base_path, '/') . '/';
if ($base_path === '//') { $base_path = '/'; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyReceitas</title>
    
    <!-- FONTE AWESOME (ÍCONES) - ESSENCIAL -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- CSS CORRIGIDO COM BASE_PATH -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>static/style.css">
</head>
<body class="light-mode">
    <header class="navbar">
        <a href="<?php echo $base_path; ?>index.php" class="logo-container">
            <i class="fa-solid fa-utensils"></i>
        </a>
        
        <?php 
        $pagina_atual = basename($_SERVER['PHP_SELF']); 
        if ($pagina_atual !== 'login.php' && $pagina_atual !== 'cadastro.php' && $pagina_atual !== 'register.php'): 
        ?>
        <!-- Depois: transformado em formulário -->
        <form class="search-bar" action="<?= $base_path ?>views/recipes/recipe_list.php" method="GET">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="q" placeholder="Buscar receitas..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <!-- botão invisível para permitir submit com Enter -->
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <?php endif; ?>
        
        <div class="nav-icons">
            <!-- Botão de Tema (Sol/Lua) -->
            <button class="theme-toggle" id="theme-switcher" title="Alternar Tema">
                <i class="fa-solid fa-sun"></i>
            </button>
            <!--
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
            -->

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
                <a href="<?php echo $base_path; ?>views/receitas_curtidas.php" class="sidebar-item">
                    <i class="fa-solid fa-heart"></i>
                    <span>Minhas receitas Favoritas</span>
                </a>
                <a href="<?php echo $base_path; ?>views/logout.php" class="sidebar-item" style="color: var(--danger);">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Sair da Conta</span>
                </a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>views/login.php" class="sidebar-item highlight">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Entrar / Cadastrar</span>
                </a>
            <?php endif; ?>

            <hr style="border: 0; border-top: 1px solid var(--border); margin: 10px 20px;">

            <a href="<?php echo $base_path; ?>views/restaurant/restaurant_list.php" class="sidebar-item">
                <i class="fa-solid fa-store"></i>
                <span>Restaurantes</span>
            </a>
            <a href="<?php echo $base_path; ?>views/chefs/chef_list.php" class="sidebar-item">
                <i class="fa-solid fa-people-group"></i>
                <span>Chefes</span>
            </a>
            <a href="<?php echo $base_path; ?>views/recipes/recipe_list.php" class="sidebar-item">
                <i class="fa-solid fa-book-open"></i>
                <span>Todas as Receitas</span>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPerfil = document.getElementById('btnPerfil');
        const sidebar = document.getElementById('userSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const fecharSidebarBtn = document.getElementById('fecharSidebar');
        const themeSwitcher = document.getElementById('theme-switcher');
        const body = document.body;

        // Sidebar
        btnPerfil?.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.add('ativa');
            overlay.classList.add('ativa');
        });

        const fechar = () => {
            sidebar.classList.remove('ativa');
            overlay.classList.remove('ativa');
        };

        fecharSidebarBtn?.addEventListener('click', fechar);
        overlay?.addEventListener('click', fechar);

        // Dark Mode Corrigido (Usando a classe 'dark-mode' do seu CSS)
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            themeSwitcher.innerHTML = '<i class="fa-solid fa-moon"></i>';
        }

        themeSwitcher?.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeSwitcher.innerHTML = isDark ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
        });
    });
    </script>