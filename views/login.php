<?php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Se já estiver logado, redireciona para o perfil correto
if (isset($_SESSION['user_id'])) {
    header("Location:user_profile.php?id=" . $_SESSION['user_id'] . "&type=" . $_SESSION['user_type']);
    exit;
}

$conn = database::getConexao();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // LÓGICA DE LOGIN MULTI-TABELA
    if ($action === 'login') {
        $email = $_POST['login_email'];
        $password = $_POST['login_password'];
        $tables = ['users' => 'user', 'chef' => 'chef', 'restaurants' => 'restaurant'];
        $found = false;

        foreach ($tables as $table => $type) {
            $stmt = $conn->prepare("SELECT id, name, password FROM $table WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = $type; // Salva se é user, chef ou restaurant
                header("Location:user_profile.php");
                exit;
            }
        }
        $message = "<div class='alert error'>E-mail ou senha incorretos!</div>";
    }
}
?>

<div class="container">
    <style>
        .auth-wrapper { display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; margin-top: 40px; }
        .auth-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex: 1; min-width: 300px; max-width: 450px; }
        .type-selector { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 20px; }
        .type-btn { padding: 10px; border: 2px solid #dcb382; border-radius: 10px; cursor: pointer; text-align: center; text-decoration: none; color: #8b2538; font-weight: bold; transition: 0.3s; }
        .type-btn:hover { background: #8b2538; color: white; }
    </style>

    <?php if(!empty($message)) echo $message; ?>

    <div class="auth-wrapper">
        <!-- CARD DE LOGIN -->
        <div class="auth-card">
            <h2><i class="fa-solid fa-right-to-bracket"></i> Entrar</h2>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>E-mail:</label>
                    <input type="email" name="login_email" required>
                </div>
                <div class="form-group">
                    <label>Senha:</label>
                    <input type="password" name="login_password" required>
                </div>
                <button type="submit" class="btn-auth">Entrar</button>
            </form>
        </div>

        <!-- CARD DE SELEÇÃO DE CADASTRO -->
        <div class="auth-card">
            <h2><i class="fa-solid fa-user-plus"></i> Criar conta nova</h2>
            <p>Escolha como deseja se cadastrar para iniciar as etapas:</p>
            <div class="type-selector">
                <a href="register_steps.php?type=user" class="type-btn"><i class="fa-solid fa-user"></i><br>Usuário</a>
                <a href="register_steps.php?type=chef" class="type-btn"><i class="fa-solid fa-utensils"></i><br>Chefe</a>
                <a href="register_steps.php?type=restaurant" class="type-btn"><i class="fa-solid fa-shop"></i><br>Restaurante</a>
            </div>
        </div>
    </div>
</div>