// script2.js - controla modal, edição inline, toggleAll e destaque de linha

// Função para enviar edição ao servidor
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

// Função global para marcar/desmarcar todos
window.toggleAll = function(master) {
  document.querySelectorAll('input[name="selecionados[]"]').forEach(cb => {
    cb.checked = master.checked;
    // dispara change para aplicar destaque
    cb.dispatchEvent(new Event('change', { bubbles: true }));
  });
};

document.addEventListener("DOMContentLoaded", () => {
  // --- Modal ---
  const modal = document.getElementById("modal");
  const abrirModal = document.getElementById("abrirModal");
  const fecharModal = document.getElementById("fecharModal");

  if (abrirModal && modal) {
    abrirModal.addEventListener("click", (ev) => {
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

  // fechar clicando fora do conteúdo
  window.addEventListener("click", (ev) => {
    if (modal && ev.target === modal) {
      modal.style.display = "none";
      modal.setAttribute('aria-hidden', 'true');
    }
  });

  // Esc para fechar modal
  window.addEventListener("keydown", (ev) => {
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

  // --- Destaque de linha ao marcar checkbox (delegation) ---
  document.addEventListener("change", (e) => {
    const el = e.target;
    if (el && el.matches('input[name="selecionados[]"]')) {
      const tr = el.closest('tr');
      if (tr) tr.classList.toggle('selecionado', el.checked);
    }
  });

  // aplicar destaque inicial (caso algum checkbox já esteja marcado)
  document.querySelectorAll('input[name="selecionados[]"]').forEach(cb => {
    const tr = cb.closest('tr');
    if (tr) tr.classList.toggle('selecionado', cb.checked);
  });
});

const scrollTop = document.querySelector('.scroll-top');
const scrollInner = document.querySelector('.scroll-inner');
const tableScroll = document.querySelector('.table-scroll');
const table = tableScroll.querySelector('table');

// Ajusta a largura da scroll-top dinamicamente
function atualizarScrollTop() {
  scrollInner.style.width = table.scrollWidth + 'px';
}

// Sincronizar scroll horizontal
scrollTop.addEventListener('scroll', () => {
  tableScroll.scrollLeft = scrollTop.scrollLeft;
});

tableScroll.addEventListener('scroll', () => {
  scrollTop.scrollLeft = tableScroll.scrollLeft;
});

// Atualiza no carregamento e ao redimensionar
window.addEventListener('load', atualizarScrollTop);
window.addEventListener('resize', atualizarScrollTop);

function imprimirTabela() {
  const tabela = document.querySelector('.table-scroll table');
  const win = window.open('', '', 'height=600,width=1200');
  win.document.write('<html><head><title>Imprimir Tabela</title>');
  win.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #000; padding: 8px; text-align: center; }</style>');
  win.document.write('</head><body>');
  win.document.write(tabela.outerHTML);
  win.document.write('</body></html>');
  win.document.close();
  win.print();
}


// --- Arrastar para selecionar (com auto-scroll robusto) ---
(function() {
  const container = document.querySelector('.table-scroll');
  if (!container) return; // nada a fazer se não existir

  let isDragging = false;
  let isSelecting = true;      // true = marcar; false = desmarcar
  let lastRowId = null;        // para evitar retrabalhar a mesma linha
  let lastMouseX = 0, lastMouseY = 0;
  let rafId = null;

  const MARGIN = 60;     // área (px) perto da borda que dispara scroll
  const MAX_SPEED = 30;  // px por frame máximo

  function startAutoScrollLoop() {
    if (rafId) return;
    function loop() {
      if (!isDragging) {
        rafId = null;
        return;
      }
      const rect = container.getBoundingClientRect();

      // velocidade vertical proporcional à distância para borda
      let v = 0;
      if (lastMouseY > rect.bottom - MARGIN) {
        const d = lastMouseY - (rect.bottom - MARGIN);
        v = Math.min(MAX_SPEED, (d / MARGIN) * MAX_SPEED);
      } else if (lastMouseY < rect.top + MARGIN) {
        const d = (rect.top + MARGIN) - lastMouseY;
        v = -Math.min(MAX_SPEED, (d / MARGIN) * MAX_SPEED);
      }

      // velocidade horizontal proporcional
      let h = 0;
      if (lastMouseX > rect.right - MARGIN) {
        const d = lastMouseX - (rect.right - MARGIN);
        h = Math.min(MAX_SPEED, (d / MARGIN) * MAX_SPEED);
      } else if (lastMouseX < rect.left + MARGIN) {
        const d = (rect.left + MARGIN) - lastMouseX;
        h = -Math.min(MAX_SPEED, (d / MARGIN) * MAX_SPEED);
      }

      if (v !== 0) container.scrollTop += v;
      if (h !== 0) container.scrollLeft += h;

      rafId = requestAnimationFrame(loop);
    }
    rafId = requestAnimationFrame(loop);
  }

  function stopAutoScrollLoop() {
    if (rafId) {
      cancelAnimationFrame(rafId);
      rafId = null;
    }
  }

  // determina a linha/td atual sob as coordenadas (clientX/clientY)
  function processPointerAt(x, y) {
    lastMouseX = x; lastMouseY = y;
    const el = document.elementFromPoint(x, y);
    if (!el) return;

    const td = el.closest('td');
    if (!td) return;

    // só atuamos na primeira coluna
    if (typeof td.cellIndex === 'number' && td.cellIndex === 0) {
      const tr = td.closest('tr');
      if (!tr) return;

      const rowId = tr.getAttribute('data-id') || tr.rowIndex || tr.textContent; // fallback
      if (rowId === lastRowId) return; // já processada nesta posição

      lastRowId = rowId;
      const checkbox = td.querySelector('input[type="checkbox"]');
      if (!checkbox) return;

      // aplica o modo atual (isSelecting) à linha
      checkbox.checked = isSelecting;
      tr.classList.toggle('selecionado', isSelecting);
    }
  }

  // pointerdown no container (captura apenas quando clicar na 1ª coluna)
  container.addEventListener('pointerdown', function (ev) {
    const td = ev.target.closest('td');
    if (!td || typeof td.cellIndex !== 'number' || td.cellIndex !== 0) return;
    // inicia arrasto
    isDragging = true;
    lastRowId = null; // reset para permitir processar a primeira linha
    // decide o modo com base no estado inicial do checkbox nesta linha
    const checkbox = td.querySelector('input[type="checkbox"]');
    if (!checkbox) return;

    isSelecting = !checkbox.checked; // se já marcado -> desmarcar; senão marcar
    // aplica à linha atual imediatamente
    const tr = td.closest('tr');
    checkbox.checked = isSelecting;
    tr.classList.toggle('selecionado', isSelecting);

    // capturar o pointer para garantir receber moves mesmo fora do container
    try { container.setPointerCapture(ev.pointerId); } catch (e) {}

    processPointerAt(ev.clientX, ev.clientY);
    startAutoScrollLoop();

    // evita seleção de texto
    ev.preventDefault();
  });

  // pointermove no container: atualiza coords e processa linha sob cursor
  container.addEventListener('pointermove', function(ev) {
    if (!isDragging) return;
    processPointerAt(ev.clientX, ev.clientY);
  });

  // pointerup: finaliza arrasto
  container.addEventListener('pointerup', function(ev) {
    if (!isDragging) return;
    isDragging = false;
    lastRowId = null;
    stopAutoScrollLoop();
    try { container.releasePointerCapture(ev.pointerId); } catch (e) {}
  });

  // caso o pointer seja solto fora do container, também garantimos cleanup
  document.addEventListener('pointerup', function () {
    if (isDragging) {
      isDragging = false;
      lastRowId = null;
      stopAutoScrollLoop();
    }
  });

})(); // fim IIFE



function imprimirSelecionados() {
  const tabelaOriginal = document.querySelector('.table-scroll table');
  const cabecalho = tabelaOriginal.querySelector('thead').cloneNode(true);

  // pega apenas as linhas selecionadas
  const linhasSelecionadas = tabelaOriginal.querySelectorAll('tbody tr input[type="checkbox"]:checked');

  if (linhasSelecionadas.length === 0) {
    alert("Nenhuma linha selecionada para impressão!");
    return;
  }

  // cria nova tabela temporária
  const tabelaTemp = document.createElement('table');
  tabelaTemp.appendChild(cabecalho);

  const tbodyTemp = document.createElement('tbody');
  linhasSelecionadas.forEach(input => {
    const linhaClone = input.closest('tr').cloneNode(true);
    // remove checkbox da cópia (opcional)
    linhaClone.querySelector('td:first-child').innerHTML = "";
    tbodyTemp.appendChild(linhaClone);
  });

  tabelaTemp.appendChild(tbodyTemp);

  // abre nova janela só com a tabela
  const janela = window.open('', '', 'width=900,height=600');
  janela.document.write(`
    <html>
      <head>
        <title>Imprimir Projetos Selecionados</title>
        <style>
          table { border-collapse: collapse; width: 100%; }
          th, td { border: 1px solid #333; padding: 6px; text-align: left; }
          th { background: #eee; }
        </style>
      </head>
      <body>
        ${tabelaTemp.outerHTML}
      </body>
    </html>
  `);
  janela.document.close();
  janela.print();
}

const btnExpandir = document.getElementById('btnExpandir');
const tableContainer = document.querySelector('.table-container');

btnExpandir.addEventListener('click', () => {
  tableContainer.classList.toggle('fullscreen');
});
