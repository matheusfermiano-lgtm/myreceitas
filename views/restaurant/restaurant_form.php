<?php
// views/restaurant/restaurant_form.php
require_once dirname(dirname(__DIR__)) . '/base.php';
require_once dirname(dirname(__DIR__)) . '/models/dao/restaurantDAO.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurantDAO = new RestaurantDAO();
    
    $photoName = "default_restaurant.png";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $targetDir = dirname(dirname(__DIR__)) . "/uploads/";
        $photoName = time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $targetDir . $photoName);
    }

    $newRestaurant = new Restaurant(
        null,
        $_POST['name'],
        $_POST['email'],       // Capturando o e-mail do form
        $_POST['password'],    // Capturando a senha do form
        $_POST['address'],
        $_POST['phone'],
        $_POST['description'],
        $photoName,
        $_POST['menu_description']
    );

    try {
        if ($restaurantDAO->create($newRestaurant)) {
            $message = "<div class='alert success'>Restaurante cadastrado com sucesso!</div>";
        } else {
            $message = "<div class='alert error'>Erro ao cadastrar restaurante.</div>";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "<div class='alert error'>Este e-mail já está sendo utilizado por outro restaurante!</div>";
        } else {
            $message = "<div class='alert error'>Erro no banco de dados: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<div class="container">
    <style>
        .form-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 700px; margin: 40px auto; }
        .form-card h2 { color: #8b2538; margin-top: 0; border-bottom: 2px solid #dcb382; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; outline: none; font-size: 15px; }
        .form-group input:focus, .form-group textarea:focus { border-color: #8b2538; }
        .btn-submit { background: linear-gradient(135deg, #8b2538, #b0354b); color: white; border: none; padding: 14px 28px; font-size: 16px; font-weight: bold; border-radius: 25px; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { opacity: 0.9; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>

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
                <label for="menu_description">Cardápio (Pratos Principais):</label>
                <textarea id="menu_description" name="menu_description" rows="4" placeholder="Ex: Lasanha de Costela, Risoto de Alho Poró..."></textarea>
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