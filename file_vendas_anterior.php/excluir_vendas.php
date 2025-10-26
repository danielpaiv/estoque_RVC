<?php
    session_start();
            include_once('conexao.php');
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

    $id = intval($_GET['id']);
    $conn->query("DELETE FROM vendas WHERE id=$id");

    $conn->close();
    header("Location: listar_vendas.php");
    exit;
?>