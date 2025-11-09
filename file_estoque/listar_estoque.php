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

             // Consultar as entradas realizadas no dia atual para o usuário logado
            $sql_estoque = "SELECT * FROM estoque WHERE user_id = ? ORDER BY id DESC";
            $stmt = $conn->prepare($sql_estoque);
            $stmt->bind_param('i', $user_id); 
            $stmt->execute();
            $result_estoque = $stmt->get_result();

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
            z-index: 1000; 
        }

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

            
    </style>
</head>
<body>
    <!--<div class="faixa-inclinada"></div>-->
    <header>
        <h1>ESTOQUE</h1>
        

        <button onclick="window.location.href='http://localhost/controle_combustivel/estoque_RVC/painel.php'">Voltar</button>
        <button onclick="window.location.href='formulario_estoque.php'">Adicionar</button>
        <button class="limpar" id="limparFiltros" onclick="limparFiltros()">Limpar Filtros</button>

        <label for="dataFiltro">Filtrar Data:</label><?php date_default_timezone_set('America/Sao_Paulo'); ?>
        <input type="date" id="dataFiltro" value="<?php echo date('Y-m-d'); ?>"oninput="aplicarFiltros()" >

        <label for="filtroPosto">Filtrar Posto:</label>
        <select id="filtroPosto" class="filtro-servicos" onchange="aplicarFiltros()">
            <option value="">Todos</option>
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

        <label for="filtroNome">Filtrar Produto:</label>
        <select id="filtroNome" class="filtro-servicos" onchange="aplicarFiltros()">
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
                <th>Nome</th>
                <th>Posto</th>
                <th>Produto</th>
                <th>Estoque do Sistema</th>
                <th>Estoque Físico</th>
                <th>Diferença</th>
                <th>Data</th>
                <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_estoque->num_rows > 0): ?>
            <?php while($row = $result_estoque->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <!--<td><?= $row['user_id'] ?></td>-->
                    <td><?= $row['nome'] ?></td>
                    <td><?= $row['posto'] ?></td>
                    <td><?= $row['produto'] ?></td>
                    <td><?= $row['estoque_sistema'] ?></td>
                    <td><?= $row['estoque_fisico'] ?></td>
                    <td><?= $row['diferenca'] ?></td>
                    <td><?= $row['data_venda'] ?></td>
                    <td>
                        <a href="confirmar_senha_editar.php?id=<?= $row['id'] ?>" class="btn btn-editar">Editar</a>
                        <a href="confirmar_senha.php?id=<?= $row['id'] ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este item?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">Nenhum produto cadastrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<p style="color:white">Usuário: <?php echo $nome; ?></p>
<p style="color:white">ID: <?php echo $user_id; ?></p>

 <script>
    // Função para filtrar e limpar todos os filtros
        function limparFiltros() {
            // limpa os campos de filtro
            
            document.getElementById('dataFiltro').value = '';
            document.getElementById('filtroNome').value = '';
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
        
        const dataFiltro = document.getElementById('dataFiltro').value.toLowerCase();
        const nomeFiltro = document.getElementById('filtroNome').value.toLowerCase();
        const postoFiltro = document.getElementById('filtroPosto').value.toLowerCase();

        const tabela = document.getElementById('clientesTabela');
        const linhas = tabela.getElementsByTagName('tr');

        for (let i = 1; i < linhas.length; i++) {
            
            const colPosto = linhas[i].getElementsByTagName('td')[2];   // Posto
            const colProduto = linhas[i].getElementsByTagName('td')[3]; // Produto
            const colData = linhas[i].getElementsByTagName('td')[7];    // Data

            if ( colPosto && colProduto && colData) {
            
            const posto = colPosto.textContent.toLowerCase();
            const produto = colProduto.textContent.toLowerCase();
            const data = colData.textContent.toLowerCase();

            
            const condPosto = postoFiltro === "" || posto.includes(postoFiltro);
            const condProduto = nomeFiltro === "" || produto.includes(nomeFiltro);
            const condData = dataFiltro === "" || data.includes(dataFiltro);

            // só mostra se atender a todos os filtros ativos
            if (condPosto && condProduto && condData) {
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