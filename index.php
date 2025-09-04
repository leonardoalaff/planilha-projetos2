<?php
// Carregar lista de projetos de um arquivo JSON
$arquivo = "projetos.json";
$projetos = [];

if (file_exists($arquivo)) {
    $conteudo = file_get_contents($arquivo);
    $projetos = !empty($conteudo) ? json_decode($conteudo, true) : [];
}

// Garantir que $projetos seja sempre array
if (!is_array($projetos)) {
    $projetos = [];
}

$lista = $projetos; // mostrar todos sem paginação

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel de Projetos</title>
  <link rel="stylesheet" href="style2.css">
</head>
<body>
  <!-- MENU LATERAL -->
  <aside class="sidebar">
    <h2>PAINEL</h2>
    <a href="#" class="add" id="btnAdd">+ Adicionar projeto</a>
  </aside>

  <!-- CONTEÚDO PRINCIPAL -->
  <main>
    <header>
      <h1 class="t-projetos-em-andamento">Projetos em andamento</h1>
      <form method="GET" class="search">
        <input type="text" name="projeto" placeholder="Projeto">
        <input type="date" name="data">
        <input type="text" name="nome" placeholder="Nome">
        <button type="submit">Pesquisar</button>
      </form>
    </header>

    <!-- TABELA DE PROJETOS -->
    <form class="table-container" method="POST" action="processa.php">
  <button type="submit" class="excluir">Excluir selecionados</button>

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
            <tr>
              <td><input type="checkbox" name="selecionados[]" value="<?= $proj['pedido'] ?>"></td>

              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="pedido"><?= $proj['pedido'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="cliente"><?= $proj['cliente'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="projhc"><?= $proj['projhc'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="entrega"><?= $proj['entrega'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="quantidade"><?= $proj['quantidade'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="unidade"><?= $proj['unidade'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="status"><?= $proj['status'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="responsavel"><?= $proj['responsavel'] ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="atualizacao"><?= $proj['atualizacao'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="comercial"><?= $proj['comercial'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="detalhamento"><?= $proj['detalhamento'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="producao"><?= $proj['producao'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="descricao"><?= $proj['descricao'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="munsell"><?= $proj['munsell'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="pintura"><?= $proj['pintura'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="comprador"><?= $proj['comprador'] ?? '' ?></td>
              <td contenteditable="true" data-pedido="<?= $proj['pedido'] ?>" data-campo="local_entrega"><?= $proj['local_entrega'] ?? '' ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="18">Nenhum projeto cadastrado ainda</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</form>

    </div>
  </div>

<script>
// MODAL
const modal = document.getElementById('modal');
const btnAdd = document.getElementById('btnAdd');
const btnClose = document.getElementById('btnClose');

btnAdd.onclick = () => modal.style.display = 'flex';
btnClose.onclick = () => modal.style.display = 'none';
window.onclick = e => { if (e.target == modal) modal.style.display = 'none'; }

// EDIÇÃO INLINE
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("td[contenteditable]").forEach(cell => {
    cell.addEventListener("blur", salvarEdicao);
    cell.addEventListener("keydown", e => {
      if (e.key === "Enter") {
        e.preventDefault(); 
        cell.blur();
      }
    });
  });
});

function salvarEdicao(e) {
  const cell = e.target;
  const pedido = cell.dataset.pedido;
  const campo = cell.dataset.campo;
  const valor = cell.innerText.trim();

  fetch("editar.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `pedido=${encodeURIComponent(pedido)}&campo=${encodeURIComponent(campo)}&valor=${encodeURIComponent(valor)}`
  }).then(res => res.text())
    .then(resp => console.log("Resposta:", resp))
    .catch(err => console.error("Erro:", err));
}

// TOGGLE CHECKBOX
function toggleAll(master) {
  document.querySelectorAll('input[name="selecionados[]"]').forEach(cb => cb.checked = master.checked);
}

// Destacar linha ao marcar checkbox
document.querySelectorAll('input[name="selecionados[]"]').forEach(cb => {
  cb.addEventListener('change', () => {
    if (cb.checked) {
      cb.closest('tr').classList.add('selecionado');
    } else {
      cb.closest('tr').classList.remove('selecionado');
    }
  });
});

</script>

</body>
</html>
