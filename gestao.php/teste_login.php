<?php
    session_start();

    if (isset($_POST['submit']) && !empty($_POST['nome']) && !empty($_POST['senha'])) {// Check if the form is submitted and fields are not empty
        include_once('config.php');
        $nome = $_POST['nome']; 
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM adm WHERE nome = ? AND senha = ?";// Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome, $senha);// Bind the parameters to the SQL query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows < 1) {// If no user found, unset session variables and redirect to login page
            unset($_SESSION['nome']);
            unset($_SESSION['senha']);
            header('Location: http://localhost/controle_combustivel/estoque_RVC/index.php');
        } else {
            $user_data = $result->fetch_assoc();
            $_SESSION['user_id'] = $user_data['id']; // Armazena o user_id na sessão
            $_SESSION['nome'] = $user_data['nome'];// Armazena o nome na sessão
            $_SESSION['senha'] = $user_data['senha'];// Armazena a senha na sessão
            header('Location: painel.php');// Redirect to produtos.php
            exit();
        }
    } else {
        header('Location: index.php');
    }
?>
