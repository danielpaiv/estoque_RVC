<?php
    
session_start();
include_once('conexao.php');

if (!isset($_SESSION['nome'])) {
  header("Location: login.php");
  exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

$id = intval($_POST['id']);
$senhaDigitada = $_POST['senha'];

// Verifica se a senha está correta
$sql = "SELECT senha FROM usuarios WHERE nome = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['nome']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario || $usuario['senha'] !== $senhaDigitada) {
  echo "<script>alert('Senha incorreta! A exclusão foi cancelada.'); window.location.href='listar_vendas.php';</script>";
  exit;
}

// Se senha estiver correta, executa o DELETE
$conn->query("DELETE FROM vendas WHERE id = $id");

$conn->close();
header("Location: listar_vendas.php");
exit;
?>
