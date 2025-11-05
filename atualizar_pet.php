<?php
    session_start();

    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: login.php');
        exit;
    }

    $erros = $_SESSION['erros'] ?? [];
    $sucesso = $_SESSION['sucesso'] ?? null;
    $usuario_logado = $_SESSION['usuario'];
    
    // Limpa as variáveis da sessão
    unset($_SESSION['erros']);
    unset($_SESSION['sucesso']);

    $nome_pet = $_GET['nome'] ?? '';
    $pet_para_atualizar = null;

    // Busca o pet pelo nome no arquivo
    $arquivo_pets = 'auth/data/pets.txt';

    if (file_exists($arquivo_pets) && !empty($nome_pet)) {
        $linhas = file($arquivo_pets, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($linhas as $linha) {
            $dados_pet = explode(';', $linha);
            
            // Verifica se a linha pertence ao usuário logado e se o nome confere
            if (count($dados_pet) >= 5 && trim($dados_pet[0]) === $usuario_logado && trim($dados_pet[1]) === $nome_pet) {
                $pet_para_atualizar = [
                    'nome' => trim($dados_pet[1]),
                    'idade' => trim($dados_pet[2]),
                    'especie' => trim($dados_pet[3]),
                    'raca' => trim($dados_pet[4]),
                    // Mantém o serviço para passar o novo pet
                    'servico_original' => (count($dados_pet) > 5) ? trim($dados_pet[5]) : "Nenhum",
                ];
                break; // Encontrou o pet, pode sair do loop
            }
        }
    }
    
    if ($pet_para_atualizar === null && !empty($nome_pet)) {
        $_SESSION['erros'][] = "\"{$nome_pet}\" não encontrado ou não pertence a você.";
        header('Location: home.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBC: Atualizar pet</title>
    <link rel="stylesheet" href="styles/common.css">
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
        form button { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="conteudo">
        <h1>Atualizar pet: <?= htmlspecialchars($pet_para_atualizar['nome'] ?? 'Erro') ?></h1>

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
            <input type="hidden" name="acao" value="atualizar">
            <input type="hidden" name="nome_original" value="<?= htmlspecialchars($pet_para_atualizar['nome'] ?? '') ?>">
            <input type="hidden" name="servico_original" value="<?= htmlspecialchars($pet_para_atualizar['servico_original'] ?? '') ?>">

            <div class="bloco-form">
                <label for="nome">Nome do pet:</label>
                <input type="text" name="nome" id="nome" 
                       value="<?= htmlspecialchars($pet_para_atualizar['nome'] ?? '') ?>" required>
            </div>

            <div class="bloco-form">
                <label for="idade">Idade (em anos):</label>
                <input type="number" name="idade" id="idade" min="0" 
                       value="<?= htmlspecialchars($pet_para_atualizar['idade'] ?? '') ?>" required>
            </div>
            
            <div class="bloco-form">
                <label for="especie">Espécie:</label>
                <input type="text" name="especie" id="especie" 
                       value="<?= htmlspecialchars($pet_para_atualizar['especie'] ?? '') ?>" required>
            </div>

            <div class="bloco-form">
                <label for="raca">Raça:</label>
                <input type="text" name="raca" id="raca" 
                       value="<?= htmlspecialchars($pet_para_atualizar['raca'] ?? '') ?>">
            </div>

            <button type="submit">Atualizar pet</button>
            <div id="voltar-btn" style="margin-top: 15px;"><a href="home.php">Cancelar e voltar</a></div>
        </form>
    </div>
</body>
</html>