<?php       
       /*Configurações do banco de dados*/
        $servername = "localhost"; // Ou o IP do servidor
        $username = "root"; // Usuário do MySQL
        $password = ""; // Senha do MySQL
        $dbname = "estoque_RVC"; // Nome do banco de dados
    
        // Criar conexão
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexão
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        

        // Consultar os produtos no estoque
        $sql_usuarios = "SELECT id, nome FROM usuarios";
        $result_usuarios = $conn->query($sql_usuarios);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>LOGIN | RVC</title>
  <style>
    * {/* Reseta o padding e margin de todos os elementos */
      margin: 0;/* Reseta a margem de todos os elementos */
      padding: 0;/* Reseta o padding de todos os elementos */
      box-sizing: border-box;/* Inclui padding e border no cálculo de largura e altura */
    }

    body {
      font-family: Arial, sans-serif;
      height: 100vh;
      background: linear-gradient(to bottom, #0a1b7e, #0080ff);
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      color: white;
    }

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

    .container {
      z-index: 1;
      text-align: center;
      width: 100%;
      max-width: 400px;
      padding: 20px;
    }

    h1 {
      font-size: 28px;
      margin-bottom: 10px;
    }

    p {
      margin-bottom: 40px;
      font-size: 14px;
    }

    form {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      text-align: left;
      width: 100%;
      margin-left: 40px;
    }

    input[type="text"],
    input[type="password"] {
      width: 80%;
      padding: 10px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(to right, #b0f5ff, #55d4ff);
      color: #000;
      font-weight: bold;
    }

    input[type="submit"] {
      width: 150px;
      padding: 10px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(to right, #b0f5ff, #55d4ff);
      color: #000;
      font-weight: bold;
      cursor: pointer;
    }

    select {
      width: 80%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background: linear-gradient(to right, #b0f5ff, #55d4ff);
      color: #000;
      cursor: pointer;
    }
    select:hover {
      background: linear-gradient(to right, #55d4ff, #b0f5ff);
    }
    #data_entrada:hover {
      background: #218838;
    }
  </style>
</head>
<body>
  <div class="faixa-inclinada"></div>

  <div class="container">
    <h1>BEM VINDO AO SISTEMA</h1>
    <p>ACESSE O SISTEMA COM USUÁRIO E SENHA</p>

    <form action="teste_login.php" method="post">
      <div style="width: 100%;">
        <label for="usuario">🤵</label>
        <select  id="nome" class="nome" name="nome" required autofocus>
                  <option value="">Selecione</option>
                  <?php
                  if ($result_usuarios && $result_usuarios->num_rows > 0) {
                      while($row = $result_usuarios->fetch_assoc()) {
                          echo "<option value='" . $row['nome'] . "'>" . $row['nome'] . "</option>";
                      }
                  } else {
                      echo "<option value=''>Nenhum usuário encontrado</option>";
                  }
                  ?>
        </select>
      </div>
      <div style="width: 100%;">
        <label for="senha">🔒</label>
        <input type="password" id="senha" name="senha" required placeholder="Digite sua senha">
      </div>
      <input type="submit" name="submit" value="Enviar">
    </form>
  </div>
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
              document.querySelector("input[type=submit]").click(); // Se for o último, envia
            }
          }
        });
      });
  </script>
</body>
</html>   
