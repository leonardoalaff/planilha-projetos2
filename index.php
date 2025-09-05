<?php
// Carregar lista de projetos de um arquivo JSON
$arquivo = "projetos.json";
$projetos = [];

// Pegando valores do GET
$filtroProjeto = $_GET['projeto'] ?? '';
$filtroData = $_GET['data'] ?? '';
$filtroNome = $_GET['nome'] ?? '';


if (file_exists($arquivo)) {
    $conteudo = file_get_contents($arquivo);
    $projetos = !empty($conteudo) ? json_decode($conteudo, true) : [];
}

// Garantir que $projetos seja sempre array
if (!is_array($projetos)) {
    $projetos = [];
}

$lista = array_filter($projetos, function($proj) use ($filtroProjeto, $filtroData, $filtroNome) {
    $passou = true;

    if ($filtroProjeto !== '') {
        $passou = $passou && stripos($proj['pedido'], $filtroProjeto) !== false;
    }

    if ($filtroData !== '') {
        $passou = $passou && ($proj['entrega'] === $filtroData);
    }

    if ($filtroNome !== '') {
        $passou = $passou && stripos($proj['cliente'], $filtroNome) !== false;
    }

    return $passou;
});

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel de Projetos</title>
  <link rel="stylesheet" href="style4.css">
  
</head>
<body>
  <!-- MENU LATERAL -->
  <aside class="sidebar">
    <h2>PAINEL</h2>
    <a href="#" class="add" id="abrirModal">+ Adicionar Projeto</a>
  </aside>

  <!-- CONTEÚDO PRINCIPAL -->
  <main>
    <header>
      <h1 class="t-projetos-em-andamento">Projetos em andamento</h1>
      <form method="GET" class="search">
        <input type="text" name="projeto" placeholder="Projeto" value="<?= htmlspecialchars($filtroProjeto) ?>">
<input type="date" name="data" value="<?= htmlspecialchars($filtroData) ?>">
<input type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($filtroNome) ?>">

        <button class="btn-pesquisar" type="submit">Pesquisar</button>
      </form>
    </header>


    <!-- TABELA DE PROJETOS -->
    <form class="table-container" method="POST" action="processa.php">
  <button type="submit" class="excluir">Excluir selecionados</button>
  <button class="imprimir" type="button" onclick="imprimirSelecionados()">🖨️ Imprimir</button>
  <button type="button" class="expandir" id="btnExpandir">⛶</button>


  <!-- Barra de scroll horizontal no topo -->
  <div class="scroll-top">
    <div class="scroll-inner"></div>
  </div>

  <!-- wrapper com scroll onde apenas o tbody vai rolar -->
  <div class="table-scroll">
    <table>
      <thead>
        <tr>
          <th><input class="checkbox" type="checkbox" onclick="toggleAll(this)"></th>
          <th>Pedido</th>
          <th>Cliente</th>
          <th>Proj. HC</th>
          <th>Entrega</th>
          <th>Quantid.</th>
          <th>Un.</th>
          <th>Status</th>
          <th>Responsável</th>
          <th>Atualização</th>
          <th>Comercial</th>
          <th>Detalhamento</th>
          <th>Produção</th>
          <th>Descrição e Observações</th>
          <th>Munsell Color</th>
          <th>Pintura</th>
          <th>Comprador</th>
          <th>Local de Entrega</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($lista)): ?>
          <?php foreach ($lista as $proj): ?>
            <tr data-id="<?= $proj['id'] ?>">
              <td>
                <input type="checkbox" name="selecionados[]" value="<?= isset($proj['id']) ? $proj['id'] : uniqid() ?>">
              </td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="pedido"><?= htmlspecialchars($proj['pedido']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="cliente"><?= htmlspecialchars($proj['cliente']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="projhc"><?= htmlspecialchars($proj['projhc']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="entrega"><?= htmlspecialchars($proj['entrega']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="quantidade"><?= htmlspecialchars($proj['quantidade']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="unidade"><?= htmlspecialchars($proj['unidade']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="status"><?= htmlspecialchars($proj['status']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="responsavel"><?= htmlspecialchars($proj['responsavel']) ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="atualizacao"><?= htmlspecialchars($proj['atualizacao'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="comercial"><?= htmlspecialchars($proj['comercial'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="detalhamento"><?= htmlspecialchars($proj['detalhamento'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="producao"><?= htmlspecialchars($proj['producao'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="descricao"><?= htmlspecialchars($proj['descricao'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="munsell"><?= htmlspecialchars($proj['munsell'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="pintura"><?= htmlspecialchars($proj['pintura'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="comprador"><?= htmlspecialchars($proj['comprador'] ?? '') ?></td>
              <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="local_entrega"><?= htmlspecialchars($proj['local_entrega'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="18">Nenhum projeto cadastrado ainda</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</form>

  </main>

  <div class="modal" id="modal">
  <div class="modal-content">
    <!-- Remova esta linha: -->
    <!-- <span class="close" id="btnClose">&times;</span> -->

    <h2>Novo Projeto</h2>
    <form method="POST" action="salvar.php">
      <input type="text" name="pedido" placeholder="Pedido" required>
      <input type="text" name="cliente" placeholder="Cliente" required>
      <input type="text" name="projhc" placeholder="Proj. HC">
      <input type="date" name="entrega">
      <input type="number" name="quantidade" placeholder="Quantidade">
      <input type="text" name="unidade" placeholder="Unidade">
      <input type="text" name="status" placeholder="Status">
      <input type="text" name="responsavel" placeholder="Responsável">
      <input type="text" name="atualizacao" placeholder="Atualização">
      <input type="text" name="comercial" placeholder="Comercial">
      <input type="text" name="detalhamento" placeholder="Detalhamento">
      <input type="text" name="producao" placeholder="Produção">
      <textarea name="descricao" placeholder="Descrição e Observações"></textarea>
      <input type="text" name="munsell" placeholder="Munsell Color">
      <input type="text" name="pintura" placeholder="Pintura">
      <input type="text" name="comprador" placeholder="Comprador">
      <input type="text" name="local_entrega" placeholder="Local de Entrega">
      <button type="submit">Salvar</button>
    </form>
  </div>
</div>



  <!-- carregar script externo -->
  <script src="script2.js"></script>
</body>
</html>
