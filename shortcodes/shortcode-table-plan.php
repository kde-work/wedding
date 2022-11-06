<?php
add_shortcode('tableplan', 'wb_tableplan_shortcode');
function wb_tableplan_shortcode ($atts) {
	if ( is_user_logged_in() ) {
		wb_scripts__tableplan();
		$user_id = wp_get_current_user()->ID;
		$params = shortcode_atts( array(
			'without-table' => 'Gjester uten stol',
		), $atts );
		global $wb_file;
		ob_start();
		?>
		<div class="tp">
			<div class="wb__loading"></div>
			<div class="gl__guest-titel">Bordplan</div>

            <?php
//            delete_user_meta($user_id, 'wb_tableplan');
            $server_data = unserialize(base64_decode(get_user_meta($user_id, 'wb_tableplan', 1)));
            $id = (isset($_GET['id'])) ? $_GET['id'] : -1;
            echo wb_tableplan_script($server_data, $id);
            if ($id == -1) :
                ?>
                <div class="tableplans">
                    <div class="tableplans__create">
                        <a href="./?id" class="wb-button-regular">Opprett ny bordplan</a>
                    </div>
                    <?php
                    if (!empty($server_data)) :
                        ?>
                        <h3 class="tableplans__title">Deres bordplaner</h3>
                        <table class="tableplans__lines">
	                    <?php
	                    foreach($server_data as $table_id => $data) {
	                        echo wb_tableplan_table_line($data['data']['Name'], $table_id);
	                    }
	                    ?>
                        </table>
                        <?php
                    endif;
                    ?>
                </div>
                <?php
            else :
                ?>
                <div class="tableplan">
                <div class="tableplan__menu">
                    <div id="side_menu" style="width: 180px; margin-left: 2px; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(215, 227, 234); border-left-width: 1px; border-left-style: solid; border-left-color: rgb(215, 227, 234); background-color: rgb(255, 255, 255);z-index:1000;">
                        <div class="tp-menu">
                            <i class="fa fa-floppy-o tp-menu-icon tp-menu-icon--save" aria-hidden="true" onclick="ShowSubMenu('m10');return false;" title="Save"></i>
                            <i class="fa fa-print tp-menu-icon tp-menu-icon--print" aria-hidden="true" onclick="PrintPage();return false;" title="Print"></i>
                            <i class="fa fa-file-text-o tp-menu-icon tp-menu-icon--statistics" aria-hidden="true" onclick="ShowSubMenu('m9');return false;" title="Statistics"></i>
                            <i class="fa fa-cogs tp-menu-icon tp-menu-icon--settings" aria-hidden="true" onclick="ShowSubMenu('m8');return false;" title="Settings"></i>
                        </div>

                        <div id="tablesMenu" class="menu_button" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); /*background-color: rgb(111, 191, 255);*/ height: 36px; padding: 2px 10px; font-size:16px;" onclick="ShowSubMenu('m0');"><div class="tp-icon tp-icon--table"></div> Legg til bord »</div>
                        <div id="objectsMenu" class="menu_button" style="top: 84px; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); /*background-color: rgb(111, 191, 255);*/ height: 36px; padding: 2px 10px; font-size:16px;" onclick="ShowSubMenu('m12');"><div class="tp-icon tp-icon--object"></div> Objekter »</div>
                        <!--                    <div id="guestsMenu" class="menu_button" style="top: 125px; /*background-color: rgb(111, 191, 255);*/ height: 36px; padding-top: 4px; background-position: initial initial; background-repeat: initial initial;" onclick="ShowSubMenu('m6');">&nbsp;<img class="guests_icon" src="--><?php //echo plugins_url('/html/img/planner/blank.png', $wb_file); ?><!--">Guests »</div>-->

                        <div class="tableplan__guests">
                            <div class="tp-guests">
			                    <?php
			                    $guests = unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1)));
			                    $i = 0;
			                    $guests_tables = array();
			                    foreach ($guests as $guest) {
				                    $i++;
				                    ob_start();
				                    ?>
                                    <div id="tp-guest--<?php echo $i; ?>" class="tp-guest tp-guest--member pot_guest tp-guest--<?php echo $i; ?>" draggable>
                                        <div class="tp-guest__status <?php echo ($guest['status'])?'tp-guest__status--checked':''; ?>"></div>
                                        <div class="tp-guest__name"><?php echo $guest['name']; ?></div>
                                        <div class="tp-guest__family"><?php echo $guest['family']; ?></div>
                                        <div class="tp-guest__role"><?php echo $guest['role']; ?></div>
                                    </div>
				                    <?php
				                    $html = ob_get_clean();
				                    if (!isset($guest['table']) OR $guest['table'] OR !$guest['table-id']) {
					                    $guest['table'] = '__default__';
					                    $guest['table-id'] = '__default__';
				                    }
				                    if (!isset($guests_tables[$guest['table']])) {
					                    $guests_tables[$guest['table']] = array(
						                    'table-id' => $guest['table-id'],
                                            'guests' => array()
                                        );
				                    }
				                    array_push(
				                        $guests_tables[$guest['table']]['guests'],
                                        array(
				                            'html' => $html
                                        )
                                    );
			                    }
			                    usort($guests_tables, function ($a, $b){
				                    if ($a == '__default__') return 1;
				                    if ($b == '__default__') return -1;
				                    if ($a == $b) {
					                    return 0;
				                    }
				                    return ($a > $b) ? +1 : -1;
			                    });
			                    foreach ($guests_tables as $table_name => $guests_of_table) {
				                    if ($table_name == '__default__') {
					                    $table_name = $params['without-table'];
				                    }
				                    echo "<div class=\"tp-guests__table-box tp-guests__table-box--{$guests_of_table['table-id']}\">";
				                    echo "<div class=\"tp-guests__table\">{$table_name}</div>";
				                    foreach ($guests_of_table['guests'] as $guest_of_table) {
//					                    echo $guest_of_table['html'];
				                    }
				                    echo "</div>";
			                    }
			                    ?>
                            </div>
                        </div>
