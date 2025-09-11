<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estoque_anp";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

$id = intval($_GET['id']);
$conn->query("DELETE FROM estoque WHERE id=$id");

$conn->close();
header("Location: listar_estoque.php");
exit;
?>