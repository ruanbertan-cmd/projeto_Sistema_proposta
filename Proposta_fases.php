<?php
include_once('conexao.php');

// Consulta os registros da tabela detalhados abaixo:
$sql = "SELECT id,volume,unidade_medida,formato,tipologia,borda,cor,local_uso,data_previsao,preco,nome_produto,marca,embalagem FROM formulario ORDER BY id DESC";
$result = mysqli_query($conexao, $sql);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Propostas</title>
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
    <h1>Fases Propostas</h1>
 
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
            <th>Nome Produto</th>
            <th>Marca</th>
            <th>Embalagem</th>
            <th>Dados Completo</th>
            <th>Status</th>

        </tr>
        <?php
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";

                foreach($row as $key => $col){
                    echo "<td>" . htmlspecialchars($col) . "</td>";
                }

                $id = isset($row['id']) ? htmlspecialchars($row['id']) : null;

                if (!in_array('id', $row)) {
                    echo "<td><a href='proposta_detalhes.php?id=" . $row['id'] . "'>Ver Detalhes</a></td>";
                }
                
                $id = isset($row['id']) ? htmlspecialchars($row['id']) : null;

                if ($id != null) {
                    echo "<td><a href='aprovar_proposta.php?id=$id' 
                        style='margin-right:5px; padding:5px 10px; background-color:#28a745; color:white; text-decoration:none; border-radius:4px;'
                        onclick=\"return confirm('Tem certeza que deseja aprovar esta proposta?');\">
                        Aprovar
                    </a>";
                    echo "<a href='rejeitar_proposta.php?id=$id' 
                        style='padding:5px 10px; background-color:#dc3545; color:white; text-decoration:none; border-radius:4px;'
                        onclick=\"return confirm('Tem certeza que deseja rejeitar esta proposta?');\">
                        Rejeitar
                    </a></td>";
                }

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='16'>Nenhuma proposta encontrada.</td></tr>";
        }
        ?>

    </table>
    </main>
</body>
</html>
