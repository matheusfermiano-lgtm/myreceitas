<?php
// Subindo 2 níveis: de 'users' para 'views' e de 'views' para a raiz
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/models/model/user.php'; 
require_once dirname(__DIR__, 2) . '/models/dao/userDAO.php';
require_once dirname(__DIR__, 2) . '/config/validation.php';

$formError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeInput = trim($_POST['name'] ?? '');
    [$nomeOk, $nomeErro] = validarNome($nomeInput);

    // Telefone: envia/salva APENAS números
    $phoneLimpo = limparTelefone($_POST['phone'] ?? '');
    [$telOk, $telErro] = validarTelefone($phoneLimpo);

    if (!$nomeOk) {
        $formError = $nomeErro;
    } elseif (!$telOk) {
        $formError = $telErro;
    } else {
        // Criptografando a senha antes de salvar
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $novoUsuario = new User(
            $nomeInput, 
            $_POST['email'], 
            $password, 
            $phoneLimpo !== '' ? $phoneLimpo : null, 
            $_POST['address'] ?? null
        );
        
        $dao = new userDAO();
        if ($dao->create($novoUsuario)) {
            echo "<div class='container'><p style='color: green; font-weight: bold;'>Usuário cadastrado com sucesso!</p></div>";
        }
    }
}
?>

<div class="container">
    <div class="form-wrapper">
        <h2 style="color: #8b2538; text-align: center; margin-top: 0;">Criar Conta no MyReceitas</h2>
        <?php if (!empty($formError)): ?>
            <p style="color: red; font-weight: bold; text-align: center;"><?php echo htmlspecialchars($formError); ?></p>
        <?php endif; ?>
        <form method="POST" action="user_form.php" id="form-cadastro" novalidate>
            
            <div class="form-group">
                <label>Nome Completo:</label>
                <input type="text" name="name" placeholder="Digite o seu nome" required
                    data-validate-nome maxlength="100"
                    value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                <small class="field-error" style="color:red; display:none;">Use apenas letras e espaços (sem números, emojis ou caracteres especiais).</small>
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
                <input type="text" name="phone" placeholder="(00) 00000-0000" inputmode="numeric"
                    data-validate-telefone maxlength="15"
                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                <small class="field-error" style="color:red; display:none;">Digite apenas números (DDD + número, 10 ou 11 dígitos).</small>
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