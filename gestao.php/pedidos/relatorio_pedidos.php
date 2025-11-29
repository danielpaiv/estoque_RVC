<?php
include_once('conexao.php');

$sql = "SELECT * FROM pedidos ORDER BY data_pedido DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Pedidos</title>
</head>
<body>

<h1>Relatório de Pedidos</h1>
<p>Gerado em: <?= date("d/m/Y H:i") ?></p>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>ID Estoque</th>
        <th>Posto</th>
        <th>Produto</th>
        <th>Volume</th>
        <th>Data do Pedido</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['id_estoque'] ?></td>
        <td><?= $row['posto'] ?></td>
        <td><?= $row['produto'] ?></td>
        <td><?= $row['volume'] ?></td>
        <td><?= $row['data_pedido'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
    window.print();
</script>

</body>
</html>
