<?php
// Configurações do banco de dados
$servername = "localhost"; // Ou o IP do servidor
$username = "root"; // Usuário do MySQL
$password = ""; // Senha do MySQL
$dbname = "estoque_rvc"; // Nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

$id = intval($_GET['id']);
$conn->query("DELETE FROM vendas WHERE id=$id");

$conn->close();
header("Location: listar_vendas.php");
exit;
?>