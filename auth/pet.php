<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: ../login.php');
    exit;
}

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_logado = $_SESSION['usuario'];
    $nome_pet_temp  = $_POST['nome'] ?? '';
    $idade_temp     = $_POST['idade'] ?? '';
    $especie_temp   = $_POST['especie'] ?? '';
    $raca_temp      = $_POST['raca'] ?? '';

    // Remove espaços em branco
    $nome_pet = trim($nome_pet_temp);
    $idade    = trim($idade_temp);
    $especie  = trim($especie_temp);
    $raca     = trim($raca_temp);

    // Validação dos dados
    if (empty($nome_pet)) {
        $erros[] = "O nome do pet é obrigatório.";
    }
    if (empty($idade) || !is_numeric($idade) || (int)$idade < 0) {
        $erros[] = "A idade deve ser um número inteiro positivo.";
    }
    if (empty($especie)) {
        $erros[] = "A espécie do pet é obrigatória.";
    }

    // Verifica e armazena os dados antigos em caso de erro
    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        $_SESSION['dados_antigos_pet'] = [
            'nome'    => $nome_pet_temp,
            'idade'   => $idade_temp,
            'especie' => $especie_temp,
            'raca'    => $raca_temp,
        ];
        header('Location: ../adicionar_pet.php');
        exit;
    }

    // Salva no arquivo em 'pets.txt'
    $arquivo = 'data/pets.txt';
    $caminho_arquivo = __DIR__ . '/' . $arquivo;

    // Abre o arquivo para anexar
    $fp = fopen($caminho_arquivo, 'a');

    if ($fp === false) {
        $_SESSION['erros'][] = "Erro ao abrir o arquivo para escrita.";
        header('Location: ../adicionar_pet.php');
        exit;
    }

    // Formato do arquivo em C: usuarioLogado;nome_pet;idade;especie;raca\n
    $linha = sprintf("%s;%s;%s;%s;%s\n", 
                     $usuario_logado, 
                     $nome_pet, 
                     $idade, 
                     $especie, 
                     $raca);
                     
    fwrite($fp, $linha);
    fclose($fp);

    $_SESSION['sucesso'] = "\"{$nome_pet}\" foi adicionado com sucesso!";
    header('Location: ../home.php'); // Redireciona para 'home' após o sucesso
    exit;
} else {
    header('Location: ../adicionar_pet.php');
    exit;
}
?>