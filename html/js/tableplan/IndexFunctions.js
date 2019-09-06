
function GetRowHTMLbyID(guestID) {
  var htmlRow = "<tr id=\"l_" + guestID + "\">" +
      "<td>&nbsp;" + tablePlan.Guests[guestID].GuestName + "</td>" +
      "<td>&nbsp;" + GuestType2String(tablePlan.Guests[guestID].GuestType) + "</td>" +
      "<td>&nbsp;" + GuestRSVP2String(tablePlan.Guests[guestID].GuestRSVP) + "</td>" +
      "<td>&nbsp;" + tablePlan.MenuId2String(tablePlan.Guests[guestID].GuestMeal) + "</td>" +
      "<td>&nbsp;<a href=\"#\" class=\"edit_guest\" title=\"Редактировать\" onclick=\"Edit_Guest('" + guestID + "');return false;\"></a> &nbsp;<a href=\"#\" class=\"delete_guest\" title=\"Удалить\" onclick=\"Delete_Guest('" + guestID + "');return false;\"></a></td>" +
      "</tr>";
    
  return htmlRow;
}

function ShowSubMenu(id) {
    HideAllSubMenu();

    var subMenu = document.getElementById(id);

    if (id == 'm6') { // guest menu
        // populate options lists
        $('#new_guest_name')[0].value = "";
        PopulatePossibleGuestOptions("new_guest_sex", "new_guest_rsvp", "new_guest_meal");
        
        $("#new_guest_sex").val('0');
        $("#new_guest_rsvp").val('0');
        $("#new_guest_meal").val('0');

        // populate guest list
        $('#guestlist_table > tbody > tr').remove();

        for (var guestID in tablePlan.Guests) {
            $('#guestlist_table > tbody').append(GetRowHTMLbyID(guestID)); //.aeq($('#guestlist_table > tbody > tr').length - 1).before(htmlRow);
        }

        $('#guestlist_table > tbody').append("<tr style=\"padding:0px\">" +
                                               "<td style=\"padding:0px; width:300px;\"><img src=\"/Images/planner/blank.png\" /></td>" +
                                               "<td style=\"padding:0px; width:75px;\"><img src=\"/Images/planner/blank.png\" /></td>" +
                                               "<td style=\"padding:0px; width:85px;\"><img src=\"/Images/planner/blank.png\" /></td>" +
                                               "<td style=\"padding:0px; width:145px;\"><img src=\"/Images/planner/blank.png\" /></td>" +
                                               "<td style=\"padding:0px;\"><img src=\"/Images/planner/blank.png\" /></td>" +
                                               "</tr>");
    }

    if (id == 'm8') { // settings menu
        // populate menu values
        // for (var i = 1; i <= 9; ++i) {
        //     $("#meal" + i)[0].value = tablePlan.MenuList[i];
        // }

        // show grid control
        $("#show_gridlines")[0].checked = !tablePlan.HideGrid;
    }

    if (id == 'm9') {
        // statistic menu

        var lGuestCount = 0;
        var lUnseatedGuests = 0;
        var lTableCount = 0;
        var lSeatCount = 0;
        var lFreeSeatCount = 0;

        for (var guestID in tablePlan.Guests) {
            lGuestCount++;
            if (tablePlan.Guests[guestID].IsUnseated())
                lUnseatedGuests++;
        }

        for (var tableID in tablePlan.Tables) {
            lTableCount++;
            for (var sID in tablePlan.Tables[tableID].Seats) {
                lSeatCount++;
                if (tablePlan.Tables[tableID].Seats[sID].IsFree())
                    lFreeSeatCount++;
            }
        }

        $("#statTables")[0].innerHTML = lTableCount;
        $("#statSeats")[0].innerHTML = lSeatCount;
        $("#statGuests")[0].innerHTML = lGuestCount;
        $("#statUnseatGuests")[0].innerHTML = lUnseatedGuests;
        $("#statFreeSeats")[0].innerHTML = lFreeSeatCount;
    }

    if (id == 'm10') { // Save menu
        if (subMenu == null)
            return;

        // set plan name
        $('#edit_plan_name')[0].value = tablePlan.Name;
    }

    if (id == 'm0') { // add table menu
        AddTableMenuSelect();

        // set default table name
        SetDefaultTableName();
    }

    if (id == 'm12') { // add table menu
        AddObjectMenuSelect();
    }

    if (id == 'm11') { // print menu
        PrintTypeChanged()
    }
    

    if (subMenu != null) {
        if (id == 'm10') { // Save menu
            subMenu.style.display = 'flex'
        } else {
            subMenu.style.display = 'block'
        }
    }
}

