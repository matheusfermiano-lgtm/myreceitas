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