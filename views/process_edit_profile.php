<?php
if (!isset($_SESSION)) session_start();
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/validation.php';
require_once dirname(__DIR__) . '/models/dao/userDAO.php';
require_once dirname(__DIR__) . '/models/dao/chefDAO.php';
require_once dirname(__DIR__) . '/models/model/user.php';
require_once dirname(__DIR__) . '/models/model/chef.php';

$id_logado = $_SESSION['user_id'] ?? $_SESSION['restaurant_id'] ?? null;
$tipo_logado = $_SESSION['user_type'] ?? 'user';
if ($tipo_logado === 'restaurante') {
    $tipo_logado = 'restaurant';
}

if (!$id_logado) {
    header("Location: login.php");
    exit;
}

// ========== Função de upload ==========
function uploadPhoto($file, $oldPhoto = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK || $file['size'] === 0) {
        return $oldPhoto;
    }

    $targetDir = dirname(__DIR__) . '/static/assets/uploads/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) {
        return $oldPhoto; // extensão não permitida
    }

    $nome = uniqid('profile_') . '.' . $ext;
    $caminho = $targetDir . $nome;

    if (move_uploaded_file($file['tmp_name'], $caminho)) {
        // Remove foto antiga se existir
        if ($oldPhoto && $oldPhoto !== 'default.png' && $oldPhoto !== 'default_chef.png' && file_exists($targetDir . $oldPhoto)) {
            unlink($targetDir . $oldPhoto);
        }
        return $nome;
    }
    return $oldPhoto; // falha no upload
}

// Dados comuns (+ validação: nome sem números/emoji/especiais, telefone só números)
$name  = trim($_POST['name'] ?? '');
[$nomeOk, $nomeErro] = validarNome($name);
$phone = limparTelefone($_POST['phone'] ?? '');
[$telOk, $telErro] = validarTelefone($phone);
$address = $_POST['address'] ?? '';
$email = $_POST['email'] ?? '';

if (!$nomeOk) {
    die("Erro ao salvar: " . htmlspecialchars($nomeErro) . " <a href='javascript:history.back()'>Voltar</a>");
}
if (!$telOk) {
    die("Erro ao salvar: " . htmlspecialchars($telErro) . " <a href='javascript:history.back()'>Voltar</a>");
}

$conn = database::getConexao();

if ($tipo_logado === 'chef') {
    $chefDAO = new chefDAO();
    $chefAtual = $chefDAO->read($id_logado);
    if (!$chefAtual) {
        die("Chef não encontrado.");
    }

    $oldPhoto = method_exists($chefAtual, 'getPhoto') ? $chefAtual->getPhoto() : 'default_chef.png';
    $newPhoto = uploadPhoto($_FILES['photo'] ?? null, $oldPhoto);

    // Atualiza os dados do chef
    $chefAtual->setName($name);
    $chefAtual->setEmail($email);
    if (method_exists($chefAtual, 'setPhone')) $chefAtual->setPhone($phone);
    if (method_exists($chefAtual, 'setAddress')) $chefAtual->setAddress($address);
    $chefAtual->setPhoto($newPhoto);
    $chefAtual->setRegionOperation($_POST['region_operation'] ?? '');
    $chefAtual->setServicesOffered($_POST['services_offered'] ?? '');
    $chefAtual->setDescription($_POST['description'] ?? '');
    $chefAtual->setProfessionalExperience($_POST['professional_experience'] ?? '');

    $chefDAO->update($chefAtual);
    $redirect = "user_profile.php?id=$id_logado&type=chef";
    header("Location: $redirect&msg=atualizado");

} elseif ($tipo_logado === 'restaurant') {
    $stmt = $conn->prepare("SELECT photo FROM restaurants WHERE id = ?");
    $stmt->execute([$id_logado]);
    $oldPhoto = $stmt->fetchColumn();

    $newPhoto = uploadPhoto($_FILES['photo'] ?? null, $oldPhoto);

    $sql = "UPDATE restaurants SET
                name = ?, email = ?, phone = ?, address = ?,
                photo = ?, opening_hours = ?, location_map_link = ?,
                description = ?, services_offered = ?, menu_description = ?
            WHERE id = ?";
    $conn->prepare($sql)->execute([
        $name, $email, $phone, $address,
        $newPhoto,
        $_POST['opening_hours'] ?? '',
        $_POST['location_map_link'] ?? '',
        $_POST['description'] ?? '',
        $_POST['services_offered'] ?? '',
        $_POST['menu_description'] ?? '',
        $id_logado
    ]);
    $redirect = "restaurant_profile.php?id=$id_logado";
    header("Location: $redirect&msg=atualizado");

} else {
    $userDAO = new userDAO();
    $userAtual = $userDAO->read($id_logado);
    if (!$userAtual) {
        die("Usuário não encontrado.");
    }

    $oldPhoto = method_exists($userAtual, 'getPhoto') ? $userAtual->getPhoto() : 'default.png';
    $newPhoto = uploadPhoto($_FILES['photo'] ?? null, $oldPhoto);

    $userAtual->setName($name);
    $userAtual->setEmail($email);
    if (method_exists($userAtual, 'setPhone')) $userAtual->setPhone($phone);
    if (method_exists($userAtual, 'setAddress')) $userAtual->setAddress($address);
    if (method_exists($userAtual, 'setPhoto')) $userAtual->setPhoto($newPhoto);

    $userDAO->update($userAtual);
    $redirect = "user_profile.php";
    header("Location: $redirect?msg=atualizado"); 
}

exit;