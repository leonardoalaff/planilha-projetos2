<!-- salvar_darkmode.php -->

<?php
session_start();

// só usuários autenticados podem alterar
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado']);
    exit;
}

$darkmode = isset($_POST['darkmode']) && ($_POST['darkmode'] === '1' || $_POST['darkmode'] === 'true');

$usuariosFile = 'usuarios.json';
$usuarios = file_exists($usuariosFile) ? json_decode(file_get_contents($usuariosFile), true) : [];

$updated = false;
foreach ($usuarios as &$u) {
    if (isset($u['usuario']) && $u['usuario'] === $_SESSION['usuario']) {
        $u['darkmode'] = $darkmode;
        $_SESSION['darkmode'] = $darkmode; // atualiza a sessão também
        $updated = true;
        break;
    }
}
unset($u);

if ($updated) {
    file_put_contents($usuariosFile, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Usuário não encontrado']);
}
