<?php
function wb_form_mail( $name, $email, $phone, $message = '' ) {
	$from_email = "robot@{$_SERVER['HTTP_HOST']}";
	$email_to = get_userdata(get_current_user_id())->user_email;

	$title_email = "From site {$_SERVER['HTTP_HOST']}. Message from Wedding Page"; // WRITE HERE
	$body = "<p>Form information:</p><br><p>Name: <b>$name</b></p><p>Email: <b>$email</b></p><p>Phone: <b>$phone</b></p><p>Message: <b>$message</b></p>";

	echo $email_to;
	echo $title_email;
	echo $body;

	sms_send_mime_mail(
		$from_email,
		$email_to,
		"UTF-8",
		"UTF-8",
		$title_email,
		$body
	);
}

function wb_mail( $email_to = "omigos99@yandex.ru" ) {
	$from_email = "robot@{$_SERVER['HTTP_HOST']}";

	$title_email = "From site {$_SERVER['HTTP_HOST']}. For gift-coordinator"; // WRITE HERE
	$body = "<p>Information for gift-coordinator.</p><br><br><p>Wish list:</p>";

	$wishlist = wb_mail_wishlist();
	$body .= $wishlist;

	sms_send_mime_mail(
		$from_email,
		$email_to,
		"UTF-8",
		"UTF-8",
		$title_email,
		$body
	);
}
function wb_mail_wishlist() {
	$user_id = wp_get_current_user()->ID;
	$items = unserialize(base64_decode(get_user_meta($user_id, 'wl_save', 1)));

	ob_start();
	?>
	<table class="gl__table guest-table gl__table--group" border="1">
		<tr class="gl__tr gl__tr--header">
			<th class="gl__th gl__th--status gl__w--70">Status</th>
			<th class="gl__th gl__th--name">Wishname</th>
			<th class="gl__th gl__th--link">Link or Location</th>
			<th class="gl__th gl__th--description">Description</th>
			<th class="gl__th gl__th--quantity">Quantity wanted</th>
			<th class="gl__th gl__th--price">Price</th>
			<th class="gl__th gl__th--notes gl__w--70 gl__ta--center">Notat</th>
		</tr>
		<?php
		foreach ($items as $item) {
			?>
			<tr class="gl__item">
				<td class="gl__td gl__td--status"><?php echo ($item['status'])?'✔':''; ?></td>
				<td class="gl__td gl__td--name"><?php echo $item['name']; ?></td>
				<td class="gl__td gl__td--link"><?php echo $item['link']; ?></td>
				<td class="gl__td gl__td--description"><?php echo $item['description']; ?></td>
				<td class="gl__td gl__td--quantity"><?php echo $item['quantity']; ?></td>
				<td class="gl__td gl__td--price"><?php echo $item['price']; ?></td>
				<td class="gl__td gl__td--notes gl__ta--center gl-modal"><?php echo $item['notes']; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
	return ob_get_clean();
}

function sms_send_mime_mail(
	$email_from, // email отправителя
	$email_to, // email получателя
	$data_charset, // кодировка переданных данных
	$send_charset, // кодировка письма
	$subject, // тема письма
	$body // текст письма
) {
	$to = $email_to;
	$from = $email_from;
	$subject = sms_mime_header_encode($subject, $data_charset, $send_charset);
	if ($data_charset != $send_charset) {
		$body = iconv($data_charset, $send_charset, $body);
	}
	$headers = "From: $from\r\n";
	$headers .= "Content-type: text/html; charset=$send_charset\r\n";
	return wp_mail($to, $subject, $body, $headers);
}

function sms_mime_header_encode($str, $data_charset, $send_charset) {
	if ($data_charset != $send_charset) {
		$str = iconv($data_charset, $send_charset, $str);
	}
	return "=?" . $send_charset . "?B?" . base64_encode($str) . "?=";
}