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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Excluir projetos selecionados
    if (!empty($_POST['selecionados']) && is_array($_POST['selecionados'])) {
        $selecionados = $_POST['selecionados'];
        $projetos = array_filter($projetos, function($proj) use ($selecionados) {
            return !in_array($proj['id'], $selecionados);
        });
        file_put_contents($arquivo, json_encode(array_values($projetos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header('Location: index.php'); // volta para a página principal
        exit;
    }

    // Atualizar célula editada
    $pedido      = $_POST['pedido'] ?? '';
    $campo       = $_POST['campo'] ?? '';
    $valor       = $_POST['valor'] ?? '';
    $ultimaalteracao = $_POST['ultimaalteracao'] ?? '';

    if ($pedido && $campo) {
        foreach ($projetos as &$proj) {
            if ($proj['pedido'] === $pedido) {
                $proj[$campo] = $valor;
                if ($ultimaalteracao) $proj['ultimaalteracao'] = $ultimaatualizacao;
                break;
            }
        }
        unset($proj);
        file_put_contents($arquivo, json_encode($projetos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    header('Location: index.php'); // evita mostrar "OK"
    exit;
}
