<?php
// Ativa o buffer para evitar o erro de "Headers already sent"
ob_start(); 

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/model/user.php';
require_once dirname(__DIR__) . '/models/model/chef.php';
require_once dirname(__DIR__) . '/models/model/restaurant.php';
require_once dirname(__DIR__) . '/models/dao/userDAO.php';
require_once dirname(__DIR__) . '/models/dao/chefDAO.php';
require_once dirname(__DIR__) . '/models/dao/restaurantDAO.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$type = $_GET['type'] ?? 'user';
$step = $_GET['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        $_SESSION['reg_data'] = $_POST;
        
        // --- UPLOAD DA FOTO PRINCIPAL DO RESTAURANTE (Etapa 1) ---
        if ($type === 'restaurant' && isset($_FILES['main_photo']) && $_FILES['main_photo']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['main_photo']['tmp_name'];
            $fileName = basename($_FILES['main_photo']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                $uploadDir = dirname(__DIR__) . '/assets/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $newFileName = 'rest_main_' . uniqid() . '.' . $fileExt;
                if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                    $_SESSION['reg_data']['photo'] = $newFileName; 
                }
            }
        }
        
        header("Location: register_steps.php?type=$type&step=2");
        exit;
    }
    
    if ($step == 2) {
        $_SESSION['reg_data'] = array_merge($_SESSION['reg_data'], $_POST);
        
        // --- UPLOAD DE FOTO DE PERFIL (Para Chef E Usuário Comum) ---
        if (($type === 'chef' || $type === 'user') && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['photo']['tmp_name'];
            $fileName = basename($_FILES['photo']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                $uploadDir = dirname(__DIR__) . '/assets/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $prefix = ($type === 'chef') ? 'chef_' : 'user_';
                $newFileName = $prefix . uniqid() . '.' . $fileExt;
                if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                    $_SESSION['reg_data']['photo'] = $newFileName; 
                }
            }
        }

        // --- UPLOAD DA GALERIA DO RESTAURANTE (Etapa 2) ---
        if ($type === 'restaurant' && isset($_FILES['gallery'])) {
            $galleryImages = [];
            $uploadDir = dirname(__DIR__) . '/assets/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = basename($_FILES['gallery']['name'][$key]);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $newFileName = 'gallery_' . uniqid() . '_' . $key . '.' . $fileExt;
                        if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                            $galleryImages[] = $newFileName;
                        }
                    }
                }
            }
            $_SESSION['reg_data']['gallery'] = $galleryImages;
        }
        
        header("Location: register_steps.php?type=$type&step=3");
        exit;
    }

    if ($step == 3) {
        $data = array_merge($_SESSION['reg_data'], $_POST);
        
        if($data['pass'] !== $data['pass_confirm']) {
             $error = "Senhas não conferem!";
        } else {
            $hashedPassword = password_hash($data['pass'], PASSWORD_DEFAULT);
            $success = false;

            if ($type === 'user') {
                $dao = new userDAO();
                $obj = new User($data['name'], $data['email'], $hashedPassword, $data['phone'] ?? '', $data['address'] ?? '');
                $photoName = $data['photo'] ?? 'default_user.png';
                $success = $dao->create($obj, $photoName);
            } 
            elseif ($type === 'chef') {
                $dao = new chefDAO();
                $obj = new Chef($data['name'], $data['email'], $hashedPassword);
                $obj->setPhone($data['phone'] ?? '');
                $obj->setAddress($data['address'] ?? '');
                $obj->setProfessionalExperience($data['professional_experience'] ?? '');
                $obj->setRegionOperation($data['region_operation'] ?? '');
                $obj->setDescription($data['description'] ?? '');
                $obj->setServicesOffered($data['services_offered'] ?? '');
                if(isset($data['photo'])) $obj->setPhoto($data['photo']);
                
                $success = $dao->create($obj);
            } 
            elseif ($type === 'restaurant') {
                $dao = new RestaurantDAO();
                $obj = new Restaurant($data['name'], $data['email'], $hashedPassword);
                $obj->setPhone($data['phone'] ?? '');
                $obj->setAddress($data['address'] ?? '');
                $obj->setLocationMapLink($data['location_map_link'] ?? '');
                $obj->setDescription($data['description'] ?? '');
                $obj->setOpeningHours($data['opening_hours'] ?? '');
                $obj->setServicesOffered($data['services_offered'] ?? '');
                if(isset($data['photo'])) $obj->setPhoto($data['photo']);
                
                $success = $dao->create($obj);

                if ($success) {
                    $restId = $obj->getId();
                    
                    // Salvar Galeria de Imagens
                    if (!empty($data['gallery'])) {
                        foreach ($data['gallery'] as $imgPath) {
                            $dao->addGalleryImage($restId, $imgPath);
                        }
                    }

                    // Salvar vínculo com Chefs
                    if (!empty($data['linked_chefs'])) {
                        foreach ($data['linked_chefs'] as $chefId) {
                            $dao->linkChef($restId, $chefId);
                        }
                    }
                }
            }

            if ($success) {
                unset($_SESSION['reg_data']);
                header("Location: login.php?msg=sucesso");
                exit;
            } else {
                $error = "Erro ao salvar no banco de dados.";
            }
        }
    }
}

