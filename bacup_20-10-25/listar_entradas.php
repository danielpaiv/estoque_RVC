<?php
        session_start();
        include_once('conexao.php');
        
         if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
                unset($_SESSION['nome']);
                unset($_SESSION['senha']);
                header('Location: index.php');
                exit();  // Importante adicionar o exit() após o redirecionamento
            }

            //esse codigo é responsável por criptografar a pagina viinculado ao codigo teste login.
            // Verificar se as variáveis de sessão 'email' e 'senha' não estão definidas
            if (!isset($_SESSION['nome']) || !isset($_SESSION['senha'])) {
                unset($_SESSION['nome']);
                unset($_SESSION['senha']);
                header('Location: index.php');
                exit();  // Importante adicionar o exit() após o redirecionamento
            }
            /* Configurações do banco de dados
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "estoque_anp";
            */
            // Criar conexão
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Verificar conexão
            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

            // Consultar as entradas
            $sql = "SELECT * FROM entradas ORDER BY id DESC";
            $result = $conn->query($sql);

            // Consultar os produtos no estoque
            $sql_produtos = "SELECT id, produto FROM produtos";
            $result_produtos = $conn->query($sql_produtos);

            // Consultar os postos no estoque
            $sql_postos = "SELECT id, posto FROM postos";
            $result_postos = $conn->query($sql_postos);

             // Fechar conexão
            $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LISTAR ENTRADAS - RVC</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
             margin: 40px; 
             
             color: #333; 
             background-color: #0038a0;
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
         button { margin-bottom: 20px; 
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
            padding: 10px 0;
            }
            table {
            background: #fff;
            border-collapse: collapse;
            width: 100%;
             margin-top: 155.5px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
           
        }
        .tabela-header th {
            position: sticky;
            top: 155px; /* Ajuste conforme o layout */
            background: #007BFF;
            color: white;
            z-index: 10; /* Mantém sobre as linhas */
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
        .btn-editar, .btn-excluir { 
            padding: 5px 12px; 
            border: none; 
            border-radius: 8px; 
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
    </style>
</head>
<body>
     <header>
        <h1>LISTA DE ENTRADAS - RVC</h1>

        <button onclick="window.location.href='formulario_entradas.php'">Voltar</button>
        <button class="limpar" id="limparFiltros" onclick="limparFiltros()">Limpar Filtros</button>

        <label for="dataFiltro">Filtrar por Data:</label><?php date_default_timezone_set('America/Sao_Paulo'); ?>
        <input type="date" id="dataFiltro" value="<?php echo date('Y-m-d'); ?>" oninput="filtrarData()">

        <label for="filtroPosto">Filtrar por Posto:</label>
        <select id="filtroPosto" class="filtro-servicos" onchange="filtrarPorPosto()">

        

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
                <th>ID</th>
                <th>Posto</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Data de Entrada</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['posto'] . "</td>";
                    echo "<td>" . $row['produto'] . "</td>";
                    echo "<td>" . $row['quantidade'] . "</td>";
                    echo "<td>" . $row['data_entrada'] . "</td>";
                    echo "<td><a href='editar_entradas.php?id=" . $row['id'] . "' class='btn-editar'>Editar</a> <a href='excluir_entradas.php?id=" . $row['id'] . "' class='btn-excluir'onclick=\"return confirm('Tem certeza que deseja excluir este item?')\">Excluir</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nenhuma entrada encontrada</td></tr>";
            }

           
            ?>
        </tbody>
    </table>

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
                const td = tr[i].getElementsByTagName('td')[4]; // coluna "Data"
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
                const td = tr[i].getElementsByTagName('td')[2]; // coluna "Nome"
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
                const td = tr[i].getElementsByTagName('td')[1]; // coluna "Posto"
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