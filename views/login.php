<?php
require_once dirname(__DIR__) . '/base.php';
require_once dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Se já estiver logado, redireciona para o perfil correto
if (isset($_SESSION['user_id'])) {
    header("Location: user_profile.php");
    exit;
}

$conn = database::getConexao();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // LÓGICA DE LOGIN MULTI-TABELA (Mantida exatamente igual)
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
                header("Location: user_profile.php");
                exit;
            }
        }
        $message = "<div class='alert error'>E-mail ou senha incorretos!</div>";
    }
}
?>

<div class="container">
    <style>
        /* Container Centralizado */
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            margin-top: 20px;
        }

        /* Card Único e Moderno */
        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .auth-card h2 {
            color: #8b2538;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.6rem;
            text-align: center;
            font-weight: 600;
        }

        /* Estilização dos Inputs (Correção do visual antigo) */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
            font-size: 1rem;
            transition: all 0.2s;
            background-color: #fafafa;
        }

        .form-group input:focus {
            border-color: #8b2538;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(139, 37, 56, 0.1);
        }

        /* Botão de Submit Principal */
        .btn-auth {
            background: linear-gradient(135deg, #8b2538, #a83248);
            color: white;
            border: none;
            padding: 14px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(139, 37, 56, 0.2);
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(139, 37, 56, 0.3);
        }

        /* Links de Alternância de Telas */
        .auth-toggle-text {
            text-align: center;
            margin-top: 25px;
            font-size: 0.95rem;
            color: #666;
        }

        .auth-toggle-text a {
            color: #8b2538;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s;
        }

        .auth-toggle-text a:hover {
            text-decoration: underline;
            color: #dcb382;
        }

        /* Seleção de tipo de cadastro (Seletor de Grid) */
        .type-selector {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 20px;
        }

        .type-btn {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border: 2px solid #eef0f2;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.25s ease;
        }

        .type-btn i {
            font-size: 1.3rem;
            margin-right: 15px;
            color: #8b2538;
            width: 25px;
            text-align: center;
        }

        .type-btn:hover {
            border-color: #8b2538;
            background-color: #fdf5f6;
            color: #8b2538;
            transform: translateX(4px);
        }

        /* Alertas de Erro */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            max-width: 400px;
            margin: 20px auto 0 auto;
            text-align: center;
            font-weight: 500;
        }
        .error { background-color: #fde8e8; color: #9b1c1c; border: 1px solid #f8b4b4; }
        
        /* Classes utilitárias para esconder seções via JS */
        .hidden { display: none !important; }
    </style>

    <?php if(!empty($message)) echo $message; ?>
    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'sucesso') echo "<div class='alert' style='background:#def7ec; color:#03543f; border:1px solid #bcf0da;'>Cadastro realizado com sucesso! Faça seu login.</div>"; ?>

    <div class="auth-container">
        <div class="auth-card">
            
            <div id="login-section">
                <h2><i class="fa-solid fa-right-to-bracket"></i> Entrar no MyReceitas</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label>E-mail:</label>
                        <input type="email" name="login_email" placeholder="Ex: seuemail@site.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Senha:</label>
                        <input type="password" name="login_password" placeholder="Digite sua senha" required>
                    </div>
                    
                    <button type="submit" class="btn-auth">Entrar</button>
                </form>
                
                <div class="auth-toggle-text">
                    Não tem uma conta? <a href="#" id="go-to-register">Faça o cadastro aqui</a>
                </div>
            </div>

            <div id="register-section" class="hidden">
                <h2><i class="fa-solid fa-user-plus"></i> Criar conta nova</h2>
                <p style="color: #666; text-align: center; font-size: 0.95rem; margin-bottom: 20px;">
                    Escolha o tipo de perfil ideal para começar:
                </p>
                
                <div class="type-selector">
                    <a href="register_steps.php?type=user" class="type-btn">
                        <i class="fa-solid fa-user"></i>
                        <span>Sou Usuário / Cozinheiro</span>
                    </a>
                    <a href="register_steps.php?type=chef" class="type-btn">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Sou Chef de Cozinha</span>
                    </a>
                    <a href="register_steps.php?type=restaurant" class="type-btn">
                        <i class="fa-solid fa-shop"></i>
                        <span>Sou um Restaurante</span>
                    </a>
                </div>
                
                <div class="auth-toggle-text" style="margin-top: 30px;">
                    Já possui conta? <a href="#" id="go-to-login">Voltar para o Login</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginSection = document.getElementById('login-section');
    const registerSection = document.getElementById('register-section');
    const btnGoToRegister = document.getElementById('go-to-register');
    const btnGoToLogin = document.getElementById('go-to-login');

    // Ao clicar em "Faça o cadastro aqui"
    btnGoToRegister.addEventListener('click', function(e) {
        e.preventDefault();
        loginSection.classList.add('hidden');
        registerSection.classList.remove('hidden');
    });

    // Ao clicar em "Voltar para o Login"
    btnGoToLogin.addEventListener('click', function(e) {
        e.preventDefault();
        registerSection.classList.add('hidden');
        loginSection.classList.remove('hidden');
    });
});
</script>
</body>
</html>