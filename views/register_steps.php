<?php
// Ativa o buffer para evitar o erro de "Headers already sent"
ob_start(); 

require_once dirname(__DIR__) . '/config/database.php';
// Importando todos os DAOs e Models necessários
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
        header("Location: register_steps.php?type=$type&step=2");
        exit;
    }
    
    if ($step == 2) {
        $_SESSION['reg_data'] = array_merge($_SESSION['reg_data'], $_POST);
        header("Location: register_steps.php?type=$type&step=3");
        exit;
    }

    if ($step == 3) {
        $data = array_merge($_SESSION['reg_data'], $_POST);
        
        if($data['pass'] !== $data['pass_confirm']) {
             $error = "Senhas não conferem!";
        } else {
            $hashedPassword = password_hash($data['pass'], PASSWORD_DEFAULT);

            // LÓGICA DE SALVAMENTO POR TIPO
            if ($type === 'user') {
                $dao = new userDAO();
                $obj = new User($data['name'], $data['email'], $hashedPassword, $data['phone'], $data['address']);
                $success = $dao->create($obj);
            } 
            elseif ($type === 'chef') {
                $dao = new chefDAO();
                $obj = new Chef($data['name'], $data['email'], $hashedPassword);
                $obj->setPhone($data['phone']);
                $obj->setAddress($data['address']);
                $obj->setProfessionalExperience($data['professional_experience']);
                $obj->setRegionOperation($data['region_operation']);
                $success = $dao->create($obj);
            } 
            elseif ($type === 'restaurant') {
                $dao = new RestaurantDAO();
                $obj = new Restaurant($data['name'], $data['email'], $hashedPassword);
                $obj->setPhone($data['phone']);
                $obj->setAddress($data['address']);
                $obj->setOpeningHours($data['opening_hours']);
                $obj->setLocationMapLink($data['location_map_link']);
                $success = $dao->create($obj);
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
            .form-wrapper input, .form-wrapper textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
            .btn-next { background: #8b2538; color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; width: 100%; font-weight: bold; }
        </style>

        <h2>Cadastro de <?php echo ucfirst($type); ?> - Etapa <?php echo $step; ?></h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        
        <form method="POST">
            <?php if($step == 1): ?>
                <label>Nome Completo:</label>
                <input type="text" name="name" placeholder="Ex: João Silva" required>
                <label>E-mail:</label>
                <input type="email" name="email" placeholder="email@exemplo.com" required>
                <label>Telefone:</label>
                <input type="text" name="phone" placeholder="(00) 00000-0000">
                <label>Endereço:</label>
                <textarea name="address" placeholder="Seu endereço completo"></textarea>
                <button type="submit" class="btn-next">Próxima Etapa</button>

            <?php elseif($step == 2): ?>
                <?php if($type == 'chef'): ?>
                    <label>Experiência Profissional:</label>
                    <textarea name="professional_experience" placeholder="Conte sobre sua carreira"></textarea>
                    <label>Região de Atuação:</label>
                    <input type="text" name="region_operation" placeholder="Ex: Grande Florianópolis">
                <?php elseif($type == 'restaurant'): ?>
                    <label>Horário de Funcionamento:</label>
                    <input type="text" name="opening_hours" placeholder="Ex: Seg a Sex, 11h às 23h">
                    <label>Link do Google Maps:</label>
                    <input type="text" name="location_map_link" placeholder="URL da localização">
                <?php else: ?>
                    <p>Clique em próximo para definir sua senha.</p>
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