<?php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/models/dao/userDAO.php';
require_once dirname(__DIR__) . '/models/dao/recipeDAO.php';
require_once dirname(__DIR__) . '/models/dao/restaurantDAO.php'; // Adicione este

if(!isset($_SESSION)) session_start();

$id_perfil = $_GET['id'] ?? $_SESSION['user_id'];
$tipo_perfil = $_GET['type'] ?? $_SESSION['user_type'];
$id_logado = $_SESSION['user_id'] ?? null;

// Verifica se quem está vendo é o dono do perfil
$e_o_dono = ($id_perfil == $id_logado && $tipo_perfil == $_SESSION['user_type']);

$uDAO = new userDAO();
$rDAO = new recipeDAO();

// Busca dados baseado no tipo
if($tipo_perfil == 'chef') {
    // $profile = $chefDAO->read($id_perfil);
} else if ($tipo_perfil == 'restaurant') {
    // $profile = $restaurantDAO->read($id_perfil);
} else {
    $profile = $uDAO->read($id_perfil);
}

// Lógica de Receitas (Dono vê todas, Visitante vê só públicas)
if ($e_o_dono) {
    $minhasReceitas = $rDAO->getRecipesByUser($id_perfil);
} else {
    $minhasReceitas = $rDAO->getPublicRecipesByUser($id_perfil);
}
?>

<div class="container">
    <div class="profile-header">
        <h1>Perfil de <?php echo $profile->getName(); ?></h1>
        <p>Membro desde: <?php echo date('d/m/Y', strtotime($profile->getCreatedAt())); ?></p>
        
        <?php if($e_o_dono): ?>
            <div class="alert info">🔒 Você está vendo seus dados privados (Endereço: <?php echo $profile->getAddress(); ?>)</div>
        <?php endif; ?>

        <!-- Se for Chef, mostra experiência -->
        <?php if($tipo_perfil == 'chef'): ?>
            <p><strong>Experiência:</strong> <?php echo $profile->getProfessionalExperience(); ?></p>
        <?php endif; ?>
    </div>

    <!-- Seção de Receitas (Cardápio ou Receitas) -->
    <h2><?php echo ($tipo_perfil == 'restaurant') ? '🍴 Cardápio' : '📖 Receitas'; ?></h2>
    <div class="recipe-grid">
        <?php foreach($minhasReceitas as $r): ?>
            <!-- Se for restaurante e a listagem for a geral, o SQL do readAll deve filtrar -->
            <div class="recipe-card">
                <h3><?php echo $r['name']; ?></h3>
                <!-- Badges de privacidade só aparecem para o dono -->
                <?php if($e_o_dono): ?>
                    <span><?php echo $r['is_public'] ? '🌍 Pública' : '🔒 Privada'; ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>