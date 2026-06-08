<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['resultats'])) {
    http_response_code(400);
    echo 'Données manquantes';
    exit;
}

$nomFichier = isset($data['nomFichier']) && !empty($data['nomFichier'])
    ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $data['nomFichier'])
    : 'resultat';

$format = isset($data['format']) ? $data['format'] : 'xlsx';

try {
    $spreadsheet = new Spreadsheet();
    $premiereFeuille = true;

    // Une feuille par fichier source
    foreach ($data['resultats'] as $resultat) {

        // Ignorer les fichiers sans correspondance
        if (empty($resultat['rows']))
            continue;

        if ($premiereFeuille) {
            $sheet = $spreadsheet->getActiveSheet();
            $premiereFeuille = false;
        } else {
            $sheet = $spreadsheet->createSheet();
        }

        // Nom de la feuille = nom du fichier source (max 31 caractères)
        $nomFeuille = substr(pathinfo($resultat['fichier'], PATHINFO_FILENAME), 0, 31);
        $sheet->setTitle($nomFeuille);

        // Écrire les entêtes
        $col = 1;
        foreach ($resultat['headers'] as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getCell($colLetter . '1')->setValue($header);
            $col++;
        }

        // Écrire les données
        $ligne = 2;
        foreach ($resultat['rows'] as $row) {
            $col = 1;
            foreach ($row as $valeur) {
                $colLetter = Coordinate::stringFromColumnIndex($col);
                $sheet->getCell($colLetter . $ligne)->setValue($valeur);
                $col++;
            }
            $ligne++;
        }

        // Auto-dimensionner les colonnes
        for ($i = 1; $i <= count($resultat['headers']); $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
    }

    // Sauvegarder dans fichier temporaire
    $tmpFile = tempnam(sys_get_temp_dir(), 'excel_');

    switch ($format) {
        case 'xls':
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
            $contentType = 'application/vnd.ms-excel';
            break;
        case 'csv':
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $contentType = 'text/csv';
            break;
        default:
            $writer = new Xlsx($spreadsheet);
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            break;
    }

    $writer->save($tmpFile);

    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $nomFichier . '.' . $format . '"');
    header('Content-Length: ' . filesize($tmpFile));
    header('Cache-Control: max-age=0');

    readfile($tmpFile);
    unlink($tmpFile);

} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}