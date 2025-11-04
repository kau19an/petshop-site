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
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/login.css">
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

        <form method="POST" action="auth/login.php">
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