(function ($) {    if (!$) {        console.error('JQuery and $ objects are missing');        return;    }    var $body = $('body');    // Load page    function is_td() {        return $('.wb-todo-table').length;    }    function stick_search() {        var $search_box = $('.wb-search-input'),            search_height = $search_box.height(),            $next_block = $('.wb-search-input + *'),            $wpadminbar = $('#wpadminbar'),            wpadminbar_height = 0,            $eo_page = $('.kleo-main-header');        if ($wpadminbar.length) {            wpadminbar_height = $wpadminbar.height();        }        $(window).scroll(function() {            if ($search_box.length && $eo_page.length) {                if (($(this).scrollTop() + wpadminbar_height + $eo_page.height()) > ($next_block.offset().top)) {                    if (!$search_box.hasClass("stick")) {                        $search_box.hide(0);                        $search_box.addClass("stick");                        $search_box.css("top", $eo_page.height() + wpadminbar_height);                        $next_block.css("padding-top", search_height + 'px');                        $search_box.fadeIn();                    }                } else {                    if ($search_box.hasClass("stick")){                        $search_box.fadeOut(150, function() {                            $search_box.removeClass("stick");                            $next_block.css("padding-top", 0);                            $search_box.show(0);                        });                    }                }            }        });    }    $(function () {        stick_search();    });    $(window).resize(function() {        stick_search();    });    // Search Input    $(function () {        $body.on('keyup', '.wb-search__input', function () {            wb_filters();        });    });    function wb_filter_search() {        var $this = $('.wb-search__input'),            this_val = $this.val().toLowerCase(),            $gl__item = $('.wb__tr'),            $month = $('.wb__tr--month');        $month.removeClass('wb-todo-hide-by-filter--search');        if ($this.length && this_val) {            $gl__item.each(function () {                var $this = $(this),                    $line_name = $('.wb-todo-table__title', $this),                    $date_title = $('.wb__date-title', $this);                if (                    $line_name.length && $line_name.text().toLowerCase().indexOf(this_val) === -1 ||                    $date_title.length && $date_title.data('val').toLowerCase().indexOf(this_val) === -1                ) {                    $this.closest('.wb__tr').addClass('wb-todo-hide-by-filter--search');                }            });        }        if(wb_is_filter(['category','assigned','expire','done'])) {            console.log(11111);            $month.addClass('wb-todo-hide-by-filter--search');        }    }    // New line in Assigned    $(function () {        $body.on('focus', '.td-add-new__new-assigned', function () {            var $this = $(this),                $parent_box = $this.closest('.td-checkbox-list__item--new'),                $input = $('.td-add-new__assigned--new', $parent_box);            $input.trigger('click');        });        $body.on('change', '.td-add-new__new-assigned', function () {            var $this = $(this),                $parent_box = $this.closest('.td-checkbox-list__item--new'),                $input = $('.td-add-new__assigned--new', $parent_box),                this_val = $this.val()                    .replace(/ /g, ',')                    .replace(/ /g, '"')                    .replace(/ /g, '[')                    .replace(/ /g, ']')                    .replace(/ /g, "'");            $input.val(this_val);        });    });    // Done Btn    $(function () {        $body.on('change', 'input.wb-todo-table__done', function () {            var $this = $(this),                this_id = $this.data('id'),                $input = $('.td-add-new__done--' + this_id),                $form = $('.td-add-new__form[data-id="' + this_id + '"]');            // if (!confirm('Are you sure you want to mark the task completed?')) {            //     return;            // }            $input.val($this.prop('checked')*1);            $form.trigger('submit');        });    });    // Delete Line    $(function () {        $body.on('click', '.td-add-new__delete', function () {            var $this = $(this),                this_id = $this.data('id'),                this_budget = $this.data('budget');            if (!confirm('Are you sure you want to delete this item? It also removes the budget associated with the task!')) {                return;            }            $.ajax({                type: 'POST',                url: wedding_budget.url,                async: false,                dataType: 'JSON',                data: {                    'action': 'wb_todo_delete',                    'id': this_id,                    'budget': this_budget                },                success: function (data) {                    var $box = $('.wb-todo-ajax-box');                    if ($box.length && data.html !== void 0) {                        $box.html(data.html);                        wb_filters();                        $('.datepicker:not(.hasDatepicker)').datepicker();                    }                },                error: function (xhr, ajaxOptions, thrownError) {                    console.log('wb_todo_delete-@11: '+xhr.status);                    console.log('wb_todo_delete-@12: '+thrownError);                }            });        });    });    // Saved New task    $(function () {        window.wb_object = [];        $body.on('submit', '.td-add-new__form', function (e) {            e = e || window.event;            e.preventDefault ? e.preventDefault() : (e.returnValue = false);            var $this = $(this),                form_data,                $td_add_new__note__empty = $('.td-add-new__note--empty', $this),                $body = $('body');            if ($body.hasClass('in-process')) {                return false;            }            $td_add_new__note__empty.val('');            form_data = $this.serialize();            $.ajax({                type: 'POST',                url: wedding_budget.url,                async: false,                dataType: 'JSON',                data: form_data,                beforeSend: function (xhr, ajaxOptions, thrownError) {                    $body.addClass('in-process');                },                success: function (data) {                    var $box = $('.wb-todo-ajax-box');                    if ($box.length && data.html !== void 0) {                        $box.html(data.html);                        wb_filters();                        $('.datepicker:not(.hasDatepicker)').datepicker();                    }                    // window.location.reload();                },                complete: function (xhr, ajaxOptions, thrownError) {                    $body.removeClass('in-process');                },                error: function (xhr, ajaxOptions, thrownError) {                    console.log('wb_get_save-@11: '+xhr.status);                    console.log('wb_get_save-@12: '+thrownError);                }            });        });    });    // Main filter fn    $(function () {        $body.on('click', '.wb-todo-filter__click', wb_filters);    });    function wb_filters() {        wb_details_close();        wb_tr_clear();        wb_filter_checkbox("category");        wb_filter_checkbox("assigned");        wb_filter_checkbox("done");        wb_filter_expire();        wb_filter_search();        wb_even();    }    // Filter by Category and Assigned    function wb_filter_checkbox(filter_type) {        var $categories = $('[data-type="'+filter_type+'"]'),            $checked = $('[data-type="'+filter_type+'"]:checked');        if (!$checked.length && filter_type != 'done') {            return;        }        $categories.each(function () {            var $this = $(this),                this_id = $this.data('id'),                $target_tr = $('.wb-todo__tr-'+filter_type+'--'+this_id);            if (!$this.prop('checked')) {                $target_tr.addClass('wb-todo-hide-by-filter--' + filter_type);            }/* else {                $target_tr.removeClass('wb-todo-hide-by-filter--' + filter_type);            }*/        });        $categories.each(function () {            var $this = $(this),                this_id = $this.data('id'),                $target_tr = $('.wb-todo__tr-'+filter_type+'--'+this_id);            if ($this.prop('checked')) /*{                $target_tr.addClass('wb-todo-hide-by-filter--' + filter_type);            } else */{                $target_tr.removeClass('wb-todo-hide-by-filter--' + filter_type);            }        });    }    // Filter by Expire    function wb_filter_expire() {        var $checked = $('[data-type="expire"]:checked');        if (!$checked.length) {            return;        }        var $this = $checked,            this_id = $checked.data('id'),            $tr = $('.wb-todo__tr');        $tr.each(function () {            var $this = $(this),                this_expire = $this.data('expire');            if (this_id == 'show-expired') {                if (this_expire > 0) {                    $this.addClass('wb-todo-hide-by-filter--expire');                }            }            if (this_id == '10-d') {                if (this_expire < 0 || this_expire > 10) {                    $this.addClass('wb-todo-hide-by-filter--expire');                }            }            if (this_id == '30-d') {                if (this_expire < 0 || this_expire > 30) {                    $this.addClass('wb-todo-hide-by-filter--expire');                }            }        });    }    // Clear filter settings    function wb_tr_clear() {        var $wb_todo__tr = $('.wb-todo__tr:not(.wb__tr--month)');        $wb_todo__tr            .removeClass('wb-todo-hide-by-filter--expire')            .removeClass('wb-todo-hide-by-filter--category')            .removeClass('wb-todo-hide-by-filter--assigned')            .removeClass('wb-todo-hide-by-filter--search')            .removeClass('wb-todo-hide-by-filter--done');    }    // Is filter enable    function wb_is_filter(params) {        var query = '',            $wb_todo__tr;                for (var i = 0; i < params.length; i++) {            query += '.wb-todo-hide-by-filter--' + params[i] + ', ';        }        query = query.slice(0, -2);        $wb_todo__tr = $(query.slice(0, -2));        // console.log(query, $wb_todo__tr, !!($wb_todo__tr.length));        return !!($wb_todo__tr.length);    }    // Close all More Details    function wb_details_close() {        var $wb__detail__minus = $('.wb__detail--minus');        $wb__detail__minus.trigger('click');    }    // Note field    $(function() {        if (!is_td()) {            return;        }        $body.on('focus', '.td-add-new__note', function () {            var $this = $(this);            if ($this.hasClass('td-add-new__note--empty')) {                $this.val('');                $this.removeClass('td-add-new__note--empty');            }        });    });    // More Details    $(function() {        if (!is_td()) {            return;        }        $body.on('click', '.wb__detail', function () {            var $this = $(this),                this_id = $this.data('id'),                $settings = $('.wb-todo-table__settings--'+this_id),                $tr = $this.closest('tr');            if ($this.hasClass('wb__detail--plus')) {                $settings.show();                $this.removeClass('wb__detail--plus');                $this.addClass('wb__detail--minus');                $tr.addClass('wb-settings-open');            } else {                $settings.hide();                $this.removeClass('wb__detail--minus');                $this.addClass('wb__detail--plus');                $tr.removeClass('wb-settings-open');            }        });    });    // Add new Task    $(function() {        if (!is_td()) {            return;        }        $body.on('click', '.wb-add-new-btn__btn', function () {            var $this = $(this),                $new = $('.wb-todo-table__new');            $new.show();            $this.hide();        });    });    // Grey lines    $(function() {        if (!is_td()) {            return;        }        wb_even();    });    function wb_even() {        var $wb__tr = $('tbody .wb-todo__tr:not(.wb__tr--month):not(.wb-todo-hide-by-filter--category):not(.wb-todo-hide-by-filter--assigned):not(.wb-todo-hide-by-filter--expire):not(.wb-todo-hide-by-filter--done):not(.wb-todo-hide-by-filter--search)');        $wb__tr.removeClass('wb-todo__tr--even');        window.ei = 0;        $wb__tr.each(function () {            if (window.ei % 2 == 0) {                $(this).addClass('wb-todo__tr--even');            }            window.ei++;        });    }    window.wb_filters = wb_filters;    window.wb_even = wb_even;    window.wb_tr_clear = wb_tr_clear;    window.wb_filter_checkbox = wb_filter_checkbox;    window.wb_details_close = wb_details_close;    window.wb_filter_expire = wb_filter_expire;}($ || window.jQuery));// end of file