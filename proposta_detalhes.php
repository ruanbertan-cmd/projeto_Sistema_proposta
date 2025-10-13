<?php
include_once('conexao.php');

// Consulta os registros da tabela detalhados abaixo:
$sql = "SELECT volume,unidade_medida,formato,tipologia,borda,cor,local_uso,data_previsao,preco,cliente,obra,nome_produto,marca,embalagem,observacao FROM formulario ORDER BY id";
$result = mysqli_query($conexao, $sql);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes de Propostas</title>
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

    <main class="main_proposta_fases">
    <h1>Consulta Completa dos Dados da Propostas</h1>
 
    <table class="tabela_propostas">
        
        <tr>
            <th>Volume</th>
            <th>Unidade</th>
            <th>Formato</th>
            <th>Tipologia</th>
            <th>Borda</th>
            <th>Cor</th>
            <th>Local de Uso</th>
            <th>Data Previsão</th>
            <th>Preço</th>
            <th>Cliente</th>
            <th>Obra</th>
            <th>Nome Produto</th>
            <th>Marca</th>
            <th>Embalagem</th>
            <th>Observação</th>
        </tr>
        <?php
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";

                foreach($row as $col){
                    echo "<td>" . htmlspecialchars($col) . "</td>";
                }}
        } else {
            echo "<tr><td colspan='16'>Nenhuma proposta encontrada.</td></tr>";
        }
        ?>

    </table>
    </main>
</body>
</html>
