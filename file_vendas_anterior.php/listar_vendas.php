<?php

    session_start();
    include_once('conexao.php');

        if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
        unset($_SESSION['nome']);
        unset($_SESSION['senha']);
        unset($_SESSION['user_id']);
        header('Location: http://localhost/controle_combustivel/estoque_RVC/index.php');
        exit();  // Importante adicionar o exit() após o redirecionamento
        }

        $user_id = $_SESSION['user_id']; // Recupera o user_id da sessão
        $nome = $_SESSION['nome'];
        $user_id = $_SESSION['user_id'];

        $sql_vendas = 'SELECT * FROM vendas WHERE user_id = ? ORDER BY id DESC';
        $stmt = $conn->prepare($sql_vendas);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result_vendas = $stmt->get_result();

        // Consultar as entradas
            $sql = "SELECT * FROM entradas ORDER BY id DESC";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VENDAS DIA ANTERIOR</title>
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
            background: #007BFF; 
            color: #fff; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        button:hover { 
            background: #0056b3; 
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

            
    
    </style>
</head>
<body>
    <header>
        <h1>VENDAS DIA ANTERIOR</h1>
        <button onclick="window.location.href='http://localhost/controle_combustivel/estoque_RVC/painel.php'">Voltar</button>
        <button onclick="window.location.href='formulario_vendas_dia_anterior.php'">Adicionar</button>
        <button class="limpar" id="limparFiltros" onclick="limparFiltros()">Limpar Filtros</button>

        <label for="dataFiltro">Filtrar por Data:</label><?php date_default_timezone_set('America/Sao_Paulo'); ?>
        <input type="date" id="dataFiltro" value="<?php echo date('Y-m-d'); ?>" oninput="filtrarData()">

        <label for="filtroPosto">Filtrar por Posto:</label>
        <select id="filtroPosto" class="filtro-servicos" onchange="filtrarPorPosto()">

        

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

           

        <label for="filtroNome">Filtrar por Produto:</label>
        <select id="filtroNome" class="filtro-servicos" onchange="filtrarPorNome()">
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
                   
        </select>

    </header>

    
    <table id="clientesTabela">
        <thead>
            <tr class="tabela-header">
                <th>id</th>
                <th>Usuario</th>
                <!--<th>user_ID</th>-->
                <th>Posto</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Data da Venda</th>
                <th>Ações</th>

            </tr>
        </thead>
        <tbody>
             <?php if ($result_vendas->num_rows > 0): ?>
            <?php while($row = $result_vendas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <!--<td><?php echo $row['user_id']; ?></td>-->
                        <td><?php echo $row['posto']; ?></td>
                        <td><?php echo $row['produto']; ?></td>
                        <td><?php echo $row['quantidade']; ?></td>
                        <td><?php echo $row['data_venda']; ?></td>
                        <td>
                            <a href="editar_vendas.php?id=<?php echo $row['id']; ?> " class="btn btn-editar">Editar</a> |
                            <a href="excluir_vendas.php?id=<?php echo $row['id']; ?>"  class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir esta venda?');">Excluir</a>
                        </td>
                    </tr>
            <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Nenhuma venda encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <p style="color:white">Usuário: <?php echo $nome; ?></p>
    <p style="color:white">ID: <?php echo $user_id; ?></p>
        <script>
            function limparFiltros() {
                    const table = document.getElementById('clientesTabela');
                    const button = document.getElementById('limparFiltros');
                    const tr = table.getElementsByTagName('tr');
                    document.getElementById('dataFiltro').value = '';
                    document.getElementById('filtroNome').value = '';
                    document.getElementById('filtroPosto').value = '';
                    filtrarData();
                    filtrarPorNome();
                    filtrarPorPosto();

                }
                function filtrarData() {
                    const input = document.getElementById('dataFiltro');
                    const filter = input.value.toLowerCase();
                    const table = document.getElementById('clientesTabela');
                    const tr = table.getElementsByTagName('tr');
                    for (let i = 1; i < tr.length; i++) {
                        const td = tr[i].getElementsByTagName('td')[6]; // coluna "Data"
                        if (td) {
                            const txtValue = td.textContent || td.innerText;
                            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                tr[i].style.display = '';
                            } else {
                                tr[i].style.display = 'none';
                            }
                        }
                    }
                }
                function filtrarPorNome() {
                    const input = document.getElementById('filtroNome');
                    const filter = input.value.toLowerCase();
                    const table = document.getElementById('clientesTabela');
                    const tr = table.getElementsByTagName('tr');
                    for (let i = 1; i < tr.length; i++) {
                        const td = tr[i].getElementsByTagName('td')[4]; // coluna "Nome"
                        if (td) {
                            const txtValue = td.textContent || td.innerText;
                            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                tr[i].style.display = '';
                            } else {
                                tr[i].style.display = 'none';
                            }
                        }
                    }
                }
                function filtrarPorPosto() {
                    const input = document.getElementById('filtroPosto');
                    const filter = input.value.toLowerCase();
                    const table = document.getElementById('clientesTabela');
                    const tr = table.getElementsByTagName('tr');
                    for (let i = 1; i < tr.length; i++) {
                        const td = tr[i].getElementsByTagName('td')[3]; // coluna "Posto"
                        if (td) {
                            const txtValue = td.textContent || td.innerText;
                            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                tr[i].style.display = '';
                            } else {
                                tr[i].style.display = 'none';
                            }
                        }
                    }
                }
        </script>
</body>
</html>