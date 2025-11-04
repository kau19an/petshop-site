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
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/cadastro.css">
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
        <form method="POST" action="auth/cadastro.php">
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