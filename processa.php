<?php
$arquivo = "projetos.json";
$projetos = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

// garante que todos os projetos tenham id
foreach ($projetos as &$proj) {
    if (!isset($proj['id'])) {
        $proj['id'] = uniqid();
    }
}
unset($proj);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selecionados'])) {
    $selecionados = $_POST['selecionados'];

    $projetos = array_filter($projetos, function($proj) use ($selecionados) {
        return !in_array($proj['id'], $selecionados);
    });

    file_put_contents($arquivo, json_encode(array_values($projetos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

header("Location: index.php");
exit;
