(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }
    var $body = $('body');

    function stick_search() {
        var $search_box = $('.wb-search-input'),
            $wb_search__input = $('.wb-search-input .wb-search__input'),
            $eo_navigation = $('#eo-navigation'),
            $wpadminbar = $('#wpadminbar'),
            $guestlist = $('.guestlist'),
            $eo_page = $('.eo-page'),
            padding_top_post = parseInt($eo_page.css("padding-top")),
            padding_left_post = parseInt($eo_page.css("padding-left"));

        $(window).scroll(function() {
            if ( $search_box.length!=0 ) {
                if ($(this).scrollTop()+$eo_navigation.height()+$wpadminbar.height()>($eo_page.offset().top + padding_top_post)) {
                    if (!$search_box.hasClass("stick")) {
                        $search_box.hide(0);
                        $search_box.addClass("stick");
                        $search_box.css("top", $eo_navigation.height()+$wpadminbar.height());
                        // $search_box.css("width", $guestlist.width());
                        // $search_box.css("padding-left", $guestlist.offset().left);
                        $guestlist.css('padding-top', $search_box.height());
                        $search_box.fadeIn();
                    }
                }
                else {
                    if ($search_box.hasClass("stick")){
                        $search_box.fadeOut(150, function() {
                            $search_box.removeClass("stick");
                            $guestlist.css('padding-top', 0);
                            $search_box.css("padding-left", 0);
                            $search_box.show(0);
                        });
                    }
                }
            }
        });
    }

    $(function () {
        stick_search();
    });

    $(window).resize(function() {
        stick_search();
    });


    var limitExecByInterval = function(fn, time) {
        var lock, execOnUnlock, args;
        return function() {
            args = arguments;
            if (!lock) {
                lock = true;
                var scope = this;
                setTimeout(function(){
                    lock = false;
                    if (execOnUnlock) {
                        args.callee.apply(scope, args);
                        execOnUnlock = false;
                    }
                }, time);
                return fn.apply(this, args);
            } else execOnUnlock = true;
        }
    };

    // Search Input
    $(function () {
        var $search__input = $('.gl .wb-search__input');

        $search__input.on('keyup', function () {
            var $this = $(this),
                this_val = $this.val().toLowerCase(),
                $gl__item = $('.gl__item:not(.gl__item--empty_line)');

            if (this_val) {
                $gl__item.each(function () {
                    var $this = $(this),
                        line_name = $('.guest-table__input--name', $this),
                        line_family = $('.guest-table__input--family', $this);

                    if (
                        line_name.val().toLowerCase().indexOf(this_val) === -1
                        && line_family.val().toLowerCase().indexOf(this_val)
                    ) {
                        var $gl__item__current = $this.closest('.gl__item');

                        $gl__item__current.hide();
                    }
                });
            } else {
                $gl__item.show();
            }
        });
    });

    // Contact modal
    $(function () {

        // Open Modal
        $body.on('click', '.gl-icon--contact', function () {
            var $this = $(this),
                $parent = $this.closest('.gl__td--contact'),
                $modal = $('.gl__modal--contact', $parent);

            $modal.show();
        });

        //Close modal
        $body.on('click', '.gl__close-modal', function () {
            var $this = $(this),
                $parent = $this.closest('.gl__td--contact'),
                $modal = $('.gl__modal--contact', $parent);

            $modal.hide();
        });
    });

    // Changes on inputs
    $(function () {
        $body.on('change', '.gl input, .gl select, .gl textarea', function () {
            var $this = $(this);

            if (
                !$this.hasClass('wb-add-new__input')
                && !$this.hasClass('wb-search__input')
            ) {
                gl_save();
            }
        });
    });

    // Changes on Contacts Inputs
    $(function () {
        $body.on('change', '.gl-contact-modal__right input', function () {
            var $this = $(this),
                $parent = $this.closest('.gl__td--contact'),
                $gl_icon__empty = $('.gl-icon', $parent);

            if ($this.val()) {
                $gl_icon__empty.removeClass('gl-icon--empty');
            } else {
                var $inputs = $('.gl-contact-modal__right input', $parent);

                window.is_empty = true;

                $inputs.each(function () {
                    var $this = $(this);

                    console.log($this.val());
                    if ($this.val()) {
                        window.is_empty = false;
                        return false;
                    }
                });

                if (window.is_empty) {
                    $gl_icon__empty.addClass('gl-icon--empty');
                } else {
                    $gl_icon__empty.removeClass('gl-icon--empty');
                }
                delete window.is_empty;
            }
        });
    });

    // Remove Guest
    $(function () {
        $body.on('click', '.gl .wb__delete', function () {
            var $this = $(this),
                $line = $this.closest('.gl__item');

            $line.remove();
            gl_save();
        });
    });

    // Add new Guest
    $(function () {
        $body.on('click', '.gl__td--add-elem .wb-add-new__button', function () {
            var $this = $(this),
                $parent = $this.closest('.gl'),
                $form = $('.gl__form', $parent),
                max_id = $form.data('max-id') + 1,
                $empty_line = $('.gl__item--empty_line', $parent),
                $add_new__input = $('.wb-add-new__input', $parent),
                new_name = $add_new__input.val();

            if (!new_name) {
                $add_new__input.addClass('wb-add-new__input--error');
                return;
            }

            $empty_line.before($empty_line.clone());

            var $new_line = $('.gl__item--empty_line', $parent).first(),
                $input__name = $('.guest-table__input--name', $new_line),
                $names = $('[name*="--empty_line"]', $new_line),
                $for = $('[for*="--empty_line"]', $new_line),
                $id = $('[id*="--empty_line"]', $new_line);

            $names.each(function () {
                var $this = $(this),
                    this_name = $this.attr('name');

                $this.attr('name', this_name.replace(/empty_line/g, max_id));
            });

            $for.each(function () {
                var $this = $(this),
                    this_for = $this.attr('for');

                $this.attr('for', this_for.replace(/empty_line/g, max_id));
            });

            $id.each(function () {
                var $this = $(this),
                    this_id = $this.attr('id');

                $this.attr('id', this_id.replace(/empty_line/g, max_id));
            });

            $input__name.val(new_name);
            $add_new__input.val('');

            $new_line.removeClass('gl__item--empty_line').addClass('gl__item--'+max_id);
            $form.data('max-id', max_id*1);
            gl_save();
        });
    });

    // Focus on Add new Guest input
    $(function () {
        var $add_new__input = $('.wb-add-new__input');

        $add_new__input.on('focus', function () {
            var $this = $(this);

            $this.removeClass('wb-add-new__input--error');
        });
    });

    // Change Status checkbox
    $(function () {
        $body.on('click', '.gl__status', function () {
            var $this = $(this),
                $parent = $this.closest('.gl__td--status'),
                $checkbox = $('.guest-table__input--status', $parent);

            $checkbox.trigger('click');
        });
    });

    // Saved Guest List
    $(function () {
        var $form = $('.gl__form');

        $form.on('submit', function (e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);

            var $this = $(this),
                $body = $('body');

            form_data = $this.serialize();

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: true,
                dataType: 'text',
                data: form_data,
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
                    console.log('wb_get_save-@21: '+xhr.status);
                    console.log('wb_get_save-@22: '+thrownError);
                }
            });
        });
    });

    function gl_save_handler() {
        var $form = $('.gl__form');

        $form.submit();
    }

    window.gl_save = limitExecByInterval(gl_save_handler, 3000);
    window.gl_save_handler = gl_save_handler;
    window.limitExecByInterval = limitExecByInterval;

}($ || window.jQuery));
// end of file