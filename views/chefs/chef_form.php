<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/chef.php';
require_once dirname(__DIR__, 2) . '/models/dao/chefDAO.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chefDAO = new chefDAO();
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $chef = new Chef($_POST['name'], $_POST['email'], $pass);
    $chef->setPhone($_POST['phone']);
    $chef->setAddress($_POST['address']);
    $chef->setDescription($_POST['description']);
    $chef->setProfessionalExperience($_POST['professional_experience']);
    $chef->setServicesOffered($_POST['services_offered']);
    $chef->setRegionOperation($_POST['region_operation']);
    
    // --- LÓGICA DE UPLOAD DE FOTO ---
    $photoName = 'default_chef.png'; // Valor padrão
    
    // Verifica se um arquivo foi enviado e se não houve erros de upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['photo']['tmp_name'];
        $fileName = basename($_FILES['photo']['name']);
        
        // Pega a extensão do arquivo e converte para minúsculo
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExt, $allowedExts)) {
            // Define o diretório de destino
            $uploadDir = dirname(__DIR__, 2) . '/assets/uploads/';
            
            // Cria a pasta se ela não existir
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Gera um nome único para evitar conflito de arquivos (ex: chef_64a1b2c.jpg)
            $newFileName = 'chef_' . uniqid() . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;
            
            // Move da pasta temporária para a pasta final
            if (move_uploaded_file($tmpName, $destPath)) {
                $photoName = $newFileName; // Atualiza a variável com o nome real
            } else {
                $message = "<div class='alert error'>Erro ao salvar a imagem no servidor.</div>";
            }
        } else {
            $message = "<div class='alert error'>Formato de imagem inválido. Use JPG, PNG ou WEBP.</div>";
        }
    }
    
    // Adiciona o nome da foto (seja a padrão ou a nova) no objeto Chef
    $chef->setPhoto($photoName);
    
    // Se não houve mensagens de erro da foto, prossegue com o cadastro
    if (empty($message)) {
        if ($chefDAO->create($chef)) {
            header("Location: ../users/login.php?msg=sucesso");
            exit;
        } else {
            $message = "<div class='alert error'>Erro ao cadastrar no banco de dados.</div>";
        }
    }
}
?>

<div class="container">

    <div class="form-card">
        <h2>Cadastro de Chef Profissional</h2>
        
        <?php if(!empty($message)) echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label>Foto de Perfil (Opcional)</label>
                <div class="file-input-wrapper">
                    <i class="fa-solid fa-camera fa-2x" style="color: #bbb;"></i><br>
                    <input type="file" name="photo" accept="image/png, image/jpeg, image/jpg, image/webp">
                </div>
            </div>

            <div class="form-group">
                <label>Nome Completo *</label>
                <input type="text" name="name" placeholder="Ex: João Silva" required>
            </div>

            <div class="form-group">
                <label>E-mail Profissional *</label>
                <input type="email" name="email" placeholder="contato@chefjoao.com" required>
            </div>

            <div class="form-group">
                <label>Senha *</label>
                <input type="password" name="password" placeholder="Crie uma senha forte" required>
            </div>

            <div class="form-group">
                <label>Telefone / WhatsApp</label>
                <input type="text" name="phone" placeholder="(XX) XXXXX-XXXX">
            </div>
            
            <div class="form-group">
                <label>Endereço / Local Base</label>
                <input type="text" name="address" placeholder="Ex: Rua das Flores, 123 - Centro">
            </div>

            <div class="form-group">
                <label>Resumo da sua Biografia</label>
                <textarea name="description" placeholder="Conte um pouco sobre sua paixão pela culinária..."></textarea>
            </div>

            <div class="form-group">
                <label>Experiência e Formação</label>
                <textarea name="professional_experience" placeholder="Cursos, restaurantes onde trabalhou..."></textarea>
            </div>

            <div class="form-group">
                <label>Região de Atuação</label>
                <input type="text" name="region_operation" placeholder="Ex: São Paulo e Grande SP">
            </div>

            <div class="form-group">
                <label>Serviços Oferecidos</label>
                <textarea name="services_offered" placeholder="Ex: Jantares particulares, Consultoria, Eventos..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>
    </div>
</div>