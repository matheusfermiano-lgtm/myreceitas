<?php
if (!isset($_SESSION)) session_start();

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

<div class="container" style="max-width: 900px; margin: 40px auto;">
    <div class="form-wrapper" style="max-width: 100%;">
        <h2 style="color: var(--primary); text-align: center; margin-bottom: 20px;">
            <i class="fa-solid fa-user-gear"></i> Editar Perfil (<?php echo ucfirst($tipo_logado); ?>)
        </h2>

        <form action="process_edit_profile.php" method="POST" enctype="multipart/form-data" id="form-cadastro" novalidate>
            <!-- Foto -->
            <div class="form-group">
                <label>Alterar Foto:</label>
                <input type="file" name="photo" accept="image/*">
            </div>

            <!-- Campos comuns -->
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>" required data-validate-nome maxlength="100">
                <small class="field-error" style="color:red; display:none;">Use apenas letras e espaços (sem números, emojis ou caracteres especiais).</small>
            </div>

            <div class="form-group">
                <label>E-mail:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required>
            </div>

            <div class="form-row" style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Telefone:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" inputmode="numeric" data-validate-telefone maxlength="15">
                    <small class="field-error" style="color:red; display:none;">Digite apenas números (DDD + número, 10 ou 11 dígitos).</small>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Endereço:</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>">
                </div>
            </div>

            <!-- Campos para RESTAURANTE -->
            <?php if ($tipo_logado === 'restaurant'): ?>
                <div class="form-group">
                    <label>Horário de Funcionamento:</label>
                    <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($data['opening_hours'] ?? ''); ?>" placeholder="Ex: Seg a Sex das 18h às 23h">
                </div>

                <div class="form-group">
                    <label>Link do Google Maps:</label>
                    <input type="url" name="location_map_link" value="<?php echo htmlspecialchars($data['location_map_link'] ?? ''); ?>" placeholder="https://maps.google.com/...">
                </div>

                <div class="form-group">
                    <label>Descrição do Restaurante:</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Serviços Oferecidos:</label>
                    <textarea name="services_offered" rows="3" placeholder="Ex: Wi-Fi gratuito, Estacionamento, Takeaway"><?php echo htmlspecialchars($data['services_offered'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Resumo do Cardápio / Especialidades:</label>
                    <textarea name="menu_description" rows="3" placeholder="Ex: Especialistas em comida italiana e massas artesanais"><?php echo htmlspecialchars($data['menu_description'] ?? ''); ?></textarea>
                </div>
            <?php endif; ?>

            <!-- Campos para CHEF -->
            <?php if ($tipo_logado === 'chef'): ?>
                <div class="form-group">
                    <label>Região de Atuação:</label>
                    <input type="text" name="region_operation" value="<?php echo htmlspecialchars($data['region_operation'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Serviços Oferecidos:</label>
                    <textarea name="services_offered" rows="3"><?php echo htmlspecialchars($data['services_offered'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Sobre Mim / Descrição:</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Experiência Profissional:</label>
                    <textarea name="professional_experience" rows="3"><?php echo htmlspecialchars($data['professional_experience'] ?? ''); ?></textarea>
                </div>
            <?php endif; ?>

            <!-- Botões -->
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-submit" style="flex: 1;">Salvar Alterações</button>
                <a href="javascript:history.back()" class="btn-submit" style="background: #ccc; color: #333; flex: 1; text-align: center;">Cancelar</a>
            </div>
        </form>
    </div>
</div>