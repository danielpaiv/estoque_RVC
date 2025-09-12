<?php
    session_start();

    include_once('conexao.php');

    if (isset($_POST['submit']) && !empty($_POST['nome']) && !empty($_POST['senha'])) {// Check if the form is submitted and fields are not empty
        include_once('config.php');
        $nome = $_POST['nome']; 
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM usuarios WHERE nome = ? AND senha = ?";// Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome, $senha);// Bind the parameters to the SQL query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows < 1) {// If no user found, unset session variables and redirect to login page
            unset($_SESSION['nome']);
            unset($_SESSION['senha']);
            header('Location: index.php');
        } else {
            $user_data = $result->fetch_assoc();
            //$_SESSION['user_id'] = $user_data['id']; // Armazena o user_id na sessão
            $_SESSION['nome'] = $user_data['nome'];
            $_SESSION['senha'] = $user_data['senha'];
            header('Location: formulario_estoque.php');// Redirect to produtos.php
            exit();
        }
    } else {
        header('Location: index.php');
    }
?>
