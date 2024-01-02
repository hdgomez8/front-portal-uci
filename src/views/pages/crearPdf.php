<?php
require_once __DIR__ . '/../../vendor/autoload.php';// Asegúrate de que la ruta a autoload.php sea correcta

use Dompdf\Dompdf;
use Dompdf\Options;

// Configura las opciones de Dompdf
$options = new Options();
$options->set('isPhpEnabled', true); // Permite la incorporación de PHP
$dompdf = new Dompdf($options);

// Carga el contenido HTML
$html = '<html><body><h1>Lo logramos</h1></body></html>';
$dompdf->loadHtml($html);

// Establece el tamaño del papel (por ejemplo, carta)
$dompdf->setPaper('A4');

// Renderiza el PDF
$dompdf->render();

// Genera una respuesta PDF para descargar
$dompdf->stream('archivo.pdf');
