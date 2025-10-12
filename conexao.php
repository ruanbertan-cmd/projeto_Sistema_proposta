<?php

    $dbHost = 'db';
    $dbUsername = 'appuser';
    $dbPassword = 'app123';
    $dbName = 'site_propostas';

    $conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    //if ($conexao->connect_error) {
    //    die("Erro de conexão: " . $conexao->connect_error);
    //} else {
    //echo "Conectado com sucesso!";
    //}
    //$conexao->close();

?>
    