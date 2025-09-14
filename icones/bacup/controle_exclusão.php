<?php
session_start(); // precisa iniciar sessão
include 'config.php'; // arquivo com dados de conexão

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estoque_anp";

$conn = new mysqli($servername, $username, $password, $dbname);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// --- 1. verifica se usuário está logado ---
if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado. Faça login primeiro.");
}

// --- 2. recebe ID do registro e senha digitada ---
$id = intval($_GET['id']);
$senhaDigitada = $_POST['senha'] ?? '';

// --- 3. busca senha do usuário logado ---
$stmt = $conn->prepare("SELECT senha_hash FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$stmt->bind_result($senha_hash);
$stmt->fetch();
$stmt->close();

// --- 4. valida a senha ---
if (!password_verify($senhaDigitada, $senha_hash)) {
    die("Senha incorreta. Exclusão cancelada.");
}

// --- 5. executa a exclusão ---
$stmt = $conn->prepare("DELETE FROM entradas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$conn->close();

// --- 6. redireciona ---
header("Location: listar_entradas.php");
exit;
?>
