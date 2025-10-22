<?php
    session_start();

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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBC: Página de acesso</title>
    <link rel="stylesheet" href="styles/home.css">
</head>
<body>
    <div id="conteudo">
        <h1 style="display: flex; justify-content: center; align-items: center; gap: 3%;">Bem-vindo(a), <?= htmlspecialchars($nome) ?>!</h1>
        
        <div id="tabela">
            <table id="tabela-pet">
                <caption>Seus pets e serviços agendados</caption>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Idade</th>
                        <th>Serviço agendado</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr>
                        <td>Luna</td>
                        <td>Cão</td>
                        <td>3 anos</td>
                        <td class="servico">Banho e tosa (14/10 às 10:00)</td>
                    </tr>
                    <tr>
                        <td>Milo</td>
                        <td>Gato</td>
                        <td>1 ano</td>
                        <td class="servico">Vacina antirrábica (16/10 às 09:30)</td>
                    </tr>
                    <tr>
                        <td>Bella</td>
                        <td>Cão</td>
                        <td>5 anos</td>
                        <td class="servico">Consulta odontológica (20/10 às 11:00)</td>
                    </tr> -->
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="adicionar_pet.php" id="btn-adicionar">Adicionar pet</a>
            <a href="auth/sair.php" id="btn-sair">Sair</a>
        </div>
    </div>
</body>
</html>