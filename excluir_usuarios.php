<?php
header('Content-Type: application/json');

if(!isset($_POST['usuarios'])) {
    echo json_encode(['status' => 'erro', 'msg' => 'Nenhum usuário selecionado']);
    exit;
}

$usuariosParaExcluir = $_POST['usuarios'];

// lê o arquivo JSON
$usuarios = json_decode(file_get_contents('usuarios.json'), true);

// filtra removendo os usuários selecionados (exceto adminhc)
$usuarios = array_filter($usuarios, fn($u) => 
    $u['usuario'] !== 'adminhc' && !in_array($u['usuario'], $usuariosParaExcluir)
);

// salva novamente
file_put_contents('usuarios.json', json_encode(array_values($usuarios), JSON_PRETTY_PRINT));

echo json_encode(['status' => 'ok']);