<!--                        <div class="menu" style="height:17px;padding-top:1px;top:165px;background-color:#ffffff;border-top:1px solid #D7E3EA">-->
<!--                            <span style="">&nbsp;Guests without a place:</span>-->
<!--                        </div>-->
<!--                        <div id="unseatedGuestList" class="menu" style="height:200px;top:185px;left:0;overflow:auto;border-bottom:1px solid #D7E3EA" title="Drag the guest with the mouse to the plan.">-->
<!--                        </div>-->

                        <div class="menu" style="border-bottom:1px solid #D7E3EA;padding-top:5px;">&nbsp;Size: &nbsp;
                            <input id="planSizeX" onblur="Change_Plan_X()" onkeydown="if (event.keyCode == 13) { Change_Plan_X(); }" type="text" style="width:45px;font-size:10px" value="133.8">&nbsp;x&nbsp;
                            <input id="planSizeY" onblur="Change_Plan_Y()" onkeydown="if (event.keyCode == 13) { Change_Plan_Y(); }" type="text" style="width:45px;font-size:10px" value="62.6">
                        </div>


                        <div id="m0" class="menu_content" style="width: 230px; left: 180px; top: 42px; display: none;">
                            <span class="menu_title">Valg for bord</span><br>
                            <table style="margin-top: 10px" cellspacing="0" cellpadding="0" border="0">
                                <tbody><tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="ttype0" name="table_type" value="0" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;"><img class="table_icon" id="tableicon1" src="<?php echo plugins_url('/html/img/planner/table11.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype0');return true;"></td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="radio" id="ttype1" name="table_type" value="1" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;"><img class="table_icon" id="tableicon2" src="<?php echo plugins_url('/html/img/planner/table21.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype1');return true;"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" height="20"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="ttype2" name="table_type" value="2" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;" checked="checked"><img class="table_icon" id="tableicon3" src="<?php echo plugins_url('/html/img/planner/table31.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype2');return true;"></td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="radio" id="ttype3" name="table_type" value="3" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;"><img class="table_icon" id="tableicon4" src="<?php echo plugins_url('/html/img/planner/table41.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype3');return true;"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" height="20"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="ttype4" name="table_type" value="4" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;"><img class="table_icon" id="tableicon5" src="<?php echo plugins_url('/html/img/planner/table51.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype4');return true;"></td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="radio" id="ttype5" name="table_type" value="5" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;"><img class="table_icon" id="tableicon6" src="<?php echo plugins_url('/html/img/planner/table61.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype5');return true;"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" height="20"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="ttype6" name="table_type" value="6" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;"><img class="table_icon" id="tableicon7" src="<?php echo plugins_url('/html/img/planner/table71.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype6');return true;"></td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td><input type="radio" id="ttype7" name="table_type" value="7" style="vertical-align:middle;" onclick="AddTableMenuSelect();return true;"><img class="table_icon" id="tableicon8" src="<?php echo plugins_url('/html/img/planner/table81.png', $wb_file); ?>" onclick="AddTableIconMenuClick('ttype7');return true;"></td>
                                </tr>
                                </tbody></table>
                            <br>
                            &nbsp;&nbsp;&nbsp;Stoler: &nbsp;<input type="text" value="4" id="num_table_seats" style="width: 28px">&nbsp;&nbsp; <div id="add_table_name_block" style="display:inline;">Navn: &nbsp;<input type="text" id="add_table_name" value="Bord 1" style="width: 82px"></div><br>
                            <input type="button" onclick="AddNewTable();" value="Legg til bord" style="margin-top: 10px">
                            <div style="position: absolute; top: 4px; right: 4px;">
                                <a href="#" class="close_button" onclick="HideSubMenu('m0');return false;"></a>
                            </div>
                        </div>



                        <div id="m12" class="menu_content" style="width: 230px; left: 180px; top: 83px; display: none;">
                            <span class="menu_title">Objektene</span><br>
                            <table style="margin-top: 10px" cellspacing="0" cellpadding="0" border="0">
                                <tbody><tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="otype0" name="object_type" value="0" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;" checked="checked"><img class="table_icon" id="objecticon1" src="<?php echo plugins_url('/html/img/planner/dance_floor.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype0');return true;"></td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="radio" id="otype1" name="object_type" value="1" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;"><img class="table_icon" id="objecticon2" src="<?php echo plugins_url('/html/img/planner/DJ.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype1');return true;"></td>
                                </tr>
                                <tr>
                                    <td style="text-align:center">Dansegulv</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">DJ Benk</td>
                                </tr>
                                <tr>
                                    <td colspan="3" height="5"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="otype2" name="object_type" value="2" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;"><img class="table_icon" id="objecticon3" src="<?php echo plugins_url('/html/img/planner/pillar.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype2');return true;"></td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="radio" id="otype3" name="object_type" value="3" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;"><img class="table_icon" id="objecticon4" src="<?php echo plugins_url('/html/img/planner/bar.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype3');return true;"></td>
                                </tr>
                                <tr>
                                    <td style="text-align:center">Stolpe</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">Bar / Snacks</td>
                                </tr>
                                <tr>
                                    <td colspan="3" height="5"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="otype4" name="object_type" value="4" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;"><img class="table_icon" id="objecticon5" src="<?php echo plugins_url('/html/img/planner/gifts_table.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype4');return true;"></td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="radio" id="otype5" name="object_type" value="5" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;"><img class="table_icon" id="objecticon6" src="<?php echo plugins_url('/html/img/planner/cake_table.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype5');return true;"></td>
                                </tr>
                                <tr>
                                    <td style="text-align:center">Gavebord</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">Kakebord</td>
                                </tr>
                                <tr>
                                    <td colspan="3" height="5"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="otype6" name="object_type" value="6" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;"><img class="table_icon" id="objecticon7" src="<?php echo plugins_url('/html/img/planner/any_object.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype6');return true;"></td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="text-align:center">Annet objekt</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">&nbsp;</td>
                                </tr>
                                </tbody></table>
                            <br>
                            <div id="add_object_name_block" style="display:none;">&nbsp;&nbsp;&nbsp;Tittel: &nbsp;<input type="text" id="add_object_name" style="width: 127px"><br></div>
                            <input type="button" onclick="AddNewObject();" value="Add" style="margin-top: 10px">
                            <div style="position: absolute; top: 4px; right: 4px;">
                                <a href="#" class="close_button" onclick="HideSubMenu('m12');return false;"></a>
                            </div>
                        </div>


                        <div id="m6" class="box" style="display: none;">
                            <div class="menu_content_guest" style="width: 700px; height: 380px; top: 50%; left: 50%; margin: -200px 0 0 -350px; display:block; opacity: initial;">
                                <a href="#" onclick="document.getElementById('uploadResult').value=''; document.getElementById('import_guest_dialog').style.display='block' ;return false;">Last ned gjesteliste fra fil</a>
                                <div style="width:690px;height:47px;padding:3px 5px 5px 5px;background-color:#6FBFFF;margin-top:5px;">
                                    <table id="guest_list" style="text-align:left; color:#ffffff;">
                                        <tbody>
                                        <tr>
                                            <td style="width:300px">Gjest navn</td>
                                            <td style="width:75px">Kjønn</td>
                                            <td style="width:85px">Status</td>
                                            <td style="width:145px">Meny</td>
                                            <td style="width:75px">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input id="new_guest_name" type="text" style="width:290px" onkeydown="if (event.keyCode == 13) { AddNewGuest(); }">
                                            </td>
                                            <td>
                                                <select id="new_guest_sex" style="width:65px"></select>
                                            </td>
                                            <td>
                                                <select id="new_guest_rsvp" style="width:75px"></select>
                                            </td>
                                            <td>
                                                <select id="new_guest_meal" style="width:135px"></select>
                                            </td>
                                            <td style="width:65px">
                                                <input type="button" value=" Add " onclick="AddNewGuest();">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="guestlist_content" style="/*position:absolute;left:5px;top:60px;*/width:700px;height:303px;overflow:auto;/*margin-top:23px;*/">
                                    <table id="guestlist_table" style="padding:5px;">
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <div style="position:absolute;top:2px;left:690px;"><a href="#" class="close_button" onclick="HideSubMenu('m6');return false;"></a></div>
                            </div>
                        </div>


                        <div id="import_guest_dialog" class="box" style="display: none;">
                            <div class="menu_content_guest" style="width: 600px; height: 340px; top: 50%; left: 50%; margin: -170px 0 0 -300px; display:block; opacity: initial;">
                                <input id="upload_button" type="button" value=" Выбрать файл " onclick=""><br>
                                <p>
                                    <input id="fileupload" type="file" name="files" style="display: none;">
                                </p>
                                <p style="padding-top:5px;">
                                    Preview:
                                </p>
                                <p>
                                    <textarea id="uploadResult" style="margin: 2px; width: 350px; height: 250px; resize: none;" readonly="readonly"></textarea>
                                </p>
                                <p style="padding-top:5px;">
                                    <input type="button" value=" Добавить в список гостей " onclick="if (AddNewGuestsFromTextArea()){HideSubMenu('import_guest_dialog');return false;} else {return false;}">  <input type="button" value=" Отмена " onclick="    HideSubMenu('import_guest_dialog');return false;">
                                </p>

                                <table style="position: absolute; left: 65%; top: 5%; bottom: 5%; width: 30%;">
                                    <tbody><tr>
                                        <td>
                                            <h5>Hvordan opprette en fil</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <br><strong>Word fil:</strong><br>
                                            <ul>
                                                <li>Hver gjest må være på separat linje.</li>
                                                <li>Lagre som tekstfil (Fil -> Lagre Som) - Velg .txt</li>
                                            </ul>
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <br><strong>For Excel fil</strong><br>
                                            <ul>
                                                <li>Gjestens navn må være på første celle i raden</li>
                                                <li>Pass på at det kun er en kolonne i filen</li>
                                                <li>Lagre som CSV fil (Fil -> Lagre som)</li>
                                            </ul>
                                        </td>
                                    </tr>
                                    </tbody></table>

                                <div style="position: absolute; left: 61%; top: 5%; bottom: 5%; border-left: 1px solid gray;"></div>
                                <div style="position:absolute;top:2px;right:2px;"><a href="#" class="close_button" onclick="HideSubMenu('import_guest_dialog');return false;"></a></div>

                            </div>
                        </div>
                    </div>

                    <div id="tablePropertiesMenu" style="position: absolute;  z-index: 2000; display: none; border: 1px solid rgb(215, 227, 234); background-color: rgb(215, 227, 234);" class="tp-context-menu">
                        <div class="menu_title" style=""><strong>Bord</strong></div><!--Стол-->
                        <div id="tablePropAddSeat" class="table_menu_button" style="" onclick="TableMenuAddSeat(this.parentNode.TableID);">Legg til stol</div>
                        <div id="tablePropRemoveSeat" class="table_menu_button" style="" onclick="TableMenuRemoveSeat(this.parentNode.TableID);">Fjern stol</div>
                        <div id="tablePropRemove" class="table_menu_button" style="" onclick="TableMenuDeleteTable(this.parentNode.TableID);">Fjern</div>
                        <div id="tablePropRename" class="table_menu_button" style="" onclick="TableMenuRenameTable(this.parentNode.TableID);">Endre navn</div>

                        <div style="position: absolute; top: 4px; right: 4px;">
                            <a href="#" class="close_button" onclick="HideSubMenu('tablePropertiesMenu');return false;"></a>
                        </div>
                    </div>

                    <div id="objectPropertiesMenu" style="position: absolute;  z-index: 2000; display: none; border: 1px solid rgb(215, 227, 234); background-color: rgb(215, 227, 234);" class="tp-context-menu">
                        <div class="menu_title" style=""><strong>Objekt</strong></div>
                        <div id="objectPropRename" class="table_menu_button" style="" onclick="ObjectMenuRename(this.parentNode.ObjectID);">Endre navn</div>
                        <div id="objectPropRemove" class="table_menu_button" style="" onclick="ObjectMenuDelete(this.parentNode.ObjectID);">Fjern</div>

                        <div style="position: absolute; top: 4px; right: 4px;">
                            <a href="#" class="close_button" onclick="HideSubMenu('objectPropertiesMenu');return false;"></a>
                        </div>
                    </div>

                    <div id="m13" class="box" style="display: none;">
                        <div id="objectRenameMenu" class="menu_content_guest" style="width: 300px; top: 50%; left: 50%; margin: -60px 0 0 -150px; position: absolute;  z-index: 2000; display: block;">
                            <div style="margin-top:5px;">
                                Nytt navn:<br><input id="object_edit_name" style="width:240px" type="text" onkeydown="if (event.keyCode == 13) { Rename_Object(); }"> <input onclick="Rename_Object()" value="OK" style="width:43px; margin-top:-10px;margin-left:2px" type="button">
                            </div>
                            <div style="position:absolute;top:2px;left:290px;"><a href="#" class="close_button" onclick="HideSubMenu('m13');return false;"></a></div>
                        </div>
                    </div>

                    <div id="seatPropertiesMenu" style="position: absolute;  z-index: 2000; display: none; border: 1px solid rgb(215, 227, 234); background-color: rgb(215, 227, 234);" class="tp-context-menu">
                        <div class="menu_title"><strong>Stol</strong></div>

                        <div id="seatGuestUnseat" class="table_menu_button" onclick="UnseatGuestFromTable(this.parentNode.GuestID);">Tøm stolen</div>
