// script.js

// --- Função para salvar edição inline ---
function salvarEdicao(e) {
  const cell = e.target;
  const pedido = cell.dataset.pedido;
  const campo = cell.dataset.campo;
  const valor = cell.innerText.trim();

  if (!pedido || !campo) return;

  fetch("editar.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `pedido=${encodeURIComponent(pedido)}&campo=${encodeURIComponent(campo)}&valor=${encodeURIComponent(valor)}`
  })
  .then(res => res.text())
  .then(text => console.log("editar.php:", text))
  .catch(err => console.error("Erro ao salvar edição:", err));
}

// --- Marcar/desmarcar todos ---
window.toggleAll = function(master) {
  document.querySelectorAll('input[name="selecionados[]"]').forEach(cb => {
    cb.checked = master.checked;
    cb.dispatchEvent(new Event('change', { bubbles: true }));
  });
};

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modal");
  const abrirModal = document.getElementById("abrirModal");
  const fecharModal = document.getElementById("fecharModal");

  if (abrirModal && modal) {
    abrirModal.addEventListener("click", ev => {
      ev.preventDefault();
      modal.style.display = "flex";
      modal.setAttribute('aria-hidden', 'false');
    });
  }
  if (fecharModal && modal) {
    fecharModal.addEventListener("click", () => {
      modal.style.display = "none";
      modal.setAttribute('aria-hidden', 'true');
    });
  }

  window.addEventListener("click", ev => {
    if (modal && ev.target === modal) {
      modal.style.display = "none";
      modal.setAttribute('aria-hidden', 'true');
    }
  });

  window.addEventListener("keydown", ev => {
    if (ev.key === "Escape" && modal && modal.style.display === "flex") {
      modal.style.display = "none";
      modal.setAttribute('aria-hidden', 'true');
    }
  });

  // --- Edição inline ---
  document.querySelectorAll("td[contenteditable]").forEach(cell => {
    cell.addEventListener("blur", salvarEdicao);
    cell.addEventListener("keydown", e => {
      if (e.key === "Enter") {
        e.preventDefault();
        cell.blur();
      }
    });
  });

  // --- Destaque de linha ---
  document.addEventListener("change", e => {
    const el = e.target;
    if (el && el.matches('input[name="selecionados[]"]')) {
      const tr = el.closest('tr');
      if (tr) tr.classList.toggle('selecionado', el.checked);
    }
  });

  document.querySelectorAll('input[name="selecionados[]"]').forEach(cb => {
    const tr = cb.closest('tr');
    if (tr) tr.classList.toggle('selecionado', cb.checked);
  });
});

// --- Scroll horizontal ---
const scrollTop = document.querySelector('.scroll-top');
const scrollInner = document.querySelector('.scroll-inner');
const tableScroll = document.querySelector('.table-scroll');
const table = tableScroll.querySelector('table');

function atualizarScrollTop() {
  scrollInner.style.width = table.scrollWidth + 'px';
}

scrollTop.addEventListener('scroll', () => {
  tableScroll.scrollLeft = scrollTop.scrollLeft;
});
tableScroll.addEventListener('scroll', () => {
  scrollTop.scrollLeft = tableScroll.scrollLeft;
});
window.addEventListener('load', atualizarScrollTop);
window.addEventListener('resize', atualizarScrollTop);