function SetDefaultTableName() {
    // set default table name
    $('#add_table_name')[0].value = "Table " + (tablePlan.GetNumberOfTables() + 1);
}

function IsGuestNumberInLicenseLimits(guestCount) {
    return true;
}

function AddNewGuest() {
    // check number of guests.
    var nGuestCount = 0;
    for (var g in tablePlan.Guests) {
        ++nGuestCount;
    }

    if (IsGuestNumberInLicenseLimits(nGuestCount + 1) == false) {
        return;
    }

    var lName = $('#new_guest_name')[0].value;
    if ((lName == null) || (lName == "")) {
        // TODO если из одних пробелов имя, то тоже ничего не делать
        return;
    }
    var lType = $('#new_guest_sex')[0].value;
    var lRSVP = $('#new_guest_rsvp')[0].value;
    var lMeal = $('#new_guest_meal')[0].value;

    var newGuest = tablePlan.AddNewGuest(null, lName, lType, lRSVP, lMeal, null, null);

    $('#guestlist_table > tbody > tr').eq($('#guestlist_table > tbody > tr').length - 1).before(GetRowHTMLbyID(newGuest.GuestID));

    // clear options
    $('#new_guest_name')[0].value = "";
    $("#new_guest_sex").val('0');
    $("#new_guest_rsvp").val('0');
    $("#new_guest_meal").val('0');
}

function AddNewGuestsFromTextArea() {
    // check number of guests.
    var nGuestCount = 0;
    for (var g in tablePlan.Guests) {
        ++nGuestCount;
    }

    // count new guests
    var nNewGuestCount = 0;
    var lines = $('#uploadResult').val().split('\n');
    for (var i = 0; i < lines.length; i++) {
        var g = lines[i].trim();
        if (g != "") {
            ++nNewGuestCount;
        }
    }

    // check for license limitations
    if (IsGuestNumberInLicenseLimits(nNewGuestCount + nGuestCount) == false) {
        return false;
    }

    // add guests
    for (var i = 0; i < lines.length; i++) {
        var g = lines[i].trim();
        if (g != "") {
            var lName = g;
            var lType = 0;
            var lRSVP = 0;
            var lMeal = 0;

            var newGuest = tablePlan.AddNewGuest(null, lName, lType, lRSVP, lMeal, null, null);

            $('#guestlist_table > tbody > tr').eq($('#guestlist_table > tbody > tr').length - 1).before(GetRowHTMLbyID(newGuest.GuestID));

            // clear options
            $('#new_guest_name')[0].value = "";
            $("#new_guest_sex").val('0');
            $("#new_guest_rsvp").val('0');
            $("#new_guest_meal").val('0');
        }
    }

    return true;
}

function HideSubMenu(id) {
    var subMenu = document.getElementById(id);
    if (subMenu != null)
        subMenu.style.display = 'none'
}

function HideAllSubMenu() {
    // m13 - object rename
    // m12 - objects menu
    // m12 - print menu
    // m10 - Save plan
    // m9 - statistics
    // m8 - settings
    // m7 - table rename
    // m6 - guests
    // m5 - print
    // m4 - save
    // m0 - tables

    // tablePropertiesMenu - table properties
    // seatPropertiesMenu - seat properties
    // objectPropertiesMenu - Object's menu

    HideSubMenu('m0');
    HideSubMenu('m4');
    HideSubMenu('m5');
    HideSubMenu('m6');
    HideSubMenu('m7');
    HideSubMenu('m8');
    HideSubMenu('m9');
    HideSubMenu('m10');
    HideSubMenu('m11');
    HideSubMenu('m12');
    HideSubMenu('m13');
    HideSubMenu('tablePropertiesMenu');
    HideSubMenu('seatPropertiesMenu');
    HideSubMenu('objectPropertiesMenu');
}