<!--                        <div id="seatGuestDelet" class="table_menu_button" onclick="DeleteGuestFromTable(this.parentNode.GuestID);">Remove guest</div>-->
                        <div style="position: absolute; top: 4px; right: 4px; cursor:pointer;">
                            <a href="#" class="close_button" onclick="HideSubMenu('seatPropertiesMenu');return false;"></a>
                        </div>
                    </div>

                    <div id="m7" class="box" style="display: none;">
                        <div id="tableRenameMenu" class="menu_content_guest" style="width: 300px; top: 50%; left: 50%; margin: -60px 0 0 -150px; position: absolute;  z-index: 2000; display: block;">
                            <div style="margin-top:5px;">
                                Nytt bordnavn:<br><input id="edit_name" style="width:240px" type="text" onkeydown="if (event.keyCode == 13) { Rename_Table(); }"> <input onclick="Rename_Table()" value="OK" style="width:43px; margin-top:-10px;margin-left:2px" type="button">
                            </div>
                            <div style="position:absolute;top:2px;left:290px;"><a href="#" class="close_button" onclick="HideSubMenu('m7');return false;"></a></div>
                        </div>
                    </div>

                    <div id="m8" class="menu_content" style="width: 217px; left: 183px; top: 20px; /*position: fixed;*/ display: none;">
                        <span class="menu_title">Innstillinger</span><br><br>
                        <input id="show_gridlines" onclick="ShowGridClick();" checked="checked" type="checkbox"> Vis rutenett
                        <?php /*
                        <br><br>
                        <span class="menu_title">Menu options</span><br><br>
                        <input id="meal1" value="Стандартное" size="13" maxlength="14" tabindex="1" type="text">&nbsp;&nbsp;<input id="meal2" value="Детское" size="13" maxlength="14" tabindex="2" type="text">&nbsp;&nbsp;<input id="meal3" value="Вегатарианское" size="13" maxlength="14" tabindex="3" type="text"><br><br>
                        <input id="meal4" value="Вариант 1" size="13" maxlength="14" tabindex="4" type="text">&nbsp;&nbsp;<input id="meal5" value="Вариант 2" size="13" maxlength="14" tabindex="5" type="text">&nbsp;&nbsp;<input id="meal6" value="Вариант 3" size="13" maxlength="14" tabindex="6" type="text"><br><br>
                        <input id="meal7" value="Вариант 4" size="13" maxlength="14" tabindex="7" type="text">&nbsp;&nbsp;<input id="meal8" value="Вариант 5" size="13" maxlength="14" tabindex="8" type="text">&nbsp;&nbsp;<input id="meal9" value="Вариант 6" size="13" maxlength="14" tabindex="9" type="text"><br><br>
                        <input type="button" value=" Сохранить меню " onclick="SaveSettings();HideSubMenu('m8');return false;">
                        */ ?>
                        <div class="close_button"><i class="fa fa-window-close-o" aria-hidden="true" onclick="HideSubMenu('m8');return false;"></i></div>
                    </div>

                    <div id="alertDlgBox" class="box" style="display: none;">
                        <div id="alertDlg" class="menu_content_guest" style="width: 200px; height: 100px; top: 50%; left: 50%; margin: -60px 0 0 -150px; position: absolute;  z-index: 2000; display: block;">
                            <div style="margin-top:5px;">
                                <p id="alertDlgText" style="text-align:center;">

                                </p>
                                <p id="alertDlgButtons" style="text-align:center;">

                                </p>
                            </div>
                            <div id="alertDlgHide" style="position:absolute;top:2px;left:290px;"><a href="#" class="close_button" onclick="HideSubMenu('alertDlgBox');return false;"></a></div>
                        </div>
                    </div>

                    <div id="m9" class="menu_content" style="width: 180px; left: 183px; top: 20px; /*position: fixed;*/ display: none;">
                        <span class="menu_title">Statistics</span><br>
                        <table style="border-spacing:10px;">
                            <tbody><tr>
                                <td>Antall bord:</td>
                                <td><span id="statTables"></span></td>
                            </tr>
                            <tr>
                                <td>Antall stoler:</td>
                                <td><span id="statSeats"></span></td>
                            </tr>
                            <tr>
                                <td>Antall gjester:</td>
                                <td><span id="statGuests"></span></td>
                            </tr>
                            <tr>
                                <td>Gjester uten stol:</td>
                                <td><span id="statUnseatGuests"></span></td>
                            </tr>
                            <tr>
                                <td>Ledige stoler:</td>
                                <td><span id="statFreeSeats"></span></td>
                            </tr>
                            </tbody></table>
                        <div style="padding-left:10px;">
                        </div>
                        <div class="close_button"><i class="fa fa-window-close-o" aria-hidden="true" onclick="HideSubMenu('m9');return false;"></i></div>
                    </div>

                    <div id="m10" class="box planSaveMenu-box" style="">
                        <div id="planSaveMenu" class="menu_content_guest" style="">
                            <div style="margin-top:5px;">
                                Bordplan Navn:<br><input id="edit_plan_name" style="width:240px" type="text"> <input onclick="if ($('#edit_plan_name')[0].value.length > 0) {tablePlan.Name = $('#edit_plan_name')[0].value; TryToSavePlan();}" value="OK" style="width:43px; margin-top:-10px;margin-left:2px" type="button">
                            </div>
                            <div style="position:absolute;top:2px;left:290px;"><a href="#" class="close_button" onclick="HideSubMenu('m10');return false;"></a></div>
                        </div>
                    </div>

                    <div id="m11" class="menu_content" style="width: 270px; left: 183px; top: 110px; /*position: fixed;*/ display: none;">
                        <span class="menu_title">Lag PDF for utskrift</span>
                        <table style="border-spacing:10px;">
                            <tbody><tr>
                                <td>Type av:</td>
                                <td>
                                    <select id="printType" style="width:160px" onchange="PrintTypeChanged(); return true;">
                                        <option value="8" selected="selected">Bordplan</option>
                                        <option value="16">Gjesteliste</option>

                                    </select>
                                </td>
                            </tr>
                            <tr id="printSize">
                                <td>Størrelse:</td>
                                <td>
                                    <select id="printSize" style="width:160px">
                                        <option value="0" selected="selected">A4</option>
                                        <option value="1">A3</option>
                                        <option value="2">A2</option>
                                        <option value="4">A1</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="printTitle">
                                <td>Header:</td>
                                <td><input id="inputPrintTitle" type="text" style="width:150px;"></td>
                            </tr>
                            <tr id="printTables">
                                <td style="text-align:right;"><input id="inputPrintTables" type="checkbox" checked="checked"></td>
                                <td>Kontur av bord</td>
                            </tr>
                            <tr id="printSeats">
                                <td style="text-align:right;"><input id="inputPrintSeats" type="checkbox" checked="checked"></td>
                                <td>Kontur av stoler</td>
                            </tr>
                            <tr id="printColor">
                                <td style="text-align:right;"><input id="inputPrintColor" type="checkbox"></td>
                                <td>Fargeprint</td>
                            </tr>
                            <tr id="printMenu" style="display:none;">
                                <td style="text-align:right;"><input id="inputPrintMenu" type="checkbox"></td>
                                <td>Menyvalg</td>
                            </tr>

                            </tbody></table>
                        <input id="printButton" type="button" value=" Скачать PDF файл " onclick="PrintPlan();HideSubMenu('m8');return false;">

                        <div style="position:absolute;top:4px;right:4px;"><a href="#" class="close_button" onclick="HideSubMenu('m11');return false;"></a></div>
                    </div>
                    <form method="POST" id="dataToSubmit" class="dataToSubmit">
                        <input type="hidden" name="tablePlanModel" value="">
                        <input type="hidden" name="action" value="wb-tableplan-print">
                    </form>
                </div>
                <div class="tableplan__field">
                    <div id="plannerCanvas" class="plannerCanvas" style="position: absolute; left: 0; top: 0; border: 1px solid rgb(215, 227, 234); z-index: 100; width: 1338px; height: 626px; background-image: url('<? echo plugins_url('/html/img/planner/grid.gif', $wb_file); ?>');" oncontextmenu="return false;" onmousedown="PlannerCanvasMouseDown(); return false;" droppable=""><div style="position: relative; display: inline-block; width: 1338px; height: 626px;" class="kineticjs-content" role="presentation"><canvas style="padding: 0; 0.margin: 0; border: 0 none; background: transparent none repeat scroll 0% 0%; position: absolute; top: 0; left: 0; width: 1338px; height: 626px;" width="1338" height="626"></canvas></div></div>
                </div>
            </div>
                <?php
            endif;
            ?>
		</div>
		<?php
	}
	return ob_get_clean();
}

