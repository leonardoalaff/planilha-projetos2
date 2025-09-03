<?php
$arquivo = "projetos.json";
$projetos = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido = $_POST['pedido'];
    $campo = $_POST['campo'];
    $valor = $_POST['valor'];

    foreach ($projetos as &$proj) {
        if ($proj['pedido'] == $pedido) {
            $proj[$campo] = $valor;
            break;
        }
    }

    file_put_contents($arquivo, json_encode($projetos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "OK";
}
