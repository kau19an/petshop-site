<?php
    session_start();

    $erros = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe e limpa os dados do formulário
        $usuario_temp = $_POST['usuario'] ?? '';
        $senha_temp = $_POST['senha'] ?? '';

        $usuario = trim($usuario_temp);
        $senha = $senha_temp;

        // Salvando os dados antigos de 'usuario' para preencher o campo se houver erro
        $_SESSION['dados_antigos'] = [
            'usuario' => $usuario_temp,
        ];

        // Validação básica se os campos estão preenchidos
        if (empty($usuario) && empty($senha)) {
            $erros[] = "Preencha o campo \"Usuário\" e \"Senha\".";
        } else if (empty($usuario)) {
            $erros[] = "Preencha o campo \"Usuário\".";
        } else if (empty($senha)) {
            $erros[] = "Preencha o campo \"Senha\".";
        }

        // Se houver erros de preenchimento, redireciona de volta
        if (!empty($erros)) {
            $_SESSION['erros'] = $erros;
            header('Location: ../login.php');
            exit;
        }

        $arquivo = 'data/clientes.txt';

        // Se o arquivo de usuários não existir, exibe um erro
        if (!file_exists($arquivo)) {
            $erros[] = "Não foi possível encontrar o arquivo de usuários. Tente se cadastrar novamente.";
            $_SESSION['erros'] = $erros;
            header('Location: ../login.php');
            exit;
        }

        //                              ↓ para ignorar linhas em branco ↓
        $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $login_sucesso = false; // Status de login falso
        $usuario_logado_nome = '';

        foreach ($linhas as $linha) {
            // Divide cada dado do 'usuarios.txt' sempre que chega no ponto e vírgula
            $dados = explode(';', $linha);

            if (count($dados) < 6) {
                continue;
            }

            $usuario_arquivo = $dados[4]; // índice 4 está o usuário
            $senha_arquivo = $dados[5];  // índice 5 está a senha

            if ($usuario === $usuario_arquivo && $senha === $senha_arquivo) {
                $login_sucesso = true;
                break;
            }
        }

        // Se o usuário logar com sucesso,
        if ($login_sucesso) {
            $_SESSION['logado'] = true; // o status é verdadeiro
            $_SESSION['usuario'] = $usuario;
            
            header('Location: ../home.php'); // Redireciona para a página de acesso
            exit;
        } else { // Senão, exibe um erro e redireciona de volta à página de login
            $erros[] = "Usuário ou senha inválidos.";
            $_SESSION['erros'] = $erros;
            header('Location: ../login.php');
            exit;
        }
    } else {
        header('Location: ../login.php');
        exit;
    }
?>