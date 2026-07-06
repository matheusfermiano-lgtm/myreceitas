<?php
if(!isset($_SESSION)) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

require_once dirname(__DIR__) . '/config/database.php';

$id_logado = $_SESSION['user_id'] ?? $_SESSION['restaurant_id'];
$tipo_logado = $_SESSION['user_type'] ?? 'user';

if ($tipo_logado === 'restaurante') {
    $tipo_logado = 'restaurant';
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

$db = database::getConexao();

// Upload da foto de perfil
$foto_nova = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $extensao = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $nome_arquivo = $tipo_logado . "_" . uniqid() . "." . $extensao;
    $destino = dirname(__DIR__) . "/assets/uploads/" . $nome_arquivo;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $destino)) {
        $foto_nova = $nome_arquivo;
    }
}

// 1. Processamento de CHEF
if ($tipo_logado === 'chef') {
    $region = trim($_POST['region_operation'] ?? '');
    $services = trim($_POST['services_offered'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $experience = trim($_POST['professional_experience'] ?? '');

    if ($foto_nova) {
        $sql = "UPDATE chef SET name = ?, email = ?, phone = ?, address = ?, region_operation = ?, services_offered = ?, description = ?, professional_experience = ?, photo = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $email, $phone, $address, $region, $services, $description, $experience, $foto_nova, $id_logado]);
    } else {
        $sql = "UPDATE chef SET name = ?, email = ?, phone = ?, address = ?, region_operation = ?, services_offered = ?, description = ?, professional_experience = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $email, $phone, $address, $region, $services, $description, $experience, $id_logado]);
    }

    header("Location: user_profile.php?id=" . $id_logado . "&type=chef");
    exit;
} 
// 2. Processamento de RESTAURANTE (Sincronizado com a tabela 'restaurants')
elseif ($tipo_logado === 'restaurant') {
    $opening_hours = trim($_POST['opening_hours'] ?? '');
    $location_map_link = trim($_POST['location_map_link'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $services_offered = trim($_POST['services_offered'] ?? '');
    $menu_description = trim($_POST['menu_description'] ?? '');

    if ($foto_nova) {
        $sql = "UPDATE restaurants SET name = ?, email = ?, phone = ?, address = ?, opening_hours = ?, location_map_link = ?, description = ?, services_offered = ?, menu_description = ?, photo = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $email, $phone, $address, $opening_hours, $location_map_link, $description, $services_offered, $menu_description, $foto_nova, $id_logado]);
    } else {
        $sql = "UPDATE restaurants SET name = ?, email = ?, phone = ?, address = ?, opening_hours = ?, location_map_link = ?, description = ?, services_offered = ?, menu_description = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $email, $phone, $address, $opening_hours, $location_map_link, $description, $services_offered, $menu_description, $id_logado]);
    }

    header("Location: restaurant_profile.php?id=" . $id_logado);
    exit;
} 
// 3. Processamento de USUÁRIO PADRÃO
else {
    if ($foto_nova) {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, photo = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $email, $phone, $address, $foto_nova, $id_logado]);
    } else {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $email, $phone, $address, $id_logado]);
    }

    header("Location: user_profile.php?id=" . $id_logado . "&type=user");
    exit;
}