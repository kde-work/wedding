<?php
use Dompdf\Dompdf;
function wb_report( $data ) {
	$replace = [
		'%USERNAME%' => $data['user_name'],
		'%EMAIL%' => $data['user_mail'],
		'%DATE CURRENT%' => $data['time'],
		'%SUBSCRIPTION NAME%' => $data['role'],
		'%ORDER ID%' => $data['order_id'],
		'%CARD%' => $data['card_no'],
		'%VALUE%' => $data['amount'],
		'%DATE VALID%' => $data['end_date'],
		'%LOGO%' =>  __DIR__ . "/logo.png"
	];
	$file_name = "bill_{$data['order_id']}_{$data['user_name']}_" .str_replace( [' ', ':'], '-', $data['time'] ) . ".pdf";

	$html = file_get_contents( __DIR__ . '/report.html' );
	$html = strtr( $html, $replace );


	$dompdf = new Dompdf();
	$dompdf->loadHtml( $html, 'UTF-8' );
	$dompdf->setPaper( 'A4', 'portrait' );
	$dompdf->render();

	// Brother show
	//$dompdf->stream('schet-10');

	// Server save
	$pdf = $dompdf->output();
	file_put_contents( $data['folder'] . "/{$file_name}", $pdf );
	return [
		'file_name' => $file_name,
		'abs' => $data['folder'] . "/{$file_name}"
	];
}
