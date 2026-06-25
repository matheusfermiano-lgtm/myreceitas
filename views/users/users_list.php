<?php
require_once '../../base.php';
require_once dirname(__DIR__, 2) . '/models/model/user.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/userDAO.php';

$dao = new userDAO();

// Tratamento de exclusão
if (isset($_GET['delete_id'])) {
    $dao->deleteById($_GET['delete_id']);
    echo "<div class='container'><p style='color: green; font-weight: bold;'>Usuário removido com sucesso!</p></div>";
}

$usuarios = $dao->readAll();
?>
<style>
    .header-list { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header-list h2 { color: #8b2538; font-size: 28px; margin: 0; }
    .btn-add { background-color: #8b2538; color: white; padding: 10px 20px; text-decoration: none; border-radius: 20px; font-weight: bold; }
    
    .user-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .user-card { background: white; border: 1px solid #eaeaea; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .user-card i { font-size: 40px; color: #ff6a28; margin-bottom: 15px; }
    .user-card h3 { margin: 0 0 5px 0; color: #333; font-size: 18px; }
    .user-card p { margin: 5px 0; color: #666; font-size: 14px; }
    .card-actions { margin-top: 15px; display: flex; justify-content: center; gap: 10px; }
    .card-actions a { padding: 6px 12px; border-radius: 5px; text-decoration: none; color: white; font-size: 12px; font-weight: bold; }
    .btn-edit { background-color: #f39c12; }
    .btn-delete { background-color: #e74c3c; }
</style>

<div class="container">
    <div class="header-list">
        <h2>Usuários Registados</h2>
        <a href="views/users/user_form.php" class="btn-add">+ Novo Usuário</a>
    </div>

    <div class="user-grid">
        <?php if(empty($usuarios)): ?>
            <p>Nenhum usuário cadastrado até ao momento.</p>
        <?php else: ?>
            <?php foreach($usuarios as $u): ?>
                <div class="user-card">
                    <i class="fa-solid fa-circle-user"></i>
                    <h3><?= htmlspecialchars($u->getName()) ?></h3>
                    <p><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($u->getEmail()) ?></p>
                    <p><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($u->getPhone() ?: 'Não informado') ?></p>
                    
                    <div class="card-actions">
                        <a href="views/users/user_edit.php?id=<?= $u->getId() ?>" class="btn-edit"><i class="fa-solid fa-user-pen"></i> Gerir</a>
                        <a href="views/users/users_list.php?delete_id=<?= $u->getId() ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja remover este utilizador?');"><i class="fa-solid fa-user-xmark"></i> Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>