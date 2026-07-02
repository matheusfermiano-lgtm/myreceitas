<?php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/models/dao/restaurantDAO.php';

if(!isset($_SESSION)) session_start();

$id_perfil = $_GET['id'] ?? $_SESSION['user_id'];
$id_logado = $_SESSION['user_id'] ?? null;
$e_o_dono = ($id_perfil == $id_logado && $_SESSION['user_type'] == 'restaurant');

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

<style>
    .profile-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .profile-banner {
        background: #8b2538;
        height: 200px;
        border-radius: 16px 16px 0 0;
        position: relative;
    }
    .profile-header-card { background: #fff; border-radius: 0 0 16px 16px; padding: 20px 40px 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; align-items: flex-end; margin-top: -80px; position: relative; z-index: 2; }
    .profile-avatar { width: 150px; height: 150px; border-radius: 12px; border: 5px solid #fff; background: #eee; object-fit: cover; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .profile-titles { margin-left: 30px; flex-grow: 1; }
    .profile-titles h1 { margin: 0; color: #333; font-size: 2.2rem; }
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; }
    .badge-role { background: #fdf5f6; color: #8b2538; border: 1px solid #f8e1e4; }
    
    .profile-body { display: grid; grid-template-columns: 1fr 2.5fr; gap: 30px; margin-top: 30px; }
    .info-card { background: #fff; border-radius: 16px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); margin-bottom: 25px; }
    .info-card h3 { color: #8b2538; margin-top: 0; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
    .contact-list { list-style: none; padding: 0; margin: 0; }
    .contact-list li { margin-bottom: 15px; display: flex; align-items: flex-start; gap: 12px; color: #555; font-size: 0.95rem; }
    .contact-list i { color: #8b2538; margin-top: 4px; width: 20px; text-align: center; }
    
    /* Galeria */
    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
    .gallery-img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px; transition: transform 0.3s; }
    .gallery-img:hover { transform: scale(1.05); }

    /* Equipe */
    .chef-team-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
    .chef-card { text-align: center; padding: 15px; border: 1px solid #eee; border-radius: 12px; text-decoration: none; color: inherit; transition: box-shadow 0.2s; }
    .chef-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
    .chef-card img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }

    @media (max-width: 768px) { .profile-body { grid-template-columns: 1fr; } }
</style>

<div class="profile-container">
    <div class="profile-banner"></div>
    <div class="profile-header-card">
        <img src="../assets/uploads/<?php echo htmlspecialchars($foto); ?>" alt="Logo do Restaurante" class="profile-avatar">
        <div class="profile-titles">
            <h1><?php echo htmlspecialchars($nome); ?></h1>
            <span class="badge badge-role"><i class="fa-solid fa-shop"></i> Perfil Oficial de Restaurante</span>
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
                <p style="line-height: 1.6; color: #555;"><?php echo nl2br(htmlspecialchars($servicos)); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="main-content">
            <?php if(!empty($descricao)): ?>
            <div class="info-card">
                <h3><i class="fa-solid fa-quote-left"></i> A Casa</h3>
                <p style="line-height: 1.6; color: #555;"><?php echo nl2br(htmlspecialchars($descricao)); ?></p>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <h3><i class="fa-solid fa-images"></i> Galeria de Fotos</h3>
                <?php if(empty($galeria)): ?>
                    <p style="color: #888;">Nenhuma foto cadastrada.</p>
                <?php else: ?>
                    <div class="gallery-grid">
                        <?php foreach($galeria as $img): ?>
                            <img src="../assets/uploads/<?php echo htmlspecialchars($img); ?>" class="gallery-img" alt="Foto do Restaurante">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <h3><i class="fa-solid fa-users"></i> Nossa Equipe (Chefs)</h3>
                <?php if(empty($chefsVinculados)): ?>
                    <p style="color: #888;">Nenhum chef vinculado ao restaurante no momento.</p>
                <?php else: ?>
                    <div class="chef-team-grid">
                        <?php foreach($chefsVinculados as $c): 
                            $fotoChef = empty($c['photo']) ? 'default.png' : $c['photo'];
                        ?>
                            <a href="user_profile.php?id=<?php echo $c['id']; ?>&type=chef" class="chef-card">
                                <img src="../assets/uploads/<?php echo htmlspecialchars($fotoChef); ?>" alt="Foto do Chef">
                                <div><strong><?php echo htmlspecialchars($c['name']); ?></strong></div>
                                <div style="font-size: 0.8rem; color: #888;">Ver perfil</div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>