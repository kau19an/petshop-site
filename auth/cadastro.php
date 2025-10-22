<?php

session_start();

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe todos os dados do formulário
    $nome_completo_temp = $_POST['nome'] ?? '';
    $data_nascimento = $_POST['datanasc'] ?? '';
    $email_temp = $_POST['email'] ?? '';
    $telefone_temp = $_POST['telefone'] ?? '';
    $usuario_temp = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Remove os espaços em branco iniciais e finais
    $nome_completo = trim($nome_completo_temp);
    $email = trim($email_temp);
    $telefone = trim($telefone_temp);
    $usuario = trim($usuario_temp);
    
    // Validação do nome completo
    if (empty($nome_completo)) {
        $erros[] = "É necessário informar o nome completo.";
    } else if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $nome_completo)) {
        $erros[] = "O nome completo deve conter apenas letras e ter entre 3 e 50 caracteres.";
    }

    // Validação do e-mail
    if (empty($email)) {
        $erros[] = "É necessário informar um e-mail.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Usa a função nativa do PHP para validar o formato do e-mail
        $erros[] = "O e-mail informado não é válido.";
    }

    // Validação do telefone com o formato (XX) XXXXX-XXXX
    if (empty($telefone)) {
        $erros[] = "É necessário informar um telefone.";
    } else if (!preg_match('/^\([0-9]{2}\)\s[0-9]{5}-[0-9]{4}$/', $telefone)) {
        $erros[] = "O telefone deve estar no formato: (XX) XXXXX-XXXX.";
    }

    // Validação da data de nascimento
    if (empty($data_nascimento)) {
        $erros[] = "É necessário informar a data de nascimento.";
    } else {
        // Valida se a data é válida e se a pessoa tem pelo menos 18 anos
        $datanasc = new DateTime($data_nascimento);
        $hoje = new DateTime(); // Pega a data atual
        $idade = $hoje->diff($datanasc)->y;

        if ($idade < 18) {
            $erros[] = "Você deve ter pelo menos 18 anos para se cadastrar.";
        }
    }

    // Validação do nome de usuário
    if (empty($usuario)) {
        $erros[] = "É necessário informar um nome de usuário.";
    } else if (!preg_match('/^.{5,10}$/', $usuario)) {
        $erros[] = "O nome de usuário deve ter entre 5 e 10 caracteres.";
    }

    // Validação da senha
    if (empty($senha)) {
        $erros[] = "É necessário informar uma senha.";
    } else {
        if (!preg_match('/[a-z]/', $senha)) {
            $erros[] = "A senha deve conter pelo menos uma letra minúscula.";
        }
        if (!preg_match('/[A-Z]/', $senha)) {
            $erros[] = "A senha deve conter pelo menos uma letra maiúscula.";
        }
        if (!preg_match('/[0-9]/', $senha)) {
            $erros[] = "A senha deve conter pelo menos um número.";
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $senha)) {
            $erros[] = "A senha deve conter pelo menos um caractere especial.";
        }
    }

    // Verifica se houve erros e redireciona
    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        // Salva os dados antigos para preencher os campos do formulário novamente
        $_SESSION['dados_antigos'] = [
            'nome' => $nome_completo_temp,
            'email' => $email_temp,
            'datanasc' => $data_nascimento,
            'telefone' => $telefone_temp,
            'usuario' => $usuario_temp,
        ];
        header('Location: ../cadastro.php');
        exit;
    } else {
        // Se não houver erros, salva os dados no arquivo
        $arquivo = 'data/clientes.txt';
        $fp = fopen($arquivo, 'a');

        // Verifica se o arquivo foi aberto com sucesso
        if ($fp === false) {
            $_SESSION['erros'][] = "Erro ao abrir o arquivo para escrita.";
            header('Location: ../cadastro.php');
            exit;
        }

        fprintf($fp, "%s;%s;%s;%s;%s;%s\n", $nome_completo, $data_nascimento, $email, $telefone, $usuario, $senha);

        // Escreve a string completa no arquivo
        fwrite($fp, $linha);

        // Fecha o arquivo para salvar os dados
        fclose($fp);

        $_SESSION['sucesso'] = "Cadastro realizado com sucesso!";
        header('Location: ../cadastro.php');
        exit;
    }
} else {
    header('Location: ../cadastro.php');
    exit;
}