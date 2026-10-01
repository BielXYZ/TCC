<?php
$servidor = "localhost";
$usuario_db = "root";
$senha_db = "";
$banco = "alunos";

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco;charset=utf8", $usuario_db, $senha_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rm = trim($_POST['RM'] ?? '');
    $nome = trim($_POST['nome'] ?? '');

    if (!empty($nome) && preg_match('/^[0-9]{5}$/', $rm)) {
        
        // 1. Verificação separada com seu próprio statement
        $sql_verifica = "SELECT * FROM nome_e_rm WHERE RM = :rm LIMIT 1";
        $stmt_v = $pdo->prepare($sql_verifica);
        $stmt_v->bindValue(':rm', $rm, PDO::PARAM_INT);
        $stmt_v->execute();
        $usuario = $stmt_v->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            echo "Login realizado com sucesso! Bem-vindo de volta, " . htmlspecialchars($usuario['Nome']) . "! <a href='../Login.html'>Voltar</a>";
        } else {
            // 2. Inserção separada com seu próprio statement
            $sql_insere = "INSERT INTO nome_e_rm (Nome, RM) VALUES (:nome, :rm)";
            $stmt_i = $pdo->prepare($sql_insere);
            $stmt_i->bindValue(':nome', $nome);
            $stmt_i->bindValue(':rm', $rm, PDO::PARAM_INT);

            if ($stmt_i->execute()) {
                echo "Primeiro acesso! Cadastro realizado com sucesso! <a href='../Login.html'>Voltar</a>";
            } else {
                echo "Erro ao cadastrar. <a href='../Login.html'>Voltar</a>";
            }
        }

    } else {
        echo "Digite um RM válido com exatamente 5 números e preencha o nome. <a href='../Login.html'>Voltar</a>";
    }
}
?>