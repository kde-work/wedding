(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }

    // Load page
    function is_td() {
        return $('.wb-todo-table').length;
    }

    // New line in Assigned
    $(function () {
        var $td_add_new__new_assigned = $('.td-add-new__new-assigned');

        $td_add_new__new_assigned.on('focus', function () {
            var $this = $(this),
                $parent_box = $this.closest('.td-checkbox-list__item--new'),
                $input = $('.td-add-new__assigned--new', $parent_box);

            $input.trigger('click');
        });

        $td_add_new__new_assigned.on('change', function () {
            var $this = $(this),
                $parent_box = $this.closest('.td-checkbox-list__item--new'),
                $input = $('.td-add-new__assigned--new', $parent_box),
                this_val = $this.val()
                    .replace(/ /g, ',')
                    .replace(/ /g, '"')
                    .replace(/ /g, '[')
                    .replace(/ /g, ']')
                    .replace(/ /g, "'");

            $input.val(this_val);
        });
    });

    // Done Btn
    $(function () {
        var $wb_todo_table__done = $('input.wb-todo-table__done');

        $wb_todo_table__done.on('change', function () {
            var $this = $(this),
                this_id = $this.data('id'),
                $input = $('.td-add-new__done--' + this_id),
                $form = $('.td-add-new__form[data-id="' + this_id + '"]');

            // if (!confirm('Are you sure you want to mark the task completed?')) {
            //     return;
            // }
            $input.val($this.prop('checked')*1);
            $form.trigger('submit');
        });
    });

    // Delete Line
    $(function () {
        var $td_add_new__delete = $('.td-add-new__delete');

        $td_add_new__delete.on('click', function () {
            var $this = $(this),
                this_id = $this.data('id'),
                this_budget = $this.data('budget');

            if (!confirm('Are you sure you want to delete this item? It also removes the budget associated with the task!')) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: false,
                dataType: 'JSON',
                data: {
                    'action': 'wb_todo_delete',
                    'id': this_id,
                    'budget': this_budget
                },
                success: function (data) {
                    // console.log(data);
                    window.location.reload();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('wb_get_save-@21: '+xhr.status);
                    console.log('wb_get_save-@22: '+thrownError);
                }
            });
        });
    });

    // Saved New task
    $(function () {
        var $form = $('.td-add-new__form');

        window.wb_object = [];

        $form.on('submit', function (e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);

            var $this = $(this),
                form_data = '',
                $td_add_new__note__empty = $('.td-add-new__note--empty', $this),
                /*$note = $('.td-add-new__note', $this),
                $name = $('.td-add-new__name', $this),
                $date = $('.td-add-new__date', $this),
                $assigned = $('.td-add-new__assigned', $this),
                $category = $('.td-add-new__group', $this),
                $budget = $('.td-add-new__budget', $this),
                $action = $('[name="action"]', $this),
                data_ajax = {},*/
                $body = $('body');

            if ($body.hasClass('in-process')) {
                return false;
            }

            $td_add_new__note__empty.val('');
            form_data = $this.serialize();

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: false,
                dataType: 'text',
                data: form_data,
                beforeSend: function (xhr, ajaxOptions, thrownError) {
                    $body.addClass('in-process');
                },
                success: function (data) {
                    // console.log(data);
                    window.location.reload();
                },
                complete: function (xhr, ajaxOptions, thrownError) {
                    $body.removeClass('in-process');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('wb_get_save-@21: '+xhr.status);
                    console.log('wb_get_save-@22: '+thrownError);
                }
            });
        });
    });

    // Main filter fn
    $(function () {
        var $wb_todo_filter__click = $('.wb-todo-filter__click');

        $wb_todo_filter__click.on('click', function () {
            wb_details_close();
            wb_tr_clear();
            wb_filter_checkbox("category");
            wb_filter_checkbox("assigned");
            wb_filter_checkbox("done");
            wb_filter_expire();
            wb_even();
        });
    });

    // Filter by Category and Assigned
    function wb_filter_checkbox(filter_type) {
        var $categories = $('[data-type="'+filter_type+'"]'),
            $checked = $('[data-type="'+filter_type+'"]:checked');

        if (!$checked.length && filter_type != 'done') {
            return;
        }

        $categories.each(function () {
            var $this = $(this),
                this_id = $this.data('id'),
                $target_tr = $('.wb-todo__tr-'+filter_type+'--'+this_id);

            if (!$this.prop('checked')) {
                $target_tr.addClass('wb-todo-hide-by-filter--' + filter_type);
            }/* else {
                $target_tr.removeClass('wb-todo-hide-by-filter--' + filter_type);
            }*/
        });

        $categories.each(function () {
            var $this = $(this),
                this_id = $this.data('id'),
                $target_tr = $('.wb-todo__tr-'+filter_type+'--'+this_id);

            if ($this.prop('checked')) /*{
                $target_tr.addClass('wb-todo-hide-by-filter--' + filter_type);
            } else */{
                $target_tr.removeClass('wb-todo-hide-by-filter--' + filter_type);
            }
        });
    }

    // Filter by Expire
    function wb_filter_expire() {
        var $checked = $('[data-type="expire"]:checked');

        if (!$checked.length) {
            return;
        }

        var $this = $checked,
            this_id = $checked.data('id'),
            $tr = $('.wb-todo__tr');

        $tr.each(function () {
            var $this = $(this),
                this_expire = $this.data('expire');

            if (this_id == 'show-expired') {
                if (this_expire > 0) {
                    $this.addClass('wb-todo-hide-by-filter--expire');
                }
            }
            if (this_id == '10-d') {
                if (this_expire < 0 || this_expire > 10) {
                    $this.addClass('wb-todo-hide-by-filter--expire');
                }
            }
            if (this_id == '30-d') {
                if (this_expire < 0 || this_expire > 30) {
                    $this.addClass('wb-todo-hide-by-filter--expire');
                }
            }
        });
    }

    // Clear filter settings
    function wb_tr_clear() {
        var $wb_todo__tr = $('.wb-todo__tr');

        $wb_todo__tr
            .removeClass('wb-todo-hide-by-filter--expire')
            .removeClass('wb-todo-hide-by-filter--category')
            .removeClass('wb-todo-hide-by-filter--assigned')
            .removeClass('wb-todo-hide-by-filter--done');
    }

    // Close all More Details
    function wb_details_close() {
        var $wb__detail__minus = $('.wb__detail--minus');

        $wb__detail__minus.trigger('click');
    }

    // Note field
    $(function() {
        if (!is_td()) {
            return;
        }
        var $td_add_new__note = $('.td-add-new__note');

        $td_add_new__note.on('focus', function () {
            var $this = $(this);

            if ($this.hasClass('td-add-new__note--empty')) {
                $this.val('');
                $this.removeClass('td-add-new__note--empty');
            }
        });
    });

    // More Details
    $(function() {
        if (!is_td()) {
            return;
        }
        var $wb__detail__plus = $('.wb__detail');

        $wb__detail__plus.on('click', function () {
            var $this = $(this),
                this_id = $this.data('id'),
                $settings = $('.wb-todo-table__settings--'+this_id),
                $tr = $this.closest('tr');

            if ($this.hasClass('wb__detail--plus')) {
                $settings.show();
                $this.removeClass('wb__detail--plus');
                $this.addClass('wb__detail--minus');
                $tr.addClass('wb-settings-open');
            } else {
                $settings.hide();
                $this.removeClass('wb__detail--minus');
                $this.addClass('wb__detail--plus');
                $tr.removeClass('wb-settings-open');
            }
        });
    });

    // Add new Task
    $(function() {
        if (!is_td()) {
            return;
        }
        var $wb_add_new_btn__btn = $('.wb-add-new-btn__btn');

        $wb_add_new_btn__btn.on('click', function () {
            var $this = $(this),
                $new = $('.wb-todo-table__new');

            $new.show();
            $this.hide();
        });
    });

    // Grey lines
    $(function() {
        if (!is_td()) {
            return;
        }
        wb_even();
    });
    function wb_even() {
        var $wb__tr = $('tbody .wb-todo__tr:not(.wb-todo-hide-by-filter--category):not(.wb-todo-hide-by-filter--assigned):not(.wb-todo-hide-by-filter--expire):not(.wb-todo-hide-by-filter--done)');

        $wb__tr.removeClass('wb-todo__tr--even');
        window.ei = 0;
        $wb__tr.each(function () {
            if (window.ei % 2 == 0) {
                $(this).addClass('wb-todo__tr--even');
            }
            window.ei++;
        });
    }

    window.wb_even = wb_even;
    window.wb_tr_clear = wb_tr_clear;
    window.wb_filter_checkbox = wb_filter_checkbox;
    window.wb_details_close = wb_details_close;
    window.wb_filter_expire = wb_filter_expire;
}($ || window.jQuery));
// end of file