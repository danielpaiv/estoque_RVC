<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Tela de Login</title>
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
  </style>
</head>
<body>
  <div class="faixa-inclinada"></div>

  <div class="container">
    <h1>BEM VINDO AO SISTEMA</h1>
    <p>ACESSE O SISTEMA COM USUÁRIO E SENHA</p>

    <form>
      <div style="width: 100%;">
        <label for="usuario">🤵</label>
        <input type="text" id="usuario" name="usuario" required placeholder="Digite seu usuário" autofocus>
      </div>
      <div style="width: 100%;">
        <label for="senha">🔒</label>
        <input type="password" id="senha" name="senha" required placeholder="Digite sua senha">
      </div>
      <input type="submit" value="ENTRAR">
    </form>
  </div>
</body>
</html>   
