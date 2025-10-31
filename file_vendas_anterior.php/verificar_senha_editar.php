<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['nome'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !isset($_POST['senha'])) {
  header("Location: listar_vendas.php");
  exit;
}

$id = intval($_POST['id']);
$senhaDigitada = trim($_POST['senha']);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Busca a senha do usuário logado
$sql = "SELECT senha FROM usuarios WHERE nome = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['nome']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
  echo "<script>alert('Usuário não encontrado.'); window.location.href='listar_vendas.php';</script>";
  exit;
}

$senhaArmazenada = (string)$usuario['senha'];

// Verifica senha (hash ou texto simples)
$senhaOK = false;
if (password_verify($senhaDigitada, $senhaArmazenada) || hash_equals($senhaArmazenada, $senhaDigitada)) {
  $senhaOK = true;
}

if (!$senhaOK) {
  echo "<script>alert('Senha incorreta! Edição cancelada.'); window.location.href='listar_vendas.php';</script>";
  exit;
}

// Se a senha estiver correta, redireciona para editar_vendas.php
header("Location: editar_vendas.php?id=$id");
exit;
?>
