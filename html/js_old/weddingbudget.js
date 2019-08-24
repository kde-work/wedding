(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }

    // Main refresh function
    function wb_refresh(is_save) {
        wb_refresh_total_value();
        wb_refresh_line_items();
        if (is_save) {
            wb_save();
        }
    }

    // Button Remove line
    $(function() {
        if (!is_wb()) {
            return;
        }
        var $wb__delete = $('.wb__delete');

        $wb__delete.on('click', function () {
            wb_remove_line($(this), 1, 1);
        });
    });

    // keyup
    $(function() {
        if (!is_wb()) {
            return;
        }
        var $wb__input = $('.wb__input');

        $wb__input.on('keyup', function () {
            wb_refresh(0);
        }).on('change', function () {
            wb_refresh(1);
        });
    });

    $(function() {
        if (!is_wb()) {
            return;
        }
        // wb_get_save();
        // wb_load();
        wb_refresh(0);
    });

    // Loader
    $(function() {
        if (!is_wb()) {
            return;
        }
        var $wb__table = $('.wb__table');

        $wb__table.removeClass('wb__table--load');
    });

    // total input
    $(function() {
        if (!is_wb()) {
            return;
        }
        wb_get_wb_total_input_by_server();
    });
    $(function() {
        if (!is_wb()) {
            return;
        }
        var $wb_total_input__input = $('.wb-total-input__input');

        $wb_total_input__input.on('change', function () {
            var $self_form = $wb_total_input__input.closest('form');

            $self_form.submit();
        });
    });

	// Saved Total wedding budget
    $(function() {
        if (!is_wb()) {
            return;
        }
		var $wb_total_input = $('.wb-total-input');

        $wb_total_input.on('submit', function (e){
			e = e || window.event;
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);
			
			var $this = $(this),
				ajaxData = $this.serialize(),
				$body = $('body');
				
			if ($body.hasClass('in-process')) {
				return false;
			}

			// запрос к серверу
			$.ajax({
				type: 'POST',
				url: wedding_budget.url,
                async: true,
				dataType: 'text', // ответ ждем в text-формате
				data: ajaxData, // данные для отправки
				beforeSend: function (xhr, ajaxOptions, thrownError) {
					$body.addClass('in-process');
				},
				success: function (data) { // событие после удачного обращения к серверу и получения ответа
				},
				complete: function (xhr, ajaxOptions, thrownError) {
                    $body.removeClass('in-process');
                    wb_refresh(0);
				},
				error: function (xhr, ajaxOptions, thrownError) { // в случае неудачного завершения запроса к серверу
					console.log('form-for-servis-@21: '+xhr.status); // покажем ответ сервера
					console.log('form-for-servis-@22: '+thrownError); // и текст ошибки
				}
			});
			
			return false;
		});
    });

    // Button 'Add new line'
    $(function() {
        if (!is_wb()) {
            return;
        }
        var $wb_add_new__button = $('.wb-add-new__button');

        $wb_add_new__button.on('click', function () {
            var $this = $(this),
                group_id = $this.data('group-id'),
                $wb__table = $('.wb__table--' + group_id), // current table
                $wb_add_new__input = $('.wb-add-new__input--' + group_id), // input
                name_of_new_line = $wb_add_new__input.val();

            if (!name_of_new_line) {
                return false;
            }

            wb_create_new_line($wb__table, name_of_new_line, '', '', 1);
            $wb_add_new__input.val('');
        });
    });

    // Load page
    function is_wb() {
        return $('.wb-total-input__input').length;
    }

    // Load page
    function wb_load() {
        for (var i in window.wb_object) {
            if (window.wb_object[i*1] === void 0) continue;
            var $wb__table = $('.wb__table--' + window.wb_object[i*1].group_id);

            if (window.wb_object[i*1].id == 'clear-line') {
                wb_create_new_line($wb__table, window.wb_object[i*1].name, window.wb_object[i*1].estimate, window.wb_object[i*1].real, false);
            } else {
                var $template_tr = $('.wb__tr--'+window.wb_object[i*1].id, $wb__table),
                    $wb__input__estimate = $('.wb__input--estimate', $template_tr),
                    $wb__input__real = $('.wb__input--real', $template_tr),
                    $wb__td__title = $('.wb__td--title', $template_tr);

                if (window.wb_object[i*1].option == 'remove-line') {
                    var $wb__delete = $('.wb__delete', $template_tr);

                    wb_remove_line($wb__delete, 0, 0);
                    continue;
                }

                $wb__input__estimate.val(window.wb_object[i*1].estimate);
                $wb__input__real.val(window.wb_object[i*1].real);
                $wb__td__title.html(window.wb_object[i*1].name);
            }
        }
    }

    // Create new line
    function wb_create_new_line($table, name, estimate, real, is_refresh) {
        var $wb__tr__clear_line = $('.wb__tr--clear-line', $table), // template td
            $wb_add_new = $('.wb-add-new', $table); // parent tr by button
        
        $wb__tr__clear_line.clone().insertBefore($wb_add_new);
        var $template_tr = $('.wb__tr--clear-line:last', $table),
            $wb__input__estimate = $('.wb__input--estimate', $template_tr),
            $wb__input__real = $('.wb__input--real', $template_tr),
            $wb__td__title = $('.wb__td--title', $template_tr),
            $wb__delete = $('.wb__delete', $template_tr),
            $wb__todo = $('.wb__td--in-todo', $template_tr);

        $template_tr.removeClass('wb__tr--clear-line').addClass('wb__tr--item');
        $wb__td__title.html(name);
        $wb__input__estimate.val(estimate);
        $wb__input__real.val(real);
        wb_add_todo_icon($wb__todo, 'clear-line', name);

        $wb__input__estimate.add($wb__input__real).on('keyup', function () {
            wb_refresh(0);
        }).on('change', function () {
            wb_refresh(1);
        });
        $wb__delete.on('click', function () {
            wb_remove_line($(this), 1, 1);
        });
        if (is_refresh) {
            wb_refresh(1);
        }
    }

    // Add TO-DO icon
    function wb_add_todo_icon($par, item_id, item_name) {
        var $body = $('body');

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'text',
            data: {
                'action' : 'wb_todo_icon',
                'id' : item_id,
                'name' : item_name
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) {
                // console.log(data);
                if (data) {
                    $par.html(data);
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('wb_save-@21: '+xhr.status);
                console.log('wb_save-@22: '+thrownError);
            }
        });
    }

    // Saved data in the server
    function wb_save() {
        var $wb__tr__item = $('.wb__tr--item'),
            $body = $('body');

        window.wb_object = [];

        $wb__tr__item.each(function () {
            var $this = $(this),
                $wb__td__title = $('.wb__td--title', $this),
                $wb__input__estimate = $('.wb__input--estimate', $this),
                $wb__input__real = $('.wb__input--real', $this),
                option = $this.data('option'),
                group_id = $this.data('group-id'),
                id = $this.data('id');

            window.wb_object.push({
                'group_id': group_id,
                'id': id,
                'option': option,
                'estimate': $wb__input__estimate.val(),
                'real': $wb__input__real.val(),
                'name': $wb__td__title.html()
            });
        });

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'text',
            data: {
                'action' : 'wb_save',
                'data' : window.wb_object
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) {
                console.log(data);
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('wb_save-@21: '+xhr.status);
                console.log('wb_save-@22: '+thrownError);
            }
        });
    }

    // Returned data from the server
    function wb_get_save() {
        var $body = $('body');

        window.wb_object = [];

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'JSON',
            data: {
                'action' : 'wb_get_save'
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) {
                // console.log(data);
                window.wb_object = data;
                wb_load();
                wb_refresh(0);
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('wb_get_save-@21: '+xhr.status);
                console.log('wb_get_save-@22: '+thrownError);
            }
        });
    }

    // Refresh total value
    function wb_refresh_total_value() {
        var $wb__input__estimate = $('.wb__input--estimate'), // all inputs with estimate cost
            $wb__input__real = $('.wb__input--real'), // all inputs with real cost
            $wb_total_spent__estimate = $('.wb-total-spent__estimate'), // result value of estimate
            $wb_total_spent__real = $('.wb-total-spent__real'), // result value of estimate
            $wb_total_spent__difference = $('.wb-total-spent__difference'), // difference between real and estimate
            $wb_wedding_total_budget__val = $('.wb-wedding-total-budget__val'), // Total wedding budget
            $wb_rest__estimate = $('.wb-rest__estimate'), // rest estimate money
            $wb_rest__real = $('.wb-rest__real'), // rest real money
            total_input = wb_get_wb_total_input(),
            rest__estimate,
            rest__real,
            difference;

        if (!total_input) {
            total_input = 0;
        }

        $wb_wedding_total_budget__val.html(wb_beauty_price(total_input));

        window.total_real = 0;
        window.total_estimate = 0;

        $wb__input__estimate.each(function () {
            var $this = $(this);

            window.total_estimate += $this.val().replace(',', '.') * 1;
        });
        window.total_estimate = window.total_estimate.toFixed(2);

        $wb__input__real.each(function () {
            var $this = $(this);

            window.total_real += $this.val().replace(',', '.') * 1;
        });
        window.total_real = window.total_real.toFixed(2);

        if (window.total_estimate)
            $wb_total_spent__estimate.html(wb_beauty_price(window.total_estimate));
        if (window.total_real)
            $wb_total_spent__real.html(wb_beauty_price(window.total_real));

        difference = (window.total_estimate - window.total_real).toFixed(2);
        if (difference >= 0) {
            $wb_total_spent__difference.removeClass('wb__minus');
            $wb_total_spent__difference.addClass('wb__plus');
        } else {
            $wb_total_spent__difference.removeClass('wb__plus');
            $wb_total_spent__difference.addClass('wb__minus');
        }
        $wb_total_spent__difference.html(wb_beauty_price(difference));

        rest__estimate = (total_input - window.total_estimate).toFixed(2);
        if (rest__estimate >= 0) {
            $wb_rest__estimate.removeClass('wb__minus');
            $wb_rest__estimate.addClass('wb__plus');
        } else {
            $wb_rest__estimate.removeClass('wb__plus');
            $wb_rest__estimate.addClass('wb__minus');
        }
        $wb_rest__estimate.html(wb_beauty_price(rest__estimate));

        rest__real = (total_input - window.total_real).toFixed(2);
        if (rest__real >= 0) {
            $wb_rest__real.removeClass('wb__minus');
            $wb_rest__real.addClass('wb__plus');
        } else {
            $wb_rest__real.removeClass('wb__plus');
            $wb_rest__real.addClass('wb__minus');
        }
        $wb_rest__real.html(wb_beauty_price(rest__real));
    }

    // Refreshed difference value for lines
    function wb_refresh_line_items(){
        var $wb__tr__item = $('.wb__tr--item');

        $wb__tr__item.each(function () {
            var $this = $(this),
                $wb__input__estimate = $('.wb__input--estimate', $this),
                $wb__input__real = $('.wb__input--real', $this),
                $wb__td__difference = $('.wb__td--difference', $this),
                difference;

            if (!$wb__input__estimate.length || !$wb__input__real.length) {
                return;
            }
            var estimate = wb_cn($wb__input__estimate),
                real = wb_cn($wb__input__real);

            if (!estimate || !real) {
                difference = '—';
            } else {
                difference = wb_beauty_price((estimate - real).toFixed(2));
            }
            $wb__td__difference.html(difference);
        });
    }

    // Clear number from input
    function wb_cn($elem){
        return $elem.val().replace(',', '.');
    }

    // Returned value of Total wedding budget from the server
    function wb_get_wb_total_input_by_server() {
        var $body = $('body'),
            result;

        if ($body.hasClass('in-process')) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'text', // ответ ждем в text-формате
            data: {
                'action' : 'get_wb_total_input'
            }, // данные для отправки
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) { // событие после удачного обращения к серверу и получения ответа
                var $wb_total_input__input = $('.wb-total-input__input');

                window.wb_result = data;
                $wb_total_input__input.val((data)?data:'0');
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
            },
            error: function (xhr, ajaxOptions, thrownError) { // в случае неудачного завершения запроса к серверу
                console.log('form-for-servis-@21: '+xhr.status); // покажем ответ сервера
                console.log('form-for-servis-@22: '+thrownError); // и текст ошибки
            }
        });
        result = window.wb_result;
        window.wb_result = 0;
        return result;
    }

    // Returned value of Total wedding budget from the server
    function wb_get_wb_total_input() {
        var $wb_total_input__input = $('.wb-total-input__input');

        return $wb_total_input__input.val();
    }

    // Returned beauty price for number
    function wb_beauty_price(data){
        var price = Number.prototype.toFixed.call(parseFloat(data) || 0, 2),
            body_width = $('body').width();

        // price = price.replace(/(\D)/g, ".");

        if (body_width < 600) {
            price = price.slice(0, -3);
        } else {
            price = price.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        }

        return price;
    }

    // Remove line
    function wb_remove_line($this, is_confirm, is_refresh) {
        if (is_confirm) {
            if (!confirm('Are you sure you want to delete the line?')) {
                return false;
            }
        }
        var $wb__tr__item = $this.closest('.wb__tr--item');

        if($wb__tr__item.data('id') == 'clear-line') {
            $wb__tr__item.remove();
        } else {
            $wb__tr__item.addClass('wb__tr--remove').html('').data('option', 'remove-line');
        }

        if (is_refresh) {
            wb_refresh(1);
        }
    }
    
    window.is_wb = is_wb;
    window.wb_load = wb_load;
    window.wb_refresh = wb_refresh;
    window.wb_get_wb_total_input = wb_get_wb_total_input;
    window.wb_get_wb_total_input_by_server = wb_get_wb_total_input_by_server;
    window.wb_beauty_price = wb_beauty_price;
    window.wb_refresh_total_value = wb_refresh_total_value;
    window.wb_refresh_line_items = wb_refresh_line_items;
    window.wb_cn = wb_cn;
    window.wb_create_new_line = wb_create_new_line;
    window.wb_save = wb_save;
    window.wb_get_save = wb_get_save;
    window.wb_remove_line = wb_remove_line;

}($ || window.jQuery));
// end of file