function PopulatePossibleGuestOptions(controlSexID, controlRsvpID, controlMealID) {

    $("#" + controlSexID).find('option').remove();
    for (var i = 0; i < 7; ++i) {
        $("#" + controlSexID).append('<option value=' + i + '>' + GuestType2String(i) + '</option>');
    }

    $('#' + controlRsvpID).find('option').remove();
    for (var i = 0; i < 4; ++i) {
        $('#' + controlRsvpID).append('<option value=' + i + '>' + GuestRSVP2String(i) + '</option>');
    }

    $('#' + controlMealID).find('option').remove();
    for (var i = 0; i < 10; ++i) {
        $('#' + controlMealID).append('<option value=' + i + '>' + tablePlan.MenuId2String(i) + '</option>');
    }
}


function Edit_Guest(guestID) {
    CancelEditedGuest();

    var htmlRow = "<tr style=\"height:30px;padding:0px;\" id=\"l_" + guestID + "\">" +
                    "<td><input id=\"edit_guest_name\" type=\"text\" value=\"" + tablePlan.Guests[guestID].GuestName + "\" style=\"width:290px\"></td>" +
                    "<td><select id=\"edit_guest_sex\" style=\"width:65px\"></select></td>" +
                    "<td><select id=\"edit_guest_rsvp\" style=\"width:75px\"></select></td>" +
                    "<td><select id=\"edit_guest_meal\" style=\"width:135px\"></select></td>" +
                    "<td><input type=\"button\" value=\" ОК \" onclick=\"SaveEditedGuest('" + guestID + "');\"></td>" +
                   "</tr>";

    $("#l_" + guestID).replaceWith(htmlRow);

    PopulatePossibleGuestOptions("edit_guest_sex", "edit_guest_rsvp", "edit_guest_meal");

    $("#edit_guest_sex").val(tablePlan.Guests[guestID].GuestType);
    $("#edit_guest_rsvp").val(tablePlan.Guests[guestID].GuestRSVP);
    $("#edit_guest_meal").val(tablePlan.Guests[guestID].GuestMeal);
}

function SaveEditedGuest(guestID) {
    var lName = $('#edit_guest_name')[0].value;
    if ((lName == null) || (lName == "")) {
        // TODO если из одних пробелов имя, то тоже ничего не делать
        return;
    }

    var lType = $('#edit_guest_sex')[0].value;
    var lRSVP = $('#edit_guest_rsvp')[0].value;
    var lMeal = $('#edit_guest_meal')[0].value;

    tablePlan.Guests[guestID].GuestName = lName;
    tablePlan.Guests[guestID].GuestType = lType;
    tablePlan.Guests[guestID].GuestRSVP = lRSVP;
    tablePlan.Guests[guestID].GuestMeal = lMeal;

    $("#l_" + guestID).replaceWith(GetRowHTMLbyID(guestID));

    // update unseated guest list seated guests

    // unseated guest list
    if ($("#" + guestID).length == 1)
        $("#" + guestID).html(lName);

    // seated guest
    var guestObject = tablePlan.Guests[guestID];
    if (!guestObject.IsUnseated()) {
        tablePlan.Tables[guestObject.GuestTable].Seats[guestObject.GuestSeat].SetGuest(guestID);
        kineticLayer.draw();
    }
}

function CancelEditedGuest() {
    var editNames = $("#edit_guest_name");

    if (editNames.length == 0)
        return;

    for (var i = 0; i < editNames.length; ++i) {
        var rowID = editNames[i].parentNode.parentNode.id;

        var guestID = rowID.substring(2);

        $("#" + rowID).replaceWith(GetRowHTMLbyID(guestID));
    }
}

