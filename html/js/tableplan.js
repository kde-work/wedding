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
    var leftSideMenu = document.getElementById("side_menu");

    var horizontalResize = document.getElementById("resize_horizontal");
    horizontalResize.style.left =
        browserWidth -
        _browserBorderShift -
        7 /*parseInt(horizontalResize.style.width)*/ +
        "px";
    horizontalResize.style.top =
        parseInt(leftSideMenu.style.top) +
        (browserHeight - _browserBorderShift - parseInt(leftSideMenu.style.top)) /
        2 +
        "px";
    HorizontalResizePositionChanged();

    var verticalResize = document.getElementById("resize_vertical");
    verticalResize.style.left =
        parseInt(leftSideMenu.style.width) +
        (browserWidth - parseInt(leftSideMenu.style.width) - _browserBorderShift) /
        2 +
        "px";
    verticalResize.style.top =
        browserHeight -
        _browserBorderShift -
        7 /*parseInt(verticalResize.style.height)*/ +
        "px";
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
        this.Control = document.getElementById("unseatedGuestList");

        // guest - Guest object
        this.AddGuestToList = function (guest) {
            this.Control.innerHTML +=
                '<div id="' +
                guest.GuestID +
                '"  class="pot_guest" draggable>' +
                guest.GuestName +
                "</div>";
        };
    }

    // editor init
    function InitTablePlan() {
        var modelPlanID = -1;

        tablePlan = new TablePlan(modelPlanID, "New Plan");//Новый План

        // init editor

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