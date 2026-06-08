<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Recherche Excel</title>
    <link rel="stylesheet" href="/css/style.css">
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

    <script src="/js/script.js"></script>
</body>

</html>