function Delete_Guest(guestID) {
    // delete from seated, unseated and full guests list

    if (!confirm('Вы уверены, что хотите удалить гостя \'' + tablePlan.Guests[guestID].GuestName + '\'?')) {
        return;
    }

    // seated guests
    var guestObject = tablePlan.Guests[guestID];
    if ((guestObject != null) && (!guestObject.IsUnseated())) {
        tablePlan.Tables[guestObject.GuestTable].Seats[guestObject.GuestSeat].UnSitGuest();
        kineticLayer.draw();
    }

    // unseated guest list
    if ($("#" + guestID).length == 1)
        $("#" + guestID).remove();

    // remove from full guest list
    delete tablePlan.Guests[guestID];

    // remove from table
    $("#l_" + guestID).remove();
}

function CheckTableAndObjectLimitation(objectName) {
    return true;
}

function AddNewTable() {
    // check number of tables.
    if (!CheckTableAndObjectLimitation('table'))
        return;

    var lNumSeats = parseInt(document.getElementById('num_table_seats').value);
    var sTableName = document.getElementById('add_table_name').value;
    var tableType = 0;

    var selected = $('[name="table_type"]:checked');
    if (selected.length > 0) {
        tableType = parseInt(selected.val());
    }

    tablePlan.AddNewTable(null, tableType, lNumSeats, sTableName, 400, 200, 0, null);
    kineticLayer.draw();

    SetDefaultTableName();
}

function Change_Plan_X() {
    var editPlanWidth = document.getElementById("planSizeX");
    SetPlanWidth(parseFloat(editPlanWidth.value) * 10);
}

function Change_Plan_Y() {
    var editPlanHeight = document.getElementById("planSizeY");
    SetPlanHeight(parseFloat(editPlanHeight.value) * 10);
}

function SetPlanHeight(height) {
    var $plannerField = $(".plannerCanvas"),
        verticalResize = document.getElementById("resize_vertical");

    verticalResize.style.top = $plannerField.offset().top + height - 2 + 'px';
    VerticalResizePositionChanged();
}

function SetPlanWidth(width) {
    var $plannerField = $(".plannerCanvas"),
        horizontalResize = document.getElementById("resize_horizontal");

    $plannerField.width(width);
    SetHtmlBodyWidth(width, $plannerField);

    horizontalResize.style.left = Math.floor(width + $plannerField.offset().left - 2) + 'px';
    HorizontalResizePositionChanged(width);
}

function HorizontalResizePositionChanged(is_width) {
    var $plannerField = $(".plannerCanvas");
    var horizontalResize = document.getElementById("resize_horizontal"),
        offset_left = $plannerField.offset().left,
        $tableplan__field = $('.tableplan__field'),
        planner_width;

    if (is_width) {
        planner_width = is_width;
    } else {
        planner_width = Math.floor(parseInt(horizontalResize.style.left) + 2 - offset_left);
    }

    // SetHtmlBodyWidth(planner_width, $plannerField);
    $tableplan__field.width(planner_width);
    $plannerField.width(planner_width);

    var editPlanWidth = document.getElementById("planSizeX");
    editPlanWidth.value = planner_width / 10;

    // move vertical size box
    var verticalStyle = document.getElementById("resize_vertical").style;
    verticalStyle.left = Math.floor(planner_width / 2 + parseInt($plannerField.css('left')) - 2 + offset_left) + 'px';

    // resize stage
    kineticStage.setWidth(planner_width);
}

function VerticalResizePositionChanged() {
    var $plannerField = $(".plannerCanvas"),
        $verticalResize = $(".resize_vertical"),
        offset_top = $plannerField.offset().top,
        planner_height = parseInt($verticalResize.css('top')) - offset_top + 2,
        $tableplan = $('.tableplan');
    
    $plannerField.height(planner_height);
    $tableplan.height(planner_height);

    var editPlanHeight = document.getElementById("planSizeY");
    editPlanHeight.value = planner_height / 10;

    // move horizontal size box
    var horizontalStyle = document.getElementById("resize_horizontal").style;
    horizontalStyle.top = Math.floor(planner_height / 2 - 2 + offset_top) + 'px';

    // resize stage
    kineticStage.setHeight(planner_height);
}

function SetHtmlBodyWidth(width, $plannerField) {
    var $body = $('body'),
        $html = $('html'),
        offset_left = $plannerField.offset().left,
        $container = $('.container');

    // if Planner > window width
    if ((window.innerWidth) < (width + offset_left)) {
        $container.css({
            'max-width' : 'initial'
        });
        $body.width(width + offset_left);
        $html.css({
            'overflow' : 'auto'
        });
        $body.width(width + $plannerField.offset().left);
    } else {
        $container.css({
            'max-width' : ''
        });
        $html.css({
            'overflow' : ''
        });
        $body.css({
            'width' : 'auto'
        });
    }
}