function wb_tableplan_table_line($name, $id){
	ob_start();
    ?>
    <tr class="tableplan-line tableplan-line--<?php echo $id; ?>">
        <td class="tableplan-line__title"><a href="./?id=<?php echo $id; ?>"><div class="tp-icon tp-icon--table"></div> <?php echo $name; ?></a></td>
        <td class="tableplan-line__edit"><a href="./?id=<?php echo $id; ?>">Endre</a></td>
<!--        <td class="tableplan-line__duplicate"><div class="tableplan-line__duplicate-plan" data-id="--><?php //echo $id; ?><!--">Duplicate</div></td>-->
        <td class="tableplan-line__remove"><div class="tableplan-line__remove-plan" data-id="<?php echo $id; ?>">Slett</div></td>
    </tr>
    <?php
	return ob_get_clean();
}

function wb_tableplan_script($data, $id){
	$user_id = wp_get_current_user()->ID;
    $data = (isset($data[$id])) ? $data[$id]['data'] : [];
	ob_start();
	?>
    <script>
        // editor init
        function InitTablePlan() {
            var modelPlanID = <?php echo (isset($id) AND $id) ? $id : '-1'; ?>;

	        <?php echo (isset($data['Width']) AND $data['Width']) ? "SetPlanWidth({$data['Width']});\n" : ''; ?>
	        <?php echo (isset($data['Height']) AND $data['Height']) ? "SetPlanHeight({$data['Height']});\n" : ''; ?>

            tablePlan = new TablePlan(modelPlanID, <?php echo (isset($data['Name']) AND $data['Name']) ? "'{$data['Name']}'" : '"Bordplan 1"'; ?>);//Новый План

            // init editor

	        <?php
            if(!empty($data['Tables'])){
	            foreach ($data['Tables'] as $table) {
		            if(!empty($table['Seats'])){
                        echo "var seatsId = [];\n";
			            foreach ($table['Seats'] as $seat) {
                            echo "seatsId.push('{$seat['Id']}');\n";
		                }
		                $count = count($table['Seats']);
                        echo "tablePlan.AddNewTable('{$table['Id']}', {$table['Type']}, $count, '{$table['Name']}', {$table['CenterX']}, {$table['CenterY']}, {$table['Angle']}, seatsId);\n";
		            }
                }
            }
            ?>
            // var seatsId = [];
            // seatsId.push('35163EAD-EF85-CEFF-75D2-99DB95E79A55');
            // seatsId.push('5EE4BAA9-7E11-0B58-C981-F8122F297C27');
            // seatsId.push('DC0D2B03-865A-B41E-0EE0-B429F1D1A107');
            // seatsId.push('F506D348-DD18-C679-2721-6F1D5170F0FE');
            // seatsId.push('11B3F84B-4B93-862F-580F-FDD5F3DF3BA3');
            // tablePlan.AddNewTable('9BD4DE97-6930-133C-5B43-43271D534FF1', 0, 5, 'Table 2', 220, 186, 0, seatsId);
            // var seatsId = [];
            // seatsId.push('F11B4B8B-7441-99FC-5505-DAF35B004B7C');
            // seatsId.push('8C5EB468-9AB5-8C53-384F-334C491340E9');
            // seatsId.push('78688A48-CBEC-8DAA-8911-C36C2538C5F7');
            // seatsId.push('DB67D0E8-CD8D-A763-6435-A8F780BF0BF6');
            // seatsId.push('C06D902C-46A1-93FF-10EE-6964D58F4841');
            // tablePlan.AddNewTable('B7CC5E7F-ED55-8CCD-0CB6-161ECDC902F8', 5, 5, 'Table 2', 1767, 336, 0, seatsId);

	        <?php
	        if(!empty($data['RectObjects'])){
		        foreach ($data['RectObjects'] as $RectObject) {
                    echo "tablePlan.AddNewRectObject('{$RectObject['Id']}', '{$RectObject['Name']}', {$RectObject['CenterX']}, {$RectObject['CenterY']}, {$RectObject['Width']}, {$RectObject['Height']});\n";
		        }
	        }
	        ?>
            // tablePlan.AddNewRectObject('1E6A28EE-109B-0864-FD00-91D0D5D71E5A', 'Table с тортом', 474, 400, 120, 120);

	        <?php
	        $Guests = unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1)));
