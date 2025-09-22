<!-- index.php -->

<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}

// define variável para uso no HTML
$isDark = !empty($_SESSION['darkmode']) ? true : false;
?>

 <?php
// Carregar lista de projetos de um arquivo JSON
$arquivo = "projetos.json";

// Ler projetos
$projetos = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];
if (!is_array($projetos)) $projetos = [];

// Pegando filtros
$filtroProjeto = $_GET['projeto'] ?? '';
$filtroData    = $_GET['data'] ?? '';
$filtroNome    = $_GET['nome'] ?? '';

// Filtrar lista
$lista = array_filter($projetos, function($proj) use ($filtroProjeto, $filtroData, $filtroNome) {
    $passou = true;
    if ($filtroProjeto !== '') $passou = $passou && stripos($proj['projhc'], $filtroProjeto) !== false;
    if ($filtroData !== '')    $passou = $passou && ($proj['entrega'] === $filtroData);
    if ($filtroNome !== '')    $passou = $passou && stripos($proj['cliente'], $filtroNome) !== false;
    return $passou;
});
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel de Projetos</title>
  <link rel="stylesheet" href="style10.css">
</head>
<body class="<?= $isDark ? 'dark-mode' : '' ?>">
  <!-- MENU LATERAL -->
  <aside class="sidebar">
    <div class="bem-vindo">
    Bem-vindo, <?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuário') ?>!
  </div>
    <h2> <div class="icone-painel"></div> PAINEL</h2>
    <!-- Mensagem de boas-vindas -->
  
    <a href="#" class="add" id="abrirModal"><div class="icone-adicionar"></div> Adicionar Projeto</a>

    <abbr class="switch" title="Modo Dark"><label class="switch">
  <input type="checkbox" id="toggleDarkMode"> <?= $isDark ? '' : '' ?> >
  <span class="slider"></span>
</label></abbr>

<button id="btnConfig"><div class="icone-config"></div> Configurações</button>

  </aside>

  <!-- CONTEÚDO PRINCIPAL -->
  <main>
    <header>
      <h1 class="t-projetos-em-andamento">Projetos em andamento
        <span></span>
      </h1>

      <form method="GET" class="search">
        <input type="text" name="projeto" placeholder="Projeto" value="<?= htmlspecialchars($filtroProjeto) ?>">
        <input type="date" name="data" value="<?= htmlspecialchars($filtroData) ?>">
        <input type="text" name="nome" placeholder="Caractere" value="<?= htmlspecialchars($filtroNome) ?>">
        <button class="btn-pesquisar" type="submit">Pesquisar</button>
      </form>
    </header>

    <!-- TABELA DE PROJETOS -->
    <form class="table-container" id="tabelaProjetos" method="POST" action="processa.php">
      <div class="botoes">
        <button type="submit" class="excluir btn-excluir">Excluir</button>
        <button class="imprimir btn-imprimir" type="button" id="btnPrint">
          <span class="icone-impressora">🖨️</span> Imprimir
          <span class="papel"></span>
        </button>
        <button type="button" class="expandir" id="btnExpandir">
          <abbr title="Expandir" class="abbr-expandir">⛶</abbr>
        </button>
      </div>

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
              <th>Última alteração<button class="filtro-btn" data-col="1">▼</button></th>
              <th>Pedido<button class="filtro-btn" data-col="2">▼</button></th>
              <th>Cliente<button class="filtro-btn" data-col="3">▼</button></th>
              <th>Proj. HC<button class="filtro-btn" data-col="4">▼</button></th>
              <th>Entrega<button class="filtro-btn" data-col="5">▼</button></th>
              <th>Quantid.<button class="filtro-btn" data-col="6">▼</button></th>
              <th>Un.<button class="filtro-btn" data-col="7">▼</button></th>
              <th>Status<button class="filtro-btn" data-col="8">▼</button></th>
              <th>Responsável <button class="filtro-btn" data-col="9">▼</button></th>
              <th>Atualização<button class="filtro-btn" data-col="10">▼</button></th>
              <th>Comercial<button class="filtro-btn" data-col="11">▼</button></th>
              <th>Detalhamento<button class="filtro-btn" data-col="12">▼</button></th>
              <th>Produção<button class="filtro-btn" data-col="13">▼</button></th>
              <th>Descrição e Observações<button class="filtro-btn" data-col="14">▼</button></th>
              <th>Munsell Color<button class="filtro-btn" data-col="15">▼</button></th>
              <th>Pintura <button class="filtro-btn" data-col="16">▼</button></th>
              <th>Comprador <button class="filtro-btn" data-col="17">▼</button></th>
              <th>Local de Entrega <button class="filtro-btn" data-col="18">▼</button></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($lista)): ?>
              <?php foreach ($lista as $proj): ?>
                <tr data-id="<?= $proj['id'] ?>">
                  <td><input type="checkbox" name="selecionados[]" value="<?= isset($proj['id']) ? $proj['id'] : uniqid() ?>"></td>
                  <?php
                  $campos = ['ultimaalteracao','pedido','cliente','projhc','entrega','quantidade','unidade','status','responsavel','atualizacao','comercial','detalhamento','producao','descricao','munsell','pintura','comprador','local_entrega'];
                  foreach ($campos as $campo):
                  ?>
                    <td contenteditable="true" data-pedido="<?= htmlspecialchars($proj['pedido']) ?>" data-campo="<?= $campo ?>"><?= htmlspecialchars($proj[$campo] ?? '') ?></td>
                  <?php endforeach; ?>
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

  <!-- MODAL NOVO PROJETO -->
  <div class="modal" id="modal">
    <div class="modal-content">
      <h2>Novo Projeto</h2>
      <form method="POST" action="salvar.php">
        <input type="text" name="pedido" placeholder="Pedido">
        <input type="text" name="cliente" placeholder="Cliente">
        <input type="text" name="projhc" placeholder="Proj. HC">
        <input type="date" name="entrega">
        <input type="text" name="quantidade" placeholder="Quantidade">
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
        <button type="submit" id="btn-salvar">Salvar</button>
      </form>
    </div>
  </div>

  <!-- Modal de impressão -->
  <div id="printModal" class="print-modal" style="display:none;">
    <div class="print-modal-content" role="dialog" aria-modal="true" aria-labelledby="printModalTitle">
      <h3 id="printModalTitle">Escolha as colunas para imprimir</h3>
      <form id="printColumnsForm"></form>
      <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
        <button type="button" id="selecionarTodasCols">Selecionar todas</button>
        <button type="button" id="desselecionarTodasCols">Desmarcar todas</button>
        <button type="button" id="confirmarImpressao">Imprimir</button>
        <button type="button" id="cancelarImpressao">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- Modal de Configurações -->
