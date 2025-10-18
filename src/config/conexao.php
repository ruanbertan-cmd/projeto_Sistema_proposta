<?php
    $dbHost = 'db';
    $dbUsername = 'appuser';
    $dbPassword = 'app123';
    $dbName = 'site_propostas';

    try {
        // Monta a string de conexão (DSN)
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";

        // Cria a conexão PDO
        $conexao = new PDO($dsn, $dbUsername, $dbPassword);

        // Define o modo de erro para exceções
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Opcional: ativa o modo de fetch associativo por padrão
        $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // echo "Conectado com sucesso!";
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
?>