function SetTopLeftOfPopupMenu(e, subMenu) {
    var $plannerField = $(".plannerCanvas"),
        offset_left = $plannerField.offset().left,
        offset_top = $plannerField.offset().top;

    e = fixEvent(e);
    subMenu.style.left = e.pageX - offset_left + 'px';
    subMenu.style.top = e.pageY - offset_top + 'px';

    subMenu.style.display = 'block'
}

function UnseatGuestFromTable(guestID) {
    // remove from seat
    var guestObject = tablePlan.Guests[guestID];
    if ((guestObject != null) && (!guestObject.IsUnseated())) {
        tablePlan.Tables[guestObject.GuestTable].Seats[guestObject.GuestSeat].UnSitGuest();
        kineticLayer.draw();
    }

    // add to unseated guests list
    unseatedGuestListControl.AddGuestToList(tablePlan.Guests[guestID]);

    // close menu
    HideSubMenu('seatPropertiesMenu');
}

function DeleteGuestFromTable(guestID) {
    Delete_Guest(guestID);

    // close menu
    HideSubMenu('seatPropertiesMenu');
}

function TableMenuAddSeat(tableID) {
    tablePlan.AddSeatToTable(tableID);

    HideSubMenu('tablePropertiesMenu');
}

function TableMenuRemoveSeat(tableID) {
    // check for minimum table seats
    if (tablePlan.Tables[tableID] <= MinimumTableSeats(tablePlan.Tables[tableID].TableType))
        return;
    
    // find and remove last empty seat
    var emptySeatID = null;
    for (var sID in tablePlan.Tables[tableID].Seats) {
        if (!tablePlan.Tables[tableID].Seats[sID].IsFree()) {
            continue;
        }
        emptySeatID = sID;
    }

    if (emptySeatID == null) {
        // table is full
        DlgNoEmptySeats();
    }
    else {
        tablePlan.Tables[tableID].DeleteSeat(emptySeatID);
    }

    HideSubMenu('tablePropertiesMenu');
}

function TableMenuRenameTable(tableID) {
    HideSubMenu('tablePropertiesMenu');

    var subMenu = document.getElementById('tableRenameMenu');
    if (subMenu == null)
        return;
    subMenu.TableID = tableID;

    // set table name
    $('#edit_name')[0].value = tablePlan.Tables[tableID].TableNameObject.getText();

    // show parent div
    ShowSubMenu('m7');
}

function Rename_Table() {
    var newName = $('#edit_name')[0].value;
    var tableID = $('#tableRenameMenu')[0].TableID;

    tablePlan.Tables[tableID].SetName(newName);
    kineticLayer.draw();

    HideSubMenu('m7');
}

function TableMenuDeleteTable(tableID) {
    //Вы уверены, что хотите удалить стол и перенести всех гостей с него в список гостей без места?
    if (!confirm('Are you sure you want to delete the table \'' + tablePlan.Tables[tableID].TableNameObject.getText() + '\' and transfer all guests from it to the guest list without a seat?')) {
        HideSubMenu('tablePropertiesMenu');
        return;
    }

    // move guests to unseated list
    for (var sID in tablePlan.Tables[tableID].Seats) {
        if (tablePlan.Tables[tableID].Seats[sID].IsFree()) {
            continue;
        }

        var guestID = tablePlan.Tables[tableID].Seats[sID].GuestID;
        if ((guestID == null) || (guestID == ""))
            continue;

        tablePlan.Tables[tableID].Seats[sID].UnSitGuest();
        // add to unseated guests list
        unseatedGuestListControl.AddGuestToList(tablePlan.Guests[guestID]);
    }

    // delete table 
    tablePlan.Tables[tableID].destroy();
    delete tablePlan.Tables[tableID];

    kineticLayer.draw();
    HideSubMenu('tablePropertiesMenu');
}

