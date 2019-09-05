<?phpfunction wb_guestlist_shortcode ($atts) {	if ( is_user_logged_in() ) {        wb_scripts__guestlist ();        $user_id = wp_get_current_user()->ID;        $items = unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1)));        $role_list = $gender_list = $invited_to_list = '';        extract( shortcode_atts( array(                'role_list' => 'Ordinær gjest, Forlover, Brudepike, Brudesvenn, Toastmaster, Brud, Brudgom, Brudens Mor, Brudens Far, Brudgommens Mor, Brudgommens Far',                'gender_list' => 'Dame, Mann',                'invited_to_list' => 'Seremoni, Middag, Kaffe, Full dag',            ), $atts )        );		ob_start();	    ?>		<div class="guestlist gl">            <div class="wb__loading"></div>			<div class="gl__guest-titel">Gjesteliste</div>			<div class="wb-search-input">				<input type="text" id="wb-search-input" autocomplete="off" class="wb-search__input" name="search-input" placeholder="Search for guest ...">			</div>            <form class="gl__form" data-max-id="<?php echo count($items); ?>">                <table class="gl__table guest-table gl__table--group gl__table--<?php //echo $group['term_id'] ?>">                    <tr class="gl__tr gl__tr--header">                        <th class="gl__th gl__th--status gl__w--70">Status</th>                        <th class="gl__th gl__th--name">Navn</th>                        <th class="gl__th gl__th--family">Familienavn</th>                        <th class="gl__th gl__th--role">Rolle</th>                        <th class="gl__th gl__th--meal">Måltid</th>                        <th class="gl__th gl__th--gender">Kjønn</th>                        <th class="gl__th gl__th--invited-to gl__w--106">Invitert til</th>                        <th class="gl__th gl__th--notes gl__w--70 gl__ta--center">Notat</th>                        <th class="gl__th gl__th--contact gl__w--70 gl__ta--center">Kontakt</th>                        <th class="gl__th gl__th--option gl__w--60 gl__ta--right">Slett</th>                    </tr>                    <?php//                    $i = 0;                    foreach ($items as $key => $val) {//                        $i++;//                        print_r($val);                        echo wb_guestlist_item_template($role_list, $gender_list, $invited_to_list, $key, $val['status'], $val['name'], $val['family'], $val['role'], $val['gender'], $val['invited-to'], $val['meal'], $val['notes'], $val['phone'], $val['email'], $val['address']);                    }                    echo wb_guestlist_item_template($role_list, $gender_list, $invited_to_list, 'empty_line');                    ?>                    <tr class="gl__tr wb-add-new">                        <td class="gl__td gl__td--status"></td>                        <td class="gl__td gl__td--add-elem">                            <input autocomplete="off" class="wb-add-new__input" type="text" title="Add new guest">                        </td>                        <td class="gl__td gl__td--add-elem" colspan="8">                            <div class="wb-add-new__button" data-group-id="">Legg til en ny gjest</div>                        </td>                    </tr>                </table>                <input type="hidden" name="action" value="gl_save">                <input type="hidden" name="role_list" value="<?php echo $role_list; ?>">                <input type="hidden" name="gender_list" value="<?php echo $gender_list; ?>">                <input type="hidden" name="invited_to_list" value="<?php echo $invited_to_list; ?>">            </form>        </div>		<?php	}	$output = ob_get_contents(); ob_end_clean();	return $output;}add_shortcode('guestlist', 'wb_guestlist_shortcode');function wb_guestlist_item_template($role_list, $gender_list, $invited_to_list, $id, $status = '', $name = '', $family = '', $role = '', $gender = '', $invited_to = '', $meal = '', $notes = '', $phone = '', $email = '', $address = '') {	ob_start();    ?>    <tr class="gl__item gl__item--<?php echo $id; ?>" data-id="<?php echo $id; ?>">        <td class="gl__td gl__td--status"><input type="checkbox" autocomplete="off" class="guest-table__input guest-table__input--status" name="status--<?php echo $id; ?>" title="status" <?php echo ($status)?'checked':''; ?>><div class="gl__status" title="Gjestestatus Ventende/Deltar"></div></td>        <td class="gl__td gl__td--name"><input type="text" autocomplete="off" class="guest-table__input guest-table__input--name" name="name--<?php echo $id; ?>" title="Guest Name" placeholder="Gjestens Navn" value="<?php echo $name; ?>"></td>        <td class="gl__td gl__td--family"><input type="text" autocomplete="off" class="guest-table__input guest-table__input--family" name="family--<?php echo $id; ?>" placeholder="Side av familie" value="<?php echo $family; ?>"></td>        <td class="gl__td gl__td--role"><select class="guest-table__input guest-table__select guest-table__input--role" name="role--<?php echo $id; ?>" title="Guest Role" autocomplete="off"><?php                            $role_list = explode(',', $role_list);                            $is_selected = false;                            $html = '';                            foreach ($role_list as $role_i) {                                $selected = '';                                $role_i = trim($role_i);                                if ($role AND $role == $role_i AND !$is_selected) {                                    $selected = 'selected';                                    $is_selected = true;                                }                                $html .= "<option {$selected}>{$role_i}</option>";                            }                            if ($is_selected) {                                echo "<option disabled>Rolle</option>";                            } else {                                echo "<option selected disabled>Rolle</option>";                            }                            echo $html;                                        ?></select></td>        <td class="gl__td gl__td--meal"><input type="text" autocomplete="off" class="guest-table__input guest-table__input--meal" name="meal--<?php echo $id; ?>" title="Meal" placeholder="Type rett" value="<?php echo $meal; ?>"></td>        <td class="gl__td gl__td--gender"><select class="guest-table__input guest-table__select guest-table__input--gender" name="gender--<?php echo $id; ?>" title="Dame/Mann" autocomplete="off"><?php                            $gender_list = explode(',', $gender_list);                            $is_selected = false;                            $html = '';                            foreach ($gender_list as $gender_i) {                                $selected = '';                                $gender_i = trim($gender_i);                                if ($gender AND $gender == $gender_i AND !$is_selected) {                                    $selected = 'selected';                                    $is_selected = true;                                }                                $html .= "<option {$selected}>{$gender_i}</option>";                            }                            if ($is_selected) {                                echo "<option disabled>Dame/Mann</option>";                            } else {                                echo "<option selected disabled>Dame/Mann</option>";                            }                            echo $html;                                                    ?></select></td>        <td class="gl__td gl__td--invited-to"><select class="guest-table__input guest-table__select guest-table__input--invited-to" name="invited-to--<?php echo $id; ?>" title="Invited to" autocomplete="off"><?php                            $invited_to_list = explode(',', $invited_to_list);                            $is_selected = false;                            $html = '';                            foreach ($invited_to_list as $invited_to_i) {                                $selected = '';                                $invited_to_i = trim($invited_to_i);                                if ($invited_to AND $invited_to == $invited_to_i AND !$is_selected) {                                    $selected = 'selected';                                    $is_selected = true;                                }                                $html .= "<option {$selected}>$invited_to_i</option>";                            }                            if ($is_selected) {                                echo "<option disabled>Invitert til</option>";                            } else {                                echo "<option selected disabled>Invitert til</option>";                            }                            echo $html;                                        ?></select></td><!--        <td class="gl__td gl__td--notes"><textarea type="text" class="guest-table__input guest-table__textarea guest-table__input--notes" name="notes----><?php //echo $id; ?><!--" placeholder="Notat på gjest" autocomplete="off">--><?php //echo $notes; ?><!--</textarea></td>-->        <td class="gl__td gl__td--notes gl__ta--center gl-modal"><div class="gl-icon <?php echo (!$notes)?'gl-icon--empty':''; ?> gl-icon--notes gl-modal__btn" data-target="notes" title="Contact Information in pop up"></div>            <div class="gl__modal gl__modal--notes">                <div class="gl-contact-modal">                    <div class="gl-contact-modal__header">                        <div class="gl-contact-modal__title">Notat på gjest</div>                    </div>                    <div class="gl-contact-modal__body">                        <table class="gl-contact-modal__table">                            <tr class="gl-contact-modal__phone">                                <td class="">                                    <textarea type="text" class="guest-table__input guest-table__textarea guest-table__input--notes" name="notes--<?php echo $id; ?>" placeholder="Notat på gjest" autocomplete="off"><?php echo $notes; ?></textarea>                                </td>                            </tr>                        </table>                        <div class="wb-add-new__button gl__close-modal">Lukk og lagre</div>                    </div>                </div>                <div class="gl__overlay gl__close-modal"></div>            </div>        </td>        <td class="gl__td gl__td--contact gl__ta--center gl-modal"><div class="gl-icon <?php echo (!$phone AND !$email AND !$address)?'gl-icon--empty':''; ?> gl-icon--contact gl-modal__btn" data-target="contact" title="Contact Information in pop up"></div>            <div class="gl__modal gl__modal--contact">                <div class="gl-contact-modal">                    <div class="gl-contact-modal__header">                        <div class="gl-contact-modal__title">Kontaktinformasjon for gjesten</div>                    </div>                    <div class="gl-contact-modal__body">                        <table class="gl-contact-modal__table">                            <tr class="gl-contact-modal__phone">                                <td class="gl-contact-modal__left"><label for="phone--<?php echo $id; ?>">telefon</label></td>                                <td class="gl-contact-modal__right"><input type="text" autocomplete="off" id="phone--<?php echo $id; ?>" placeholder="Guest Phone" name="phone--<?php echo $id; ?>" value="<?php echo $phone; ?>"></td>                            </tr>                            <tr class="gl-contact-modal__email">                                <td class="gl-contact-modal__left"><label for="email--<?php echo $id; ?>">e-post</label></td>                                <td class="gl-contact-modal__right"><input type="email" autocomplete="off" id="email--<?php echo $id; ?>" placeholder="Guest email" name="email--<?php echo $id; ?>" value="<?php echo $email; ?>"></td>                            </tr>                            <tr class="gl-contact-modal__address">                                <td class="gl-contact-modal__left"><label for="address--<?php echo $id; ?>">adresse</label></td>                                <td class="gl-contact-modal__right"><input type="text" autocomplete="off" id="address--<?php echo $id; ?>" placeholder="Guest address" name="address--<?php echo $id; ?>" value="<?php echo $address; ?>"></td>                            </tr>                        </table>                        <div class="wb-add-new__button gl__close-modal">Lukk og lagre</div>                    </div>                </div>                <div class="gl__overlay gl__close-modal"></div>            </div>        </td>        <td class="gl__td gl__td--option gl__ta--right"><div class="wb__delete" title="Remove line"></div></td>    </tr>    <?php	return ob_get_clean();}