// --- Imprimir linhas selecionadas ---
function imprimirSelecionados() {
  const tabelaOriginal = document.querySelector('.table-scroll table');
  const linhasSelecionadas = tabelaOriginal.querySelectorAll('tbody tr input[type="checkbox"]:checked');

  if (linhasSelecionadas.length === 0) {
    alert("Nenhuma linha selecionada para impressão!");
    return;
  }

  // Descobrir quais colunas imprimir
  const colunas = Array.from(tabelaOriginal.querySelectorAll('thead th'));
  const colunasSelecionadas = colunas.map((th, i) => {
    const cb = th.querySelector('input.col-select');
    return cb ? cb.checked : true; // default: true se não tiver checkbox
  });

  // Criar tabela temporária para impressão
  const tabelaTemp = document.createElement('table');

  // Cabeçalho
  const theadClone = tabelaOriginal.querySelector('thead').cloneNode(true);
  theadClone.querySelectorAll('th').forEach((th, idx) => {
    if (!colunasSelecionadas[idx]) th.remove();
    else {
      // remover checkbox do cabeçalho para impressão
      const cb = th.querySelector('input.col-select');
      if (cb) cb.remove();
    }
  });
  tabelaTemp.appendChild(theadClone);

  // Linhas selecionadas
  const tbodyTemp = document.createElement('tbody');
  linhasSelecionadas.forEach(input => {
    const trClone = input.closest('tr').cloneNode(true);
    trClone.querySelectorAll('td').forEach((td, idx) => {
      if (!colunasSelecionadas[idx]) td.remove();
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
}


// --- Expandir tabela ---
const btnExpandir = document.getElementById('btnExpandir');
const tableContainer = document.querySelector('.table-container');
btnExpandir.addEventListener('click', () => {
  tableContainer.classList.toggle('fullscreen');
});

// --- Edição e navegação com setas ---
document.addEventListener("DOMContentLoaded", () => {
  const table = document.querySelector(".table-scroll table");

  table.addEventListener("keydown", e => {
    const cell = e.target;
    if (cell.tagName === "TD" && cell.isContentEditable) {
      let nextCell = null;
      const row = cell.parentElement;
      const colIndex = Array.from(row.children).indexOf(cell);
      const rowIndex = Array.from(row.parentElement.children).indexOf(row);

      if (e.key === "ArrowRight") nextCell = row.cells[colIndex + 1];
      else if (e.key === "ArrowLeft") nextCell = row.cells[colIndex - 1];
      else if (e.key === "ArrowDown") nextCell = row.parentElement.rows[rowIndex + 1]?.cells[colIndex];
      else if (e.key === "ArrowUp") nextCell = row.parentElement.rows[rowIndex - 1]?.cells[colIndex];
      else if (e.key === "Enter") nextCell = row.parentElement.rows[rowIndex + 1]?.cells[colIndex];
      else if (e.key === "Delete") {
        e.preventDefault();
        cell.innerText = "";
        salvarEdicao({target: cell});
      }

      if (nextCell && nextCell.isContentEditable) {
        salvarEdicao({target: cell});
        nextCell.focus();
        const range = document.createRange();
        const sel = window.getSelection();
        range.selectNodeContents(nextCell);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      }
    }
  });
});

// --- Arrastar para selecionar ---
(function() {
  const container = document.querySelector('.table-scroll');
  if (!container) return;

  let isDragging = false;
  let isSelecting = true;
  let lastRowId = null;
  let lastMouseX = 0, lastMouseY = 0;
  let rafId = null;

  const MARGIN = 60;
  const MAX_SPEED = 30;

  function startAutoScrollLoop() {
    if (rafId) return;
    function loop() {
      if (!isDragging) { rafId = null; return; }
      const rect = container.getBoundingClientRect();
      let v = 0, h = 0;

      if (lastMouseY > rect.bottom - MARGIN) v = Math.min(MAX_SPEED, ((lastMouseY - (rect.bottom - MARGIN)) / MARGIN) * MAX_SPEED);
      else if (lastMouseY < rect.top + MARGIN) v = -Math.min(MAX_SPEED, ((rect.top + MARGIN) - lastMouseY) / MARGIN * MAX_SPEED);

      if (lastMouseX > rect.right - MARGIN) h = Math.min(MAX_SPEED, ((lastMouseX - (rect.right - MARGIN)) / MARGIN) * MAX_SPEED);
      else if (lastMouseX < rect.left + MARGIN) h = -Math.min(MAX_SPEED, ((rect.left + MARGIN) - lastMouseX) / MARGIN * MAX_SPEED);

      container.scrollTop += v;
      container.scrollLeft += h;
      rafId = requestAnimationFrame(loop);
    }
    rafId = requestAnimationFrame(loop);
  }

  function stopAutoScrollLoop() {
    if (rafId) cancelAnimationFrame(rafId);
    rafId = null;
  }

  function processPointerAt(x, y) {
    lastMouseX = x; lastMouseY = y;
    const td = document.elementFromPoint(x, y)?.closest('td');
    if (!td || td.cellIndex !== 0) return;

    const tr = td.closest('tr');
    if (!tr) return;

    const rowId = tr.getAttribute('data-id') || tr.rowIndex;
    if (rowId === lastRowId) return;
    lastRowId = rowId;

    const checkbox = td.querySelector('input[type="checkbox"]');
    if (!checkbox) return;

    checkbox.checked = isSelecting;
    tr.classList.toggle('selecionado', isSelecting);
  }

  container.addEventListener('pointerdown', ev => {
    const td = ev.target.closest('td');
    if (!td || td.cellIndex !== 0) return;

    isDragging = true;
    lastRowId = null;

    const checkbox = td.querySelector('input[type="checkbox"]');
    if (!checkbox) return;
    isSelecting = !checkbox.checked;

    const tr = td.closest('tr');
    checkbox.checked = isSelecting;
    tr.classList.toggle('selecionado', isSelecting);

    try { container.setPointerCapture(ev.pointerId); } catch(e) {}
    processPointerAt(ev.clientX, ev.clientY);
    startAutoScrollLoop();
    ev.preventDefault();
  });

  container.addEventListener('pointermove', ev => {
    if (!isDragging) return;
    processPointerAt(ev.clientX, ev.clientY);
  });

  container.addEventListener('pointerup', ev => {
    if (!isDragging) return;
    isDragging = false;
    lastRowId = null;
    stopAutoScrollLoop();
    try { container.releasePointerCapture(ev.pointerId); } catch(e) {}
  });

  document.addEventListener('pointerup', () => {
    if (isDragging) {
      isDragging = false;
      lastRowId = null;
      stopAutoScrollLoop();
    }
  });

})();




document.addEventListener('DOMContentLoaded', () => {
  const table = document.querySelector('.table-scroll table');
  let menuAtivo = null;

  const escapeHtml = s => String(s)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');

  function fecharMenu() {
    if (menuAtivo) { menuAtivo.remove(); menuAtivo = null; }
  }

  function abrirMenu(btn) {
    fecharMenu();
    const th = btn.closest('th');
    if (!th) return;

    // calcula índice da coluna no thead (0 = checkbox, 1 = Pedido, ...)
    const colIndex = Array.from(th.parentElement.children).indexOf(th);

    // coleta valores únicos da coluna (string)
    const valoresSet = new Set();
    table.querySelectorAll('tbody tr').forEach(tr => {
      const td = tr.cells[colIndex];
      valoresSet.add(td ? td.textContent.trim() : '');
    });
    const valores = Array.from(valoresSet);

    // monta menu
    const menu = document.createElement('div');
    menu.className = 'filtro-menu';
    const itens = [];
    itens.push(`<label><input type="checkbox" data-select-all checked> Selecionar todos</label>`);
    valores.forEach(v => {
      const valAttr = v === '' ? '__EMPTY__' : escapeHtml(v);
      const labelText = v === '' ? '(vazio)' : escapeHtml(v);
      itens.push(`<label><input type="checkbox" value="${valAttr}" checked> ${labelText}</label>`);
    });

    menu.innerHTML = `
      <div style="min-width:200px; max-height:160px; overflow:auto;">
        ${itens.join('')}
      </div>
      <div style="display:flex; gap:6px; margin-top:8px;">
        <button type="button" data-apply style="flex:1">Aplicar</button>
        <button type="button" data-cancel style="flex:1">Cancelar</button>
      </div>
    `;

    document.body.appendChild(menu);
    menuAtivo = menu;

    // posiciona abaixo do botão (considera scroll)
    const rect = btn.getBoundingClientRect();
    menu.style.left = `${rect.left + window.scrollX}px`;
    menu.style.top  = `${rect.bottom + window.scrollY}px`;
    menu.style.display = 'block';

    // evita fechamento ao clicar dentro
    menu.addEventListener('click', e => e.stopPropagation());

    // select all
    const selectAll = menu.querySelector('[data-select-all]');
    selectAll.addEventListener('change', (e) => {
      const checked = e.target.checked;
      menu.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        if (cb !== e.target) cb.checked = checked;
      });
    });

    // aplicar
    menu.querySelector('[data-apply]').addEventListener('click', () => {
      const selected = Array.from(menu.querySelectorAll('input[type="checkbox"]'))
        .filter(cb => cb.checked && !cb.hasAttribute('data-select-all'))
        .map(cb => cb.value);

      table.querySelectorAll('tbody tr').forEach(tr => {
        const td = tr.cells[colIndex];
        const v = td ? td.textContent.trim() : '';
        const compare = v === '' ? '__EMPTY__' : v;
        tr.style.display = (selected.length === 0 || selected.includes(compare)) ? '' : 'none';
      });

      fecharMenu();
    });

    // cancelar
    menu.querySelector('[data-cancel]').addEventListener('click', fecharMenu);
  }

  // liga aos botões
  document.querySelectorAll('.filtro-btn').forEach(btn => {
    // garante que seja type="button" mesmo se o HTML não tiver
    btn.setAttribute('type','button');
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      abrirMenu(btn);
    });
  });

  // fecha ao clicar fora
  document.addEventListener('click', (e) => {
    if (menuAtivo && !menuAtivo.contains(e.target)) fecharMenu();
  });

  // fecha em resize/scroll (útil)
  window.addEventListener('resize', fecharMenu);
  window.addEventListener('scroll', fecharMenu, true);
});


