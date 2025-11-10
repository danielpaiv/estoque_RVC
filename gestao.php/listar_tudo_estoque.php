<?php
        session_start();
        include_once('conexao.php');
        
         if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
                unset($_SESSION['nome']);
                unset($_SESSION['senha']);
                header('Location: http://localhost/controle_combustivel/estoque_RVC/index.php');
                exit();  // Importante adicionar o exit() após o redirecionamento
            }

            $user_id = $_SESSION['user_id']; // Recupera o user_id da sessão
            $nome = $_SESSION['nome'];
            $user_id = $_SESSION['user_id'];

            // Conectar ao MySQL
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

            // Consultar todos os produtos
            $sql = "SELECT * FROM estoque ORDER BY id DESC";
            $result = $conn->query($sql);

            // Consultar os produtos no estoque
            $sql_produtos = "SELECT id, produto FROM produtos";
            $result_produtos = $conn->query($sql_produtos);

            // Consultar os postos no estoque
            $sql_postos = "SELECT id, posto FROM postos";
            $result_postos = $conn->query($sql_postos);

            // Consultar os produtos no estoque
            $sql_usuarios = "SELECT id, nome FROM usuarios";
            $result_usuarios = $conn->query($sql_usuarios);


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>ESTOQUE</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
             margin: 40px; 
             
             color: #333; 
             background-color: #0038a0;
            }
        h1 { 
            text-align: center; 
        }
        /*table { width: 100%; border-collapse: collapse; margin-top: 150px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #007BFF; color: white;  }*/
        .btn { padding: 5px 10px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        .btn-editar { 
            text-decoration: none; 
            background: #ffc107; 
            color: #000; 
        } 
        .btn-excluir { 
            text-decoration: none;
             background: #dc3545; 
             color: #fff; 
            }
        .btn-editar:hover {  
            background: #e0a800; 
        }
        .btn-excluir:hover {  
            background: #a71d2a; 
        }
        button { 
            margin-bottom: 20px; 
            padding: 10px 15px; 
            border: none; 
            background: #28a745; 
            color: #fff; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        button:hover { 
            background: #218838; 
        }
        input, select {
             margin-left: 10px; 
             padding: 5px; 
             border: 1px solid #ccc; 
             border-radius: 5px; 
             background: #ffffffff; 
             color: #080808ff;  
             cursor: pointer;
            }
        input:hover, select:hover { 
            background: #218838; 
            color: #fff;
        }
        #dataFiltro:hover {
            background: #218838;
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

        header { 
            text-align: center; 
            margin-bottom: 70px; 
            position: fixed; 
            top: 0px; 
            left: 0; 
            right: 0; 
            background: #fff; 
            z-index: 1000; }
        table {
            background: #fff;
            border-collapse: collapse;
            width: 100%;
             margin-top: 135.5px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
           
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #007BFF;
            color: white;
        }

        .tabela-header th {
            position: sticky;
            top: 135px; /* Ajuste conforme o layout */
            background: #007BFF;
            color: white;
            z-index: 10; /* Mantém sobre as linhas */
        }
        .faixa-inclinada {
            position: absolute;/* Coloca a faixa atrás do conteúdo principal */
            bottom: 0;/* Ajusta a posição para o fundo */
            left: 0;/* Ajusta a posição para o fundo */
            width: 100%;/* Preenche toda a largura da tela */
            height: 70%;/* Preenche 70% da altura da tela */
            background: linear-gradient(to bottom, #0a1b7e, #0080ff);/* Cria um gradiente azul */
            position: absolute;/* Coloca a faixa atrás do conteúdo principal*/ 
            background-color: #0038a0;
            clip-path: polygon(0 25%, 100% 0%, 100% 100%, 0% 100%);/* Inclinada para baixo */
            transform: skewY(-10deg);/* Inclinada para baixo */
            transform-origin: bottom left;/* Ajusta a origem da transformação */
            z-index: -10;/* Coloca atrás do conteúdo principal */
        }
        .limpar{
             margin-left: 10px; 
             padding: 5px; 
             border: 1px solid #ccc; 
             border-radius: 5px; 
             background: #ffffffff; 
             color: #080808ff;  
             cursor: pointer;
        }
        .limpar:hover { 
             background: #218838; 
             color: #fff;
        }

         #totalGasolinaComum {
            font-weight: bold;
            color: #ff0000b4;
            text-align: right;
            padding-right: 10px;

        }
        #totalGasolinaAditivada {
            font-weight: bold;
            color: #1565c0;
            text-align: right;
            padding-right: 10px;

        }
        #totalDiesel {
            font-weight: bold;
            color: #222222ff;
            text-align: right;
            padding-right: 10px;

        }
        #totalEtanol {
            font-weight: bold;
            color: #2e7d32;
            text-align: right;
            padding-right: 10px;

        }
        p {
            font-size: 18px;
            color: #fff;
        }

        
        /* 🔴 Estoque baixo */
        .estoque-baixo {
        background: linear-gradient(to bottom, #ac1f1fff, #ff2e2e);
        color: #ffffff;
        font-weight: bold;
        }

        /* 🟢 Estoque alto */
        .estoque-alto {
        background: linear-gradient(to bottom, #035803ff, #00ff00);
        color: #000000;
        font-weight: bold;
        }

        /* Efeitos ao passar o mouse */
        .estoque-baixo:hover {
        background: linear-gradient(to bottom, #f56200, #ff2e2e);
        color: #000000;
        }

        .estoque-alto:hover {
        background: linear-gradient(to bottom, #2e7d32, #00ff00);
        color: #ffffff;
        }

        .estoque-medio {
        background-color: #ffcc00;
        color: #000000;
        font-weight: bold;
        }

            
    </style>
</head>
<body>
    <!--<div class="faixa-inclinada"></div>-->
    <header>
        <h1>ESTOQUE</h1>
        <button onclick="window.location.href='painel.php'">Voltar</button>
        <button class="limpar" id="limparFiltros" onclick="limparFiltros()">Limpar Filtros</button>

         <label for="usuario"></label>
        <select  id="filtroUsuario" class="filtro-servicos" onchange="aplicarFiltros()" name="nome" required autofocus>
                  <option value="">Usuário</option>
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

        <label for="dataFiltro">Filtrar Data:</label><?php date_default_timezone_set('America/Sao_Paulo'); ?>
        <input type="date" id="dataFiltro" value="<?php echo date('Y-m-d'); ?>"oninput="aplicarFiltros()" >

        <label for="filtroPosto">Filtrar Posto:</label>
        <select id="filtroPosto" class="filtro-servicos" onchange="aplicarFiltros()">
            <option value="">Todos</option>
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

        <label for="filtroProduto">Filtrar Produto:</label>
        <select id="filtroProduto" class="filtro-servicos" onchange="aplicarFiltros()">
            <option value="">Todos</option>
            <?php
                if ($result_produtos && $result_produtos->num_rows > 0) {
                    while($row = $result_produtos->fetch_assoc()) {
                        echo "<option value='" . $row['produto'] . "'>" . $row['produto'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum produto encontrado</option>";
                }
                ?>
                    <!--<option value="">Todos</option>
                        <option value="GASOLINA COMUM">GASOLINA COMUM</option>
                        <option value="GASOLINA DURA MAIS">GASOLINA DURA MAIS</option>
                        <option value="ETANOL">ETANOL</option>
                        <option value="DIESEL S10">DIESEL S10</option>
                    --> 
        </select>

    </header>

<table id="clientesTabela">
    <thead>
        <tr class="tabela-header">
                <th>ID</th>
                <!--<th>user_ID</th>-->
                <th>Usuário</th>
                <th>Posto</th>
                <th>Produto</th>
                <th>Estoque do Sistema</th>
                <th>Estoque Físico</th>
                <th>Diferença</th>
                <th>Data</th>
                
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <!--<td><?= $row['user_id'] ?></td>-->
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['posto']) ?></td>
                    <td><?= htmlspecialchars($row['produto']) ?></td>

                    <!-- Coluna Estoque do Sistema -->
                    <?php
                        $estoqueSistema = (float)$row['estoque_sistema'];
                        if ($estoqueSistema < 2500) {
                            $classeSistema = 'estoque-baixo';
                        } elseif ($estoqueSistema > 2499) {
                            $classeSistema = 'estoque-alto';
                        } else {
                            $classeSistema = '';
                        }
                    ?>
                    <td class="<?= $classeSistema ?>">
                        <?= htmlspecialchars($row['estoque_sistema']) ?>
                    </td>

                    <!-- Coluna Estoque Físico -->
                    <?php
                        $estoqueFisico = (float)$row['estoque_fisico'];
                        if ($estoqueFisico < 2500) {
                            $classeFisico = 'estoque-baixo';
                        } elseif ($estoqueFisico > 2500) {
                            $classeFisico = 'estoque-alto';
                        } else {
                            $classeFisico = '';
                        }
                    ?>
                    <td class="<?= $classeFisico ?>">
                        <?= htmlspecialchars($row['estoque_fisico']) ?>
                    </td>

                    <td><?= $row['diferenca'] ?></td>
                    <td><?= $row['data_venda'] ?></td>
                    <!--<td>
                        <a href="editar_estoque.php?id=<?= $row['id'] ?>" class="btn btn-editar">Editar</a>
                        <a href="excluir_estoque.php?id=<?= $row['id'] ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este item?')">Excluir</a>
                        
                    </td>-->
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">Nenhum produto cadastrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

  <!-- Totais de Combustíveis
    <div style="margin-top: 20px; width: 30%; background-color: #06161dff; padding: 10px;">
    <p><strong>Total GASOLINA COMUM:</strong> <span id="totalGasolinaComum">0 L</span></p>
    <p><strong>Total  GASOLINA DURA MAIS:</strong> <span id="totalGasolinaAditivada">0 L</span></p>
    <p><strong>Total  DIESEL S10:</strong> <span id="totalDiesel">0 L</span></p>
    <p><strong>Total  ETANOL:</strong> <span id="totalEtanol">0 L</span></p>
    </div>
  -->
   <!--
    <script>
    // Função para somar os combustíveis visíveis na tabela
        function somarCombustiveisVisiveis() {
        const linhas = document.querySelectorAll("#clientesTabela tbody tr");
        let totalGasolinaComum = 0;
        let totalGasolinaAditivada = 0;
        let totalDiesel = 0;
        let totalEtanol = 0;

        linhas.forEach(linha => {
            // Verifica se a linha está visível
            const estilo = window.getComputedStyle(linha);
            if (estilo.display === "none") return; // ignora linhas ocultas

            // Obtém o produto e a quantidade da linha
            const produto = linha.cells[3]?.textContent.trim().toUpperCase();
            const quantidade = parseFloat(linha.cells[4]?.textContent.trim()) || 0;

            // Adiciona à soma correspondente
            if (produto === "GASOLINA COMUM") totalGasolinaComum += quantidade;
            if (produto === "GASOLINA DURA MAIS") totalGasolinaAditivada += quantidade;
            if (produto === "DIESEL S10") totalDiesel += quantidade;
            if (produto === "ETANOL") totalEtanol += quantidade;
        });
        // Atualiza os totais na página
        document.getElementById("totalGasolinaComum").textContent = totalGasolinaComum.toFixed(2) + " L";// Atualiza o total no elemento HTML
        document.getElementById("totalGasolinaAditivada").textContent = totalGasolinaAditivada.toFixed(2) + " L";
        document.getElementById("totalDiesel").textContent = totalDiesel.toFixed(2) + " L";
        document.getElementById("totalEtanol").textContent = totalEtanol.toFixed(2) + " L";
        }

        // Executa ao carregar
        somarCombustiveisVisiveis();

        // Atualiza automaticamente se houver filtros aplicados por JS
        document.addEventListener("input", somarCombustiveisVisiveis);
        document.addEventListener("change", somarCombustiveisVisiveis);
    </script>
   -->
    <p style="color:white">Usuário: <?php echo $nome; ?></p>
    <p style="color:white">ID: <?php echo $user_id; ?></p>

     <script>
    // Função para aplicar e limpar todos os filtros
        function limparFiltros() {
            // limpa os campos de filtro
            document.getElementById('filtroUsuario').value = '';
            document.getElementById('dataFiltro').value = '';
            document.getElementById('filtroProduto').value = '';
            document.getElementById('filtroPosto').value = '';

            // reexibe todas as linhas da tabela
            const linhas = document.querySelectorAll('#clientesTabela tbody tr');
            linhas.forEach(linha => {
                linha.style.display = '';
            });

            // atualiza a soma visível (se existir)
            if (typeof somarCombustiveisVisiveis === "function") {
                somarCombustiveisVisiveis();
            }
            }
        function aplicarFiltros() {
        const usuarioFiltro = document.getElementById('filtroUsuario').value.toLowerCase();
        const dataFiltro = document.getElementById('dataFiltro').value.toLowerCase();
        const nomeFiltro = document.getElementById('filtroProduto').value.toLowerCase();
        const postoFiltro = document.getElementById('filtroPosto').value.toLowerCase();

        const tabela = document.getElementById('clientesTabela');
        const linhas = tabela.getElementsByTagName('tr');

        for (let i = 1; i < linhas.length; i++) {
            const colUsuario = linhas[i].getElementsByTagName('td')[1]; // Usuário
            const colPosto = linhas[i].getElementsByTagName('td')[2];   // Posto
            const colProduto = linhas[i].getElementsByTagName('td')[3]; // Produto
            const colData = linhas[i].getElementsByTagName('td')[7];    // Data

            if (colUsuario && colPosto && colProduto && colData) {
            const usuario = colUsuario.textContent.toLowerCase();
            const posto = colPosto.textContent.toLowerCase();
            const produto = colProduto.textContent.toLowerCase();
            const data = colData.textContent.toLowerCase();

            const condUsuario = usuarioFiltro === "" || usuario.includes(usuarioFiltro);
            const condPosto = postoFiltro === "" || posto.includes(postoFiltro);
            const condProduto = nomeFiltro === "" || produto.includes(nomeFiltro);
            const condData = dataFiltro === "" || data.includes(dataFiltro);

            // só mostra se atender a todos os filtros ativos
            if (condUsuario && condPosto && condProduto && condData) {
                linhas[i].style.display = "";
            } else {
                linhas[i].style.display = "none";
            }
            }
        }

        // Atualiza soma dos combustíveis visíveis (se existir essa função)
        if (typeof somarCombustiveisVisiveis === "function") {
            somarCombustiveisVisiveis();
        }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>