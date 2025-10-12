<?php
    
include_once('conexao.php');

if (isset($_POST['botaoEnviar'])) {
    $volume = $_POST['volume'] ?? '';
    $unidade_medida = $_POST['unidade_medida'] ?? '';
    $formato = $_POST['formato'] ?? '';
    $tipologia = $_POST['tipologia'] ?? '';
    $borda = $_POST['borda'] ?? '';
    $cor = $_POST['cor'] ?? '';
    $local_uso = $_POST['local_uso'] ?? '';
    $data_previsao = $_POST['data_previsao'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $cliente = $_POST['cliente'] ?? '';
    $obra = $_POST['obra'] ?? '';
    $nome_produto = $_POST['nome_produto'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $embalagem = $_POST['embalagem'] ?? '';
    $observacao = $_POST['observacao'] ?? '';

    $sql = "INSERT INTO formulario(volume,unidade_medida,formato,tipologia,borda,cor,local_uso,data_previsao,preco,cliente,obra,nome_produto,marca,embalagem,observacao)
    VALUES ('$volume','$unidade_medida','$formato','$tipologia','$borda','$cor','$local_uso','$data_previsao','$preco','$cliente','$obra','$nome_produto','$marca','$embalagem','$observacao')";

    if (mysqli_query($conexao, $sql)) {
        echo "<script>alert('Proposta enviada com sucesso!');</script>";
    } else {
        echo "Erro ao enviar proposta: " . mysqli_error($conexao);
    }}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário Propostas</title>
    <link rel="stylesheet" href="style.css">
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
            <div>
                <h1>Dados para Personalização de Produto (Proposta PR)</h1>
            </div>

            <div class="entrada_formulario">
                <label for="volume">Volume</label>
                <input type="text" name="volume" placeholder="Ex: 567.10 ou 1000.12" required>
                <label for="unidade_medida">Unidade de medida</label>
                <select name="unidade_medida" required>
                    <option value="">Selecione...</option>
                    <option value="pc">pc</option>
                    <option value="m2">m2</option>
                </select>
            </div>
            <div class="entrada_formulario">
                <label for="formato">Formato (cm)</label>
                <input type="text" name="formato" placeholder="Ex: 10x60, 20x120, 60x60, etc">
            </div>
            <div class="entrada_formulario">
                <label for="tipologia">Tipologia</label>
                <input type="text" name="tipologia" placeholder="Ex: Azulejo, Porcelanato, etc">
            </div>
            <div class="entrada_formulario">
                <label for="borda">Bold ou retificado</label>
                <input type="text" name="borda" placeholder="Ex: Bold ou Retificado">
            </div>
            <div class="entrada_formulario">
                <label for="cor">Cor</label>
                <input type="text" name="cor" placeholder="Ex: Branco, Cinza, Bege, etc">
            </div>
            <div class="entrada_formulario">
                <label for="local_uso">Local de uso do produto</label>
                <input type="text" name="local_uso" placeholder="Ex: Piso, Parede, Fachada, Piscina, etc">
            </div>
            <div class="entrada_formulario">
                <label for="data_previsao">Previsão entrega da obra/projeto</label>
                <input type="date" name="data_previsao" placeholder="Ex: 01/10/2025" required>
            </div>
            <div class="entrada_formulario">
                <label for="preco">Referência de preço (se houver)</label>
                <input type="number" name="preco" placeholder="Ex: 99.90">
            </div>
            <div class="entrada_formulario">
                <label for="cliente">Cliente</label>
                <input type="text" name="cliente" placeholder="Ex: Ruan, Maria, João, etc">
            </div>
            <div class="entrada_formulario">
                <label for="obra">Nome obra</label>
                <input type="text" name="obra" placeholder="Ex: Edifício Tal, Casa Tal, etc">
            </div>
            <div class="entrada_formulario">
                <label for="nome_produto">Sugestão nome do produto (se houver)</label>
                <input type="text" name="nome_produto" placeholder="Ex: Marmore Carrara, Cimento Queimado, etc">
            </div>
            <div class="entrada_formulario">
                <label for="marca">Marca sugerida</label>
                <input type="text" name="marca" placeholder="Ex: Eliane, Decortiles, Elizabeth, Eliane Floor, etc">
            </div>
            <div class="entrada_formulario">
                <label for="embalagem">Embalagem especial</label>
                <input type="text" name="embalagem" placeholder="Ex: Sim ou Não">
            </div>
            <div class="entrada_formulario">
                <label for="observacao">Observações</label>
                <input type="text" name="observacao" placeholder="Ex: Observações adicionais">
            </div>
            <div class="botao_enviar">
                <button type="submit" name="botaoEnviar">Enviar Solicitação!</button>
            </div>

        </form>
        </main>


    

<!--
Volume (pç ou m²):   
Formato (cm):
Tipologia (Azulejo, Porcelanato e etc):
Acabamento:
Bold ou retificado:
Cor:
Local de uso do produto (piso, parede, fachada, piscina, etc.):                               
Previsão entrega da obra/projeto:
Referência de preço (se houver):
Cliente:
Nome obra (se houver):
Sugestão nome do produto (se houver):
Marca sugerida* (Eliane/Decortiles/Elizabeth):
Embalagem especial (sim ou não):
Observações:
-->




</body>
</html>