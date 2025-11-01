<?php
session_start();

// --- CONFIGURAÇÃO DE ERROS PARA PRODUÇÃO ---
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log'); // Crie a pasta 'logs' com permissão de escrita
ini_set('display_errors', 0);
error_reporting(E_ALL);

include(__DIR__ . '/../src/config/conexao.php');
include(__DIR__ . '/../src/functions/verificar_lote_func.php');

if (isset($_POST['botaoEnviar'])) {
    $volume = floatval(str_replace(',', '.', preg_replace('/[^0-9.,]/', '', $_POST['volume'] ?? '')));
    $unidade_medida = mb_strtoupper(trim($_POST['unidade_medida'] ?? ''), 'UTF-8');
    $polo = mb_strtoupper(trim($_POST['polo'] ?? ''), 'UTF-8');
    $formato = mb_strtoupper(trim($_POST['formato'] ?? ''), 'UTF-8');
    $tipologia = mb_strtoupper(trim($_POST['tipologia'] ?? ''), 'UTF-8');
    $borda = mb_strtoupper(trim($_POST['borda'] ?? ''), 'UTF-8');
    $cor = mb_strtoupper(trim($_POST['cor'] ?? ''), 'UTF-8');
    $local_uso = mb_strtoupper(trim($_POST['local_uso'] ?? ''), 'UTF-8');
    $data_previsao = $_POST['data_previsao'] ?? '';
    $preco = floatval(str_replace(',', '.', preg_replace('/[^0-9.,]/', '', $_POST['preco'] ?? '')));
    $cliente = mb_strtoupper(trim($_POST['cliente'] ?? ''), 'UTF-8');
    $obra = mb_strtoupper(trim($_POST['obra'] ?? ''), 'UTF-8');
    $nome_produto = mb_strtoupper(trim($_POST['nome_produto'] ?? ''), 'UTF-8');
    $marca = mb_strtoupper(trim($_POST['marca'] ?? ''), 'UTF-8');
    $embalagem = mb_strtoupper(trim($_POST['embalagem'] ?? ''), 'UTF-8');
    $observacao = mb_strtoupper(trim($_POST['observacao'] ?? ''), 'UTF-8');

    // Verifica lote mínimo
    $loteCheck = verificarLoteMinimoCSV($polo, $tipologia, $formato, $volume, $unidade_medida);
    if (!$loteCheck['status']) {
        echo "<script>alert('{$loteCheck['mensagem']}'); window.history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO formulario(
        volume, unidade_medida, polo, formato, tipologia, borda, cor, local_uso,
        data_previsao, preco, cliente, obra, nome_produto, marca, embalagem, observacao
    ) VALUES (
        :volume, :unidade_medida, :polo, :formato, :tipologia, :borda, :cor, :local_uso,
        :data_previsao, :preco, :cliente, :obra, :nome_produto, :marca, :embalagem, :observacao
    )";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':volume' => $volume,
            ':unidade_medida' => $unidade_medida,
            ':polo' => $polo,
            ':formato' => $formato,
            ':tipologia' => $tipologia,
            ':borda' => $borda,
            ':cor' => $cor,
            ':local_uso' => $local_uso,
            ':data_previsao' => $data_previsao,
            ':preco' => $preco,
            ':cliente' => $cliente,
            ':obra' => $obra,
            ':nome_produto' => $nome_produto,
            ':marca' => $marca,
            ':embalagem' => $embalagem,
            ':observacao' => $observacao
        ]);

        echo "<script>alert('Proposta enviada com sucesso!'); window.location.href = 'proposta_cadastro.php';</script>";
    } catch (PDOException $e) {
        echo "Erro ao enviar proposta: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Propostas</title>

    <style>
        /* ===== Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #7d7d7dff, #d3d3d3ff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header.barra_navegacao {
            background-color: #9a9a9a;
            padding: 15px 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            top: 0;
            width: 100%;
            z-index: 1000;
            position: sticky;
        }

        .navbar_container ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .navbar_container a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .navbar_container a:hover {
            text-decoration: underline;
        }

        main.main_proposta_cadastro {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        form {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            padding: 40px 50px;
            width: 100%;
            max-width: 700px;
            animation: fadeIn 0.8s ease-in-out;
            font-size: 1rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            text-align: center;
            color: #3a3a3a;
            margin-bottom: 25px;
        }

        .entrada_formulario {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
        }

        label {
            text-align: left;
            font-size: 14px;
            color: #444;
            margin-bottom: 6px;
            font-size: 1rem;
        }

        input, select {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: border 0.2s;
            font-size: 0.8rem;
        }

        input:focus, select:focus {
            border-color: #a9a9a9ff;
            outline: none;
        }

        .botao_enviar {
            text-align: center;
            margin-top: 25px;
        }

        button {
            padding: 12px 30px;
            background-color: #9a9a9a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        button:hover {
            background-color: #626364;
        }

        button:active {
            transform: scale(0.98);
        }

        @media (max-width: 768px) {
            form {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <header class="barra_navegacao">
        <nav class="navbar">
            <div class="navbar_container">
                <ul>
                    <li><a href="proposta_cadastro.php">Cadastro</a></li>
                    <li><a href="proposta_fases.php">Fases</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="main_proposta_cadastro">
        <form action="proposta_cadastro.php" method="POST">
            <h1>Dados para Personalização de Produto</h1>

            <div class="entrada_formulario">
                <label for="volume">Volume</label>
                <input type="number" name="volume" step="0.01" placeholder="Ex: 2000, 3000.19 e etc" required>
            </div>

            <div class="entrada_formulario">
                <label for="unidade_medida">Unidade de medida</label>
                <select name="unidade_medida" required>
                    <option value="">Selecione...</option>
                    <option value="pc">pc</option>
                    <option value="m2">m²</option>
                </select>
            </div>

            <div class="entrada_formulario">
                <label for="polo">Polo</label>
                <select name="polo" required>
                    <option value="">Selecione...</option>
                    <option value="SC">SC</option>
                    <option value="BA">BA</option>
                    <option value="PB">PB</option>
                </select>
            </div>

            <div class="entrada_formulario">
                <label for="formato">Formato (cm)</label>
                <input type="text" name="formato" placeholder="Ex: 10x60, 20x120, 60x60, etc" required>
            </div>

            <div class="entrada_formulario">
                <label for="tipologia">Tipologia</label>
                    <select name="tipologia" required>
                        <option value="">Selecione...</option>
                        <option value="PORC GL">Porc GL</option>
                        <option value="PORC UGL">Porc UGL</option>
                        <option value="AZULEJO">Azulejo</option>
                    </select>
            </div>

            <div class="entrada_formulario">
                <label for="borda">Borda</label>
                    <select name="borda" required>
                        <option value="">Selecione...</option>
                        <option value="RETIFICADO">Retificado</option>
                        <option value="BOLD">Bold</option>
                    </select>
            </div>

            <div class="entrada_formulario">
                <label for="cor">Cor</label>
                <input type="text" name="cor" placeholder="Ex: Branco, Cinza, Bege, etc" required>
            </div>

            <div class="entrada_formulario">
                <label for="local_uso">Local de uso do produto</label>
                <input type="text" name="local_uso" placeholder="Ex: Piso, Parede, Fachada, Piscina, etc" required>
            </div>

            <div class="entrada_formulario">
                <label for="data_previsao">Previsão entrega da obra/projeto</label>
                <input type="date" name="data_previsao" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="entrada_formulario">
                <label for="preco">Referência de preço (se houver)</label>
                <input type="number" name="preco" step="0.01" placeholder="Ex: 99,99">
            </div>

            <div class="entrada_formulario">
                <label for="cliente">Cliente</label>
                <input type="text" name="cliente" placeholder="Ex: Construtora Tal, etc" required>
            </div>

            <div class="entrada_formulario">
                <label for="obra">Nome da obra</label>
                <input type="text" name="obra" placeholder="Ex: Edifício Tal, Casa Tal, etc" required>
            </div>

            <div class="entrada_formulario">
                <label for="nome_produto">Sugestão nome do produto</label>
                <input type="text" name="nome_produto" placeholder="Ex: Marmore Branco Ac 120x120, Vila Dourada Ext 90x90, etc">
            </div>

            <div class="entrada_formulario">
                <label for="marca">Marca sugerida</label>
                    <select name="marca" required>
                        <option value="">Selecione...</option>
                        <option value="ELIANE">Eliane</option>
                        <option value="DECORTILES">Decortiles</option>
                        <option value="ELIZABETH">Elizabeth</option>
                        <option value="ELIANEFLOOR">Elianefloor</option>
                    </select>
            </div>

            <div class="entrada_formulario">
                <label for="embalagem">Embalagem especial</label>
                    <select name="embalagem" required>
                        <option value="">Selecione...</option>
                        <option value="SIM">Sim</option>
                        <option value="NAO">Não</option>
                    </select>
            </div>

            <div class="entrada_formulario">
                <label for="observacao">Observações</label>
                <input type="text" name="observacao" placeholder="Observações adicionais">
            </div>

            <div class="botao_enviar">
                <button type="submit" name="botaoEnviar">Enviar Solicitação</button>
            </div>
        </form>
    </main>
</body>
</html>