function ObjectMenuRename(objectID) {
    HideSubMenu('objectPropertiesMenu');

    var subMenu = document.getElementById('objectRenameMenu');
    if (subMenu == null)
        return;
    subMenu.ObjectID = objectID;

    // set table name
    $('#object_edit_name')[0].value = tablePlan.RectObjects[objectID].ObjectNameObject.getText();

    // show parent div
    ShowSubMenu('m13');
}

function Rename_Object() {
    var newName = $('#object_edit_name')[0].value;
    var objectID = $('#objectRenameMenu')[0].ObjectID;

    tablePlan.RectObjects[objectID].SetName(newName);
    kineticLayer.draw();

    HideSubMenu('m13');
}

function ObjectMenuDelete(objectID) {
    //Вы уверены, что хотите удалить
    if (!confirm('Are you sure you want to delete \'' + tablePlan.RectObjects[objectID].ObjectNameObject.getText() + '\'?')) {
        HideSubMenu('objectPropertiesMenu');
        return;
    }

    // delete table 
    tablePlan.RectObjects[objectID].destroy();
    delete tablePlan.RectObjects[objectID];

    kineticLayer.draw();
    HideSubMenu('objectPropertiesMenu');
}


function SaveSettings() {
    for (var i = 1; i <= 9; ++i) {
        tablePlan.MenuList[i] = $("#meal" + i)[0].value;
    }
}

function ShowGrid() {
    if (tablePlan.HideGrid == true) {
        window.plannerCanvas = $("#plannerCanvas")[0].style.backgroundImage;
        $("#plannerCanvas")[0].style.backgroundImage = 'url()';
    }
    else {
        $("#plannerCanvas")[0].style.backgroundImage = window.plannerCanvas;
    }
}

function ShowGridClick() {
    // show grid control
    tablePlan.HideGrid = !$("#show_gridlines")[0].checked;

    ShowGrid();
}

function PlannerCanvasMouseDown() {
    HideAllSubMenu();
}

// some radio button was click on AddTableMenu
function AddTableMenuSelect() {

    var selected = $('[name="table_type"]:checked');

    if (selected.length > 0) {
        if (selected.val() == 7) {
            $("#add_table_name_block")[0].style.display = 'none';
        }
        else {
            $("#add_table_name_block")[0].style.display = 'inline';
        }
    }
}

// click on table picture instead od radio button
function AddTableIconMenuClick(tableTypeId) {
    $("#" + tableTypeId)[0].checked = true;
    AddTableMenuSelect();
}

// some radio button was click on AddTableMenu
function AddObjectMenuSelect() {

    var selected = $('[name="object_type"]:checked');

    if (selected.length > 0) {
        if (selected.val() == 6) {
            $("#add_object_name_block")[0].style.display = 'inline';
        }
        else {
            $("#add_object_name_block")[0].style.display = 'none';
        }
    }
}

// click on table picture instead od radio button
function AddObjectIconMenuClick(objectTypeId) {
    $("#" + objectTypeId)[0].checked = true;
    AddObjectMenuSelect();
}

function AddNewObject() {
    // check number of tables.
    if (!CheckTableAndObjectLimitation('object'))
        return;

    var sObjectName = document.getElementById('add_object_name').value;
    var objectType = 0;

    var selected = $('[name="object_type"]:checked');
    if (selected.length > 0) {
        objectType = parseInt(selected.val());
    }

    if (objectType != 6)
        tablePlan.AddNewRectObject(null, RectObjectName[objectType], 400, 200, RectObjectWidth[objectType], RectObjectHeight[objectType]);
    else
        tablePlan.AddNewRectObject(null, sObjectName, 400, 200, RectObjectWidth[objectType], RectObjectHeight[objectType]);

    kineticLayer.draw();
}

function ShowDialog(message, buttons, h, w) {

    $("#alertDlg")[0].style.width = w + 'px';
    $("#alertDlg")[0].style.height = h + 'px';

    $("#alertDlg").css("margin-top", -1*h+"px");
    $("#alertDlg").css("margin-left", -0.5*w+"px");

    $("#alertDlgHide")[0].style.left = (w - 10) + 'px';

    $("#alertDlgText")[0].innerHTML = message;
    $("#alertDlgButtons")[0].innerHTML = buttons;

    $('#alertDlgBox')[0].style.display = 'block'

    //ShowSubMenu('alertDlgBox');
}

