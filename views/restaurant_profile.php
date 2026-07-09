<?php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/models/dao/restaurantDAO.php';
require_once dirname(__DIR__) . '/models/dao/recipeDAO.php';
$recipeDAO = new recipeDAO();
$menu = $recipeDAO->getRecipesByRestaurant($id_perfil);

if(!isset($_SESSION)) session_start();

$id_perfil = $_GET['id'] ?? $_SESSION['user_id'];
$id_logado = $_SESSION['user_id'] ?? null;

// Ajuste para aceitar tanto 'restaurant' quanto 'restaurante' vindo da sessão
$user_type = $_SESSION['user_type'] ?? '';
$e_o_dono = ($id_perfil == $id_logado && ($user_type === 'restaurant' || $user_type === 'restaurante'));

$restDAO = new RestaurantDAO();
$profile = $restDAO->getById($id_perfil);

// Extração segura de dados
$isObj = is_object($profile);
$nome = $isObj ? $profile->getName() : ($profile['name'] ?? 'Restaurante');
$foto = $isObj ? ($profile->getPhoto() ?: 'default.png') : ($profile['photo'] ?? 'default.png');
$email = $isObj ? $profile->getEmail() : ($profile['email'] ?? '');
$telefone = $isObj && method_exists($profile, 'getPhone') ? $profile->getPhone() : ($profile['phone'] ?? '');
$endereco = $isObj && method_exists($profile, 'getAddress') ? $profile->getAddress() : ($profile['address'] ?? '');
$horario = $isObj && method_exists($profile, 'getOpeningHours') ? $profile->getOpeningHours() : ($profile['opening_hours'] ?? '');
$mapa = $isObj && method_exists($profile, 'getLocationMapLink') ? $profile->getLocationMapLink() : ($profile['location_map_link'] ?? '');
$servicos = $isObj && method_exists($profile, 'getServicesOffered') ? $profile->getServicesOffered() : ($profile['services_offered'] ?? '');
$descricao = $isObj && method_exists($profile, 'getDescription') ? $profile->getDescription() : ($profile['description'] ?? '');

// Busca Galeria e Chefs vinculados usando PDO direto para evitar erros de DAO
$conn = database::getConexao();

$stmtG = $conn->prepare("SELECT image_path FROM restaurant_gallery WHERE restaurant_id = ?");
$stmtG->execute([$id_perfil]);
$galeria = $stmtG->fetchAll(PDO::FETCH_COLUMN);

