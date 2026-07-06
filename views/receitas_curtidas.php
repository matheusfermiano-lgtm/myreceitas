<?php
// receitas_curtidas.php
// Desenvolvido para integrar dinamicamente com o seu sistema de receitas e o estilo da sua sidebar

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definição do caminho base para os includes e links
// Como este arquivo costuma ficar na pasta 'views/', o caminho relativo padrão para a raiz é '../'
$base_path = "../"; 

// ==========================================
// IMPORTANTE CONFIGURAÇÃO DE CONEXÃO:
// Certifique-se de descomentar ou incluir o seu arquivo de banco de dados real aqui!
// exemplo: require_once $base_path . "config/conexao.php";
// ==========================================

// Redireciona de forma segura se não estiver logado ou se o tipo de conta for restaurante
$isLoggedIn = isset($_SESSION['user_id']);
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

if (!$isLoggedIn || $user_type === 'restaurant' || $user_type === 'restaurante') {
    header("Location: " . $base_path . "index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$recipes = [];

// Consulta SQL usando a tabela 'recipe_likes' que você nos enviou
if (isset($conn)) {
    try {
        $query = "SELECT r.* FROM recipes r
                  JOIN recipe_likes rl ON r.id = rl.recipe_id
                  WHERE rl.user_id = ?
                  ORDER BY rl.id DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute([$user_id]);
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Erro ao buscar receitas favoritadas: " . $e->getMessage();
    }
} else {
    // Alerta caso a variável $conn ainda não tenha sido acoplada por arquivo de config
    $connection_warning = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Receitas Curtidas</title>
    <!-- FontAwesome para manter a consistência dos ícones da sua Sidebar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Padronização de Cores Baseada na Identidade do seu Projeto */
        :root {
            --cor-primaria: #8b2538;       /* Bordô/Vermelho Escuro da sua Sidebar */
            --cor-borda: #eee2cc;          /* Bege claro do seu divisor <hr> */
            --cor-texto-principal: #2d2d2d;
            --cor-texto-secundario: #666666;
            --bg-card: #ffffff;
            --bg-pagina: #faf7f2;          /* Off-white suave harmonizado com o bege */
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-pagina);
            color: var(--cor-texto-principal);
            margin: 0;
            padding: 0;
        }

        .main-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
        }

        /* Cabeçalho da Página */
        .page-header {
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 2px solid var(--cor-borda);
            padding-bottom: 18px;
            margin-bottom: 35px;
        }

        .page-header h1 {
            color: var(--cor-primaria);
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
        }

        .page-header i {
            color: var(--cor-primaria);
            font-size: 2.2rem;
        }

        /* Grid Dinâmico de Cards de Receita */
        .recipes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .recipe-card {
            background-color: var(--bg-card);
            border: 1px solid var(--cor-borda);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .recipe-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 20px rgba(139, 37, 56, 0.08);
        }

        .recipe-image {
            width: 100%;
            height: 210px;
            object-fit: cover;
            background-color: #f3ebd9;
            display: block;
        }

        .recipe-info {
            padding: 22px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .recipe-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 12px 0;
            line-height: 1.4;
        }

        .recipe-description {
            font-size: 0.95rem;
            color: var(--cor-texto-secundario);
            margin-bottom: 22px;
            line-height: 1.6;
            /* Limita o texto em 3 linhas para manter o design simétrico */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Rodapé interno do card */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f7f1e5;
            padding-top: 18px;
            margin-top: auto;
        }

        .btn-view {
            background-color: var(--cor-primaria);
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }

        .btn-view:hover {
            background-color: #6b1c2b;
        }

        /* Ícone de curtida interno do Card */
        .btn-unlike {
            background: none;
            border: none;
            color: var(--cor-primaria);
            cursor: pointer;
            font-size: 1.3rem;
            transition: transform 0.2s ease;
            padding: 6px;
        }

        .btn-unlike:hover {
            transform: scale(1.25);
        }

        /* Layout para Estado Vazio (Nenhuma curtida encontrada) */
        .empty-state {
            text-align: center;
            padding: 70px 24px;
            background-color: var(--bg-card);
            border: 2px dashed var(--cor-borda);
            border-radius: 16px;
            max-width: 600px;
            margin: 50px auto;
        }

        .empty-state i {
            font-size: 4rem;
            color: #d8cdb4;
            margin-bottom: 24px;
        }

        .empty-state h2 {
            margin: 0 0 12px 0;
            color: #333333;
            font-size: 1.6rem;
        }

        .empty-state p {
            color: var(--cor-texto-secundario);
            font-size: 1.05rem;
            margin: 0 0 30px 0;
            line-height: 1.5;
        }

        .btn-explore {
            display: inline-block;
            background-color: var(--cor-primaria);
            color: white;
            text-decoration: none;
            padding: 12px 26px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .btn-explore:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        
        /* Banner Informativo Técnico */
        .warning-banner {
            background-color: #fff3cd;
            color: #856404;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #ffeeba;
            font-size: 0.92rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <!-- 
        SUGESTÃO DE IMPLEMENTAÇÃO:
        Para manter sua barra de navegação/header padrão ativa aqui, 
        basta fazer o include do seu arquivo de topo logo abaixo:
        <?php // include $base_path . 'includes/header.php'; ?>
    -->

    <div class="main-container">
        


        <!-- Cabeçalho da Página -->
        <div class="page-header">
            <i class="fa-solid fa-heart"></i>
            <h1>Minhas Receitas Curtidas</h1>
        </div>

        <?php if (!empty($recipes)): ?>
            <!-- Grid Ativo de Receitas Favoritadas -->
            <div class="recipes-grid">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card" id="recipe-card-<?php echo $recipe['id']; ?>">
                        
                        <!-- Caminho adaptável para imagens cadastradas ou fallback visual elegante -->
                        <img src="<?php echo !empty($recipe['imagem']) ? $base_path . htmlspecialchars($recipe['imagem']) : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?q=80&w=600&auto=format&fit=cover'; ?>" 
                             alt="<?php echo htmlspecialchars($recipe['titulo']); ?>" 
                             class="recipe-image">
                        
                        <div class="recipe-info">
                            <div>
                                <h2 class="recipe-title"><?php echo htmlspecialchars($recipe['titulo']); ?></h2>
                                <p class="recipe-description">
                                    <?php echo htmlspecialchars($recipe['descricao'] ?? 'Explore os detalhes para conferir os ingredientes e o modo de preparo completo deste prato.'); ?>
                                </p>
                            </div>
                            
                            <div class="card-footer">
                                <a href="<?php echo $base_path; ?>views/recipes/recipe_details.php?id=<?php echo $recipe['id']; ?>" class="btn-view">Ver Receita</a>
                                
                                <!-- Botão de Interação com Efeito Visual -->
                                <button class="btn-unlike" title="Remover dos favoritos" onclick="removerCurtida(<?php echo $recipe['id']; ?>)">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Estado Vazio (Tratamento se o usuário não tiver curtido nada) -->
            <div class="empty-state">
                <i class="fa-regular fa-heart"></i>
                <h2>Seu caderno de receitas está vazio</h2>
                <p>Você ainda não favoritou nenhuma receita. Navegue pelo site, encontre seus pratos prediletos e clique no ícone de coração para salvá-los aqui!</p>
                <a href="<?php echo $base_path; ?>views/recipes/recipe_list.php" class="btn-explore">Explorar Receitas</a>
            </div>
        <?php endif; ?>

    </div>

    <script>
    // Função em JavaScript para dar feedback visual imediato ao descurtir
    function removerCurtida(recipeId) {
        if (confirm("Deseja mesmo remover esta receita do seu caderno de curtidas?")) {
            // Dica: Aqui você pode disparar uma requisição assíncrona (Fetch/AJAX) para um script do tipo 'toggle_like.php'
            
            // Exemplo de comportamento imediato no Front-End para polimento de UX:
            const cardElement = document.getElementById('recipe-card-' + recipeId);
            if (cardElement) {
                cardElement.style.transition = 'all 0.3s ease';
                cardElement.style.opacity = '0';
                cardElement.style.transform = 'scale(0.9)';
                
                setTimeout(() => {
                    cardElement.remove();
                    // Se não sobrarem mais itens na listagem, recarrega a página para atualizar o layout para o estado vazio
                    if (document.querySelectorAll('.recipe-card').length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        }
    }
    </script>
</body>
</html>