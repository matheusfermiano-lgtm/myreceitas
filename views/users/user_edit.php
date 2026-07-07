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