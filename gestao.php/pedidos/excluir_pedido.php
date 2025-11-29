<?php
include_once('conexao.php');

$id = intval($_GET['id']);
$sql = "DELETE FROM pedidos WHERE id = $id";

$conn->query($sql);

header("Location: listar_pedidos.php");
exit;
