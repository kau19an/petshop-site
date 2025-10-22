<?php
    session_start();

    // Redireciona se não estiver logado
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        $_SESSION['erros'][] = "Você precisa estar logado para adicionar um pet.";
        header('Location: login.php');
        exit;
    }

    $erros = $_SESSION['erros'] ?? [];
    $sucesso = $_SESSION['sucesso'] ?? null;
    $dados_antigos = $_SESSION['dados_antigos_pet'] ?? [];

    // Limpa as variáveis da sessão
    unset($_SESSION['erros']);
    unset($_SESSION['sucesso']);
    unset($_SESSION['dados_antigos_pet']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBC: Adicionar pet</title>
    <link rel="stylesheet" href="styles/cadastro.css">
    <style>
        .bloco-form {
            width: 100%;
            margin-bottom: 15px;
        }
        .bloco-form input, .bloco-form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        form button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="conteudo">
        <h1>Adicionar novo pet</h1>

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

        <form method="POST" action="auth/pet.php">
            <div class="bloco-form">
                <label for="nome">Nome do pet:</label>
                <input type="text" name="nome" id="nome" 
                       value="<?= htmlspecialchars($dados_antigos['nome'] ?? '') ?>" required>
            </div>

            <div class="bloco-form">
                <label for="idade">Idade (em anos):</label>
                <input type="number" name="idade" id="idade" min="0" 
                       value="<?= htmlspecialchars($dados_antigos['idade'] ?? '') ?>" required>
            </div>
            
            <div class="bloco-form">
                <label for="especie">Espécie:</label>
                <input type="text" name="especie" id="especie" 
                       value="<?= htmlspecialchars($dados_antigos['especie'] ?? '') ?>" placeholder="Por exemplo: Cão, Gato, Pássaro" required>
            </div>

            <div class="bloco-form">
                <label for="raca">Raça:</label>
                <input type="text" name="raca" id="raca" 
                       value="<?= htmlspecialchars($dados_antigos['raca'] ?? '') ?>" placeholder="Por exemplo: Labrador, Siamês">
            </div>

            <button type="submit">Adicionar pet</button>
            <div id="voltar-btn" style="margin-top: 15px;">
                <a href="home.php">Voltar para a página inicial</a>
            </div>
        </form>
    </div>
</body>
</html>