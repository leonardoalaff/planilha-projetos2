// --- Abrir modal ao clicar no botão imprimir ---
function imprimirSelecionados() {
  const linhasSelecionadas = document.querySelectorAll('tbody tr input[type="checkbox"]:checked');
  if (linhasSelecionadas.length === 0) {
    alert("Nenhuma linha selecionada para impressão!");
    return;
  }
  document.getElementById("printModal").style.display = "flex";
}

// --- Botão cancelar ---
document.getElementById("cancelarImpressao").addEventListener("click", () => {
  document.getElementById("printModal").style.display = "none";
});

// --- Botão confirmar ---
document.getElementById("confirmarImpressao").addEventListener("click", () => {
  const tabelaOriginal = document.querySelector('.table-scroll table');
  const linhasSelecionadas = tabelaOriginal.querySelectorAll('tbody tr input[type="checkbox"]:checked');

  // Pegar colunas escolhidas (sem subtrair 1!)
  const colunasEscolhidas = Array.from(
    document.querySelectorAll('#printColumnsForm input[name="colunas"]:checked')
  ).map(cb => parseInt(cb.value));

  // Criar tabela temporária
  const tabelaTemp = document.createElement('table');

  // Cabeçalho
  const theadClone = tabelaOriginal.querySelector('thead').cloneNode(true);
  theadClone.querySelectorAll('th').forEach((th, idx) => {
    if (!colunasEscolhidas.includes(idx)) th.remove();
    else {
      const cb = th.querySelector('input'); // remove checkbox se tiver
      if (cb) cb.remove();
    }
  });
  tabelaTemp.appendChild(theadClone);

  // Linhas selecionadas
  const tbodyTemp = document.createElement('tbody');
  linhasSelecionadas.forEach(input => {
    const trClone = input.closest('tr').cloneNode(true);
    trClone.querySelectorAll('td').forEach((td, idx) => {
      if (!colunasEscolhidas.includes(idx)) td.remove();
      else if (idx === 0) td.innerHTML = ""; // limpa a checkbox da primeira coluna
    });
    tbodyTemp.appendChild(trClone);
  });
  tabelaTemp.appendChild(tbodyTemp);

  // Abrir janela de impressão
  const win = window.open('', '', 'width=900,height=600');
  win.document.write('<html><head><title>Imprimir Projetos</title>');
  win.document.write('<style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #333;padding:6px;text-align:left;}th{background:#eee;}</style>');
  win.document.write('</head><body>');
  win.document.write(tabelaTemp.outerHTML);
  win.document.write('</body></html>');
  win.document.close();
  win.print();

  // Fechar modal
  document.getElementById("printModal").style.display = "none";
});
