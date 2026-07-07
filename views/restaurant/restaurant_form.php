<?php
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/restaurant.php';
require_once dirname(__DIR__, 2) . '/models/dao/restaurantDAO.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurantDAO = new RestaurantDAO();
    
    // Tratamento de Foto
    $photoName = "default_restaurant.png";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $targetDir = "../../uploads/";
        $photoName = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $targetDir . $photoName);
    }

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $newRestaurant = new Restaurant(
        $_POST['name'],
        $_POST['email'],
        $password
    );
    
    // Setando os novos campos profissionais
    $newRestaurant->setAddress($_POST['address']);
    $newRestaurant->setPhone($_POST['phone']);
    $newRestaurant->setDescription($_POST['description']);
    $newRestaurant->setPhoto($photoName);
    $newRestaurant->setOpeningHours($_POST['opening_hours']); // Novo
    $newRestaurant->setServicesOffered($_POST['services_offered']); // Novo
    $newRestaurant->setLocationMapLink($_POST['location_map_link']); // Novo

    try {
        if ($restaurantDAO->create($newRestaurant)) {
            $message = "<div class='alert success'>Restaurante cadastrado com sucesso!</div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert error'>Erro: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container">

    <div class="form-card">
        <h2><i class="fa-solid fa-shop"></i> Cadastrar Novo Restaurante</h2>
        <?php echo $message; ?>
        
        <form action="restaurant_form.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nome do Restaurante:</label>
                <input type="text" id="name" name="name" required placeholder="Ex: Cantina Di Milano">
            </div>

            <div class="form-group">
                <label for="email">E-mail de Acesso:</label>
                <input type="email" id="email" name="email" required placeholder="Ex: contato@cantina.com">
            </div>

            <div class="form-group">
                <label for="password">Senha de Acesso:</label>
                <input type="password" id="password" name="password" required placeholder="Digite uma senha segura">
            </div>
            
            <div class="form-group">
                <label for="address">Endereço Completo:</label>
                <input type="text" id="address" name="address" required placeholder="Rua, Número, Bairro - Cidade">
            </div>

            <div class="form-group">
                <label for="phone">Telefone de Contato:</label>
                <input type="text" id="phone" name="phone" placeholder="Ex: (47) 99999-9999">
            </div>

            <div class="form-group">
                <label for="description">Descrição do Estabelecimento:</label>
                <textarea id="description" name="description" rows="4" placeholder="Conte um pouco sobre a especialidade da casa..."></textarea>
            </div>

            <div class="form-group">
                <label for="phone">Horario de Funcionamento:</label>
                <input type="text" name="opening_hours" placeholder="Ex: Seg a Sex das 10h as 22h">
            </div>

             <div class="form-group">
                <label for="phone">Link de localização:</label>
                <input type="text" name="location_map_link" placeholder="Link do Google Maps">
            </div>
            
             <div class="form-group">
                <label for="phone">Serviços:</label>
                <textarea name="services_offered" placeholder="Ex: Marmitas, Eventos..."></textarea>
            </div>

            <div class="form-group">
                <label for="photo">Foto do Restaurante:</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>
    </div>
</div>
</body>
</html>