<!-- nova_conta.php -->

<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoUsuario = $_POST['usuario'] ?? '';
    $novaSenha   = $_POST['senha'] ?? '';

    if ($novoUsuario && $novaSenha) {
        $usuarios = json_decode(file_get_contents("usuarios.json"), true);

        // verificar se já existe
        foreach ($usuarios as $u) {
            if ($u['usuario'] === $novoUsuario) {
                header("Location: login.php?msg=usuario_existe");
                exit;
            }
        }

        $usuarios[] = [
            "usuario" => $novoUsuario,
            "senha" => $novaSenha,
            "darkmode" => false
        ];
        file_put_contents("usuarios.json", json_encode($usuarios, JSON_PRETTY_PRINT));

        // volta pro login com mensagem de sucesso
        header("Location: login.php?msg=sucesso");
        exit;
    }
}
