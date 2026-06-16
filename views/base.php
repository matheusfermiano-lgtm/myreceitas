<?php
$tema_salvo = $_COOKIE['tema'] ?? 'light';
$classe_dark = ($tema_salvo === 'dark') ? 'dark-mode' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyReceitas</title>
    <style>
         :root {
            --bg: #f8fafc;
            --bg-gradient: linear-gradient(135deg, #afefff 0%, #00ff88 100%);
            --card: linear-gradient(135deg, #ddfff4 0%, #c0ffe1 100%);
            --nav: linear-gradient(135deg, #00ccff 0%, #00ff22 100%);
            --primary: #2563eb;      
            --primary-hover: #1d4ed8;
            
            --titulo: #0f172a;
            --texto: #001046;
            --texto-light: #f8fafc;
            --my: #ffffff;
            
            --butsav: #16a34a;
            --butsavhov: #0a702f;
            --butdel: #dc2626;
            --butdelhov: #991b1b;
            --butadd: #2563eb;
            --butaddhov: #1d4ed8;


            --table-head: linear-gradient(135deg, #afefff 0%, #00ff88 100%);
            
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --radius: 12px;
        }

        body.dark-mode {
            --bg: #081018;
            --bg-gradient: linear-gradient(135deg, #355c66 0%, #009955 100%);
            --card: linear-gradient(135deg, #377460 0%, #22523b 100%);
            --nav: linear-gradient(135deg, #004353 0%, #003a08 100%);
            --primary: #13398b;      
            --primary-hover: #0d2a79;
            
            --titulo: #f5f8ff;
            --texto: #f0f3ff;
            --texto-light: #f8fafc;
            --my: #ffffff;
            
            --butsav: #0f7233;
            --butsavhov: #013b17;
            --butdel: #741717;
            --butdelhov: #4d0404;
            --butadd: #0c2763;
            --butaddhov: #06194d;


            --table-head: linear-gradient(135deg, #355c66 0%, #009955 100%);
            
            --shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.4);
            --radius: 12px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--texto);
            min-height: 100vh;
            padding-bottom: 50px;
            transition: 2.4s;
        }

        nav {
            background: var(--nav);
            padding: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            box-shadow: var(--shadow);
            transition: 1.1s;
        }

        nav a { color: var(--texto-light); text-shadow: 1px 1px 2px var(--shadow); text-decoration: none; font-weight: 500; }

        .card {
            background: var(--card);
            max-width: 85%;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: 0.4s;
        }

        h2 { color: var(--titulo); margin-bottom: 1rem; }

        .table-container { overflow-x: auto; margin-top: 1rem; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--shadow);
        }

        th {
            background: var(--table-head);
            padding: 1rem;
            text-align: left;
            color: var(--titulo);
            font-size: 0.85rem;
            text-transform: uppercase;

        }

        td { padding: 1rem; border-bottom: 1px solid #e2e8f0; }
        
        body.dark-mode td { border-bottom: 1px solid #334151; }

        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
            border: none;
            cursor: pointer;
            text-align: center;
        }

        .btn-toggle-theme {
            background: transparent;
            color: var(--texto-light);
            border: 2px solid var(--texto-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-shadow: 1px 1px 2px var(--shadow);
            box-shadow: var(--shadow);
        }

        .btn-primary { background: var(--primary); color: white; margin-bottom: 1rem; padding: 0.6rem 1.2rem; border-radius: 6px; }
        .btn-success { background: var(--butsav); color: white; margin-bottom: 1rem; padding: 0.6rem 1.2rem; border-radius: 6px; }
        .btn-success:hover { background: var(--butsavhov); }
        .btn-editar { background: var(--primary); color: white; margin-bottom: 1rem; padding: 0.6rem 1.2rem; border-radius: 6px; }
        .btn-editar:hover { background: var(--primary-hover); }
        .btn-danger { background: var(--butdel); color: white; margin-bottom: 1rem; padding: 0.6rem 1.2rem; border-radius: 6px; }
        .btn-danger:hover { background: var(--butdelhov);  }
        .btn-primary { background: var(--butadd); color: white; margin-bottom: 1rem; padding: 0.6rem 1.2rem; border-radius: 6px; }
        .btn-primary:hover { background: var(--butaddhov); }

        .form-container {
            background: var(--card);
            max-width: 500px;
            margin: 2rem auto;
            padding: 2.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            color: var(--texto);
        }

        .form-container h2 {
            color: var(--titulo);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.6);
            color: var(--texto);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        body.dark-mode .form-group input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-group input:focus {
            border-color: var(--primary);
        }


        .btn-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .btn-submit {
            background: var(--butsav);
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: var(--butsavhov);
        }

        .link-cancel {
            color: var(--butdel);
            text-decoration: none;
            font-weight: 500;
        }

        .link-cancel:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            font-family: sans-serif;
        }

        .alert-erro {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }

        .alert-sucesso {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }

        footer.footer {
            text-align: center;
            padding: 1rem;
            background: var(--nav);
            color: var(--texto-light);
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: var(--shadow);
        }


        /* RESPONSIVIDADE */
        @media (max-width: 768px) {
            nav { flex-direction: column; gap: 0.5rem; }
            .card { margin: 1rem; padding: 1rem; }
        }
    </style>
</head>
<body class="<?= $classe_dark ?>">

    <nav>
        <h1 style="color: var(--my); margin-right: auto;">MyReceitas</h1>
        <a href="index.php?pagina=recipes" class="btn btn-toggle-theme">Receitas</a>
        <a href="index.php?pagina=users" class="btn btn-toggle-theme">Usuarios</a>
        <button id="toggle-theme" class="btn btn-toggle-theme">
            <i class="fas <?= ($tema_salvo === 'dark') ? 'fa-sun' : 'fa-moon' ?>"></i> 
            <?= ($tema_salvo === 'dark') ? 'Modo Claro' : 'Modo Escuro' ?>
        </button>
    </nav>


    <script>
        const btnTheme = document.getElementById('toggle-theme');
        const body = document.body;

        btnTheme.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            
            const isDark = body.classList.contains('dark-mode');
            
            const icon = isDark ? 'fa-sun' : 'fa-moon';
            const texto = isDark ? 'Modo Claro' : 'Modo Escuro';
            btnTheme.innerHTML = `<i class="fas ${icon}"></i> ${texto}`;
            

            const valorTema = isDark ? 'dark' : 'light';
            document.cookie = `tema=${valorTema}; max-age=${30 * 24 * 60 * 60}; path=/`;
        });
    </script>
