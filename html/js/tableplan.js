unregGuests = 10;
unregTables = 5;
// free user
freeGuests = 20;
freeTables = 10;
// personal
personalGuests = 200;
personalTables = 50;
// professional
profGuests = 1000;
profTables = 200;


var kineticStage = null;
var kineticLayer = null;
var tablePlan = null;
var unseatedGuestListControl = null;

var dragAndDropSeatOver = null;
var serverCall = false;

$(function () {
    // block default context menu
    document.oncontextmenu = function () {
        return false;
    };
    var browserWindow = $(window);
    var browserWidth = browserWindow.width();
    var browserHeight = browserWindow.height();

    // initialize kinetics
    kineticStage = new Kinetic.Stage({
        container: "plannerCanvas",
        width: 10,
        height: 10
    });

    kineticLayer = new Kinetic.Layer();

    // add the layer to the stage
    kineticStage.add(kineticLayer);

    // set position of resize boxes. Maximum for current screen resolutions
    var $tableplan = $(".tableplan"),
        $horizontalResize = $(".resize_horizontal"),
        $resize_vertical = $(".resize_vertical"),
        offset_top = $tableplan.offset().top,
        offset_left = $tableplan.offset().left;

    $horizontalResize.css({
        'left' : Math.floor(offset_left + $tableplan.outerWidth()) + "px",
        'top' : Math.floor(offset_top + $tableplan.height() / 2) + "px"
    });

    $resize_vertical.css({
        'left' : Math.floor(offset_left + $tableplan.outerWidth() / 2) + "px",
        'top' : offset_top + $tableplan.height() + "px"
    });

    HorizontalResizePositionChanged();
    VerticalResizePositionChanged();

    unseatedGuestListControl = new UnseatedGuestListControl();

    // init editor
    InitTablePlan();

    function RegistrationRequiredForSave() {
        return;
        // var r = confirm(
        //     "Сохранение доступно только для зарегистрированных пользователей. Хотите зарегистрироваться?"
        // );
        // if (r == true) {
        //     window.location.href = "/rassadka/prices-wedding-table-planner";
        // } else {
        //     return;
        // }
    }

    function TryToSavePlan() {
        // user authenticated or not
        // RegistrationRequiredForSave();
        SaveCurrentPlan();
        HideAllSubMenu();
    }

    function SaveCurrentPlan() {
        if (serverCall == true) return;

        serverCall = true;

        var data2send = tablePlan.GetData2Save();

        $.ajax({
            url: "/planner/Save",
            type: "POST",
            data: JSON.stringify({changedTablePlan: data2send}),
            dataType: "json",
            contentType: "application/json; charset=utf-8",
            traditional: true,
            success: function (data) {
                if (data.ErrorMessage != "") {
                    DlgErrorFromServer(data.ErrorMessage);
                } else {
                    tablePlan.PlanID = data.PlanID;
                    document.title = tablePlan.Name;
                    DlgPlanSaved();
                }
            },
            complete: function (data) {
                serverCall = false;
            },
            error: function (data) {
                DlgErrorDuringSave();
            }
        });
    }

    function PrintCurrentPlan(printParams) {
        var data2send = tablePlan.GetData2Save();
        data2send.PlanTitle = $("#inputPrintTitle")[0].value;
        data2send.PrintParameters = printParams;

        $("input[name=tablePlanModel]").val(JSON.stringify(data2send));

        $("form#dataToSubmit").submit();
    }

    // Unseated guest list object control
    function UnseatedGuestListControl() {
        // guest - Guest object
        this.AddGuestToList = function (guest, table_id) {
            if (!table_id) table_id = '__default__';
            var $table_box = $('.tp-guests__table-box--' + table_id);

            $table_box.append(
                '<div id="' +
                guest.GuestID +
                '"  class="tp-guest tp-guest--member pot_guest tp-guest--'+ guest.GuestID +'" draggable>' +
                guest.GuestName +
                "</div>");
            var $guest = $('.tp-guest--'+ guest.GuestID);
            $guest.width($guest.width());
        };
    }

    // editor init
    function InitTablePlan() {
        var modelPlanID = -1;

        SetPlanWidth(2113);
        SetPlanHeight(610);

        tablePlan = new TablePlan(modelPlanID, "New Plan");//Новый План

        // init editor
// init editor
        var seatsId = [];
        seatsId.push('35163EAD-EF85-CEFF-75D2-99DB95E79A55');
        seatsId.push('5EE4BAA9-7E11-0B58-C981-F8122F297C27');
        seatsId.push('DC0D2B03-865A-B41E-0EE0-B429F1D1A107');
        seatsId.push('F506D348-DD18-C679-2721-6F1D5170F0FE');
        seatsId.push('11B3F84B-4B93-862F-580F-FDD5F3DF3BA3');
        tablePlan.AddNewTable('9BD4DE97-6930-133C-5B43-43271D534FF1', 0, 5, 'Table 2', 220, 186, 0, seatsId);
        var seatsId = [];
        seatsId.push('F11B4B8B-7441-99FC-5505-DAF35B004B7C');
        seatsId.push('8C5EB468-9AB5-8C53-384F-334C491340E9');
        seatsId.push('78688A48-CBEC-8DAA-8911-C36C2538C5F7');
        seatsId.push('DB67D0E8-CD8D-A763-6435-A8F780BF0BF6');
        seatsId.push('C06D902C-46A1-93FF-10EE-6964D58F4841');
        tablePlan.AddNewTable('B7CC5E7F-ED55-8CCD-0CB6-161ECDC902F8', 5, 5, 'Table 2', 1767, 336, 0, seatsId);
        tablePlan.AddNewRectObject('1E6A28EE-109B-0864-FD00-91D0D5D71E5A', 'Table с тортом', 474, 400, 120, 120);
        tablePlan.AddNewGuest('454FF075-7DFC-472B-BACD-6126A84CAF43', 'qwe1 2', 1, 0, 0, '9BD4DE97-6930-133C-5B43-43271D534FF1', 'F506D348-DD18-C679-2721-6F1D5170F0FE');
        tablePlan.AddNewGuest('4DCB5537-DE2C-BBC9-A521-6CBD81235546', '34234wr', 3, 1, 0, '', '');
        tablePlan.AddNewGuest('D22C3FD9-ABD3-0C31-86D0-C7A9C3398885', 'qwsdsd sdg', 6, 1, 2, '', '');
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
});