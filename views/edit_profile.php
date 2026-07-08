<?php
if(!isset($_SESSION)) session_start();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['restaurant_id'])) {
    header("Location: login.php");
    exit;
}

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/dao/userDAO.php';
require_once dirname(__DIR__) . '/models/dao/chefDAO.php';

$id_logado = $_SESSION['user_id'] ?? $_SESSION['restaurant_id'];
$tipo_logado = $_SESSION['user_type'] ?? 'user';

// Normaliza o tipo caso esteja em português na sessão
if ($tipo_logado === 'restaurante') {
    $tipo_logado = 'restaurant';
}

$data = [];

// Carrega e normaliza os dados num array padrão para evitar erros de métodos
if ($tipo_logado === 'chef') {
    $chefDAO = new chefDAO();
    $profile = $chefDAO->read($id_logado);
    if ($profile) {
        $data = [
            'name' => $profile->getName(),
            'email' => $profile->getEmail(),
            'phone' => method_exists($profile, 'getPhone') ? $profile->getPhone() : '',
            'address' => method_exists($profile, 'getAddress') ? $profile->getAddress() : '',
            'region_operation' => $profile->getRegionOperation(),
            'services_offered' => $profile->getServicesOffered(),
            'description' => $profile->getDescription(),
            'professional_experience' => $profile->getProfessionalExperience()
        ];
    }
} elseif ($tipo_logado === 'restaurant') {
    $stmt = database::getConexao()->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->execute([$id_logado]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} else {
    $uDAO = new userDAO();
    $profile = $uDAO->read($id_logado);
    if ($profile) {
        $data = [
            'name' => $profile->getName(),
            'email' => $profile->getEmail(),
            'phone' => method_exists($profile, 'getPhone') ? $profile->getPhone() : '',
            'address' => method_exists($profile, 'getAddress') ? $profile->getAddress() : ''
        ];
    }
}

require_once dirname(__DIR__) . '/base.php';
?>

<div style="max-width: 2000px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); font-family: sans-serif;">
    <h2 style="color: #8b2538; margin-top: 0; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
        <i class="fa-solid fa-user-gear"></i> Editar Perfil (<?php echo ucfirst($tipo_logado); ?>)
    </h2>

    <form action="process_edit_profile.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px; margin-top: 20px;">
        
        <div>
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Alterar Foto:</label>
            <input type="file" name="photo" accept="image/*" style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; width: 100%;">
        </div>

        <div>
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Nome:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
        </div>

        <div>
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">E-mail:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
        </div>

        <div>
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Telefone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
        </div>

        <div>
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Endereço:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
        </div>

        <?php if ($tipo_logado === 'restaurant'): ?>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Horário de Funcionamento:</label>
                <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($data['opening_hours'] ?? ''); ?>" placeholder="Ex: Seg a Sex das 18h às 23h" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Link do Google Maps:</label>
                <input type="url" name="location_map_link" value="<?php echo htmlspecialchars($data['location_map_link'] ?? ''); ?>" placeholder="https://maps.google.com/..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Descrição do Restaurante:</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Serviços Oferecidos:</label>
                <textarea name="services_offered" rows="3" placeholder="Ex: Wi-Fi gratuito, Estacionamento, Takeaway" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($data['services_offered'] ?? ''); ?></textarea>
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Resumo do Cardápio / Especialidades:</label>
                <textarea name="menu_description" rows="3" placeholder="Ex: Especialistas em comida italiana e massas artesanais" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($data['menu_description'] ?? ''); ?></textarea>
            </div>
        <?php endif; ?>

        <?php if ($tipo_logado === 'chef'): ?>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Região de Atuação:</label>
                <input type="text" name="region_operation" value="<?php echo htmlspecialchars($data['region_operation'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Serviços Oferecidos:</label>
                <textarea name="services_offered" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($data['services_offered'] ?? ''); ?></textarea>
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Sobre Mim / Descrição:</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444;">Experiência Profissional:</label>
                <textarea name="professional_experience" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($data['professional_experience'] ?? ''); ?></textarea>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <button type="submit" style="background: #8b2538; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; cursor: pointer; flex: 1;">Salvar Alterações</button>
            <a href="javascript:history.back()" style="background: #eee; color: #333; text-align: center; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; flex: 1;">Cancelar</a>
        </div>
    </form>
</div>