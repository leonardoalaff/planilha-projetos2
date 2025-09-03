document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("td[contenteditable]").forEach(cell => {
    cell.addEventListener("blur", salvarEdicao);
    cell.addEventListener("keydown", e => {
      if (e.key === "Enter") {
        e.preventDefault(); 
        cell.blur(); // força salvar ao apertar Enter
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


function toggleAll(source) {
  checkboxes = document.querySelectorAll('input[type="checkbox"]');
  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i] != source)
      checkboxes[i].checked = source.checked;
  }
}

// Abrir modal
document.getElementById("btnAdd").onclick = function(e) {
  e.preventDefault();
  document.getElementById("modal").style.display = "flex";
};

// Fechar modal
document.getElementById("btnClose").onclick = function() {
  document.getElementById("modal").style.display = "none";
};

// Fechar clicando fora
window.onclick = function(e) {
  if (e.target === document.getElementById("modal")) {
    document.getElementById("modal").style.display = "none";
  }
};

