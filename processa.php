<?php
$arquivo = "projetos.json";
$projetos = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selecionados'])) {
    $selecionados = $_POST['selecionados'];

    $projetos = array_filter($projetos, function($proj) use ($selecionados) {
        return !in_array($proj['pedido'], $selecionados);
    });

    file_put_contents($arquivo, json_encode(array_values($projetos), JSON_PRETTY_PRINT));
}

header("Location: index.php");
exit;
