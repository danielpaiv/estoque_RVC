<?php
      session_start();
        include_once('conexao.php');
        
         if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
                unset($_SESSION['nome']);
                unset($_SESSION['senha']);
                header('Location: http://localhost/controle_combustivel/estoque_ANP/index.php');
                exit();  // Importante adicionar o exit() após o redirecionamento
            }

            $user_id = $_SESSION['user_id']; // Recupera o user_id da sessão
            $nome = $_SESSION['nome'];
            $user_id = $_SESSION['user_id'];
            
            // Criar conexão
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Verificar conexão
            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

      $id = intval($_GET['id']);

      // Atualizar dados se o formulário foi enviado
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $posto = $_POST['posto'];
          $produto = $_POST['produto'];
          $estoque_sistema = $_POST['estoque_sistema'];
          $estoque_fisico = $_POST['estoque_fisico'];
          $data_venda = $_POST['data_venda'];

          $stmt = $conn->prepare("UPDATE estoque SET posto=?, produto=?, estoque_sistema=?, estoque_fisico=?, data_venda=? WHERE id=?");
          $stmt->bind_param("ssddsi", $posto, $produto, $estoque_sistema, $estoque_fisico, $data_venda, $id);
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

      // Consultar apenas os postos que o usuário pode acessar
    $sql_postos = "
        SELECT p.id, p.posto
        FROM postos p
        INNER JOIN usuarios_postos up ON up.posto_id = p.id
        WHERE up.usuario_id = ?
        ORDER BY p.posto
    ";
    $stmt_postos = $conn->prepare($sql_postos);
    $stmt_postos->bind_param("i", $user_id);
    $stmt_postos->execute();
    $result_postos = $stmt_postos->get_result();

    // Quando o usuário seleciona um posto
    if (isset($_POST['posto']) && !empty($_POST['posto'])) {
        $_SESSION['posto_id'] = $_POST['posto'];

        // Buscar o nome do posto
        $sql_nome = "SELECT posto FROM postos WHERE id = ?";
        $stmt_nome = $conn->prepare($sql_nome);
        $stmt_nome->bind_param("i", $_SESSION['posto_id']);
        $stmt_nome->execute();
        $result_nome = $stmt_nome->get_result();
        if ($result_nome->num_rows > 0) {
            $posto_nome = $result_nome->fetch_assoc()['posto'];
            $_SESSION['posto_nome'] = $posto_nome;
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>EDITAR ESTOQUE</title>
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
      background: linear-gradient(to bottom, #b10e0eff, #0080ff);/* Cria um gradiente azul */
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
<button onclick="window.location.href='listar_estoque.php'">Voltar</button>

<center><h1>EDITAR ESTOQUE</h1></center>


<form method="POST">

  <label for="posto">Posto:</label>
    <select id="posto" class="posto" name="posto" required autofocus>
        <option value="<?= $posto ?>"><?= $posto ?></option readonly>
        <?php
        if ($result_postos && $result_postos->num_rows > 0) {
            while($row = $result_postos->fetch_assoc()) {
                $selected = (isset($_SESSION['posto_id']) && $_SESSION['posto_id'] == $row['id']) ? 'selected' : '';
                echo "<option value='" . $row['posto'] . "' $selected>" . $row['posto'] . "</option>";
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
  <input type="number" name="estoque_sistema" step="0.001" value="<?= $estoque_sistema ?>" required>

  <label>Estoque Físico:</label>
  <input type="number" name="estoque_fisico" step="0.001" value="<?= $estoque_fisico ?>" required>

  <label>Data:</label>
  <input type="date" name="data_venda" value="<?= $data_venda ?>" required>

  <button type="submit" onclick="return confirm('Produto alterado com sucesso!')">Salvar Alterações</button>
</form>

      <p style="color:white">Usuário: <?php echo $nome; ?></p>
      <p style="color:white">ID: <?php echo $user_id; ?></p>
      
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