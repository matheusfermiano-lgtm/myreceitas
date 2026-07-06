<?php
if(!isset($_SESSION)) session_start();

require_once dirname(__DIR__) . '/models/model/user.php';
require_once dirname(__DIR__) . '/models/model/chef.php';
require_once dirname(__DIR__) . '/models/model/recipe.php';
require_once dirname(__DIR__) . '/models/dao/userDAO.php';
require_once dirname(__DIR__) . '/models/dao/recipeDAO.php';
require_once dirname(__DIR__) . '/models/dao/chefDAO.php';

$id_perfil = $_GET['id'] ?? $_SESSION['user_id'];
$tipo_perfil = $_GET['type'] ?? $_SESSION['user_type'];
$id_logado = $_SESSION['user_id'] ?? null;

if ($tipo_perfil === 'restaurant') {
    header("Location:restaurant_profile.php?id=" . $id_perfil);
    exit;
}

$uDAO = new userDAO();
$rDAO = new recipeDAO();
$chefDAO = new chefDAO();

if($tipo_perfil == 'chef') {
    $profile = $chefDAO->read($id_perfil);
} else {
    $profile = $uDAO->read($id_perfil);
}

if (!$profile) {
    die("Perfil não encontrado.");
}

// LOGICA SIMPLIFICADA (Tudo como Objeto)
$nome = $profile->getName();
$email = $profile->getEmail();
$dataCriacao = $profile->getCreatedAt();

// Correção da Foto: Fallback inteligente para Chef ou Usuário Comum
$foto_banco = (method_exists($profile, 'getPhoto')) ? $profile->getPhoto() : '';
if ($tipo_perfil === 'chef') {
    $foto = (!empty($foto_banco) && $foto_banco !== 'default.png') ? $foto_banco : 'default_chef.png';
} else {
    $foto = (!empty($foto_banco)) ? $foto_banco : 'default.png';
}

$telefone = (method_exists($profile, 'getPhone')) ? $profile->getPhone() : '';
$endereco = (method_exists($profile, 'getAddress')) ? $profile->getAddress() : '';

// Campos exclusivos de Chef
$regiao = ($tipo_perfil == 'chef') ? $profile->getRegionOperation() : '';
$servicos = ($tipo_perfil == 'chef') ? $profile->getServicesOffered() : '';
$descricao = ($tipo_perfil == 'chef') ? $profile->getDescription() : '';
$experiencia = ($tipo_perfil == 'chef') ? $profile->getProfessionalExperience() : '';

$e_o_dono = ($id_perfil == $id_logado && $tipo_perfil == $_SESSION['user_type']);

// Correção das Receitas: Chama o método certo do DAO dependendo do tipo de perfil
if ($tipo_perfil === 'chef') {
    $minhasReceitas = $e_o_dono ? $rDAO->getRecipesByChef($id_perfil) : $rDAO->getPublicRecipesByChef($id_perfil);
} else {
    $minhasReceitas = $e_o_dono ? $rDAO->getRecipesByUser($id_perfil) : $rDAO->getPublicRecipesByUser($id_perfil);
}

// --- LÓGICA DE REVIEWS DO CHEF ---
$reviews = [];
$mediaAvaliacoes = 0;
$totalReviews = 0;

