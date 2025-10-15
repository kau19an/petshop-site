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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E7E7E7;
            display: flex;
            justify-content: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        
        #conteudo {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: #333;
            margin: 5px 0;
        }

        
        #tabela {
            margin-top: 18px;
            overflow-x: auto;
        }

        #tabela-pet {
            width: 100%;
            border-collapse: collapse;
            min-width: 560px;
            font-size: 14px;
        }

        #tabela-pet caption {
            caption-side: top;
            text-align: left;
            font-weight: 600;
            padding-bottom: 8px;
            color: #444;
        }

        #tabela-pet th,
        #tabela-pet td {
            padding: 12px 14px;
            border: 1px solid #e6e6e6;
            text-align: left;
        }

        #tabela-pet thead th {
            background: linear-gradient(180deg,#f7f9fb,#eef4fb);
            color: #222;
            font-weight: 700;
        }

        #tabela-pet tbody tr:nth-child(odd) {
            background: #fbfcfd;
        }

        #tabela-pet tbody tr:hover {
            background: #f1f8ff;
        }

        .servico {
            color: #0b6efd;
            font-weight: 600;
        }

        #btn-agendar {
            padding: 8px 12px;
            background-color: #5cb85c;
            color: #fff;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-top: 12px;
        }
        #btn-agendar:hover {
            background-color: #4cae4c;
        }

        #btn-sair {
            padding: 8px 12px;
            background-color: #d9534f;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-top: 12px;
            font-size: .9em;
        }
        #btn-sair:hover {
            background-color: #c9302c;
        }
    </style>
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
                    <tr>
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
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <button id="btn-agendar" onClick="alert('Esse botão é somente de exemplo.')">Adicionar pet</button>
            <a href="logica/sair.php" id="btn-sair">Sair</a>
        </div>
    </div>
</body>
</html>