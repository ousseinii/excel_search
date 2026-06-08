<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$code = trim($_POST['code']);
$fichiers = $_FILES['fichier'];

if (empty($code) || !$fichiers) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

try {
    $resultats = [];
    $totalLignes = 0;

    // Boucler sur tous les fichiers uploadés
    for ($f = 0; $f < count($fichiers['name']); $f++) {
        $nomFichier = $fichiers['name'][$f];
        $tmpPath = $fichiers['tmp_name'][$f];

        $spreadsheet = IOFactory::load($tmpPath);
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [];
        $rows = [];
        $firstRow = true;

        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowData = [];

            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if ($firstRow) {
                $headers = $rowData;
                $firstRow = false;
                continue;
            }

            if ((string) $rowData[0] === (string) $code) {
                $rows[] = $rowData;
            }
        }

        $totalLignes += count($rows);
        $resultats[] = [
            'fichier' => $nomFichier,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    echo json_encode([
        'success' => true,
        'totalLignes' => $totalLignes,
        'resultats' => $resultats
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}