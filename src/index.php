<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outil de Recherche Excel</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div class="header-icon">🔍</div>
            <div>
                <h1>Outil de Recherche Excel</h1>
                <p>Recherchez un code dans plusieurs fichiers Excel simultanément</p>
            </div>
        </div>

        <!-- FORMULAIRE -->
        <div class="card">
            <p class="card-title">Paramètres de recherche</p>

            <form id="searchForm" enctype="multipart/form-data">

                <!-- Upload fichiers -->
                <div class="form-group">
                    <label>Fichiers Excel</label>
                    <div class="drop-zone" id="dropZone">
                        <input type="file" id="fichier" name="fichier[]" accept=".xlsx,.xls" multiple />
                        <div class="drop-zone-icon">📂</div>
                        <div class="drop-zone-text">Cliquer pour sélectionner ou glisser-déposer</div>
                        <div class="drop-zone-sub">Formats acceptés : .xlsx, .xls — plusieurs fichiers possibles</div>
                    </div>

                    <!-- Liste des fichiers sélectionnés -->
                    <div id="fileList"></div>
                </div>

                <!-- Code à rechercher -->
                <div class="form-group">
                    <label>Code / ID à rechercher</label>
                    <input type="text" id="code" name="code" placeholder="Ex : TRX-2024-00123" required />
                </div>

                <button type="submit" class="btn btn-primary" id="btnSearch" disabled>
                    🔍 Lancer la recherche
                </button>

            </form>
        </div>

        <!-- RÉSULTATS -->
        <div id="resultat"></div>

    </div>

    <script src="/js/app.js"></script>
</body>

</html>