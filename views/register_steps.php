<?php
require_once dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$type = $_GET['type'] ?? 'user';
$step = $_GET['step'] ?? 1;

// 2. Lógica de processamento (Mantenha aqui em cima!)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        $_SESSION['reg_data'] = $_POST;
        header("Location: register_steps.php?type=$type&step=2");
        exit; // Sempre use exit após um header
    }
    
    if ($step == 2) {
        $_SESSION['reg_data'] = array_merge($_SESSION['reg_data'], $_POST);
        header("Location: register_steps.php?type=$type&step=3");
        exit;
    }

    if ($step == 3) {
        // Finalizar e salvar no banco
        $data = array_merge($_SESSION['reg_data'], $_POST);
        if($data['pass'] !== $data['pass_confirm']) {
             echo "Senhas não conferem!";
        } else {
            // Aqui você chama o DAO específico baseado no $type e salva
            // Após salvar:
            unset($_SESSION['reg_data']);
            header("Location:login.php?msg=sucesso");
            exit;
        }
    }
}

require_once dirname(__DIR__) . '/base.php';

?>

<div class="container">
    <div class="form-wrapper">
        <h2>Cadastro de <?php echo ucfirst($type); ?> - Etapa <?php echo $step; ?></h2>
        
        <form method="POST">
            <?php if($step == 1): ?>
                <div class="alert info"> Nome, Email e Telefone serão públicos. Endereço é privado.</div>
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="text" name="phone" placeholder="Telefone">
                <textarea name="address" placeholder="Endereço (Privado)"></textarea>
                <button type="submit">Próximo</button>

            <?php elseif($step == 2): ?>
                <?php if($type == 'chef'): ?>
                    <textarea name="professional_experience" placeholder="Experiência Profissional"></textarea>
                    <input type="text" name="region_operation" placeholder="Região de Atuação">
                <?php elseif($type == 'restaurant'): ?>
                    <input type="text" name="opening_hours" placeholder="Horário de Funcionamento">
                    <input type="text" name="location_map_link" placeholder="Link do Google Maps">
                <?php endif; ?>
                <button type="submit">Próximo</button>

            <?php elseif($step == 3): ?>
                <input type="password" name="pass" placeholder="Criar Senha" required>
                <input type="password" name="pass_confirm" placeholder="Confirmar Senha" required>
                <button type="submit">Finalizar Cadastro</button>
            <?php endif; ?>
        </form>
    </div>
</div>