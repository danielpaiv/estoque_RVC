<?php
// Configurações do banco de dados
$servername = "localhost"; // Ou o IP do servidor
$username = "root"; // Usuário do MySQL
$password = ""; // Senha do MySQL
$dbname = "estoque_anp"; // Nome do banco de dados

// Conectar ao MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Receber os dados do formulário
$posto = $_POST['posto'];
$produto = $_POST['produto'];
$estoque_sistema = $_POST['estoque_sistema'];
$estoque_fisico = $_POST['estoque_fisico'];
$data_venda = $_POST['data_venda'];

// Preparar e executar a inserção
$sql = "INSERT INTO estoque (posto, produto, estoque_sistema, estoque_fisico, data_venda)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiis", $posto, $produto, $estoque_sistema, $estoque_fisico, $data_venda);

if ($stmt->execute()) {
    echo "Produto cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

// Fechar conexão
$stmt->close();
$conn->close();

header("Location: formulario_estoque.php");
exit();
?>