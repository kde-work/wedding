<?php
add_shortcode('tableplan', 'wb_tableplan_shortcode');
function wb_tableplan_shortcode ($atts) {
	if ( is_user_logged_in() ) {
		wb_scripts__tableplan();
		$user_id = wp_get_current_user()->ID;
		$items = unserialize(base64_decode(get_user_meta($user_id, 'tp_save', 1)));
		$params = shortcode_atts( array(
			'without-table' => 'Without table',
		), $atts );
		global $wb_file;
		ob_start();
		?>
		<div class="tp">
			<div class="wb__loading"></div>
			<div class="gl__guest-titel">Table plan</div>

            <div class="tableplan">
                <div class="tableplan__menu">
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
                                <div class="tp-guest tp-guest--member tp-guest--<?php echo $i; ?>">
                                    <div class="tp-guest__status <?php echo ($guest['status'])?'tp-guest__status--checked':''; ?>"></div>
                                    <div class="tp-guest__name"><?php echo $guest['name']; ?></div>
                                    <div class="tp-guest__family"><?php echo $guest['family']; ?></div>
                                    <div class="tp-guest__role"><?php echo $guest['role']; ?></div>
                                </div>
                                <?php
                                $html = ob_get_clean();
                                if (!isset($guest['table']) OR $guest['table']) {
                                    $guest['table'] = '__default__';
                                }
                                if (!isset($guests_tables[$guest['table']])) {
                                    $guests_tables[$guest['table']] = array();
                                }
                                array_push($guests_tables[$guest['table']], array('html' => $html));
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
                                echo "<div class=\"tp-guests__table\">{$table_name}</div>";
                                foreach ($guests_of_table as $guest_of_table) {
                                    echo $guest_of_table['html'];
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div id="side_menu" style="width: 180px; margin-left: 2px; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(215, 227, 234); border-left-width: 1px; border-left-style: solid; border-left-color: rgb(215, 227, 234); background-color: rgb(255, 255, 255);z-index:1000;">
                        <div class="tp-menu">
                            <i class="fa fa-floppy-o tp-menu-icon tp-menu-icon--save" aria-hidden="true" onclick="ShowSubMenu('m10');return false;" title="Save"></i>
                            <i class="fa fa-print tp-menu-icon tp-menu-icon--print" aria-hidden="true" onclick="/*ShowSubMenu('m11');return false;*/" title="Print"></i>
                            <i class="fa fa-file-text-o tp-menu-icon tp-menu-icon--statistics" aria-hidden="true" onclick="ShowSubMenu('m9');return false;" title="Statistics"></i>
                            <i class="fa fa-cogs tp-menu-icon tp-menu-icon--settings" aria-hidden="true" onclick="ShowSubMenu('m8');return false;" title="Settings"></i>
                        </div>

                        <div id="tablesMenu" class="menu_button" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); /*background-color: rgb(111, 191, 255);*/ height: 36px; padding: 2px 10px;" onclick="ShowSubMenu('m0');"><div class="tp-icon tp-icon--table"></div> Tables »</div>
                        <div id="objectsMenu" class="menu_button" style="top: 84px; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: rgb(255, 255, 255); /*background-color: rgb(111, 191, 255);*/ height: 36px; padding: 2px 10px;" onclick="ShowSubMenu('m12');"><div class="tp-icon tp-icon--object"></div> Objects »</div>
                        <!--                    <div id="guestsMenu" class="menu_button" style="top: 125px; /*background-color: rgb(111, 191, 255);*/ height: 36px; padding-top: 4px; background-position: initial initial; background-repeat: initial initial;" onclick="ShowSubMenu('m6');">&nbsp;<img class="guests_icon" src="--><?php //echo plugins_url('/html/img/planner/blank.png', $wb_file); ?><!--">Guests »</div>-->

                        <div class="menu" style="height:17px;padding-top:1px;top:165px;background-color:#ffffff;border-top:1px solid #D7E3EA">
                            <span style="">&nbsp;Guests without a place:</span>
                        </div>
                        <div id="unseatedGuestList" class="menu" style="height:200px;top:185px;left:0;overflow:auto;border-bottom:1px solid #D7E3EA" title="Drag the guest with the mouse to the plan.">
                        </div>

                        <div class="menu" style="border-bottom:1px solid #D7E3EA;padding-top:5px;">&nbsp;Size: &nbsp;
                            <input id="planSizeX" onblur="Change_Plan_X()" onkeydown="if (event.keyCode == 13) { Change_Plan_X(); }" type="text" style="width:45px;font-size:10px" value="133.8">&nbsp;x&nbsp;
                            <input id="planSizeY" onblur="Change_Plan_Y()" onkeydown="if (event.keyCode == 13) { Change_Plan_Y(); }" type="text" style="width:45px;font-size:10px" value="62.6">
                        </div>


                        <div id="m0" class="menu_content" style="width: 230px; left: 180px; top: 42px; display: none;">
                            <span class="menu_title">Options for tables</span><br>
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
                            &nbsp;&nbsp;&nbsp;Мест: &nbsp;<input type="text" value="4" id="num_table_seats" style="width: 20px">&nbsp;&nbsp; <div id="add_table_name_block" style="display:inline;">Имя: &nbsp;<input type="text" id="add_table_name" value="Стол 1" style="width: 82px"></div><br>
                            <input type="button" onclick="AddNewTable();" value="Добавить стол" style="margin-top: 10px">
                            <div style="position: absolute; top: 4px; right: 4px;">
                                <a href="#" class="close_button" onclick="HideSubMenu('m0');return false;"></a>
                            </div>
                        </div>



                        <div id="m12" class="menu_content" style="width: 230px; left: 180px; top: 83px; display: none;">
                            <span class="menu_title">The objects</span><br>
                            <table style="margin-top: 10px" cellspacing="0" cellpadding="0" border="0">
                                <tbody><tr>
                                    <td style="padding-left:8px;">
                                        <input type="radio" id="otype0" name="object_type" value="0" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;" checked="checked"><img class="table_icon" id="objecticon1" src="<?php echo plugins_url('/html/img/planner/dance_floor.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype0');return true;"></td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="radio" id="otype1" name="object_type" value="1" style="vertical-align:middle;" onclick="AddObjectMenuSelect();return true;"><img class="table_icon" id="objecticon2" src="<?php echo plugins_url('/html/img/planner/DJ.png', $wb_file); ?>" onclick="AddObjectIconMenuClick('otype1');return true;"></td>
                                </tr>
                                <tr>
                                    <td style="text-align:center">Dance floor</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">Пульт диджея</td>
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
                                    <td style="text-align:center">Column</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">Bar/Reception</td>
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
                                    <td style="text-align:center">Table with gifts</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">Table with cake</td>
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
                                    <td style="text-align:center">The object</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align:center">&nbsp;</td>
                                </tr>
                                </tbody></table>
                            <br>
                            <div id="add_object_name_block" style="display:none;">&nbsp;&nbsp;&nbsp;Title: &nbsp;<input type="text" id="add_object_name" style="width: 127px"><br></div>
                            <input type="button" onclick="AddNewObject();" value="Добавить" style="margin-top: 10px">
                            <div style="position: absolute; top: 4px; right: 4px;">
                                <a href="#" class="close_button" onclick="HideSubMenu('m12');return false;"></a>
                            </div>
                        </div>


                        <div id="m6" class="box" style="display: none;">
                            <div class="menu_content_guest" style="width: 700px; height: 380px; top: 50%; left: 50%; margin: -200px 0 0 -350px; display:block; opacity: initial;">
                                <a href="#" onclick="document.getElementById('uploadResult').value=''; document.getElementById('import_guest_dialog').style.display='block' ;return false;">Download guest list from file</a>
                                <div style="width:690px;height:47px;padding:3px 5px 5px 5px;background-color:#6FBFFF;margin-top:5px;">
                                    <table id="guest_list" style="text-align:left; color:#ffffff;">
                                        <tbody>
                                        <tr>
                                            <td style="width:300px">Guest name</td>
                                            <td style="width:75px">Gender</td>
                                            <td style="width:85px">Satus</td>
                                            <td style="width:145px">Menu</td>
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
                                                <input type="button" value=" Добавить " onclick="AddNewGuest();">
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
                                            <h5>How to create a file</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <br><strong>For a Word file:</strong><br>
                                            <ul>
                                                <li>Each guest should be on a separate line.</li>
                                                <li>Save as txt file (File -> Save As)</li>
                                            </ul>
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <br><strong>For Excel file</strong><br>
                                            <ul>
                                                <li>The guest name must be in the first cell of the row</li>
                                                <li>Make sure there is only one column in the file</li>
                                                <li>Save as cvs file (File -> Save As)</li>
                                            </ul>
                                        </td>
                                    </tr>
                                    </tbody></table>

                                <div style="position: absolute; left: 61%; top: 5%; bottom: 5%; border-left: 1px solid gray;"></div>
                                <div style="position:absolute;top:2px;right:2px;"><a href="#" class="close_button" onclick="HideSubMenu('import_guest_dialog');return false;"></a></div>

                            </div>
                        </div>
                    </div>

                    <div id="tablePropertiesMenu" style="position: absolute;  z-index: 2000; display: none; border: 1px solid rgb(215, 227, 234); background-color: rgb(215, 227, 234);">
                        <div class="menu_title" style="top: 0;height:20px;padding-top:5px;padding-left:5px; color:#FFFFFF; background-color: rgb(56, 168, 255); text-align: left;font-size:12px;"><strong>Table</strong></div><!--Стол-->
                        <div id="tablePropAddSeat" class="table_menu_button" style="top: 32px;height:22px;padding-top:5px;text-align:center;" onclick="TableMenuAddSeat(this.parentNode.TableID);">Add chair</div>
                        <div id="tablePropRemoveSeat" class="table_menu_button" style="top: 64px;height:22px;padding-top:5px;text-align:center;" onclick="TableMenuRemoveSeat(this.parentNode.TableID);">Remove chair</div>
                        <div id="tablePropRemove" class="table_menu_button" style="top: 96px;height:22px;padding-top:5px;text-align:center;" onclick="TableMenuDeleteTable(this.parentNode.TableID);">Remove</div>
                        <div id="tablePropRename" class="table_menu_button" style="top: 132px;height:22px;padding-top:5px;text-align:center;" onclick="TableMenuRenameTable(this.parentNode.TableID);">Rename</div>

                        <div style="position: absolute; top: 4px; right: 4px;">
                            <a href="#" class="close_button" onclick="HideSubMenu('tablePropertiesMenu');return false;"></a>
                        </div>
                    </div>

                    <div id="objectPropertiesMenu" style="position: absolute;  z-index: 2000; display: none; border: 1px solid rgb(215, 227, 234); background-color: rgb(215, 227, 234);">
                        <div class="menu_title" style="top: 0;height:20px;padding-top:5px;padding-left:5px; color:#FFFFFF; background-color: rgb(56, 168, 255); text-align: left;font-size:12px;"><strong>Объект</strong></div>
                        <div id="objectPropRename" class="table_menu_button" style="top: 32px;height:22px;padding-top:5px;text-align:center;" onclick="ObjectMenuRename(this.parentNode.ObjectID);">Rename</div>
                        <div id="objectPropRemove" class="table_menu_button" style="top: 64px;height:22px;padding-top:5px;text-align:center;" onclick="ObjectMenuDelete(this.parentNode.ObjectID);">Remove</div>

                        <div style="position: absolute; top: 4px; right: 4px;">
                            <a href="#" class="close_button" onclick="HideSubMenu('objectPropertiesMenu');return false;"></a>
                        </div>
                    </div>

                    <div id="m13" class="box" style="display: none;">
                        <div id="objectRenameMenu" class="menu_content_guest" style="width: 300px; height: 45px; top: 50%; left: 50%; margin: -60px 0 0 -150px; position: absolute;  z-index: 2000; display: block;">
                            <div style="margin-top:5px;">
                                New name:<br><input id="object_edit_name" style="width:240px" type="text" onkeydown="if (event.keyCode == 13) { Rename_Object(); }"> <input onclick="Rename_Object()" value="OK" style="width:43px; margin-top:-10px;margin-left:2px" type="button">
                            </div>
                            <div style="position:absolute;top:2px;left:290px;"><a href="#" class="close_button" onclick="HideSubMenu('m13');return false;"></a></div>
                        </div>
                    </div>

                    <div id="seatPropertiesMenu" style="position: absolute;  z-index: 2000; display: none; border: 1px solid rgb(215, 227, 234); background-color: rgb(215, 227, 234);">
                        <div class="menu_title" style="top: 0;height:20px;padding-top:5px;padding-left:5px;color:#FFFFFF; background-color: rgb(56, 168, 255); text-align: left;font-size:12px;"><strong>Table seat</strong></div>

                        <div id="seatGuestUnseat" class="table_menu_button" style="top: 0;height:22px;padding-top:6px;text-align:center;" onclick="UnseatGuestFromTable(this.parentNode.GuestID);">Empty the chair</div>
                        <div id="seatGuestDelet" class="table_menu_button" style="top: 32px;height:22px;padding-top:6px;text-align:center;" onclick="DeleteGuestFromTable(this.parentNode.GuestID);">Remove guest</div>
                        <div style="position: absolute; top: 4px; right: 4px; cursor:pointer;">
                            <a href="#" class="close_button" onclick="HideSubMenu('seatPropertiesMenu');return false;"></a>
                        </div>
                    </div>

                    <div id="m7" class="box" style="display: none;">
                        <div id="tableRenameMenu" class="menu_content_guest" style="width: 300px; height: 45px; top: 50%; left: 50%; margin: -60px 0 0 -150px; position: absolute;  z-index: 2000; display: block;">
                            <div style="margin-top:5px;">
                                New table name:<br><input id="edit_name" style="width:240px" type="text" onkeydown="if (event.keyCode == 13) { Rename_Table(); }"> <input onclick="Rename_Table()" value="OK" style="width:43px; margin-top:-10px;margin-left:2px" type="button">
                            </div>
                            <div style="position:absolute;top:2px;left:290px;"><a href="#" class="close_button" onclick="HideSubMenu('m7');return false;"></a></div>
                        </div>
                    </div>

                    <div id="m8" class="menu_content" style="width: 416px; left: 183px; top: 110px; /*position: fixed;*/ display: none;">
                        <span class="menu_title">Settings</span><br><br>
                        <input id="show_gridlines" onclick="ShowGridClick();" checked="checked" type="checkbox">Show grid
                        <br><br>
                        <span class="menu_title">Menu options</span><br><br>
                        <input id="meal1" value="Стандартное" size="13" maxlength="14" tabindex="1" type="text">&nbsp;&nbsp;<input id="meal2" value="Детское" size="13" maxlength="14" tabindex="2" type="text">&nbsp;&nbsp;<input id="meal3" value="Вегатарианское" size="13" maxlength="14" tabindex="3" type="text"><br><br>
                        <input id="meal4" value="Вариант 1" size="13" maxlength="14" tabindex="4" type="text">&nbsp;&nbsp;<input id="meal5" value="Вариант 2" size="13" maxlength="14" tabindex="5" type="text">&nbsp;&nbsp;<input id="meal6" value="Вариант 3" size="13" maxlength="14" tabindex="6" type="text"><br><br>
                        <input id="meal7" value="Вариант 4" size="13" maxlength="14" tabindex="7" type="text">&nbsp;&nbsp;<input id="meal8" value="Вариант 5" size="13" maxlength="14" tabindex="8" type="text">&nbsp;&nbsp;<input id="meal9" value="Вариант 6" size="13" maxlength="14" tabindex="9" type="text"><br><br>

                        <input type="button" value=" Сохранить меню " onclick="SaveSettings();HideSubMenu('m8');return false;">

                        <div style="position:absolute;top:4px;right:4px;"><a href="#" class="close_button" onclick="HideSubMenu('m8');return false;"></a></div>
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

                    <div id="m9" class="menu_content" style="width: 180px; left: 183px; top: 110px; /*position: fixed;*/ display: none;">
                        <span class="menu_title">Statistics</span><br>
                        <table style="border-spacing:10px;">
                            <tbody><tr>
                                <td>Number of tables:</td>
                                <td><span id="statTables"></span></td>
                            </tr>
                            <tr>
                                <td>Number of chairs:</td>
                                <td><span id="statSeats"></span></td>
                            </tr>
                            <tr>
                                <td>Number of guests:</td>
                                <td><span id="statGuests"></span></td>
                            </tr>
                            <tr>
                                <td>Guests without a seat:</td>
                                <td><span id="statUnseatGuests"></span></td>
                            </tr>
                            <tr>
                                <td>Free chairs:</td>
                                <td><span id="statFreeSeats"></span></td>
                            </tr>
                            </tbody></table>
                        <div style="padding-left:10px;">
                        </div>
                        <div style="position:absolute;top:4px;right:4px;"><a href="#" class="close_button" onclick="HideSubMenu('m9');return false;"></a></div>
                    </div>

                    <div id="m10" class="box" style="position:fixed; display:none;">
                        <div id="planSaveMenu" class="menu_content_guest" style="width: 300px; height: 45px; top: 150px; left: 40px; position: absolute;  z-index: 2000; display: block;">
                            <div style="margin-top:5px;">
                                Plan Name:<br><input id="edit_plan_name" style="width:240px" type="text"> <input onclick="if ($('#edit_plan_name')[0].value.length > 0) {tablePlan.Name = $('#edit_plan_name')[0].value; TryToSavePlan();}" value="OK" style="width:43px; margin-top:-10px;margin-left:2px" type="button">
                            </div>
                            <div style="position:absolute;top:2px;left:290px;"><a href="#" class="close_button" onclick="HideSubMenu('m10');return false;"></a></div>
                        </div>
                    </div>

                    <div id="m11" class="menu_content" style="width: 270px; left: 183px; top: 110px; /*position: fixed;*/ display: none;">
                        <span class="menu_title">Create PDF for printing</span>
                        <table style="border-spacing:10px;">
                            <tbody><tr>
                                <td>Type of:</td>
                                <td>
                                    <select id="printType" style="width:160px" onchange="PrintTypeChanged(); return true;">
                                        <option value="8" selected="selected">Seating plan</option>
                                        <option value="16">Guest list</option>

                                    </select>
                                </td>
                            </tr>
                            <tr id="printSize">
                                <td>Размер:</td>
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
                                <td>Заголовок:</td>
                                <td><input id="inputPrintTitle" type="text" style="width:150px;"></td>
                            </tr>
                            <tr id="printTables">
                                <td style="text-align:right;"><input id="inputPrintTables" type="checkbox" checked="checked"></td>
                                <td>Contours of tables</td>
                            </tr>
                            <tr id="printSeats">
                                <td style="text-align:right;"><input id="inputPrintSeats" type="checkbox" checked="checked"></td>
                                <td>Contours of chairs</td>
                            </tr>
                            <tr id="printColor">
                                <td style="text-align:right;"><input id="inputPrintColor" type="checkbox"></td>
                                <td>Color print</td>
                            </tr>
                            <tr id="printMenu" style="display:none;">
                                <td style="text-align:right;"><input id="inputPrintMenu" type="checkbox"></td>
                                <td>Menu option</td>
                            </tr>

                            </tbody></table>
                        <input id="printButton" type="button" value=" Скачать PDF файл " onclick="PrintPlan();HideSubMenu('m8');return false;">

                        <div style="position:absolute;top:4px;right:4px;"><a href="#" class="close_button" onclick="HideSubMenu('m11');return false;"></a></div>
                    </div>
                    <form action="/planner/Print" method="POST" id="dataToSubmit">
                        <input type="hidden" name="tablePlanModel" value="">
                    </form>
                </div>
                <div class="tableplan__field">
                    <div id="plannerCanvas" class="plannerCanvas" style="position: absolute; left: 0; top: 0; border: 1px solid rgb(215, 227, 234); z-index: 100; width: 1338px; height: 626px; background-image: url('<? echo plugins_url('/html/img/planner/grid.gif', $wb_file); ?>');" oncontextmenu="return false;" onmousedown="PlannerCanvasMouseDown(); return false;" droppable=""><div style="position: relative; display: inline-block; width: 1338px; height: 626px;" class="kineticjs-content" role="presentation"><canvas style="padding: 0; 0.margin: 0; border: 0 none; background: transparent none repeat scroll 0% 0%; position: absolute; top: 0; left: 0; width: 1338px; height: 626px;" width="1338" height="626"></canvas></div></div>
                </div>
            </div>
		</div>
		<?php
	}
	return ob_get_clean();
}
add_action( 'wp_body_open', 'wb_tableplan_resize_html' );
function wb_tableplan_resize_html(){
	echo "<div id=\"resize_horizontal\" class=\"resize_horizontal\" draggable=\"\" style=\"\"></div>
          <div id=\"resize_vertical\" class=\"resize_vertical\" draggable=\"\" style=\"\"></div>";
}