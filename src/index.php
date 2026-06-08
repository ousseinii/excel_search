<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Recherche Excel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        input,
        button {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
        }

        button {
            background: rgb(0, 96, 137);
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: rgb(190, 214, 0);
            color: black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: rgb(0, 96, 137);
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }
    </style>
</head>

<body>

    <h2>🔍 Outil de Recherche Excel</h2>

    <form id="searchForm" enctype="multipart/form-data">
        <label>Fichier Excel :</label>
        <input type="file" id="fichier" name="fichier[]" accept=".xlsx,.xls" multiple required />

        <label>Code / ID à rechercher :</label>
        <input type="text" id="code" name="code" placeholder="Ex: 001" required />

        <button type="submit">🔍 Rechercher</button>
    </form>

    <div id="resultat"></div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData();
            const files = document.getElementById('fichier').files;

            for (let i = 0; i < files.length; i++) {
                formData.append('fichier[]', files[i]);
            }
            formData.append('code', document.getElementById('code').value);

            document.getElementById('resultat').innerHTML = '<p>Recherche en cours...</p>';

            const response = await fetch('search.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.resultats.length > 0) {
                let html = '';
                data.resultats.forEach(resultat => {
                    html += `<h3>📄 ${resultat.fichier}</h3>`;
                    if (resultat.rows.length > 0) {
                        html += `<p>✅ <strong>${resultat.rows.length} ligne(s) trouvée(s)</strong></p>`;
                        html += '<table><thead><tr>';
                        resultat.headers.forEach(h => html += `<th>${h}</th>`);
                        html += '</tr></thead><tbody>';
                        resultat.rows.forEach(row => {
                            html += '<tr>';
                            row.forEach(cell => html += `<td>${cell}</td>`);
                            html += '</tr>';
                        });
                        html += '</tbody></table>';
                    } else {
                        html += `<p>❌ Aucune correspondance trouvée.</p>`;
                    }
                });

                html += `
                <div id="exportOptions" style="margin-top:15px;">
                    <label>Format d'export :</label>
                    <select id="formatExport" style="padding:10px; width:100%; margin:10px 0;">
                        <option value="xlsx">Excel Workbook (.xlsx)</option>
                        <option value="xls">Excel 97-2003 (.xls)</option>
                        <option value="csv">CSV (.csv)</option>
                    </select>
                    <button onclick="exporterFichier()">📥 Exporter</button>
                </div>
            `;

                document.getElementById('resultat').innerHTML = html;
                window.resultData = data;
            } else {
                document.getElementById('resultat').innerHTML = '<p>❌ Aucune correspondance trouvée dans tous les fichiers.</p>';
            }
        });

        async function exporterFichier() {
            const nomFichier = prompt('Nom du fichier :', 'resultat');
            if (!nomFichier) return;

            const format = document.getElementById('formatExport').value;

            const response = await fetch('export.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ...window.resultData,
                    nomFichier: nomFichier,
                    format: format
                })
            });

            if (!response.ok) {
                alert('Erreur lors de l\'export !');
                return;
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = nomFichier + '.' + format;
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>

</body>

</html>