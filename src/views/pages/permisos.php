<?php
ob_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Estilo personalizado para la tabla */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-cell {
            padding: 8px;
            text-align: center;
            border: 2px solid #333;
            /* Borde oscuro */
        }

        .width-20 {
            width: 20%;
        }

        .width-60 {
            width: 60%;
        }
    </style>
</head>

<body>
    <table style="height: 30px; width: 600px;">
        <tbody>
            <tr style="height: 60px;">
                <td style="width: 80px; height: 70px; border: 1px solid black;"></td>
                <td style="width: 500px; height: 70px; border: 1px solid black;text-align: center;"><strong>SOLICITUD DE MANTENIMIENTO Y REPORTE DE SERVICIO</strong></td>
                <td style="width: 20px; height: 70px; border: 1px solid black;">
                    <table style="height: 51px;" width="100">
                        <tbody>
                            <tr style="height: 20px;">
                                <td style="width: 130px; height: 22px; border: 1px solid black;font-size: 8px;text-align: center;"><strong>CODIGO:<strong><br />CMC-GI-FR-001-01</td>
                            </tr>
                            <tr style="height: 20px;">
                                <td style="width: 130px; height: 22px; border: 1px solid black;font-size: 8px;text-align: center;"><strong>FECHA DE EMISIÓN:<strong><br />31.01.17</td>
                            </tr>
                            <tr style="height: 20px;">
                                <td style="width: 130px; height: 22px; border: 1px solid black;font-size: 8px;text-align: center;"><strong>FECHA DE ACTUALIZACIÓN:<strong><br />13.10.23</td>
                            </tr>
                            <tr style="height: 20px;">
                                <td style="width: 130px; height: 22px; border: 1px solid black;font-size: 8px;text-align: center;"><strong>VERSION: 007<strong></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
<?php
$html = ob_get_clean();

// Incluir la biblioteca Dompdf
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Crear una instancia de Dompdf
$options = new Options();
$options->set('isPhpEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

try {
    // Cargar el contenido HTML en Dompdf
    $dompdf->loadHtml($html);
    $dompdf->setPaper("letter");

    // Renderizar el PDF
    $dompdf->render();

    // Generar una respuesta PDF y mostrarlo en el navegador
    $dompdf->stream("archivo_.pdf", array("Attachment" => false));
} catch (Exception $e) {
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}
