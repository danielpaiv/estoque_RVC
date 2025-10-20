<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once('conexao.php');

    $nome = trim($_POST['nome']);
    $senha = $_POST['senha'];
    $senha_confirm = $_POST['senha_confirm'];

    if ($senha !== $senha_confirm) {
        echo "❌ As senhas não conferem!";
        exit;
    }

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere no banco
    $sql = "INSERT INTO usuarios (nome, senha) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nome, $senhaHash);

    if ($stmt->execute()) {
        echo "✅ Usuário cadastrado com sucesso!";
        echo "<br><a href='index.php'>Ir para Login</a>";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
</head>
<body>
    <h2>Cadastrar Novo Usuário</h2>
    <form method="POST" action="">
        <label for="nome">Usuário:</label><br>
        <input type="text" name="nome" id="nome" required><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required><br><br>

        <label for="senha_confirm">Confirmar Senha:</label><br>
        <input type="password" name="senha_confirm" id="senha_confirm" required><br><br>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