if ($tipo_perfil === 'chef') {
    try {
        $db = database::getConexao();
        // Busca as avaliações trazendo o nome correspondente (seja da tabela de clientes ou de restaurantes)
        $stmtRev = $db->prepare("
            SELECT cr.*, 
                   COALESCE(u.name, r.name, 'Usuário Anônimo') as user_name 
            FROM chef_reviews cr 
            LEFT JOIN users u ON cr.user_id = u.id AND NOT EXISTS (SELECT 1 FROM restaurants WHERE id = cr.user_id)
            LEFT JOIN restaurants r ON cr.user_id = r.id
            WHERE cr.chef_id = ? 
            ORDER BY cr.created_at DESC
        ");
        $stmtRev->execute([$id_perfil]);
        $reviews = $stmtRev->fetchAll(PDO::FETCH_ASSOC);
        $totalReviews = count($reviews);
        
        if ($totalReviews > 0) {
            $soma = array_sum(array_column($reviews, 'rating'));
            $mediaAvaliacoes = $soma / $totalReviews;
        }
    } catch (Exception $e) {
        // Silencia erro caso as tabelas ainda estejam a ser criadas
    }
}

require_once dirname(__DIR__) . '/base.php';
?>

<style>
    .profile-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* Banner e Cabeçalho */
    .profile-banner {
        background: #8b2538;
        height: 200px;
        border-radius: 16px 16px 0 0;
        position: relative;
    }

    .profile-header-card {
        background: #fff;
        border-radius: 0 0 16px 16px;
        padding: 20px 40px 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        display: flex;
        align-items: flex-end;
        margin-top: -80px;
        position: relative;
        z-index: 2;
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid #fff;
        background: #eee;
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .profile-titles {
        margin-left: 30px;
        flex-grow: 1;
    }

    .profile-titles h1 {
        margin: 0;
        color: #333;
        font-size: 2.2rem;
    }

    .profile-badges {
        margin-top: 10px;
        display: flex;
        gap: 10px;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-role { background: #fdf5f6; color: #8b2538; border: 1px solid #f8e1e4; }
    .badge-date { background: #f0f4f8; color: #4a5568; }
    .badge-private { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

    /* Layout do Corpo (Grid) */
    .profile-body {
        display: grid;
        grid-template-columns: 1fr 2.5fr;
        gap: 30px;
        margin-top: 30px;
    }

    /* Cards Genéricos */
    .info-card {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        margin-bottom: 25px;
    }

    .info-card h3 {
        color: #8b2538;
        margin-top: 0;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .contact-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .contact-list li {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        color: #555;
        font-size: 0.95rem;
    }

    .contact-list i { color: #8b2538; margin-top: 4px; width: 20px; text-align: center; }

    .text-content { line-height: 1.7; color: #444; }

    /* Grid de Receitas */
    .recipes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .recipe-item {
        background: #fff;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .recipe-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .recipe-item h4 { margin: 0 0 10px 0; color: #333; }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
    }
    .status-public { background: #def7ec; color: #03543f; }
    .status-private { background: #fde8e8; color: #9b1c1c; }

    @media (max-width: 768px) {
        .profile-body { grid-template-columns: 1fr; }
        .profile-header-card { flex-direction: column; text-align: center; align-items: center; }
        .profile-titles { margin-left: 0; margin-top: 20px; }
        .profile-badges { justify-content: center; flex-wrap: wrap; }
    }
</style>

<div class="profile-container">
    <div class="profile-banner"></div>
    <div class="profile-header-card">
    <img src="../assets/uploads/<?php echo htmlspecialchars($foto); ?>" alt="Foto de Perfil" class="profile-avatar">
        
        <div class="profile-titles">
            <h1><?php echo htmlspecialchars($nome); ?></h1>
            <div class="profile-badges">
                <span class="badge badge-role">
                    <i class="fa-solid <?php echo $tipo_perfil == 'chef' ? 'fa-utensils' : 'fa-user'; ?>"></i>
                    <?php echo ucfirst($tipo_perfil); ?>
                </span>
                <span class="badge badge-date"><i class="fa-solid fa-calendar-alt"></i> Membro desde <?php echo date('d/m/Y', strtotime($dataCriacao)); ?></span>
                
                <?php if($e_o_dono): ?>
                <span class="badge badge-private"><i class="fa-solid fa-lock"></i> Visualização Privada (Dono)</span>
                <a href="edit_profile.php" style="background: #fff; color: #8b2538; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; text-decoration: none; margin-left: 10px; border: 1px solid #8b2538; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-pen"></i> Editar Perfil
                </a>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="profile-body">
        <div class="sidebar">
            <div class="info-card">
                <h3><i class="fa-solid fa-address-card"></i> Contato</h3>
                <ul class="contact-list">
                    <?php if(!empty($email)): ?>
                        <li><i class="fa-solid fa-envelope"></i> <span><?php echo htmlspecialchars($email); ?></span></li>
                    <?php endif; ?>
                    
                    <?php if(!empty($telefone)): ?>
                        <li><i class="fa-solid fa-phone"></i> <span><?php echo htmlspecialchars($telefone); ?></span></li>
                    <?php endif; ?>

                    <?php if(!empty($regiao)): ?>
                        <li><i class="fa-solid fa-map-location-dot"></i> <span><strong>Atende em:</strong><br><?php echo htmlspecialchars($regiao); ?></span></li>
                    <?php endif; ?>

                    <?php if($e_o_dono && !empty($endereco)): ?>
                        <li><i class="fa-solid fa-house-lock" style="color:#856404;"></i> <span><strong>Seu Endereço:</strong><br><?php echo htmlspecialchars($endereco); ?></span></li>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if(!empty($servicos)): ?>
            <div class="info-card">
                <h3><i class="fa-solid fa-bell-concierge"></i> Serviços Oferecidos</h3>
                <div class="text-content">
                    <?php echo nl2br(htmlspecialchars($servicos)); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="main-content">
            <?php if(!empty($descricao)): ?>
            <div class="info-card">
                <h3><i class="fa-solid fa-quote-left"></i> Sobre Mim</h3>
                <div class="text-content">
                    <?php echo nl2br(htmlspecialchars($descricao)); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($experiencia)): ?>
            <div class="info-card">
                <h3><i class="fa-solid fa-briefcase"></i> Experiência Profissional</h3>
                <div class="text-content">
                    <?php echo nl2br(htmlspecialchars($experiencia)); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <h3>
                    <i class="fa-solid fa-book-open"></i> 
                    <?php echo $e_o_dono ? 'Meu Acervo de Receitas' : 'Receitas Públicas'; ?>
                </h3>
                
                <?php if(empty($minhasReceitas)): ?>
                    <p style="color: #888; text-align: center; padding: 20px;">Nenhuma receita encontrada.</p>
                <?php else: ?>
                    <div class="recipes-grid">
                        <?php foreach($minhasReceitas as $r): ?>
                            <div class="recipe-item">
                                <h4><?php echo htmlspecialchars($r->getName()); ?></h4>
                                
                                <p style="font-size: 0.85rem; color: #666;">
                                    <i class="fa-regular fa-clock"></i> <?php echo $r->getPreparationTime(); ?> min
                                </p>

                                <?php if($e_o_dono): ?>
                                    <span class="status-badge <?php echo $r->getIsPublic() ? 'status-public' : 'status-private'; ?>">
                                        <?php echo $r->getIsPublic() ? '<i class="fa-solid fa-globe"></i> Pública' : '<i class="fa-solid fa-lock"></i> Privada'; ?>
                                    </span>
                                <?php endif; ?>
                                
                                <a href="recipes/recipe_view.php?id=<?php echo $r->getId(); ?>" style="display:block; margin-top:10px; font-size: 0.8rem; color: #8b2538;">Ver detalhes</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($tipo_perfil === 'chef'): ?>
            <div class="info-card" style="margin-top: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px;">
                    <h3 style="border: none; padding: 0; margin: 0;"><i class="fa-solid fa-star" style="color: #f39c12;"></i> Avaliações (<?php echo $totalReviews; ?>)</h3>
                    <?php if ($totalReviews > 0): ?>
                        <div style="font-size: 1.2rem; font-weight: bold; color: #333;">
                            Média: <span style="color: #f39c12;"><?php echo number_format($mediaAvaliacoes, 1, ',', '.'); ?> <i class="fa-solid fa-star"></i></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ((isset($_SESSION['user_id']) || isset($_SESSION['restaurant_id'])) && !$e_o_dono): ?>
                    <div style="background: #f9f9f9; padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #eee;">
                        <h4 style="margin-top: 0; color: #333;">Deixe a sua avaliação profissional</h4>
                        <form action="process_review.php" method="POST">
                            <input type="hidden" name="chef_id" value="<?php echo $id_perfil; ?>">
                            <input type="hidden" name="type" value="chef">
                            
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #555;">Classificação (1 a 5 estrelas):</label>
                                <select name="rating" required style="padding: 10px; border-radius: 6px; border: 1px solid #ccc; width: 100%; max-width: 200px;">
                                    <option value="5">⭐⭐⭐⭐⭐ (5/5) - Excelente</option>
                                    <option value="4">⭐⭐⭐⭐ (4/5) - Muito Bom</option>
                                    <option value="3">⭐⭐⭐ (3/5) - Bom</option>
                                    <option value="2">⭐⭐ (2/5) - Razoável</option>
                                    <option value="1">⭐ (1/5) - Mau</option>
                                </select>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #555;">Comentário:</label>
                                <textarea name="comment" rows="3" required placeholder="Escreva a sua avaliação..." style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; resize: vertical; font-family: inherit;"></textarea>
                            </div>
                            
                            <button type="submit" style="background: #8b2538; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s;">Publicar Avaliação</button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if (empty($reviews)): ?>
                    <p style="color: #888; text-align: center; padding: 20px; background: #fdfdfd; border-radius: 8px; border: 1px dashed #ddd;">Ainda não há avaliações para este chef. Seja o primeiro a avaliar!</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach ($reviews as $rev): ?>
                            <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #eaeaea; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <strong style="color: #333; font-size: 1.05rem;"><i class="fa-solid fa-user-circle" style="color: #8b2538;"></i> <?php echo htmlspecialchars($rev['user_name']); ?></strong>
                                    <span style="color: #999; font-size: 0.85rem;"><?php echo date('d/m/Y', strtotime($rev['created_at'])); ?></span>
                                </div>
                                
                                <div style="color: #f39c12; margin-bottom: 12px; font-size: 0.9rem;">
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rev['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                
                                <p style="color: #555; margin: 0; line-height: 1.5; font-size: 0.95rem;">
                                    "<?php echo nl2br(htmlspecialchars($rev['comment'])); ?>"
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>