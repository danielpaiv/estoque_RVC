<?php
// Configurações do banco de dados
$servername = "localhost"; // Ou o IP do servidor
$username = "root"; // Usuário do MySQL
$password = ""; // Senha do MySQL
$dbname = "estoque_rvc"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
echo "Conexão bem-sucedida!";
?>