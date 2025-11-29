<?php
include_once('conexao.php');

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id <= 0) {
            die("ID inválido!");
        }

      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "estoque_rvc";
      $conn = new mysqli($servername, $username, $password, $dbname);
      if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }


       // Atualizar dados se o formulário foi enviado
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $id= $_POST['id'];
          $posto = $_POST['posto'];
          $produto = $_POST['produto'];
          $volume = $_POST['volume'];

           $stmt = $conn->prepare("UPDATE estoque SET posto=?, produto=?, volume=? WHERE id=?");
          $stmt->bind_param("sssi", $posto, $produto, $volume, $id);
          $stmt->execute();
          $stmt->close();

          header("Location: listar_estoque.php");
          exit;
      }

      // Buscar dados atuais
      $result = $conn->query("SELECT * FROM estoque WHERE id = $id");

      if ($row = $result->fetch_assoc()) {
            $posto   = $row['posto'];
            $produto = $row['produto'];
            
        } else {
            die("Item não encontrado.");
        }
          
      
      
      // Consultar os postos na tabela postos
      $sql_postos = "SELECT id, posto FROM postos";
      $result_postos = $conn->query($sql_postos);

      // Consultar os produtos no estoque
      $sql_produtos = "SELECT id, produto FROM produtos";
      $result_produtos = $conn->query($sql_produtos);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEDIDOS</title>
    <style>
        body {
    background: linear-gradient(to bottom, #0a1b7e, #0080ff);
    font-family: Arial, sans-serif; 
    margin: 40px; }
  form { 
    background: #fff;
    max-width: 400px; 
    margin: 0 auto; 
    margin-top: -0%;
    padding: 20px; 
    border-radius: 10px; }
  label { 
    display: block; 
    margin-top: 10px; }
  input { 
    width: 50%; 
    padding: 8px; 
    margin-top: 5px; 
    border-radius: 5px; 
    border: 1px solid #ccc; }
  button { 
    margin-top: 15px; 
    padding: 10px 15px; 
    border: none; 
    background: #007BFF; 
    color: #fff; 
    border-radius: 5px; 
    cursor: pointer; }
  button:hover { 
    background: #0056b3; }
     /* Mudança de cor conforme o valor do input produto */
  .form-control[value="GASOLINA COMUM"] {
      background-color: #d32f2f;  /* vermelho */
      color: #fff;
      border-color: #b71c1c;
  }

  .form-control[value="GASOLINA DURA MAIS"] {
      background-color: #1565c0;  /* azul */
      color: #fff;
      border-color: #0d47a1;
  }

  .form-control[value="ETANOL"] {
      background-color: #2e7d32;  /* verde */
      color: #fff;
      border-color: #1b5e20;
  }

  .form-control[value="DIESEL S10"] {
      background-color: #424242;  /* cinza escuro */
      color: #fff;
      border-color: #212121;
  }
  /* Mudança de cor conforme a opção selecionada */
    .filtro-servicos:has(option:checked[value="GASOLINA COMUM"]) {
      background-color: #d32f2f;  /* vermelho */
      color: #fff;
      border-color: #b71c1c;
    }

    .filtro-servicos:has(option:checked[value="GASOLINA DURA MAIS"]) {
      background-color: #1565c0;  /* azul */
      color: #fff;
      border-color: #0d47a1;
    }

    .filtro-servicos:has(option:checked[value="ETANOL"]) {
      background-color: #2e7d32;  /* verde */
      color: #fff;
      border-color: #1b5e20;
    }

    .filtro-servicos:has(option:checked[value="DIESEL S10"]) {
      background-color: #424242;  /* preto claro (cinza escuro) */
      color: #ffffffff;
      border-color: #212121;
    }
    .filtro-servicos:has(option:checked[value="ARLA 32"]) {
      background-color: #cfc7c7ff;  /* preto claro (cinza escuro) */
      color: #070707ff;
      border-color: #3a2f2fff;
    }
    h1 {
      position: relative;
      z-index: 10; /* Garante que o título fique acima da faixa */
      color: white;
      text-align: center;
      margin-bottom: 20px;
      margin-top: -20px;
    }
    select {
      width: 80%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background: #0b2e14ff;
      color: #fff;
      cursor: pointer;
    }
    select:hover {
      background: #218838;
    }
    /* (Opcional) colorir as opções no dropdown */
    .filtro-servicos option[value="GASOLINA COMUM"] { background-color: #ffcdd2; }
    .filtro-servicos option[value="GASOLINA DURA MAIS"] { background-color: #bbdefb; }
    .filtro-servicos option[value="ETANOL"] { background-color: #c8e6c9; }
    .filtro-servicos option[value="DIESEL S10"] { background-color: #e0e0e0; }
    </style>
</head>
<body>
    <H1>PEDIDOS</H1>
    <button onclick="window.location.href='/controle_combustivel/estoque_RVC/gestao.php/listar_tudo_estoque.php'">Voltar</button>
    <button onclick="window.location.href='listar_pedidos.php'">Listar pedidos</button>

    <header>

    </header>
    <form action="presesa_pedidos.php" method="post">

        <label for="posto">Posto</label>
        <select name="posto" id="posto" class="posto" value="<?= htmlspecialchars($posto) ?>" required>
            <option value="<?= $posto ?>"><?= $posto ?></option>
            <?php
             if ($result_postos && $result_postos->num_rows > 0) {
                    while($row = $result_postos->fetch_assoc()) {
                        echo "<option value='" . $row['posto'] . "'>" . $row['posto'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum posto encontrado</option>";
                }
            ?>
            
        </select>
        <br>
    <!-- <label>Produto:</label>
        <select  id="form-control" class="filtro-servicos" name="produto" value="<?= htmlspecialchars($produto) ?>"required >
          <option value=""><?=$produto?></option>
          <?php
          if ($result_produtos && $result_produtos->num_rows > 0) {
              while($row = $result_produtos->fetch_assoc()) {
                  echo "<option value='" . $row['produto'] . "'>" . $row['produto'] . "</option>";
              }
          } else {
              echo "<option value=''>Nenhum produto encontrado</option>";
          }
          ?>
      </select>
    -->
       
       <label>Produto:</label>
        <input type="text" name="produto" class="form-control" value="<?= htmlspecialchars($produto) ?>" required readonly>
        <br><br>

        <label for="volume">Volume</label>
        <input type="text" name="volume" id="volume"  required>

        <input type="hidden" name="id" value="<?= $id ?>">

        <button type="submit" onclick="return confirm('Produto atualizado!')">Atualizar Pedido</button>
        
    </form>

     <script>
      // Captura todos os elementos de input, select e textarea
      const inputs = document.querySelectorAll("input, select, textarea");

      inputs.forEach((el, index) => {
        el.addEventListener("keydown", function (e) {
          if (e.key === "Enter") {
            e.preventDefault(); // Impede o envio do form
            const next = inputs[index + 1];
            if (next) {
              next.focus(); // Foca no próximo campo
            } else {
              document.querySelector("button[type=submit]").click(); // Se for o último, envia
            }
            } else if (e.key === "F2") {
              alert("Formulário enviado!");
          }
        });
      });
    </script>
</body>
</html>
