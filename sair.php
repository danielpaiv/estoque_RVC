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
    unset($_SESSION['nome']);
    unset($_SESSION['senha']);
    header('Location: index.php');

?>