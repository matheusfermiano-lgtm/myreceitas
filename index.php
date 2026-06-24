<?php 
require_once 'base.php'; 
?>
<style>
    .hero {
        /* ATENÇÃO: Substitua 'caminho/para/sua/imagem.jpg' pela sua imagem da colher de pau e caderno */
        background-image: url('https://placehold.co/1920x600/e3cfbc/333?text=Coloque+sua+imagem+de+fundo+aqui'); 
        background-size: cover;
        background-position: center;
        height: 500px;
        display: flex;
        align-items: center;
        padding: 0 10%;
        color: white;
    }
    .hero-content {
        max-width: 500px;
    }
    .hero h1 { 
        font-size: 56px; 
        margin: 0 0 10px 0; 
        font-weight: 600;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.4); 
    }
    .hero p { 
        font-size: 26px; 
        margin: 0; 
        font-weight: 500;
        line-height: 1.3;
        text-shadow: 1px 1px 6px rgba(0,0,0,0.4); 
    }
    .section-title { 
        display: flex; 
        justify-content: space-between; 
        align-items: baseline; 
        margin-top: 50px; 
        border-bottom: 2px solid #eee; 
        padding-bottom: 10px;
    }
    .section-title h2 { margin: 0; color: #333; font-size: 24px;}
    .section-title a { color: #8b2538; text-decoration: none; font-weight: 600; font-size: 14px;}
    .section-title a:hover { text-decoration: underline; }
</style>

<div class="hero">
    <div class="hero-content">
        <h1>MyReceitas</h1>
        <p>se a fome bateu,<br>podemos te ajudar!</p>
    </div>
</div>

<div class="container">
    <div class="section-title">
        <h2>Receitas em destaque</h2>
        <a href="recipes/recipes_list.php">Ver todas</a>
    </div>
    
    <p style="margin-top: 20px; color: #666;">Nenhuma receita em destaque no momento.</p>
</div>

</body>
</html>