//	        echo '/*';
//	        print_r($Guests);
//	        echo '*/';
	        if(!empty($Guests)){
		        foreach ($Guests as $id_Guest => $Guest) {
		            $table_data = (!empty($Guest['table_data'])) ? unserialize(base64_decode($Guest['table_data']))[$id] : [];
//			        echo '/*';
//			        echo "{$Guest['name']}\n";
//			        print_r($table_data);
//			        echo "*/\n";
			        $table_data = wb_tableplan_clear_guest($table_data);
			        echo "tablePlan.AddNewGuest('{$id_Guest}', '{$Guest['name']} {$Guest['family']}', {$table_data['Type']}, {$table_data['Meal']}, {$table_data['RSVP']}, '{$table_data['TableID']}', '{$table_data['SeatID']}');\n";
		        }
	        }
//	        if(!empty($data['Guests'])){
//		        foreach ($data['Guests'] as $Guest) {
//			        echo "tablePlan.AddNewGuest('{$Guest['Id']}', '{$Guest['Name']}', {$Guest['Type']}, {$Guest['Meal']}, {$Guest['RSVP']}, '{$Guest['TableID']}', '{$Guest['SeatID']}');\n";
//		        }
//	        }
	        ?>
            // tablePlan.AddNewGuest('454FF075-7DFC-472B-BACD-6126A84CAF43', 'qwe1 2', 1, 0, 0, '9BD4DE97-6930-133C-5B43-43271D534FF1', 'F506D348-DD18-C679-2721-6F1D5170F0FE');
            // tablePlan.AddNewGuest('4DCB5537-DE2C-BBC9-A521-6CBD81235546', '34234wr', 3, 1, 0, '', '');
            // tablePlan.AddNewGuest('D22C3FD9-ABD3-0C31-86D0-C7A9C3398885', 'qwsdsd sdg', 6, 1, 2, '', '');

            tablePlan.MenuList = {};

            tablePlan.HideGrid = Boolean(0);
            tablePlan.UserType = "";

            ShowGrid();
            kineticLayer.draw();

            var mess = "";

            if (!IsNullOrEmpty(mess)) {
                DlgErrorFromServer(mess);
            }
        }
    </script>
    <?php
	return ob_get_clean();
}
function wb_tableplan_clear_guest($Guest){
	$Guest['Type'] = (isset($Guest['Type'])) ? $Guest['Type'] : '0';
	$Guest['Meal'] = (isset($Guest['Meal'])) ? $Guest['Meal'] : '0';
	$Guest['RSVP'] = (isset($Guest['RSVP'])) ? $Guest['RSVP'] : '0';
	$Guest['TableID'] = (isset($Guest['TableID'])) ? $Guest['TableID'] : '0';
	$Guest['SeatID'] = (isset($Guest['SeatID'])) ? $Guest['SeatID'] : '0';

	return $Guest;
}

add_action( 'wp_body_open', 'wb_tableplan_resize_html' );
function wb_tableplan_resize_html(){
	$id = (isset($_GET['id'])) ? $_GET['id'] : -1;
	if ($id != -1) {
		echo "<div id=\"resize_horizontal\" class=\"resize_horizontal\" draggable=\"\" style=\"\"></div>
          <div id=\"resize_vertical\" class=\"resize_vertical\" draggable=\"\" style=\"\"></div>";
    }
}