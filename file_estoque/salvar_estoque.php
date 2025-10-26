<?php
  session_start();
     include_once('conexao.php');
        
    if (!isset($_SESSION['nome']) || !isset($_SESSION['senha']) || !isset($_SESSION['user_id'])) {
      unset($_SESSION['nome']);
      unset($_SESSION['senha']);
      unset($_SESSION['user_id']);
      header('Location: http://localhost/controle_combustivel/estoque_RVC/index.php');
      exit();  // Importante adicionar o exit() após o redirecionamento
    }
    $user_id = $_SESSION['user_id']; // Recupera o user_id da sessão

      $nome = $_SESSION['nome'];
      $user_id = $_SESSION['user_id'];

// Receber os dados do formulário

$posto = $_POST['posto'];
$produto = $_POST['produto'];
$estoque_sistema = $_POST['estoque_sistema'];
$estoque_fisico = $_POST['estoque_fisico'];
$data_venda = $_POST['data_venda'];

$estoque_sistema = str_replace(',', '.', $estoque_sistema);
$estoque_fisico = str_replace(',', '.', $estoque_fisico);

// Preparar e executar a inserção
$sql = "INSERT INTO estoque (nome, user_id, posto, produto, estoque_sistema, estoque_fisico, data_venda)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sissdds", $nome, $user_id, $posto, $produto, $estoque_sistema, $estoque_fisico, $data_venda);

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