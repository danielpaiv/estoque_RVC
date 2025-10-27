<?php
        session_start();
        include_once('conexao.php');
        
         if (!isset($_SESSION['nome']) || !isset($_SESSION['senha']) || !isset($_SESSION['user_id'])) {
                unset($_SESSION['nome']);
                unset($_SESSION['senha']);
                unset($_SESSION['user_id']);
                header('Location: http://localhost/controle_combustivel/estoque_ANP/index.php');
                exit();  // Importante adicionar o exit() após o redirecionamento
            }

            $user_id = $_SESSION['user_id']; // Recupera o user_id da sessão

            $nome = $_SESSION['nome'];
            $user_id = $_SESSION['user_id'];

            // Consultar as entradas
            $sql = "SELECT * FROM entradas ORDER BY id DESC";
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

             // Fechar conexão
            $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LISTAR TODAS AS ENTRADAS</title>
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
        <h1>LISTAR TODAS AS ENTRADAS</h1>
        <button onclick="window.location.href='listar_entradas.php'">Voltar</button>
        <button onclick="window.location.href='formulario_entradas.php'">Adicionar</button>
        <button class="limpar" id="limparFiltros" onclick="limparFiltros()">Limpar Filtros</button>

          <label for="usuario"></label>
        <select  id="filtroUsuario" class="filtro-servicos" onchange="filtrarUsuario()" name="nome" required autofocus>
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
                <th>User ID</th>
                <th>Usuário</th>
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
                    echo "<td>" . $row['user_id'] . "</td>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['posto'] . "</td>";
                    echo "<td>" . $row['produto'] . "</td>";
                    echo "<td>" . $row['quantidade'] . "</td>";
                    echo "<td>" . $row['data_entrada'] . "</td>";
                    //echo "<td><a href='editar_entradas.php?id=" . $row['id'] . "' class='btn-editar'>Editar</a> <a href='excluir_entradas.php?id=" . $row['id'] . "' class='btn-excluir'onclick=\"return confirm('Tem certeza que deseja excluir este item?')\">Excluir</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nenhuma entrada encontrada</td></tr>";
            }

           
            ?>
        </tbody>
        
        <thead>
            <tr>
                <th style="background-color: #d32f2f; color: white;">GASOLINA COMUM</th>
                <th style="background-color: #1565c0; color: white;">GASOLINA DURA MAIS</th>
                <th style="background-color: #2e7d32; color: white;">ETANOL</th>
                <th style="background-color: #424242; color: white;">DIESEL S10</th>
            </tr>
        </thead>
         <tbody>
            <tr>
                <td>
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_gasolina_comum = "SELECT SUM(quantidade) AS total_gasolina_comum FROM entradas WHERE produto = 'GASOLINA COMUM'";
                    $result_gasolina_comum = $conn->query($sql_gasolina_comum);
                    $row_gasolina_comum = $result_gasolina_comum->fetch_assoc();
                    echo $row_gasolina_comum['total_gasolina_comum'] ? $row_gasolina_comum['total_gasolina_comum'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
                <td>
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_gasolina_dura_mais = "SELECT SUM(quantidade) AS total_gasolina_dura_mais FROM entradas WHERE produto = 'GASOLINA DURA MAIS'";
                    $result_gasolina_dura_mais = $conn->query($sql_gasolina_dura_mais);
                    $row_gasolina_dura_mais = $result_gasolina_dura_mais->fetch_assoc();
                    echo $row_gasolina_dura_mais['total_gasolina_dura_mais'] ? $row_gasolina_dura_mais['total_gasolina_dura_mais'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
                <td>
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_etanol = "SELECT SUM(quantidade) AS total_etanol FROM entradas WHERE produto = 'ETANOL'";
                    $result_etanol = $conn->query($sql_etanol);
                    $row_etanol = $result_etanol->fetch_assoc();
                    echo $row_etanol['total_etanol'] ? $row_etanol['total_etanol'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
                <td>
                    
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_diesel_s10 = "SELECT SUM(quantidade) AS total_diesel_s10 FROM entradas WHERE produto = 'DIESEL S10'";
                    $result_diesel_s10 = $conn->query($sql_diesel_s10);
                    $row_diesel_s10 = $result_diesel_s10->fetch_assoc();
                    echo $row_diesel_s10['total_diesel_s10'] ? $row_diesel_s10['total_diesel_s10'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!--<table>
        <thead>
            <tr>
                <th style="background-color: #d32f2f; color: white;">GASOLINA COMUM</th>
                <th style="background-color: #1565c0; color: white;">GASOLINA DURA MAIS</th>
                <th style="background-color: #2e7d32; color: white;">ETANOL</th>
                <th style="background-color: #424242; color: white;">DIESEL S10</th>
            </tr>
        </thead>
        
        <tbody>
            <tr>
                <td>
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_gasolina_comum = "SELECT SUM(quantidade) AS total_gasolina_comum FROM entradas WHERE produto = 'GASOLINA COMUM'";
                    $result_gasolina_comum = $conn->query($sql_gasolina_comum);
                    $row_gasolina_comum = $result_gasolina_comum->fetch_assoc();
                    echo $row_gasolina_comum['total_gasolina_comum'] ? $row_gasolina_comum['total_gasolina_comum'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
                <td>
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_gasolina_dura_mais = "SELECT SUM(quantidade) AS total_gasolina_dura_mais FROM entradas WHERE produto = 'GASOLINA DURA MAIS'";
                    $result_gasolina_dura_mais = $conn->query($sql_gasolina_dura_mais);
                    $row_gasolina_dura_mais = $result_gasolina_dura_mais->fetch_assoc();
                    echo $row_gasolina_dura_mais['total_gasolina_dura_mais'] ? $row_gasolina_dura_mais['total_gasolina_dura_mais'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
                <td>
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_etanol = "SELECT SUM(quantidade) AS total_etanol FROM entradas WHERE produto = 'ETANOL'";
                    $result_etanol = $conn->query($sql_etanol);
                    $row_etanol = $result_etanol->fetch_assoc();
                    echo $row_etanol['total_etanol'] ? $row_etanol['total_etanol'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
                <td>
                    <?php
                    // Reabrir a conexão para a nova consulta
                    include('conexao.php');

                    $sql_diesel_s10 = "SELECT SUM(quantidade) AS total_diesel_s10 FROM entradas WHERE produto = 'DIESEL S10'";
                    $result_diesel_s10 = $conn->query($sql_diesel_s10);
                    $row_diesel_s10 = $result_diesel_s10->fetch_assoc();
                    echo $row_diesel_s10['total_diesel_s10'] ? $row_diesel_s10['total_diesel_s10'] . ' L' : '0 L';

                    // Fechar a conexão
                    $conn->close();
                    ?>
                </td>
            </tr>
        </tbody>
        </table>
    -->
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
            document.getElementById('filtroUsuario').value = '';
            filtrarData();
            filtrarPorNome();
            filtrarPorPosto();
            filtrarUsuario();

        }

         function filtrarUsuario() {
            const input = document.getElementById('filtroUsuario');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('clientesTabela');
            const tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[2]; // coluna "Usuário"
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