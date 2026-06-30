<?php
// Subindo 2 níveis: de 'users' para 'views' e de 'views' para a raiz
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/user.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/userDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Criptografando a senha antes de salvar
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $novoUsuario = new User(
        $_POST['name'], 
        $_POST['email'], 
        $password, 
        $_POST['phone'] ?? null, 
        $_POST['address'] ?? null
    );
    
    $dao = new userDAO();
    if ($dao->create($novoUsuario)) {
        echo "<div class='container'><p style='color: green; font-weight: bold;'>Usuário cadastrado com sucesso!</p></div>";
    }
}
?>

<style>
    .form-wrapper { background-color: #fbeceb; padding: 40px; border-radius: 12px; max-width: 600px; margin: 40px auto; }
    .form-group { display: flex; flex-direction: column; margin-bottom: 20px; }
    .form-group label { color: #d37e42; font-weight: 600; margin-bottom: 8px; font-size: 16px; }
    .form-group input, .form-group textarea { 
        background-color: #fefce5; 
        border: 1px solid #e0d9b5; 
        border-radius: 8px; 
        padding: 12px; 
        font-size: 15px; 
        outline: none;
        color: #444;
    }
    .btn-submit { 
        background: linear-gradient(to bottom, #ff9e22, #e57300); 
        color: white; 
        font-size: 20px; 
        font-weight: bold;
        padding: 15px 40px; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer; 
        display: block;
        margin: 30px auto 0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        transition: 0.2s;
    }
    .btn-submit:hover { filter: brightness(1.1); transform: translateY(-2px); }
</style>

<div class="container">
    <div class="form-wrapper">
        <h2 style="color: #8b2538; text-align: center; margin-top: 0;">Criar Conta no MyReceitas</h2>
        <form method="POST" action="user_form.php">
            
            <div class="form-group">
                <label>Nome Completo:</label>
                <input type="text" name="name" placeholder="Digite o seu nome" required>
            </div>
            
            <div class="form-group">
                <label>E-mail:</label>
                <input type="email" name="email" placeholder="exemplo@email.com" required>
            </div>

            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="password" placeholder="Digite uma senha segura" required>
            </div>

            <div class="form-group">
                <label>Telefone / Telemóvel:</label>
                <input type="text" name="phone" placeholder="(00) 00000-0000">
            </div>

            <div class="form-group">
                <label>Endereço Residencial:</label>
                <textarea name="address" rows="3" placeholder="Rua, Número, Bairro, Cidade..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>
    </div>
</div>
</body>
</html>