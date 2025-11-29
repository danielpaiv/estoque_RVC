<?php
include_once('conexao.php');

// Conexão manual (caso conexao.php não faça isso)
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "estoque_rvc";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se foi enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Pega os dados enviados do formulário
    $id         = $_POST['id']; // ID será auto-incrementado
    $id_estoque = $_POST['id'];   // ID do item no estoque
    $posto      = $_POST['posto'];
    $produto    = $_POST['produto'];
    $volume     = $_POST['volume'];

    // Verifica se o ID veio
    if (empty($id_estoque)) {
        die("Erro: ID do estoque não enviado.");
    }

    /**
     * Agora INSERE na tabela pedidos
     * Campos sugeridos: id_estoque, posto, produto, volume, data
     */

    $stmt = $conn->prepare("
        INSERT INTO pedidos (id_estoque, posto, produto, volume, data_pedido) 
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param("isss", $id_estoque, $posto, $produto, $volume);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        
        // Redireciona para a lista de pedidos ou estoque
        header("Location: /controle_combustivel/estoque_RVC/gestao.php/listar_tudo_estoque.php");
        exit;
    } else {
        die("Erro ao inserir pedido: " . $stmt->error);
    }

} else {
    die("Método inválido!");
}
