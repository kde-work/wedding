(function ($) {
    if (!$) {
        console.error('Объекты jQuery и $ отсутствуют');
        return;
    }

    window.pickerDefaults = {
        showAnim: 'slideDown',
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        changeMonth: true,
        changeYear: true,
        yearRange: "-0:+8",
        dateFormat: "yy-mm-dd",
        monthNames: ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"],
        monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des"],
        dayNames: ["Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag"],
        dayNamesMin: ["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
        dayNamesShort: ["Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"],
        firstDay: 1,
        nextText: "Neste måned",
        prevText: "Forrige måned",
        weekHeader: "U",
        currentText: "I dag",
        closeText: "Lukk"
    };

    // выбор даты
    $(function(){
        var showPicker = false;
        $.datepicker.setDefaults(window.pickerDefaults);

        // Инициализация
        $('.datepicker:not(.hasDatepicker)').datepicker();
        // $(".ui-datepicker").on("mouseenter", function() {
        //     if (!showPicker) {
        //         showPicker = true;
        //         //Reverse the years
        //         var dropYear = $("select.ui-datepicker-year");
        //         dropYear.find('option').each(function() {
        //             dropYear.prepend(this);
        //         });
        //     }
        // });
    });
}($ || window.jQuery));
// end of file