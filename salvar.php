<!-- salvar.php -->

<?php
$arquivo = "projetos.json";
$projetos = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo = [
    "id" => uniqid(),  // <-- id único
    "ultimaalteracao" => $_POST['ultimaalteracao'],
    "pedido" => $_POST['pedido'],
    "cliente" => $_POST['cliente'],
    "projhc" => $_POST['projhc'],
    "entrega" => $_POST['local_entrega'],
    "quantidade" => $_POST['quantidade'],
    "unidade" => $_POST['unidade'],
    "status" => $_POST['status'],
    "responsavel" => $_POST['responsavel'],
    "atualizacao" => $_POST['atualizacao'] ?? '',
    "comercial" => $_POST['comercial'] ?? '',
    "detalhamento" => $_POST['detalhamento'] ?? '',
    "producao" => $_POST['producao'] ?? '',
    "descricao" => $_POST['descricao'] ?? '',
    "munsell" => $_POST['munsell'] ?? '',
    "pintura" => $_POST['pintura'] ?? '',
    "comprador" => $_POST['comprador'] ?? '',
    "local_entrega" => $_POST['local_entrega'] ?? ''
];

    $projetos[] = $novo;
    file_put_contents($arquivo, json_encode($projetos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

header("Location: index.php");
exit;
