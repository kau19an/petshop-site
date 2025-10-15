<?php
    session_start();

    $erros = $_SESSION['erros'] ?? [];
    $sucesso = $_SESSION['sucesso'] ?? null;
    $dados_antigos = $_SESSION['dados_antigos'] ?? [];

    // Limpa as variáveis da sessão para que não apareçam novamente
    unset($_SESSION['erros']);
    unset($_SESSION['sucesso']);
    unset($_SESSION['dados_antigos']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBC: Página de login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            background-color: #f4f4f4;
            background-image: url('imagens/background.jpeg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 15% 75%;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        
        .conteudo {
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: rgba(255, 255, 255, .95);
            padding: 30px;
            border-radius: 4%;
            box-shadow: 0 0 20px 8px rgb(0 0 0 / 10%);
            width: 370px;
            margin-left: 8%;
        }

        h1 {
            text-align: center;
            margin: 0 0 10px 0;
        }
        h3 {
            font-size: .9em;
            text-align: center;
            color: #555;
        }

        .msg-sucesso {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .msg-erro {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .msg-erro ul {
            margin: 0;
            padding-left: 20px;
        }

        .bloco-form {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[name="nome"], input[name="email"] {
            width: 200px;
        }
        input[name="datanasc"], input[name="telefone"] {
            width: 160px;
        }
        input[name="senha"] {
            width: 172px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }

        #cadastro-btn {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="conteudo">
        <h1>Login</h1>

        <?php if (!empty($sucesso)): ?>
            <div class="msg-sucesso">
                <?= htmlspecialchars($sucesso) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="msg-erro">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="logica/login.php">
            <div style="display: flex; gap: 2%;">
                <div class="bloco-form">
                    <label for="usuario">Usuário:</label>
                    <input type="text" name="usuario" id="usuario" value="<?= htmlspecialchars($dados_antigos['usuario'] ?? '') ?>">
                </div>

                <div class="bloco-form">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha">
                </div>
            </div>
            <button type="submit">Entrar</button>
            <div id="cadastro-btn">
                <a href="cadastro.php">Não tem uma conta? Cadastre-se</a><br>
            </div>
        </form>
    </div>
</body>
</html>