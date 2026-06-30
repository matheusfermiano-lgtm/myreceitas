<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/chef.php';
require_once dirname(__DIR__, 2) . '/models/dao/chefDAO.php';

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
    
    if ($chefDAO->create($chef)) {
        header("Location: ../users/login.php?msg=sucesso");
    }
}
?>
<div class="container">
    <div class="form-card">
        <h2>Cadastro de Chef Profissional</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Nome Completo" required>
            <input type="email" name="email" placeholder="E-mail Profissional" required>
            <input type="password" name="password" placeholder="Senha" required>
            <input type="text" name="phone" placeholder="Telefone de Contato">
            <textarea name="description" placeholder="Resumo da sua biografia"></textarea>
            <textarea name="professional_experience" placeholder="Sua experiência/currículo"></textarea>
            <input type="text" name="region_operation" placeholder="Região onde atende (Ex: Joinville e região)">
            <textarea name="services_offered" placeholder="Ex: Jantares em casa, Personal Chef..."></textarea>
            <button type="submit" class="btn-auth">Finalizar Cadastro</button>
        </form>
    </div>
</div>