require_once dirname(__DIR__) . '/base.php';
?>

<div class="container">
    <div class="form-wrapper">
        <style>
            .form-wrapper { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; margin: 40px auto; }
            .form-wrapper input:not([type="checkbox"]), .form-wrapper textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
            .form-wrapper label { font-weight: bold; margin-bottom: 5px; display: block; color: #444; }
            .btn-next { background: #8b2538; color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; width: 100%; font-weight: bold; margin-top: 10px; }
            .file-input { border: 2px dashed #ddd; padding: 15px; text-align: center; border-radius: 8px; background: #fafafa; margin-bottom: 15px; }
            .privacy-box { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 12px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 20px; line-height: 1.4; }
            .chef-list { max-height: 150px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px; padding: 10px; margin-bottom: 15px; background: #fdfdfd; }
            .chef-item { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        </style>

        <h2>Cadastro de <?php echo $type === 'user' ? 'Usuário' : ucfirst($type); ?> - Etapa <?php echo $step; ?></h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            
            <?php if($step == 1): ?>
                <?php if($type === 'user'): ?>
                    <div class="privacy-box">
                        <strong>🔒 Compromisso com sua Privacidade:</strong><br>
                        Seu E-mail, Telefone e Endereço serão mantidos protegidos e ficarão visíveis <strong>apenas para você</strong> no seu painel de perfil.
                    </div>
                <?php endif; ?>

                <label>Nome <?php echo $type === 'restaurant' ? 'do Restaurante' : 'Completo'; ?>:</label>
                <input type="text" name="name" placeholder="Ex: <?php echo $type === 'restaurant' ? 'Sabor do Mar' : 'João Silva'; ?>" required value="<?php echo $_SESSION['reg_data']['name'] ?? ''; ?>">
                <label>E-mail:</label>
                <input type="email" name="email" placeholder="email@exemplo.com" required value="<?php echo $_SESSION['reg_data']['email'] ?? ''; ?>">
                <label>Telefone:</label>
                <input type="text" name="phone" placeholder="(00) 00000-0000" value="<?php echo $_SESSION['reg_data']['phone'] ?? ''; ?>">
                <label>Endereço:</label>
                <textarea name="address" placeholder="Endereço completo"><?php echo $_SESSION['reg_data']['address'] ?? ''; ?></textarea>

                <?php if($type === 'restaurant'): ?>
                    <label>Link da Localização (Google Maps):</label>
                    <input type="text" name="location_map_link" placeholder="Cole a URL aqui" value="<?php echo $_SESSION['reg_data']['location_map_link'] ?? ''; ?>">
                    
                    <label>Foto Principal do Restaurante:</label>
                    <div class="file-input">
                        <i class="fa-solid fa-store" style="color: #8b2538; margin-bottom: 5px;"></i><br>
                        <input type="file" name="main_photo" accept="image/png, image/jpeg, image/webp" style="border: none; padding:0; margin:0;">
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-next">Próxima Etapa</button>

            <?php elseif($step == 2): ?>
                
                <?php if($type == 'user'): ?>
                    <label>Escolha uma Foto de Perfil (Opcional):</label>
                    <div class="file-input">
                        <i class="fa-solid fa-image" style="color: #8b2538; margin-bottom: 5px;"></i><br>
                        <input type="file" name="photo" accept="image/png, image/jpeg, image/webp" style="border: none; padding:0; margin:0;">
                    </div>
                    <p style="color: #666; font-size: 0.95rem; text-align: center; margin-top: 10px;">Tudo pronto! Clique abaixo para definir suas credenciais.</p>
                
                <?php elseif($type == 'chef'): ?>
                    <label>Foto de Perfil (Opcional):</label>
                    <div class="file-input">
                        <input type="file" name="photo" accept="image/png, image/jpeg, image/webp" style="border: none; padding:0; margin:0;">
                    </div>
                    <label>Sua Biografia/Descrição:</label>
                    <textarea name="description" placeholder="Conte um pouco sobre sua paixão pela culinária..."></textarea>
                    <label>Experiência Profissional:</label>
                    <textarea name="professional_experience" placeholder="Conte sobre sua carreira (Restaurantes, Cursos)"></textarea>
                    <label>Serviços Oferecidos:</label>
                    <textarea name="services_offered" placeholder="Ex: Jantares particulares, Consultoria..."></textarea>
                    <label>Região de Atuação:</label>
                    <input type="text" name="region_operation" placeholder="Ex: Grande Florianópolis">
                
                <?php elseif($type == 'restaurant'): ?>
                    <label>Descrição do Restaurante:</label>
                    <textarea name="description" placeholder="Conte a história e a especialidade da casa..."></textarea>
                    
                    <label>Horário de Funcionamento:</label>
                    <input type="text" name="opening_hours" placeholder="Ex: Terça a Domingo, 18h às 23h">
                    
                    <label>Serviços Oferecidos:</label>
                    <textarea name="services_offered" placeholder="Ex: Delivery, Espaço Kids, Som ao Vivo..."></textarea>
                    
                    <label>Galeria de Imagens (Selecione várias):</label>
                    <div class="file-input">
                        <input type="file" name="gallery[]" multiple accept="image/png, image/jpeg, image/webp" style="border: none; padding:0; margin:0;">
                        <small style="display:block; color:#666; margin-top:5px;">Você pode selecionar mais de uma imagem segurando CTRL (ou CMD).</small>
                    </div>

                    <label>Vincular Chefs (Opcional):</label>
                    <div class="chef-list">
                        <?php 
                            // Busca rápida dos chefs disponíveis
                            $conn = database::getConexao();
                            $stmt = $conn->query("SELECT id, name FROM chef ORDER BY name");
                            $chefs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($chefs) > 0) {
                                foreach ($chefs as $c) {
                                    echo '<div class="chef-item">';
                                    echo '<input type="checkbox" name="linked_chefs[]" value="' . $c['id'] . '" id="chef_' . $c['id'] . '">';
                                    echo '<label style="margin:0; font-weight:normal;" for="chef_' . $c['id'] . '">' . htmlspecialchars($c['name']) . '</label>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p style="color:#888; font-size:0.9em; margin:0;">Nenhum chef cadastrado no sistema ainda.</p>';
                            }
                        ?>
                    </div>
                <?php endif; ?>
                
                <button type="submit" class="btn-next">Próxima Etapa</button>

            <?php elseif($step == 3): ?>
                <label>Crie uma Senha:</label>
                <input type="password" name="pass" required>
                <label>Confirme a Senha:</label>
                <input type="password" name="pass_confirm" required>
                <button type="submit" class="btn-next">Finalizar Cadastro</button>
            <?php endif; ?>
        </form>
    </div>
</div>