$stmtC = $conn->prepare("
    SELECT c.id, c.name, c.photo 
    FROM chef c
    JOIN restaurant_chefs rc ON c.id = rc.chef_id
    WHERE rc.restaurant_id = ?
");
$stmtC->execute([$id_perfil]);
$chefsVinculados = $stmtC->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="profile-container">
    <div class="profile-banner"></div>
    <div class="profile-header-card">
        <img src="<?php echo $base_path; ?>static/assets/uploads/<?php echo htmlspecialchars($foto); ?>" alt="Logo do Restaurante" class="profile-avatar">
        
        <div class="profile-titles">
            <h1 style="margin: 0; font-size: 28px; font-weight: bold; color: var(--primary);">
                <?php echo htmlspecialchars($nome); ?>
            </h1>

            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                <span style="background: #fce8e6; color: #a83244; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                    <i class="fa-solid fa-store"></i> Perfil Oficial de Restaurante
                </span>

                <?php if ($e_o_dono): ?>
                    <a href="edit_profile.php" style="background: #8b2538; color: #fff; padding: 5px 15px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <i class="fa-solid fa-pen-to-square"></i> Editar Perfil
                    </a>
                <?php endif; ?>
            </div>
        </div> 
    </div> 

    <div class="profile-body">
        <div class="sidebar">
            <div class="info-card">
                <h3><i class="fa-solid fa-circle-info"></i> Informações</h3>
                <ul class="contact-list">
                    <?php if(!empty($telefone)): ?>
                        <li><i class="fa-solid fa-phone"></i> <span><?php echo htmlspecialchars($telefone); ?></span></li>
                    <?php endif; ?>
                    <?php if(!empty($horario)): ?>
                        <li><i class="fa-solid fa-clock"></i> <span><strong>Horário:</strong><br><?php echo htmlspecialchars($horario); ?></span></li>
                    <?php endif; ?>
                    <?php if(!empty($endereco)): ?>
                        <li><i class="fa-solid fa-location-dot"></i> <span><strong>Endereço:</strong><br><?php echo htmlspecialchars($endereco); ?></span></li>
                    <?php endif; ?>
                    <?php if(!empty($mapa)): ?>
                        <li><i class="fa-solid fa-map"></i> <a href="<?php echo htmlspecialchars($mapa); ?>" target="_blank" style="color: #8b2538; font-weight: bold;">Ver no Google Maps</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if(!empty($servicos)): ?>
            <div class="info-card">
                <h3><i class="fa-solid fa-bell-concierge"></i> Oferecemos</h3>
                <p style="line-height: 1.6; color: var(--text);"><?php echo nl2br(htmlspecialchars($servicos)); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="main-content">
            <?php if(!empty($descricao)): ?>
            <div class="info-card">
                <h3><i class="fa-solid fa-quote-left"></i> A Casa</h3>
                <p style="line-height: 1.6; color: var(--text);"><?php echo nl2br(htmlspecialchars($descricao)); ?></p>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <h3><i class="fa-solid fa-images"></i> Galeria de Fotos</h3>
                <?php if(empty($galeria)): ?>
                    <p style="color: var(--text);">Nenhuma foto cadastrada.</p>
                <?php else: ?>
                    <div class="gallery-grid">
                        <?php foreach($galeria as $img): ?>
                            <img src="<?php echo $base_path; ?>static/assets/uploads/<?php echo htmlspecialchars($img); ?>" class="gallery-img" alt="Foto do Restaurante">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <h3><i class="fa-solid fa-users"></i> Nossa Equipe (Chefs)</h3>
                <?php if(empty($chefsVinculados)): ?>
                    <p style="color: var(--text);">Nenhum chef vinculado ao restaurante no momento.</p>
                <?php else: ?>
                    <div class="chef-team-grid">
                        <?php foreach($chefsVinculados as $c): 
                            $fotoChef = empty($c['photo']) ? 'default.png' : $c['photo'];
                        ?>
                            <a href="user_profile.php?id=<?php echo $c['id']; ?>&type=chef" class="chef-card">
                                <img src="<?php echo $base_path; ?>static/assets/uploads/<?php echo htmlspecialchars($fotoChef); ?>" alt="Foto do Chef">
                                <div><strong><?php echo htmlspecialchars($c['name']); ?></strong></div>
                                <div style="font-size: 0.8rem; color: #888;">Ver perfil</div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="info-card">
                <h3><i class="fa-solid fa-utensils"></i> Cardápio</h3>
                
                <?php if (empty($menu)): ?>
                    <p style="color: var(--text);">Nenhuma receita cadastrada ainda.</p>
                <?php else: ?>
                    <div class="recipes-grid">
                        <?php foreach ($menu as $item): ?>
                            <div class="recipe-item">
                                <h4><?= htmlspecialchars($item->getName()) ?></h4>
                                <p style="font-weight: bold; color: var(--primary);">
                                    R$ <?= number_format($item->getPrice(), 2, ',', '.') ?>
                                </p>
                                <p style="font-size: 0.9rem; color: var(--text-secondary);">
                                    <?= htmlspecialchars($item->getDescription() ?: 'Sem descrição') ?>
                                </p>
                                <a href="<?= $base_path ?>views/recipes/recipe_view.php?id=<?= $item->getId() ?>" class="btn-view" style="margin-top: 8px;">
                                    <i class="fa-solid fa-eye"></i> Ver receita
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>