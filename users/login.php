<?php
// users/login.php - Localizado na raiz do projeto dentro da pasta /users

// Caminho ajustado: sobe apenas 1 nível para chegar na raiz e achar o base.php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se já estiver logado, manda direto para a página de perfil
if (isset($_SESSION['user_id'])) {
    header("Location: user_profile.php");
    exit;
}

$conn = database::getConexao();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // LÓGICA DE CADASTRO
    if ($action === 'register') {
        $name = $_POST['reg_name'];
        $email = $_POST['reg_email'];
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            
            if ($stmt->execute()) {
                // Login automático após o cadastro com sucesso
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['user_name'] = $name;
                header("Location: user_profile.php");
                exit;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "<div class='alert error'>Este e-mail já está cadastrado!</div>";
            } else {
                $message = "<div class='alert error'>Erro: " . $e->getMessage() . "</div>";
            }
        }
    }

    // LÓGICA DE LOGIN
    if ($action === 'login') {
        $email = $_POST['login_email'];
        $password = $_POST['login_password'];

        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: user_profile.php");
            exit;
        } else {
            $message = "<div class='alert error'>E-mail ou senha incorretos!</div>";
        }
    }
}
?>

<div class="container">
    <style>
        .auth-wrapper { display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; margin-top: 40px; }
        .auth-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex: 1; min-width: 300px; max-width: 450px; }
        .auth-card h2 { color: #8b2538; border-bottom: 2px solid #dcb382; padding-bottom: 10px; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; outline: none; }
        .form-group input:focus { border-color: #8b2538; }
        .btn-auth { background: linear-gradient(135deg, #8b2538, #b0354b); color: white; border: none; padding: 12px; font-size: 16px; font-weight: bold; border-radius: 25px; cursor: pointer; width: 100%; transition: 0.3s; margin-top: 10px; }
        .btn-auth:hover { opacity: 0.9; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>

    <?php if(!empty($message)) echo $message; ?>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h2><i class="fa-solid fa-right-to-bracket"></i> Já tenho conta</h2>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>E-mail:</label>
                    <input type="email" name="login_email" required placeholder="Seu e-mail cadastrado">
                </div>
                <div class="form-group">
                    <label>Senha:</label>
                    <input type="password" name="login_password" required placeholder="Sua senha">
                </div>
                <button type="submit" class="btn-auth">Entrar</button>
            </form>
        </div>

        <div class="auth-card">
            <h2><i class="fa-solid fa-user-plus"></i> Criar conta nova</h2>
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>Nome completo:</label>
                    <input type="text" name="reg_name" required placeholder="Ex: João Silva">
                </div>
                <div class="form-group">
                    <label>E-mail:</label>
                    <input type="email" name="reg_email" required placeholder="Melhor e-mail">
                </div>
                <div class="form-group">
                    <label>Senha:</label>
                    <input type="password" name="reg_password" required placeholder="Crie uma senha segura">
                </div>
                <button type="submit" class="btn-auth">Cadastrar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>