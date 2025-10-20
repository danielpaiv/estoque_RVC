<?php
 session_start();
    include 'conexao.php';
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }
    if(!isset($_SESSION['nome']) && !isset($_SESSION['senha'])){
        header('Location: index.php');
    }
    unset($_SESSION['nome']);
    unset($_SESSION['senha']);
    header('Location:  http://localhost/controle_combustivel/estoque_ANP/index.php');

?>