<?php
// 1. Inicializa o banco e a sessão primeiro
require_once dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Se já estiver logado, redireciona para o perfil correto
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'restaurant') {
        header("Location: restaurant_profile.php");
    } else {
        header("Location: user_profile.php");
    }
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
        
        foreach ($tables as $table => $type) {
            $stmt = $conn->prepare("SELECT id, name, password FROM $table WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = $type; 
                header("Location:user_profile.php");
                exit;
            }
        }
        $message = "<div class='alert error'><i class='fa-solid fa-circle-exclamation'></i> E-mail ou senha incorretos!</div>";
    }
}

// 2. Carrega o head, navbar, etc.
require_once dirname(__DIR__) . '/base.php';
?>

<div class="container" style="margin-top: 40px;">
    
    <!-- Mensagens de Feedback -->
    <div style="margin-top: 30px;">
        <?php if(!empty($message)) echo $message; ?>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'sucesso'): ?>
            <div class='alert success'>
                <i class="fa-solid fa-circle-check"></i> Cadastro realizado com sucesso! Faça seu login.
            </div>
        <?php endif; ?>
    </div>

    <div class="auth-container">
        <!-- O card de autenticação usa a animação fade-slide-in do seu CSS -->
        <div class="auth-card fade-slide-in">
            
            <!-- SEÇÃO DE LOGIN -->
            <div id="login-section">
                <h2><i class="fa-solid fa-right-to-bracket text-accent"></i> Entrar no MyReceitas</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label for="login_email">E-mail</label>
                        <input type="email" id="login_email" name="login_email" placeholder="Ex: seuemail@site.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Senha</label>
                        <input type="password" id="login_password" name="login_password" placeholder="Digite sua senha" required>
                    </div>
                    
                    <button type="submit" class="btn-auth">Entrar</button>
                </form>
                
                <div class="auth-toggle-text">
                    Não tem uma conta? <a href="#" id="go-to-register">Faça o cadastro aqui</a>
                </div>
            </div>

            <!-- SEÇÃO DE REGISTRO (ESCONDIDA POR PADRÃO) -->
            <div id="register-section" class="hidden">
                <h2><i class="fa-solid fa-user-plus text-accent"></i> Criar conta nova</h2>
                <p style="text-align: center; color: var(--text-secondary); margin-bottom: 25px;">
                    Escolha o tipo de perfil ideal para começar sua jornada gastronômica:
                </p>
                
                <div class="type-selector">
                    <a href="register_steps.php?type=user" class="type-btn">
                        <i class="fa-solid fa-user"></i>
                        <div>
                            <strong>Sou Usuário</strong>
                            <small style="display:block; font-weight: normal; font-size: 0.8rem; opacity: 0.8;">Quero descobrir e salvar receitas.</small>
                        </div>
                    </a>
                    
                    <a href="register_steps.php?type=chef" class="type-btn">
                        <i class="fa-solid fa-utensils"></i>
                        <div>
                            <strong>Sou Chef</strong>
                            <small style="display:block; font-weight: normal; font-size: 0.8rem; opacity: 0.8;">Quero compartilhar minhas criações.</small>
                        </div>
                    </a>
                    
                    <a href="register_steps.php?type=restaurant" class="type-btn">
                        <i class="fa-solid fa-shop"></i>
                        <div>
                            <strong>Sou Restaurante</strong>
                            <small style="display:block; font-weight: normal; font-size: 0.8rem; opacity: 0.8;">Quero gerenciar minha equipe e menu.</small>
                        </div>
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
    const authCard = document.querySelector('.auth-card');

    // Ao clicar em "Faça o cadastro aqui"
    btnGoToRegister.addEventListener('click', function(e) {
        e.preventDefault();
        // Pequeno efeito de transição manual
        authCard.style.opacity = '0';
        setTimeout(() => {
            loginSection.classList.add('hidden');
            registerSection.classList.remove('hidden');
            authCard.style.opacity = '1';
        }, 200);
    });

    // Ao clicar em "Voltar para o Login"
    btnGoToLogin.addEventListener('click', function(e) {
        e.preventDefault();
        authCard.style.opacity = '0';
        setTimeout(() => {
            registerSection.classList.add('hidden');
            loginSection.classList.remove('hidden');
            authCard.style.opacity = '1';
        }, 200);
    });
});
</script>

</body>
</html>