<!-- alterar_usuario.php -->

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
        $usuariosFile = 'usuarios.json';
        $usuarios = file_exists($usuariosFile) ? json_decode(file_get_contents($usuariosFile), true) : [];

        foreach ($usuarios as &$u) {
            if ($u['usuario'] === $_SESSION['usuario']) {
                $u['usuario'] = $novoUsuario;
                $u['senha'] = $novaSenha;
                $_SESSION['usuario'] = $novoUsuario; // atualiza a sessão
                break;
            }
        }
        unset($u);

        file_put_contents($usuariosFile, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        header("Location: index.php");
        exit;
    }
}
