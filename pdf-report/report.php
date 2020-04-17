<?php
use Dompdf\Dompdf;
require_once './dompdf/autoload.inc.php';

$html = file_get_contents('./report.html');

$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Вывод файла в браузер:
//$dompdf->stream('schet-10');

// Или сохранение на сервере:
$pdf = $dompdf->output();
file_put_contents('./schet-10.pdf', $pdf);