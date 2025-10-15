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
    <title>UBC: Página de cadastro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            background-image: url('imagens/background.jpeg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 15% 75%;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        
        .conteudo {
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
            position: absolute;
            top: 15px;
            right: 0px;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 15px 0 0 15px;
            box-sizing: border-box;
            z-index: 10;
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

        #login-btn {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="conteudo">
        <h1>Cadastro</h1>

        <!-- Mensagem de sucesso -->
        <?php if (!empty($sucesso)): ?>
            <div class="msg-sucesso">
                <?= htmlspecialchars($sucesso) ?>
            </div>
        <?php endif; ?>

        <!-- Mensagem de erro -->
        <?php if (!empty($erros)): ?>
            <div class="msg-erro">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulário de cadastro -->
        <form method="POST" action="logica/cadastro.php">
            <h3>Insira os seus dados pessoais</h3>

            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 2%;">
                <div class="bloco-form">
                    <label for="nome">Nome completo:</label>
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($dados_antigos['nome'] ?? '') ?>">
                </div>

                <div class="bloco-form">
                    <label for="datanasc">Data de nascimento:</label>
                    <input type="date" name="datanasc" id="datanasc" value="<?= htmlspecialchars($dados_antigos['datanasc'] ?? '') ?>">
                </div>
                
                <div class="bloco-form">
                    <label for="email">E-mail:</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($dados_antigos['email'] ?? '') ?>" placeholder="exemplo@email.com">
                </div>

                <div class="bloco-form">
                    <label for="telefone">Telefone:</label>
                    <input type="tel" name="telefone" id="telefone" value="<?= htmlspecialchars($dados_antigos['telefone'] ?? '') ?>" placeholder="(XX) XXXXX-XXXX">
                </div>
            </div>

            <h3>Insira os seus dados de login</h3>

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
            <button type="submit">Cadastrar</button>
            <div id="login-btn">
                <a href="login.php">Já tem cadastro? Faça login</a>
            </div>
        </form>
    </div>
</body>
</html>