<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['nome'])) {
  header("Location: login.php");
  exit;
}

$id = intval($_GET['id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Confirmar edição</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 350px;
    }

    h3 {
      color: #333;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: bold;
      text-align: left;
      color: #555;
    }

    input[type="password"] {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      transition: 0.3s;
    }

    input[type="password"]:focus {
      border-color: #007bff;
      outline: none;
    }

    button {
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 10px;
      font-size: 15px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #0056b3;
    }

    a {
      display: inline-block;
      margin-top: 10px;
      color: #555;
      text-decoration: none;
      font-size: 14px;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h3>Confirme sua senha<br>para editar o item #<?php echo htmlspecialchars($id); ?>:</h3>
    
    <form method="post" action="verificar_senha_editar.php">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      
      <label for="senha">Senha:</label>
      <input type="password" id="senha" name="senha" required placeholder="Digite sua senha" autofocus>
      
      <button type="submit">Confirmar</button>
    </form>
    <a href="listar_vendas.php">Cancelar</a>
  </div>
</body>
</html>
