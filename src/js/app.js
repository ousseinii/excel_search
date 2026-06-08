// ── STATE ──
let selectedFiles = [];
let resultData = null;

// ── FILE INPUT ──
const fileInput = document.getElementById("fichier");
const fileList = document.getElementById("fileList");
const dropZone = document.getElementById("dropZone");
const btnSearch = document.getElementById("btnSearch");

fileInput.addEventListener("change", function () {
  addFiles(Array.from(this.files));
  this.value = ""; // reset pour permettre re-sélection du même fichier
});

// Drag & drop
dropZone.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropZone.classList.add("dragover");
});

dropZone.addEventListener("dragleave", () => {
  dropZone.classList.remove("dragover");
});

dropZone.addEventListener("drop", (e) => {
  e.preventDefault();
  dropZone.classList.remove("dragover");
  const files = Array.from(e.dataTransfer.files).filter(
    (f) => f.name.endsWith(".xlsx") || f.name.endsWith(".xls"),
  );
  addFiles(files);
});

function addFiles(files) {
  files.forEach((file) => {
    // Éviter les doublons
    if (
      !selectedFiles.find((f) => f.name === file.name && f.size === file.size)
    ) {
      selectedFiles.push(file);
    }
  });
  renderFileList();
}

function removeFile(index) {
  selectedFiles.splice(index, 1);
  renderFileList();
}

function renderFileList() {
  fileList.innerHTML = "";

  if (selectedFiles.length === 0) {
    btnSearch.disabled = true;
    return;
  }

  btnSearch.disabled = false;

  selectedFiles.forEach((file, index) => {
    const size = formatSize(file.size);
    const div = document.createElement("div");
    div.className = "file-item";
    div.innerHTML = `
            <span class="file-item-icon">📄</span>
            <span class="file-item-name" title="${file.name}">${file.name}</span>
            <span class="file-item-size">${size}</span>
            <button class="file-item-remove" onclick="removeFile(${index})" title="Supprimer">✕</button>
        `;
    fileList.appendChild(div);
  });
}

function formatSize(bytes) {
  if (bytes < 1024) return bytes + " o";
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " Ko";
  return (bytes / (1024 * 1024)).toFixed(1) + " Mo";
}

// ── RECHERCHE ──
document
  .getElementById("searchForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    if (selectedFiles.length === 0) {
      alert("Veuillez sélectionner au moins un fichier.");
      return;
    }

    const code = document.getElementById("code").value.trim();
    if (!code) {
      alert("Veuillez saisir un code.");
      return;
    }

    const formData = new FormData();
    selectedFiles.forEach((file) => formData.append("fichier[]", file));
    formData.append("code", code);

    const resultatDiv = document.getElementById("resultat");
    resultatDiv.innerHTML =
      '<div class="status-msg status-loading">⏳ Recherche en cours...</div>';

    try {
      const response = await fetch("search.php", {
        method: "POST",
        body: formData,
      });
      const data = await response.json();

      if (data.success) {
        resultData = data;
        renderResultats(data, code);
      } else {
        resultatDiv.innerHTML = `<div class="status-msg status-empty">❌ Erreur : ${data.message}</div>`;
      }
    } catch (err) {
      resultatDiv.innerHTML =
        '<div class="status-msg status-empty">❌ Une erreur est survenue.</div>';
    }
  });

function renderResultats(data, code) {
  const resultatDiv = document.getElementById("resultat");
  let html = "";

  data.resultats.forEach((resultat) => {
    const found = resultat.rows.length > 0;
    html += `
            <div class="result-section">
                <div class="result-file-header ${found ? "found" : "not-found"}">
                    <span style="font-size:16px;">📄</span>
                    <span class="result-file-name">${resultat.fichier}</span>
                    <span class="badge ${found ? "badge-success" : "badge-empty"}">
                        ${found ? resultat.rows.length + " ligne(s) trouvée(s)" : "Aucune correspondance"}
                    </span>
                </div>
        `;

    if (found) {
      html += '<div class="table-wrap"><table><thead><tr>';
      resultat.headers.forEach((h) => (html += `<th>${h}</th>`));
      html += "</tr></thead><tbody>";
      resultat.rows.forEach((row) => {
        html += "<tr>";
        row.forEach((cell) => (html += `<td>${cell}</td>`));
        html += "</tr>";
      });
      html += "</tbody></table></div>";
    }

    html += "</div>";
  });

  // Afficher la barre d'export uniquement s'il y a au moins un résultat
  const hasResults = data.resultats.some((r) => r.rows.length > 0);
  if (hasResults) {
    html += `
            <div class="export-bar">
                <label>Format :</label>
                <select id="formatExport">
                    <option value="xlsx">Excel Workbook (.xlsx)</option>
                    <option value="xls">Excel 97-2003 (.xls)</option>
                    <option value="csv">CSV (.csv)</option>
                </select>
                <button class="btn btn-export" onclick="exporterFichier()">📥 Exporter</button>
            </div>
        `;
  }

  resultatDiv.innerHTML = html;
}

// ── EXPORT ──
async function exporterFichier() {
  const nomFichier = prompt("Nom du fichier :", "resultat");
  if (!nomFichier) return;

  const format = document.getElementById("formatExport").value;

  try {
    const response = await fetch("export.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ...resultData, nomFichier, format }),
    });

    if (!response.ok) {
      alert("Erreur lors de l'export !");
      return;
    }

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = nomFichier + "." + format;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    alert("Erreur lors de l'export !");
  }
}
