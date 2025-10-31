
<?php

    session_start();
        include_once('conexao.php');


        //print_r($_SESSION);
        if((!isset($_SESSION['nome']) == true) and (!isset($_SESSION['senha']) == true))
        {
            unset($_SESSION['nome']);
            unset($_SESSION['senha']);
            header('Location: index.php');
        }
        $logado = $_SESSION['nome'];

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Pricipal</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #000066;
        }

        .btn {
            color: white;
            text-decoration: none;
        }

        button {
            border-radius: 5px;
            text-decoration: none;
            border: none;
            padding: 15px;
            background-image: linear-gradient(to right, rgb(130, 20, 220), rgb(44, 31, 220));
            margin: 5px;
        }

        .box {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 30px;
            width: 90%;
            max-width: 400px;
            color: white;
        }

        .box button {
            width: 100%;
            margin: 5px 0;
        }

        .box a {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .btn-danger:hover{
            background-color: black;
            color: red;
            padding:10px;
            width: 94%;
        }

    </style>
</head>
<body>
    <div class="box">
    <button><a href="http://localhost/controle_combustivel/estoque_RVC/gestao.php/listar_tudo_estoque.php"class="btn btn-danger me-5">ESTOQUE</a></button>

    <button><a href="http://localhost/controle_combustivel/estoque_RVC/gestao.php/listar_todas_entradas.php" class="btn btn-danger me-5">ENTRADAS</a></button>

    <button><a href="http://localhost/controle_combustivel/estoque_RVC/gestao.php/listar_todas_vendas.php" class="btn btn-danger me-5">VENDAS ANTERIOR</a></button>



<!--    <button><a href="mesa_4.php" class="btn btn-danger me-5">Mesa 4</a></button>
    
    <button><a href="carrinho.php" class="btn btn-danger me-5">Carrinho</a></button>

    <button><a href="estoque.php" class="btn btn-danger me-5">Estoque</a></button>

    <button><a href="#" class="btn btn-danger me-5">Ambiente</a></button>
-->
    <button><a href="sair.php" class="btn btn-danger me-5">Sair</a></button>
    </div>
</body>
</html>