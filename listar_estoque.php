<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estoque_anp";

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


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Estoque</title>
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
        .btn { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-editar { text-decoration: none; background: #ffc107; color: #000; } 
        .btn-excluir { text-decoration: none; background: #dc3545; color: #fff; }
        .btn-editar:hover {  background: #e0a800; }
        .btn-excluir:hover {  background: #a71d2a; }
        button { margin-bottom: 20px; padding: 10px 15px; border: none; background: #28a745; color: #fff; border-radius: 5px; cursor: pointer; }
        button:hover { background: #218838; }
        input, select { margin-left: 10px; padding: 5px; border: 1px solid #ccc; border-radius: 5px; background: #28a745; color: #fff;  cursor: pointer;}
        input:hover, select:hover { background: #218838; }
        #dataFiltro:hover {background: #218838;}


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

            
    </style>
</head>
<body>
    <!--<div class="faixa-inclinada"></div>-->
    <header>
        <h1>Lista de Estoque</h1>

        <button onclick="window.location.href='formulario_estoque.php'">Cadastrar Novo Produto</button>

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

        <label for="filtroNome">Filtrar por serviços:</label>
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
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['posto']) ?></td>
                    <td><?= htmlspecialchars($row['produto']) ?></td>
                    <td><?= $row['estoque_sistema'] ?></td>
                    <td><?= $row['estoque_fisico'] ?></td>
                    <td><?= $row['diferenca'] ?></td>
                    <td><?= $row['data_venda'] ?></td>
                    <td>
                        <a href="editar_estoque.php?id=<?= $row['id'] ?>" class="btn btn-editar">Editar</a>
                        <a href="excluir_estoque.php?id=<?= $row['id'] ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este item?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">Nenhum produto cadastrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<script>
        function filtrarData() {
            const input = document.getElementById('dataFiltro');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('clientesTabela');
            const tr = table.getElementsByTagName('tr');
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[5]; // coluna "Data"
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

<?php
$conn->close();
?>