<!-- Modal de Configurações -->
<div id="configModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Configurações</h2>
    <button id="btnNovaConta">Criar uma nova conta</button>
    <button id="btnAlterarUsuario">Alterar usuário e senha</button>
    <a href="logout.php" class="btn-sair">Sair da conta</a>
  </div>
</div>


<!-- Modal Criar Nova Conta -->
<div id="novaContaModal" class="modal">
  <div class="modal-content">
    <span class="closeNova">&times;</span>
    <h2>Criar Nova Conta</h2>
    <form method="POST" action="nova_conta.php">
      <input type="text" name="usuario" placeholder="Novo usuário" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <button type="submit">Salvar</button>
    </form>
  </div>
</div>

<!-- Modal Alterar Usuário e Senha -->
<div id="alterarUsuarioModal" class="modal">
  <div class="modal-content">
    <span class="closeAlterar">&times;</span>
    <h2>Alterar Usuário e Senha</h2>
    <form method="POST" action="alterar_usuario.php">
      <input type="text" name="usuario" placeholder="Novo usuário" required>
      <input type="password" name="senha" placeholder="Nova senha" required>
      <button type="submit">Salvar</button>
    </form>
  </div>
</div>


<style>
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
}
.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  width: 300px;
  margin: 10% auto;
  text-align: center;
}
.modal-content input {
  width: 90%;
  padding: 8px;
  margin: 5px 0;
}
.modal-content button {
  margin-top: 10px;
  padding: 10px;
  width: 100%;
}
.close, .closeNova {
  float: right;
  cursor: pointer;
  font-size: 20px;
}
</style>

<script>
document.getElementById("toggleDarkMode").addEventListener("change", function() {
  const darkMode = this.checked ? 1 : 0;

  fetch("salvar_darkmode.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "darkmode=" + darkMode
  }).then(() => {
    document.body.classList.toggle("dark", darkMode === 1);
  });
});
</script>

<style>
body.dark {
  background: #121212;
  color: #eee;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Botão e modal de Alterar Usuário
  const btnAlterarUsuario = document.getElementById("btnAlterarUsuario");
  const alterarUsuarioModal = document.getElementById("alterarUsuarioModal");
  const closeAlterar = document.querySelector(".closeAlterar");

  if (btnAlterarUsuario && alterarUsuarioModal) {
    btnAlterarUsuario.addEventListener("click", () => {
      alterarUsuarioModal.style.display = "flex";
    });
  }

  if (closeAlterar && alterarUsuarioModal) {
    closeAlterar.addEventListener("click", () => {
      alterarUsuarioModal.style.display = "none";
    });
  }

  // Fecha clicando fora do modal
  window.addEventListener("click", e => {
    if (e.target === alterarUsuarioModal) {
      alterarUsuarioModal.style.display = "none";
    }
  });
});
</script>


<script>
  const usuarioLogado = "<?= $_SESSION['usuario'] ?? '' ?>";
</script>
<script src="script.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="script6.js"></script>
</body>
</html>
