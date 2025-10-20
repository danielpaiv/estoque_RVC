<?php
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "estoque_rvc";
      $conn = new mysqli($servername, $username, $password, $dbname);
      if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

      $id = intval($_GET['id']);

      // Atualizar dados se o formulário foi enviado
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $posto = $_POST['posto'];
          $produto = $_POST['produto'];
          $estoque_sistema = $_POST['estoque_sistema'];
          $estoque_fisico = $_POST['estoque_fisico'];
          $data_venda = $_POST['data_venda'];

          $stmt = $conn->prepare("UPDATE estoque SET posto=?, produto=?, estoque_sistema=?, estoque_fisico=?, data_venda=? WHERE id=?");
          $stmt->bind_param("ssiisi", $posto, $produto, $estoque_sistema, $estoque_fisico, $data_venda, $id);
          $stmt->execute();
          $stmt->close();

          header("Location: listar_estoque.php");
          exit;
      }

      // Buscar dados atuais
      $result = $conn->query("SELECT * FROM estoque WHERE id=$id");
      $produto = $estoque_sistema = $estoque_fisico = $data_venda = "";
      if ($row = $result->fetch_assoc()) {
          $posto = $row['posto'];
          $produto = $row['produto'];
          $estoque_sistema = $row['estoque_sistema'];
          $estoque_fisico = $row['estoque_fisico'];
          $data_venda = $row['data_venda'];
      }

      // Consultar os produtos no estoque
      $sql_produtos = "SELECT id, produto FROM produtos";
      $result_produtos = $conn->query($sql_produtos);

      // Consultar os postos na tabela postos
      $sql_postos = "SELECT id, posto FROM postos";
      $result_postos = $conn->query($sql_postos);

      $conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>EDITAR ESTOQUE - RVC</title>
<style>
  body {
    background: linear-gradient(to bottom, #0a1b7e, #0080ff);
    font-family: Arial, sans-serif; 
    margin: 40px; }
  form { 
    max-width: 400px; 
    margin: 0 auto; 
    background: #fff; 
    padding: 20px; 
    border-radius: 10px; }
  label { 
    display: block; 
    margin-top: 10px; }
  input { 
    width: 100%; 
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

  .faixa-inclinada {
      position: absolute;/* Coloca a faixa atrás do conteúdo principal */
      bottom: 0;/* Ajusta a posição para o fundo */
      left: 0;/* Ajusta a posição para o fundo */
      width: 100%;/* Preenche toda a largura da tela */
      height: 70%;/* Preenche 70% da altura da tela */
      background: linear-gradient(to bottom, #0a1b7e, #0080ff);/* Cria um gradiente azul */
      position: absolute;/* Coloca a faixa atrás do conteúdo principal */
      background-color: #0038a0;
      clip-path: polygon(0 25%, 100% 0%, 100% 100%, 0% 100%);/* Inclinada para baixo */
      transform: skewY(-10deg);/* Inclinada para baixo */
      transform-origin: bottom left;/* Ajusta a origem da transformação */
      z-index: -10;/* Coloca atrás do conteúdo principal */
    }
    h1 {
      position: relative;
      z-index: 10; /* Garante que o título fique acima da faixa */
      color: white;
      text-align: center;
      margin-bottom: 20px;
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
     /* (Opcional) colorir as opções no dropdown */
    .filtro-servicos option[value="GASOLINA COMUM"] { background-color: #ffcdd2; }
    .filtro-servicos option[value="GASOLINA DURA MAIS"] { background-color: #bbdefb; }
    .filtro-servicos option[value="ETANOL"] { background-color: #c8e6c9; }
    .filtro-servicos option[value="DIESEL S10"] { background-color: #e0e0e0; }
     /* Mudança de cor conforme o valor do input produto */
  #form-control[value="GASOLINA COMUM"] {
      background-color: #d32f2f;  /* vermelho */
      color: #fff;
      border-color: #b71c1c;
  }

  #form-control[value="GASOLINA DURA MAIS"] {
      background-color: #1565c0;  /* azul */
      color: #fff;
      border-color: #0d47a1;
  }

  #form-control[value="ETANOL"] {
      background-color: #2e7d32;  /* verde */
      color: #fff;
      border-color: #1b5e20;
  }

  #form-control[value="DIESEL S10"] {
      background-color: #424242;  /* cinza escuro */
      color: #fff;
      border-color: #212121;
  }
</style>
</head>
<body>
<div class="faixa-inclinada"></div>

<center><h1>EDITAR ESTOQUE - RVC</h1></center>

<form method="POST">

  <label>Posto:</label>
  <select name="posto" class="filtro-servicos" value="<?= htmlspecialchars($posto) ?>" required readonly autofocus>
    <option value="<?= $posto ?>"><?= $posto ?></option readonly>
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

  <label>Produto:</label>
   <select  id="form-control" class="filtro-servicos" name="produto" required >
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

  <label>Estoque do Sistema:</label>
  <input type="number" name="estoque_sistema" value="<?= $estoque_sistema ?>" required>

  <label>Estoque Físico:</label>
  <input type="number" name="estoque_fisico" value="<?= $estoque_fisico ?>" required>

  <label>Data:</label>
  <input type="date" name="data_venda" value="<?= $data_venda ?>" required>

  <button type="submit" onclick="return confirm('Produto alterado com sucesso!')">Salvar Alterações</button>
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