<?php
    session_start();

    // Destrói todas as variáveis da seção de "estado"
    unset($_SESSION['logado']);
    unset($_SESSION['usuario']);
    unset($_SESSION['nome']);
    unset($_SESSION['sucesso']);
    unset($_SESSION['erros']);
    unset($_SESSION['dados_antigos_pet']);

    header('Location: ../login.php');
    exit;
?>