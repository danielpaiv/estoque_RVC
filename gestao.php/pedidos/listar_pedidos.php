<?php
include_once('conexao.php');

$sql = "SELECT * FROM pedidos ORDER BY id_estoque  DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Pedidos</title>
    <style>
         body { 
            font-family: Arial, sans-serif;
             margin: 40px; 
             
             color: #333; 
             background-color: #0038a0;
            }
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

    </style>
</head>
<body>
    <header>
    <button onclick="window.location.href='/controle_combustivel/estoque_RVC/gestao.php'">Voltar ao Menu</button>
    </header>


<h1>Lista de Pedidos</h1>

<a href="relatorio_pedidos.php" target="_blank">Gerar Relatório</a>
<br><br>

<table border="1" cellpadding="10">
    <tr class="tabela-header">>
        
        <th>ID Estoque</th>
        <th>Posto</th>
        <th>Produto</th>
        <th>Volume</th>
        <th>Data</th>
        <th>Ações</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        
        <td><?= $row['id_estoque'] ?></td>
        <td><?= $row['posto'] ?></td>
        <td><?= $row['produto'] ?></td>
        <td><?= $row['volume'] ?></td>
        <td><?= $row['data_pedido'] ?></td>

        <td>
            <a href="editar_pedido.php?id=<?= $row['id'] ?>" class="btn btn-editar">Editar</a> |
            <a href="excluir_pedido.php?id=<?= $row['id'] ?>"  class="btn btn-excluir" onclick="return confirm('Excluir este pedido?')">Excluir</a>
            </a>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>
