<?php
require_once __DIR__ . '/base.php';
?>
<style>
    /* ATUALIZADO: Importamos as fontes necessárias */
    @import url('https://fonts.googleapis.com/css2?family=Birthstone&family=Montserrat:wght@400;600;700&display=swap');

    .hero {
        /* Adicionada a película escura por cima do seu placeholder para destacar o texto off-white */
        background-image: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)), url('https://placehold.co/1920x600/e3cfbc/333?text=Coloque+sua+imagem+de+fundo+aqui'); 
        background-size: cover;
        background-position: center;
        height: 500px;
        display: flex;
        align-items: center;
        padding: 0 10%;
        color: #f4ebd0; /* Tom creme pro texto */
    }
    .hero-content {
        max-width: 500px;
    }
    
    .hero h1 { 
        font-family: 'Birthstone', cursive; 
        font-size: 85px; 
        margin: 0 0 -5px 0; 
        font-weight: normal; 
        color: #f4ebd0; /* Bege creme */
        text-shadow: 2px 2px 10px rgba(0,0,0,0.7); 
    }

    .hero p { 
        font-family: 'Montserrat', sans-serif; 
        font-size: 24px; 
        margin: 0; 
        font-weight: 700; 
        line-height: 1.2;
        color: #e6d8b8; /* Bege areia */
        text-shadow: 1px 1px 8px rgba(0,0,0,0.7); 
    }

    .section-title { 
        display: flex; 
        justify-content: space-between; 
        align-items: baseline; 
        margin-top: 50px; 
        border-bottom: 2px solid #e1dacb; /* Divisória combinando com o tema off-white */
        padding-bottom: 10px;
    }
    .section-title h2 { margin: 0; color: #333; font-size: 24px;}
    .section-title a { color: #8b2538; text-decoration: none; font-weight: 600; font-size: 14px;}
    .section-title a:hover { text-decoration: underline; }
</style>

<div class="hero">
    <div class="hero-content">
        <h1 class="titulo-logo">MyReceitas</h1>
        <p>Seu site de receitas<br>para todos os momentos!</p>
    </div>
</div>

<div class="container">
    <div class="section-title">
        <h2>Receitas em destaque</h2>
        <a href="views/recipes/recipes_list.php">Ver todas</a>
    </div>
    
    <p style="margin-top: 20px; color: #667;">Nenhuma receita em destaque no momento.</p>
</div>

</body>
</html>