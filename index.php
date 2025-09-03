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

// Paginação
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$porPagina = 7;
$total = count($projetos);
$paginas = $total > 0 ? ceil($total / $porPagina) : 1;
$inicio = ($pagina - 1) * $porPagina;
$lista = array_slice($projetos, $inicio, $porPagina);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel de Projetos</title>
  <link rel="stylesheet" href="style.css">
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
      <h1>Projetos em andamento</h1>
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
    </form>

    <!-- PAGINAÇÃO -->
    <div class="paginacao">
      <?php for ($i=1; $i <= $paginas; $i++): ?>
        <a href="?pagina=<?= $i ?>" class="<?= $i == $pagina ? 'ativo' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </main>

  <!-- MODAL DE ADIÇÃO -->
  <div class="modal" id="modal">
    <div class="modal-content">
      <span class="close" id="btnClose">&times;</span>
      <h2>Adicionar Projeto</h2>
      <form method="POST" action="salvar.php">
        <input type="text" name="pedido" placeholder="Pedido" required>
        <input type="text" name="cliente" placeholder="Cliente" required>
        <input type="text" name="projhc" placeholder="Proj. HC" required>
        <input type="date" name="entrega" required>
        <input type="number" name="quantidade" placeholder="Quantidade" required>
        <input type="text" name="unidade" placeholder="Unidade" required>
        <input type="text" name="status" placeholder="Status" required>
        <input type="text" name="responsavel" placeholder="Responsável" required>
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
