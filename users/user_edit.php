<?php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/models/model/user.php'; 
require_once dirname(__DIR__) . '/models/dao/userDAO.php';

$dao = new userDAO();
$usuario = null;

// Simulação de utilizador ligado (ID 1). No futuro, substitua pelo ID vindo da $_SESSION['user_id']
$id_logado = 1; 
$usuario = $dao->read($id_logado);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $usuarioAtualizado = new User($name, $email, $password, $phone, $address);
    $usuarioAtualizado->setId($id);
    
    $dao->update($usuarioAtualizado);
    echo "<div class='container'><p style='color: green; font-weight: bold;'>Perfil atualizado com sucesso!</p></div>";
    
    // Recarrega os dados atualizados no ecrã
    $usuario = $dao->read($id); 
}
?>
<style>
    .profile-container { display: flex; gap: 30px; max-width: 900px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .profile-sidebar { flex: 1; text-align: center; border-right: 1px solid #eee; padding-right: 30px; }
    .profile-form { flex: 2; background-color: #fbeceb; padding: 30px; border-radius: 12px; }
    
    .avatar-placeholder { width: 120px; height: 120px; background-color: #ff6a28; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px; margin: 0 auto 20px; }
    
    .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
    .form-group label { color: #d37e42; font-weight: 600; margin-bottom: 5px; }
    .form-group input, .form-group textarea { background-color: #fefce5; border: 1px solid #e0d9b5; border-radius: 8px; padding: 10px; font-size: 14px; outline: none; }
    
    .btn-update { background: linear-gradient(to bottom, #ff9e22, #e57300); color: white; font-size: 16px; font-weight: bold; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 20px; }
</style>

<div class="container">
    <?php if ($usuario): ?>
    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="avatar-placeholder">
                <i class="fa-regular fa-user"></i>
            </div>
            <h3><?= htmlspecialchars($usuario->getName()) ?></h3>
            <p style="color: #666;"><?= htmlspecialchars($usuario->getEmail()) ?></p>
            <p style="font-size: 13px; color: #999;">Membro da rede MyReceitas</p>
        </div>

        <div class="profile-form">
            <h3 style="color: #8b2538; margin-top: 0; margin-bottom: 20px;">Minhas Informações</h3>
            <form method="POST" action="user_edit.php">
                <input type="hidden" name="id" value="<?= $usuario->getId() ?>">
                <input type="hidden" name="password" value="<?= htmlspecialchars($usuario->getPassword()) ?>">

                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($usuario->getName()) ?>" required>
                </div>

                <div class="form-group">
                    <label>E-mail:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($usuario->getEmail()) ?>" required>
                </div>

                <div class="form-group">
                    <label>Telefone:</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($usuario->getPhone()) ?>">
                </div>

                <div class="form-group">
                    <label>Endereço:</label>
                    <textarea name="address" rows="3"><?= htmlspecialchars($usuario->getAddress()) ?></textarea>
                </div>

                <button type="submit" class="btn-update">Salvar Alterações</button>
            </form>
        </div>
    </div>
    <?php else: ?>
        <p style="text-align: center; color: red;">Utilizador não encontrado. Certifique-se de que o banco de dados contém registros.</p>
    <?php endif; ?>
</div>
</body>
</html>