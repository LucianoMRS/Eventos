<?php
// Dados de acesso ao banco (padrão do XAMPP)
$host    = "localhost";
$banco   = "eventos_academicos";
$usuario = "root";
$senha   = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);

    // Se der erro no SQL, o PHP lança uma exceção
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Os resultados vêm como array com o nome das colunas: $linha["nome"]
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar no banco de dados: " . $e->getMessage());
}
