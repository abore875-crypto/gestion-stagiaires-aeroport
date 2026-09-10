<?php
/**
 * core/PdfGenerator.php
 * Petit utilitaire qui transforme une vue PHP/HTML en fichier PDF grâce
 * à la librairie Dompdf (installée via Composer). Réutilisable pour
 * n'importe quel document (attestations, notes de service...).
 */

class PdfGenerator
{
    /**
     * Génère un PDF à partir d'un fichier de vue et l'enregistre sur disque.
     *
     * @param string $viewPath    Chemin absolu vers le fichier .php à transformer en PDF
     * @param array  $data        Variables à rendre disponibles dans la vue
     * @param string $outputPath  Chemin absolu où enregistrer le PDF généré
     */
    public static function generateFromView(string $viewPath, array $data, string $outputPath): void
    {
        require_once dirname(__DIR__) . '/vendor/autoload.php';

        extract($data);
        ob_start();
        require $viewPath;
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $dossier = dirname($outputPath);
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        file_put_contents($outputPath, $dompdf->output());
    }
}