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
    <title>Listar Entradas</title>
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
             background: #28a745; 
             color: #fff;  
             cursor: pointer;
            }

        input:hover, select:hover { 
            background: #218838; 
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
    </style>
</head>
<body>
     <header>
        <h1>Listar entradas</h1>

        <button onclick="window.location.href='formulario_entradas.php'">Voltar</button>

        <label for="dataFiltro">Filtrar por Data:</label>
        <input type="date" id="dataFiltro" oninput="filtrarData()">

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
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nenhuma entrada encontrada</td></tr>";
            }

           
            ?>
        </tbody>
    </table>

    <script>
        function filtrarData() {
            const input = document.getElementById('dataFiltro');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('clientesTabela');
            const tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[3]; // coluna "Data"
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