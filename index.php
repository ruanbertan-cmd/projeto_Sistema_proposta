<?php
    
include_once('conexao.php');

if (isset($_POST['botaoEnviar'])) {
    $volume = $_POST['volume'] ?? '';
    $formato = $_POST['formato'] ?? '';
    $tipologia = $_POST['tipologia'] ?? '';
    $borda = $_POST['borda'] ?? '';
    $cor = $_POST['cor'] ?? '';
    $localUso = $_POST['localUso'] ?? '';
    $dataPrevisao = $_POST['dataPrevisao'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $cliente = $_POST['cliente'] ?? '';
    $obra = $_POST['obra'] ?? '';
    $nomeProduto = $_POST['nomeProduto'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $embalagem = $_POST['embalagem'] ?? '';
    $observacao = $_POST['observacao'] ?? '';

    $sql = "INSERT INTO propostas(volume,formato,tipologia,borda,cor,localUso,dataPrevisao,preco,cliente,obra,nomeProduto,marca,embalagem,observacao)
    VALUES ('$volume','$formato','$tipologia','$borda','$cor','$localUso','$dataPrevisao','$preco','$cliente','$obra','$nomeProduto','$marca','$embalagem','$observacao')";

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

        <main>
        <form action="index.php" method="POST">
            <div>
                <h1>Dados para Personalização de Produto (Proposta PR)</h1>
            </div>

            <div class="entrada_formulario">
                <label for="volume">Volume (pç ou m²):</label>
                <input type="text" name="volume">
            </div>
            <div class="entrada_formulario">
                <label for="formato">Formato (cm):</label>
                <input type="text" name="formato">
            </div>
            <div class="entrada_formulario">
                <label for="tipologia">Tipologia (Azulejo, Porcelanato e etc):</label>
                <input type="text" name="tipologia">
            </div>
            <div class="entrada_formulario">
                <label for="borda">Bold ou retificado:</label>
                <input type="text" name="borda">
            </div>
            <div class="entrada_formulario">
                <label for="cor">Cor:</label>
                <input type="text" name="cor">
            </div>
            <div class="entrada_formulario">
                <label for="localUso">Local de uso do produto (piso, parede, fachada, piscina, etc.):</label>
                <input type="text" name="localUso">
            </div>
            <div class="entrada_formulario">
                <label for="dataPrevisao">Previsão entrega da obra/projeto:</label>
                <input type="text" name="dataPrevisao">
            </div>
            <div class="entrada_formulario">
                <label for="preco">Referência de preço (se houver):</label>
                <input type="text" name="preco">
            </div>
            <div class="entrada_formulario">
                <label for="cliente">Cliente:</label>
                <input type="text" name="cliente">
            </div>
            <div class="entrada_formulario">
                <label for="obra">Nome obra (se houver):</label>
                <input type="text" name="obra">
            </div>
            <div class="entrada_formulario">
                <label for="nomeProduto">Sugestão nome do produto (se houver):</label>
                <input type="text" name="nomeProduto">
            </div>
            <div class="entrada_formulario">
                <label for="marca">Marca sugerida* (Eliane/Decortiles/Elizabeth):</label>
                <input type="text" name="marca">
            </div>
            <div class="entrada_formulario">
                <label for="embalagem">Embalagem especial (sim ou não):</label>
                <input type="text" name="embalagem">
            </div>
            <div class="entrada_formulario">
                <label for="observacao">Observações:</label>
                <input type="text" name="observacao">
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