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