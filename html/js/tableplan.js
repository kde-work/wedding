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
    var $body = $('body');

    // initialize kinetics
    if ($('.plannerCanvas').length) {
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
    }

    $body.on('click', '.tableplan-line__remove-plan', function () {
        var $this = $(this),
            this_id = $this.data('id');

        if (!confirm('Are you sure you want to delete the Plane?')) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'json', // ответ ждем в json-формате
            data: {
                'id' : this_id,
                'action' : 'wb-tableplan-delete'
            }, // данные для отправки
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) { // событие после удачного обращения к серверу и получения ответа
                console.log(data);
                if (data.ErrorMessage != "") {
                    DlgErrorFromServer(data.ErrorMessage);
                } else {
                    var $line = $this.closest('.tableplan-line');
                    $line.remove();
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
                serverCall = false;
            },
            error: function (xhr, ajaxOptions, thrownError) { // в случае неудачного завершения запроса к серверу
                console.log('wb-tableplan-delete@11: '+xhr.status); // покажем ответ сервера
                console.log('wb-tableplan-delete@12: '+thrownError); // и текст ошибки
            }
        });
    });
});

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

    var data2send = tablePlan.GetData2Save(),
        $body = $('body');

    $.ajax({
        type: 'POST',
        url: wedding_budget.url,
        async: true,
        dataType: 'json', // ответ ждем в json-формате
        data: {
            'data' : data2send,
            'action' : 'wb-tableplan-save'
        }, // данные для отправки
        beforeSend: function (xhr, ajaxOptions, thrownError) {
            $body.addClass('in-process');
        },
        success: function (data) { // событие после удачного обращения к серверу и получения ответа
            if (data.ErrorMessage != "") {
                DlgErrorFromServer(data.ErrorMessage);
            } else {
                tablePlan.PlanID = data.PlanID;
                document.title = tablePlan.Name;
                if (window.location.href.indexOf('=') === -1)
                    history.pushState(null, null, window.location.href + '=' + data.PlanID);
                DlgPlanSaved();
            }
        },
        complete: function (xhr, ajaxOptions, thrownError) {
            $body.removeClass('in-process');
            serverCall = false;
        },
        error: function (xhr, ajaxOptions, thrownError) { // в случае неудачного завершения запроса к серверу
            console.log('wb-tableplan-save-@11: '+xhr.status); // покажем ответ сервера
            console.log('wb-tableplan-save-@12: '+thrownError); // и текст ошибки
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
            '<div><div id="' +
            guest.GuestID +
            '"  class="tp-guest tp-guest--member pot_guest tp-guest--'+ guest.GuestID +'" draggable>' +
            guest.GuestName +
            "</div></div>");
        // var $guest = $('.tp-guest--'+ guest.GuestID);
        // $guest.width($guest.width());
    };
}
