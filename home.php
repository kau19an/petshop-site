<?php
    session_start();

    $erros = $_SESSION['erros'] ?? [];
    $sucesso = $_SESSION['sucesso'] ?? null;

    // Limpa as variáveis da sessão para que não apareçam novamente
    unset($_SESSION['erros']);
    unset($_SESSION['sucesso']);

    $erros = [];

    // Verifica se o usuário está logado
    // Se não estiver, redireciona para a página de login
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        $erros[] = "Você precisa estar logado para acessar esta página.";
        $_SESSION['erros'] = $erros;
        header('Location: login.php');
        exit;
    }

    // Obtém o nome do usuário para exibir na página
    $nome_normal = $_SESSION['nome'] ?? ($_SESSION['usuario'] ?? 'Usuário');

    // Converte para maiúsculo a primeira letra do nome
    $nome = mb_convert_case($nome_normal, MB_CASE_TITLE);
    
    $pets_do_usuario = [];
    $usuario_logado = $_SESSION['usuario'];
    $arquivo_pets = 'auth/data/pets.txt';

    if (file_exists($arquivo_pets)) {
        // Lê todas as linhas do arquivo, ignorando as vazias
        $linhas = file($arquivo_pets, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $contador_linha = 0; // Adicionamos um contador para rastrear o ID
        
        foreach ($linhas as $linha) {
            // Formato esperado: usuarioLogado;nome_pet;idade;especie;raca[;servico_agendado]
            $dados_pet = explode(';', $linha);
            
            // Verifica se a linha tem pelo menos 5 campos
            if (count($dados_pet) >= 5) {
                // $dados_pet[0] é o 'usuarioLogado'
                if (trim($dados_pet[0]) === $usuario_logado) {
                    $contador_linha++; // Incrementa o ID real do pet
                    
                    // O serviço agendado está no índice 5. Se não existir, é "Nenhum".
                    $servico = (count($dados_pet) > 5) ? trim($dados_pet[5]) : "Nenhum";
                    
                    $pets_do_usuario[] = [
                        'id_pet' => $contador_linha, // Adiciona o ID para uso na URL
                        'usuario' => trim($dados_pet[0]),
                        'nome' => trim($dados_pet[1]),
                        'idade' => trim($dados_pet[2]),
                        'especie' => trim($dados_pet[3]),
                        'raca' => trim($dados_pet[4]),
                        'servico' => $servico, // Novo campo
                        'linha_original' => $linha // Armazena a linha completa para exclusão
                    ];
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBC: Página de acesso</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/home.css">
</head>
<body>
    <div id="conteudo">
        <h1 style="display: flex; justify-content: center; align-items: center; gap: 3%;">Bem-vindo(a), <?= htmlspecialchars($nome) ?>!</h1>
        
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

        <div id="tabela">
            <table id="tabela-pet">
                <caption>Seus pets e serviços agendados</caption>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Idade</th>
                        <th>Raça</th> <th>Ações</th>
                        <th>Serviço agendado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pets_do_usuario)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Nenhum pet cadastrado até o momento.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($pets_do_usuario as $pet): ?>
                        <tr>
                            <td><?= htmlspecialchars($pet['nome']) ?></td>
                            <td><?= htmlspecialchars($pet['especie']) ?></td>
                            <td><?= htmlspecialchars($pet['idade']) ?> anos</td>
                            <td><?= htmlspecialchars($pet['raca']) ?></td>
                            <td style="text-align: center;">
                                <button type="button" 
                                onclick="agendarServico(<?= $pet['id_pet'] ?>)"
                                style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; margin-bottom: 5px;">
                                Agendar
                                </button><br>
                                <a href="auth/pet.php?acao=remover&id=<?= $pet['id_pet'] ?>" onclick="return confirm('Tem certeza que deseja remover o pet &quot;<?= htmlspecialchars($pet['nome']) ?>&quot;?');" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; display: inline-block; text-decoration: none;">
                                Remover
                                </a>
                            </td>
                            <td class="servico"><?= htmlspecialchars($pet['servico']) ?></td> 
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <a href="adicionar_pet.php" id="btn-adicionar">Adicionar pet</a>
            <a href="auth/sair.php" id="btn-sair">Sair</a>
        </div>
    </div>

    <script>
    function agendarServico(petId) {
        // Pede ao usuário para digitar o serviço
        var servico = prompt("Digite o serviço a ser agendado:");

        // Checa se o usuário digitou algo e não cancelou
        if (servico !== null && servico.trim() !== "") {
            // Redireciona para o script PHP de processamento (auth/pet.php) com a ação 'agendar', o ID do pet e o serviço
            window.location.href = 'auth/pet.php?acao=agendar&id=' + petId + '&servico=' + encodeURIComponent(servico.trim());
        } else if (servico !== null) {
            alert("O serviço não pode estar vazio.");
        }
    }
</script>
</body>
</html>