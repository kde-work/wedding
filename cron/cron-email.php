<?php
add_action( 'init', 'wb_activation' );
function wb_activation() {
	if( ! wp_next_scheduled( 'wb_cron' ) ) {
		wp_clear_scheduled_hook( 'wb_cron' );
		wp_schedule_event( time(), 'daily', 'wb_cron' );
	}
}

if( defined('DOING_CRON') && DOING_CRON ) {
	add_action( 'wb_cron', 'wb_cron_handler' );
}

function wb_cron_handler(){
	$begin = microtime( true );
	$blogusers = get_users([
		'meta_key' => 'wb_todo'
	]);
	foreach ($blogusers as $bloguser) {
		$items = unserialize(base64_decode(get_user_meta($bloguser->ID, 'wb_todo', 1)));
		if (empty($items)) continue;
		$is_changed = false;
		$tasks = '';
		for ($i = 0; $i < count($items); $i++) {
			if (!$items[$i]['end_time_by_email']) {
				$end_time        = $items[$i]['end_time'];
				$comparing_dates = WeddingToDoClass::comparing_dates($end_time, time());
				if ($comparing_dates < 0 AND $items[$i]['name'] AND !$items[$i]['done']) {
					$items[$i]['end_time_by_email'] = 1;
					$tasks .= "<br>Gjøremål på huskelisten deres med navn: <b>{$items[$i]['name']}</b>, har nå forfalt.<br> - Fortvil ikke, når oppgaven(e) er gjort, kan dere selvfølgelig fremdeles merke oppgaver som ferdig, og dermed stryke dem av huskelisten.";
					$is_changed = 1;
				}
			}
		}
		if ($is_changed) {
			$headers = "Content-type: text/html; charset=UTF-8\r\n";
			wp_mail(
				$bloguser->user_email,
//						'omigos99@yandex.ru',
				bloginfo('name') . ' - Gjøremål til bryllupet er over forfallsdato.',
				"Heisann, {$bloguser->user_login}! <br>{$tasks}<br><br>Logg inn på <a href='". get_home_url() ."'>Bryllupsvenner.no</a>  og besøk planleggeren når dere har mulighet. <br><br> Med vennlig hilsen<br>Bryllupsvenner hos <br><b>Stil og Trend AS</b><br><br> ........... <br><br><b>Lyst på bryllupsinspirasjon?</b> <br>- Besøk nettbutikken vår https://www.tilbryllupet.no - vi sender hver dag.<br> - Besøk også bryllupsforumet for masse god inspirasjon: https://www.bryllupsvenner.no/forums <br>Har du sett et av Norges største utvalg til bryllup hos Dekorama?<br> - Sjekk ut https://www.dekorama.no/183-bryllup",
				$headers
			);
			update_user_meta($bloguser->ID, 'wb_todo', base64_encode(serialize($items)));
		}
	}
	$end = round( microtime( true ) - $begin, 2 );
	echo $end;
}

//add_action( 'init', 'wb_test' );
function wb_test() {
	if (isset($_GET['test_swb'])) {
		wb_cron_handler();
		die;
	}
}