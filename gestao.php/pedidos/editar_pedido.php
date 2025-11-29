<?php
include_once('conexao.php');

$id = intval($_GET['id']);
$sql = "SELECT * FROM pedidos WHERE id = $id";
$result = $conn->query($sql);

if (!$row = $result->fetch_assoc()) {
    die("Pedido não encontrado!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posto = $_POST['posto'];
    $produto = $_POST['produto'];
    $volume = $_POST['volume'];

    $stmt = $conn->prepare("
        UPDATE pedidos SET posto=?, produto=?, volume=? WHERE id=?
    ");
    $stmt->bind_param("sssi", $posto, $produto, $volume, $id);
    $stmt->execute();

    header("Location: listar_pedidos.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Pedido</title>
</head>
<body>

<h1>Editar Pedido</h1>

<form method="post">

    Posto:<br>
    <input type="text" name="posto" value="<?= $row['posto'] ?>" required><br><br>

    Produto:<br>
    <input type="text" name="produto" value="<?= $row['produto'] ?>" required><br><br>

    Volume:<br>
    <input type="text" name="volume" value="<?= $row['volume'] ?>" required><br><br>

    <button type="submit">Salvar</button>

</form>

</body>
</html>
