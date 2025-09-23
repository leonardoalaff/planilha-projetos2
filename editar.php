<!-- editar.php -->

<?php
$arquivo = "projetos.json";
$projetos = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $campo = $_POST['campo'] ?? '';
    $valor = $_POST['valor'] ?? '';
    $ultimaalteracao = $_POST['ultimaalteracao'] ?? '';

    if ($id && $campo) {
        foreach ($projetos as &$proj) {
            if ($proj['id'] === $id) {
                $proj[$campo] = $valor;
                if ($ultimaalteracao) $proj['ultimaalteracao'] = $ultimaalteracao;
                break;
            }
        }
        unset($proj);

        file_put_contents($arquivo, json_encode($projetos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    echo "OK";
}

