<?php
header('Content-Type: application/json');

// lê arquivo JSON
$usuarios = json_decode(file_get_contents('usuarios.json'), true);

// remove adminhc da lista (não pode excluir)
$usuarios = array_filter($usuarios, fn($u) => $u['usuario'] !== 'adminhc');

echo json_encode(array_values($usuarios));