function DlgPlanSaved(){
    ShowDialog("Seating plan saved. <br />", "<input  type=\"button\" value=\" OK \" onclick=\"HideSubMenu('alertDlgBox');return false;\">", 80, 220);
}

function DlgErrorDuringSave() {
    ShowDialog("An error occurred while saving.<br /><br /><br />", "<input  type=\"button\" value=\" OK \" onclick=\"HideSubMenu('alertDlgBox');return false;\">", 100, 220);
}

function DlgErrorFromServer(mes) {

    // ex:
    // mes = "Невозможно сохранить. Превышено максимальное число столов и объектов: 200."
    // mes = "Необходимо войти в систему, чтобы редактировать план рассадки."
    ShowDialog("<br />" + mes + "<br /><br /><br />", "<input  type=\"button\" value=\" OK \" onclick=\"HideSubMenu('alertDlgBox');return false;\">", 120, 300);
}

function DlgOfferToBuy(mes) {
    ShowDialog("<br />" + mes + "<br /><br /><br />",
        "<a style=\"text-decoration:none;\" href=\"/rassadka/prices-wedding-table-planner\" target=\"blank\" onclick=\"HideSubMenu('alertDlgBox');return true;\"><input  type=\"button\" value=\" Улучшить акаунт \" \"></a>&nbsp;&nbsp;&nbsp;<input  type=\"button\" value=\" Позже \" onclick=\"HideSubMenu('alertDlgBox');return false;\">",
        120, 400);
}

function DlgOfferToRegister(mes) {

    ShowDialog("<br />" + mes + "<br /><br /><br />",
        "<a style=\"text-decoration:none;\" href=\"/rassadka/prices-wedding-table-planner\" target=\"blank\" onclick=\"HideSubMenu('alertDlgBox');return true;\"><input  type=\"button\" value=\" Sign up \" \"></a>&nbsp;&nbsp;&nbsp;<input  type=\"button\" value=\" Later \" onclick=\"HideSubMenu('alertDlgBox');return false;\">",
        120, 400);
}

function DlgNoEmptySeats() {
    ShowDialog("There are no empty chairs. <br /><br /><br />", "<input  type=\"button\" value=\" OK \" onclick=\"HideSubMenu('alertDlgBox');return false;\">", 80, 220);
}

function DlgErrorUploadGuest() {
    ShowDialog("An error occurred while loading. <br /><br /><br />", "<input  type=\"button\" value=\" OK \" onclick=\"HideSubMenu('alertDlgBox');return false;\">", 80, 260);
}

function PrintPage() {
    var $body = $('body'),
        $tableplan__field = $('.plannerCanvas');

    $body.addClass('tp-print');
    $body.width($tableplan__field.width());
    $body.height($tableplan__field.height());
    window.print();
    $body.removeClass('tp-print');
    $body.width('initial');
    $body.height('initial');
}

function PrintTypeChanged() {

    if ($('#printType')[0].value == 1024) {
        $('#printButton')[0].value = ' Download package files ';

        $('#printSize')[0].style.display = 'none';
        $('#printTitle')[0].style.display = 'none';
        $('#printTables')[0].style.display = 'none';
        $('#printSeats')[0].style.display = 'none';
        $('#printColor')[0].style.display = 'none';
        $('#printMenu')[0].style.display = 'none';
    }
    else {
        $('#printButton')[0].value = ' Download PDF file ';
        $('#printSize')[0].style.display = '';
        $('#printTitle')[0].style.display = '';

        if ($('#printType')[0].value == 8) {
            $('#printTables')[0].style.display = '';
            $('#printSeats')[0].style.display = '';
            $('#printColor')[0].style.display = '';
            $('#printMenu')[0].style.display = 'none';
        }
        else {
            $('#printTables')[0].style.display = 'none';
            $('#printSeats')[0].style.display = 'none';
            $('#printColor')[0].style.display = 'none';
            $('#printMenu')[0].style.display = '';
        }
    }
}