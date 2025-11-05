<?php
    session_start();

    // Verifica se o usuário está logado
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: ../login.php');
        exit;
    }

    $erros = [];
    $usuario_logado = $_SESSION['usuario'];
    $arquivo = 'data/pets.txt';
    $caminho_arquivo = __DIR__ . '/' . $arquivo;

    // Lógica para adicionar pet
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'] ?? 'adicionar'; 
        
        if ($acao === 'adicionar') {
            $nome_pet_temp  = $_POST['nome'] ?? '';
            $idade_temp     = $_POST['idade'] ?? '';
            $especie_temp   = $_POST['especie'] ?? '';
            $raca_temp      = $_POST['raca'] ?? '';

            // Remove os espaços em branco
            $nome_pet = trim($nome_pet_temp);
            $idade    = trim($idade_temp);
            $especie  = trim($especie_temp);
            $raca     = trim($raca_temp);

            // Validação dos dados
            if (empty($nome_pet)) { $erros[] = "O nome do pet é obrigatório."; }
            if (empty($idade) || !is_numeric($idade) || (int)$idade < 0) { $erros[] = "A idade deve ser um número inteiro positivo."; }
            if (empty($especie)) { $erros[] = "A espécie do pet é obrigatória."; }

            if (!empty($erros)) {
                $_SESSION['erros'] = $erros;
                $_SESSION['dados_antigos_pet'] = [
                    'nome' => $nome_pet_temp, 'idade' => $idade_temp, 
                    'especie' => $especie_temp, 'raca' => $raca_temp,
                ];
                header('Location: ../adicionar_pet.php');
                exit;
            }

            // Abre o arquivo para anexar
            $fp = fopen($caminho_arquivo, 'a');
            if ($fp === false) {
                $_SESSION['erros'][] = "Erro ao abrir o arquivo para escrita.";
                header('Location: ../adicionar_pet.php');
                exit;
            }

            // Formato: usuarioLogado;nome_pet;idade;especie;raca\n
            $linha = sprintf("%s;%s;%s;%s;%s;%s\r\n", 
                            $usuario_logado, $nome_pet, $idade, $especie, $raca, "Nenhum");
                            
            fwrite($fp, $linha);
            fclose($fp);

            $_SESSION['sucesso'] = "\"{$nome_pet}\" foi adicionado com sucesso!";
            header('Location: ../adicionar_pet.php'); 
            exit;
        } else if ($acao === 'atualizar') {
            $nome_original = $_POST['nome_original'] ?? '';
            $servico_original = $_POST['servico_original'] ?? 'Nenhum'; // Mantém o serviço
            
            $nome_pet_temp  = $_POST['nome'] ?? '';
            $idade_temp     = $_POST['idade'] ?? '';
            $especie_temp   = $_POST['especie'] ?? '';
            $raca_temp      = $_POST['raca'] ?? '';

            // Remove os espaços em branco e valida
            $nome_pet = trim($nome_pet_temp);
            $idade    = trim($idade_temp);
            $especie  = trim($especie_temp);
            $raca     = trim($raca_temp);
            
            if (empty($nome_original)) { $erros[] = "O nome original do pet não foi fornecido."; }
            if (empty($nome_pet)) { $erros[] = "O novo nome do pet é obrigatório."; }
            if (empty($idade) || !is_numeric($idade) || (int)$idade < 0) { $erros[] = "A idade deve ser um número inteiro positivo."; }
            if (empty($especie)) { $erros[] = "A espécie do pet é obrigatória."; }

            if (!empty($erros)) {
                $_SESSION['erros'] = $erros;
                header('Location: ../home.php'); // Redireciona para 'home' em caso de erro
                exit;
            }

            // Reescrita do arquivo
            $lines = file($caminho_arquivo, FILE_IGNORE_NEW_LINES);
            $new_lines = [];
            $pet_modificado = false;
            
            $nova_linha = sprintf("%s;%s;%s;%s;%s;%s", 
                                  $usuario_logado, $nome_pet, $idade, $especie, $raca, $servico_original);
            
            foreach ($lines as $line) {
                $data = explode(';', $line);
                
                // Verifica se a linha tem o mínimo de campos (5) e pertence ao usuário logado
                if (count($data) >= 5 && trim($data[0]) === $usuario_logado) {
                    
                    // Se for a linha que queremos modificar
                    if (trim($data[1]) === $nome_original) {
                        
                        // Garante que o pet não está sendo usado como ID para outro pet
                        if ($pet_modificado) {
                            $erros[] = "Há mais de um pet com o mesmo nome original. Apenas o primeiro foi atualizado.";
                            $_SESSION['erros'] = $erros;
                            break; // Sai do loop para reescrever
                        }
                        
                        $new_lines[] = $nova_linha; // Substitui pela nova linha
                        $pet_modificado = true; 
                        continue; // Pula a linha original
                    }
                }
                
                // Mantém a linha inalterada
                $new_lines[] = $line;
            }

            // Reescreve o arquivo
            if ($pet_modificado) {
                $content = implode(PHP_EOL, $new_lines);
                if (!empty($new_lines)) {
                    $content .= PHP_EOL;
                }
                file_put_contents($caminho_arquivo, $content);
                $_SESSION['sucesso'] = "\"{$nome_original}\" foi atualizado com sucesso.";
            } else {
                $_SESSION['erros'][] = "\"{$nome_original}\" não foi encontrado para atualização.";
            }

            header('Location: ../home.php'); 
            exit;
        }
    }
    // Lógica para agendar ou remover
    else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && isset($_GET['id'])) {
        
        $acao = $_GET['acao'];
        $pet_id = intval($_GET['id']);
        
        if (!in_array($acao, ['agendar', 'remover']) || $pet_id <= 0) {
            $_SESSION['erros'][] = "Ação ou ID de pet inválido.";
            header('Location: ../home.php');
            exit;
        }

        if (!file_exists($caminho_arquivo)) {
            $_SESSION['erros'][] = "Arquivo de pets não encontrado.";
            header('Location: ../home.php');
            exit;
        }

        $lines = file($caminho_arquivo, FILE_IGNORE_NEW_LINES);
        $new_lines = [];
        $line_counter = 0;
        $pet_modificado = false;
        $nome_pet_acao = ""; // Para a mensagem de sucesso
        
        foreach ($lines as $line) {
            $data = explode(';', $line);
            
            // Verifica se a linha tem o mínimo de campos (5) e pertence ao usuário logado
            if (count($data) >= 5 && trim($data[0]) === $usuario_logado) {
                $line_counter++;
                
                // Se for a linha que queremos modificar/remover:
                if ($line_counter === $pet_id) {
                    
                    $nome_pet_acao = trim($data[1]); // Pega o nome para a mensagem
                    
                    if ($acao === 'remover') {
                        $pet_modificado = true; 
                        $_SESSION['sucesso'] = "\"{$nome_pet_acao}\" foi removido com sucesso!";
                        continue; // Pula a linha, removendo-a
                    } 
                    
                    else if ($acao === 'agendar' && isset($_GET['servico'])) {
                        $servico = urldecode($_GET['servico']);
                        
                        // A base é sempre os primeiros 5 campos (usuario;nome;idade;especie;raca)
                        $new_line_base = implode(';', array_slice($data, 0, 5));
                        
                        // Adiciona o novo serviço no final (6° campo)
                        $new_line = $new_line_base . ';' . $servico;
                        $new_lines[] = $new_line;
                        
                        $pet_modificado = true;
                        $_SESSION['sucesso'] = "\"{$servico}\" foi agendado com sucesso para \"{$nome_pet_acao}\".";
                        continue; // Pula o resto do loop e continua com a próxima linha
                    }
                }
            }
            
            // Mantém a linha inalterada se não for a do pet
            $new_lines[] = $line;
        }

        // Reescreve o arquivo se houve alguma modificação
        if ($pet_modificado) {
            // 'file_put_contents' sobrescreve o arquivo e implode com quebra de linha
            $content = implode(PHP_EOL, $new_lines);
            if (!empty($new_lines)) {
                $content .= PHP_EOL;
            }
            file_put_contents($caminho_arquivo, $content);
        } else {
            // Se a ação era agendar ou remover, mas 'pet_modificado' é falso, o ID estava fora do range
            if (!isset($_SESSION['sucesso'])) {
                $_SESSION['erros'][] = "Pet com ID {$pet_id} não encontrado ou inválido.";
            }
        }
        
        header('Location: ../home.php'); 
        exit;

    } else {
        // Volta para 'home.php' se a requisição não for válida
        header('Location: ../home.php');
        exit;
    }
?>