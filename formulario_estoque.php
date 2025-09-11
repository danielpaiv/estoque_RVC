<?php
  // Configurações do banco de dados
  $servername = "localhost"; // Ou o IP do servidor
  $username = "root"; // Usuário do MySQL
  $password = ""; // Senha do MySQL
  $dbname = "estoque_anp"; // Nome do banco de dados

  // Criar conexão
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Verificar conexão
  if ($conn->connect_error) {
      die("Falha na conexão: " . $conn->connect_error);
  }

  // Consultar os produtos no estoque
  $sql_produtos = "SELECT id, produto FROM produtos";
  $result_produtos = $conn->query($sql_produtos);

  // Consultar os postos na tabela postos
  $sql_postos = "SELECT id, posto FROM postos";
  $result_postos = $conn->query($sql_postos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>CADASTRO ESTOQUE</title>
  <style>
    body {
      background: linear-gradient(to bottom, #0a1b7e, #0080ff);
      font-family: Arial, sans-serif;
      margin: 40px;
      
      color: #333;
    }
    h1 {
      text-align: center;
    }
    form {
      background: #a1a1a1ff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 80px;
      border-radius: 15px;
    }
    label {
      display: block;
      margin: 20px 0 5px;
    }
    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      margin-top: 0px;
      padding: 10px 15px;
      border: none;
      background: #007BFF;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    .btn-excluir {
      background: #dc3545;
      color: #fff;
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .btn-excluir:hover {
      background: #a71d2a;
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
    #data_venda:hover {
      background: #218838;
    }
    #data_venda {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background: #28a745;
      color: #fff;
      cursor: pointer;
    }
    header {
      padding: 8px;
      text-align: center;
      margin-bottom: 70px;
      position: fixed;
      top: 0px;
      left: 0;
      right: 0;
      background: #fff;
      z-index: 1000;
    }


    /* Estilo padrão do select */
    .filtro-servicos {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      padding: 8px 12px;
      border: 2px solid #ccc;
      border-radius: 6px;
      background: #fff;
      color: #333;
      font-weight: 600;
      transition: background-color .2s, color .2s, border-color .2s;
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
      color: #fff;
      border-color: #212121;
    }

    /* (Opcional) colorir as opções no dropdown */
    .filtro-servicos option[value="GASOLINA COMUM"] { background-color: #ffcdd2; }
    .filtro-servicos option[value="GASOLINA DURA MAIS"] { background-color: #bbdefb; }
    .filtro-servicos option[value="ETANOL"] { background-color: #c8e6c9; }
    .filtro-servicos option[value="DIESEL S10"] { background-color: #e0e0e0; }
    /* Faixa azul inclinada */
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
      z-index: 0;/* Coloca atrás do conteúdo principal */
    }
    h1 {
      color: #fff;
      margin-bottom: 20px;
      position: relative;
      z-index: 1; /* Garante que o título fique acima da faixa */
    }
    
  </style>
</head>
<body>
    <div class="faixa-inclinada"></div>
  
    <h1>Cadastro de Estoque</h1>
    
    <button onclick="window.location.href='listar_estoque.php'">Listar Estoque</button>
    <button onclick="window.location.href='listar_entradas.php'">Listar Entradas</button>
    <button onclick="window.location.href='entradas.php'">Entradas</button>
  

  <form  action="salvar_estoque.php"  method="POST" >

  <label for="posto">Posto:</label>
    <select  id="posto" class="posto" name="posto" required autofocus>
          <option value="">Selecione</option>
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

      <label for="produto">Produto:</label>
    <select  id="produto" class="filtro-servicos" name="produto" required autofocus>
          <option value="">Selecione</option>
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

    <label for="sistema">Estoque do Sistema:</label>
    <input type="number" id="sistema" name="estoque_sistema" required>

    <label for="fisico">Estoque Físico:</label>
    <input type="number" id="fisico" name="estoque_fisico" required>

    <label for="data_venda">DATA:</label>
    <input type="date" id="data_venda" name="data_venda" required>

    <button type="submit">Cadastrar</button>
  </form>

</body>
</html>