document.querySelectorAll("#tabelaProjetos td[contenteditable='true']").forEach(celula => {
    let valorOriginal = "";

    // Quando a célula recebe foco, guarda o valor inicial
    celula.addEventListener("focus", function() {
        valorOriginal = this.textContent.trim();
    });

    // Quando perde o foco, compara valor atual com o original
    celula.addEventListener("blur", function() {
        const novoValor = this.textContent.trim();

        // Se não mudou nada, não faz nada
        if (novoValor === valorOriginal) {
            return;
        }

        const linha = this.closest("tr");
const id = linha.dataset.id; // pegar id ao invés de pedido
const campo  = this.dataset.campo;
const agora = new Date();
const dataHora = agora.toLocaleDateString("pt-BR") + " " + agora.toLocaleTimeString("pt-BR");
const textoUltima = `${usuarioLogado} - ${dataHora}`;
linha.querySelector("td[data-campo='ultimaalteracao']").textContent = textoUltima;

fetch("editar.php", {
  method: "POST",
  headers: { "Content-Type": "application/x-www-form-urlencoded" },
  body: `id=${encodeURIComponent(id)}&campo=${encodeURIComponent(campo)}&valor=${encodeURIComponent(novoValor)}&ultimaalteracao=${encodeURIComponent(textoUltima)}`
});



    });
});


