<?php
session_start(); 

$pdo = require_once "config/database.php";

require_once "models/model/recipe.php";
require_once "models/dao/recipeDAO.php";

require_once "models/model/user.php";
require_once "models/dao/userDAO.php";

$mensagem = "";
$tipoMensagem = "";

if (isset($_SESSION['msg'])) {
    $mensagem = $_SESSION['msg'];
    $tipoMensagem = $_SESSION['tipo_msg'];
    unset($_SESSION['msg'], $_SESSION['tipo_msg']);
}

$recipeDAO = new recipeDAO();
$userDAO = new userDAO();
$acao = $_GET['acao'] ?? '';

if ($acao === 'salvar_recipe') {
    $newRecipe = new Recipe($_POST['nome'], $_POST['descricao'], $_POST['preco'], $_POST['estoque'], $_POST['imagem']);
    $recipeDAO->create($newRecipe);
    header("Location: index.php?pagina=recipes");
    exit;
}
if ($acao === 'atualizar_recipe') {
    $recipeEditada = new Recipe($_POST['nome'], $_POST['descricao'], $_POST['preco'], $_POST['estoque'], $_POST['imagem'], $_POST['id']);
    $recipeDAO->update($recipeEditada);
    header("Location: index.php?pagina=recipes&mensagem=editado");
    exit;
}

if ($acao === 'excluir_recipe') {
    $recipeDAO->deleteById($_GET['id']);
    header("Location: index.php?pagina=recipes");
    exit;
}





if ($acao === 'salvar_user') {
    $nomeImagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeImagem = uniqid('cli_') . '.' . $extensao;
        move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/' . $nomeImagem);
    }
    
    $newUser = new User($_POST['nome'], $_POST['email'], $_POST['senha'], $nomeImagem);
    $userDAO->create($newUser);
    header("Location: index.php?pagina=users");
    exit;
}
if ($acao === 'atualizar_user') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    
    $nomeImagem = $_POST['imagem_atual'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeImagem = uniqid('cli_') . '.' . $extensao;
        move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/' . $nomeImagem);
        
        if (!empty($_POST['imagem_atual']) && file_exists('uploads/' . $_POST['imagem_atual'])) {
            unlink('uploads/' . $_POST['imagem_atual']);
        }
    }

    $UserAtualizado = new User($nome, $cpf, $email, $telefone, $endereco, $nomeImagem, $id);
    $userDAO->update($UserAtualizado);

    header("Location: index.php?pagina=users&mensagem=editado");
    exit;
}

if ($acao === 'excluir_user') {
    $userDAO->deleteById($_GET['id']);
    header("Location: index.php?pagina=users");
    exit;
}

include "views/cabecalho.php";

$pagina = $_GET['pagina'] ?? 'contatos';

switch ($pagina) {
    case 'recipe_form':
        include "views/recipes/form.php";
        break;

    case 'recipe_editar':
        $id = $_GET['id'];
        $recipe = $recipeDAO->read($_GET['id']);
        include "views/recipes/editar.php";
        break;

    case 'recipes':
        $recipes = $recipeDAO->readAll();
        include "views/recipes/lista.php";
        break;

    case 'users':
        $users = $userDAO->readAll();
        include "views/users/lista.php";
        break;


    case 'user_form':
        include "views/users/form.php";
        break;

    case 'user_editar':
        $id = $_GET['id'];
        $user = $userDAO->read($_GET['id']);
        include "views/users/editar.php";
        break;


    default:
        $recipes = $recipeDAO->readAll();
        include "views/recipes/lista.php";
        break;
}

echo "<footer class='footer'>&copy; 2026 - Desenvolvido por My Group ®</footer>";
echo "</body></html>";
?>