const toggle = document.getElementById("toggleDarkMode");

toggle.addEventListener("change", function() {
    document.body.classList.toggle('dark-mode', this.checked); // aplica/remova a classe

    // envia para o PHP salvar no banco ou sessão
    fetch("salvar_darkmode.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "darkmode=" + this.checked
    });
});

document.addEventListener("DOMContentLoaded", () => {
    fetch("salvar_darkmode.php?action=check")
        .then(res => res.json())
        .then(data => {
            if (data.darkmode) {
                document.body.classList.add('dark-mode');
                toggle.checked = true;
            }
        });
});




const configModal = document.getElementById("configModal");
const novaContaModal = document.getElementById("novaContaModal");
const btnConfig = document.getElementById("btnConfig");
const btnNovaConta = document.getElementById("btnNovaConta");
const closeConfig = document.querySelector(".close");
const closeNova = document.querySelector(".closeNova");

// abre modal de config
btnConfig.onclick = () => configModal.style.display = "block";

// fecha modal de config
closeConfig.onclick = () => configModal.style.display = "none";

// abre modal nova conta
btnNovaConta.onclick = () => {
  configModal.style.display = "none";
  novaContaModal.style.display = "block";
}

// fecha modal nova conta
closeNova.onclick = () => novaContaModal.style.display = "none";

// fecha ao clicar fora
window.onclick = (e) => {
  if (e.target == configModal) configModal.style.display = "none";
  if (e.target == novaContaModal) novaContaModal.style.display = "none";
}