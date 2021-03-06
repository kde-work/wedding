(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }

    var $body = $('body');

    // Click Buy button
    $(function () {
        $body.on('click', '.wb-webp__item--new', function () {
            if ($body.hasClass('in-process')) return;

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: true,
                dataType: 'json',
                data: {
                    'name' : wb_get_name(),
                    'template' : wb_get_template(),
                    'action' : 'wp-create-webp',
                },
                beforeSend: function (xhr, ajaxOptions, thrownError) {
                    $body.addClass('in-process');
                },
                success: function (data) {
                    console.log(data);
                    if (data.html !== void 0) {
                        $('.wb-webp').html(data.html);
                    }

                    if (data.ErrorMessage) {
                        console.error(data.ErrorMessage);
                    }
                },
                complete: function (xhr, ajaxOptions, thrownError) {
                    $body.removeClass('in-process');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('wp-create-webp-@11: '+xhr.status);
                    console.log('wp-create-webp-@12: '+thrownError);
                }
            });
        });
    });

    // Change Password
    $(function() {
        $body.on('change', '#wb-site-password', function () {
            var $this = $(this),
                $par = $this.closest('.wb-site-password__form');

            $par.addClass('wb-site-password__form--success');
        });
    });
    $(function() {
        $body.on('click', '.wb-site-password__save', wb_save_password);
    });
    function wb_save_password() {
        if ($body.hasClass('in-process')) return;

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'json',
            data: {
                'password' : $('#wb-site-password').val(),
                'action' : 'wp-save-password',
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) {
                console.log(data);
                if (data.answer !== void 0) {
                    $('.wb-site-password__form').removeClass('wb-site-password__form--success');
                }

                if (data.ErrorMessage) {
                    console.error(data.ErrorMessage);
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('wp-save-password-@11: '+xhr.status);
                console.log('wp-save-password-@12: '+thrownError);
            }
        });
    }

    // Change Page name input
    $(function() {
        $body.on('change', '#wb-site-name__name', wb_check_name);
    });
    function wb_check_name() {
        if ($body.hasClass('in-process')) return;

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'json',
            data: {
                'name' : wb_get_name(),
                'action' : 'wp-check-name',
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) {
                console.log(data);
                if (data.answer !== void 0) {
                    var $wb_site_name__form = $('.wb-site-name__form');

                    if (data.answer) {
                        if (data.answer === 'self') {
                            $wb_site_name__form.removeClass('wb-site-name__form--error').removeClass('wb-site-name__form--success');
                        } else {
                            $wb_site_name__form.removeClass('wb-site-name__form--error').addClass('wb-site-name__form--success');
                        }
                    } else {
                        $wb_site_name__form.removeClass('wb-site-name__form--success').addClass('wb-site-name__form--error');
                    }
                }

                if (data.ErrorMessage) {
                    console.error(data.ErrorMessage);
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('wp-create-webp-@11: '+xhr.status);
                console.log('wp-create-webp-@12: '+thrownError);
            }
        });
    }

    // Save page name
    $(function() {
        $body.on('click', '.wb-site-name__save', wb_save_name);
    });
    function wb_save_name() {
        if ($body.hasClass('in-process')) return;

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'json',
            data: {
                'name' : wb_get_name(),
                'action' : 'wp-save-name',
            },
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process');
            },
            success: function (data) {
                console.log(data);
                if (data.answer !== void 0) {
                    if (data.value !== void 0) {
                        var $wb_site_name__form = $('.wb-site-name__form'),
                            $wb_site_name__input = $('.wb-site-name__input'),
                            $wb_site_name__page_link = $('.wb-site-name__page_link');

                        // $('.wb-webp').html(data.html);
                        $wb_site_name__form.removeClass('wb-site-name__form--error').removeClass('wb-site-name__form--success');
                        $wb_site_name__input.val(data.value);
                        $wb_site_name__page_link.attr('href', data.href);
                    }
                }

                if (data.ErrorMessage) {
                    console.error(data.ErrorMessage);
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('wp-create-webp-@11: '+xhr.status);
                console.log('wp-create-webp-@12: '+thrownError);
            }
        });
    }

    // Slide up Change template
    $(function() {
        $body.on('click', '.wb-change-template__a', function () {
            var $this = $(this),
                $par = $this.closest('.wb-change-template'),
                $block_form = $('.wb-change-template__body', $par);

            $this.slideUp();
            $block_form.slideDown();
        });
    });

    // Click to Change template
    $(function() {
        $body.on('click', '.wb-change-template__button', function () {
            if ($body.hasClass('in-process')) return;

            if (!confirm("Wedding page data will be lost and replaced with the default data of the selected template.")) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: true,
                dataType: 'json',
                data: {
                    'template' : wb_get_template(),
                    'action' : 'wp-change-template',
                },
                beforeSend: function (xhr, ajaxOptions, thrownError) {
                    $body.addClass('in-process');
                },
                success: function (data) {
                    console.log(data);
                    if (data.html !== void 0) {
                        $('.wb-webp').html(data.html);
                    }

                    if (data.ErrorMessage) {
                        console.error(data.ErrorMessage);
                    }
                },
                complete: function (xhr, ajaxOptions, thrownError) {
                    $body.removeClass('in-process');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('wp-create-webp-@11: '+xhr.status);
                    console.log('wp-create-webp-@12: '+thrownError);
                }
            });
        });
    });

    function wb_get_template() {
        var $input = $('.wb-new-page__radio:checked');

        if ($input.length) {
            return $input.val();
        } else {
            return 1;
        }
    }

    function wb_get_name() {
        var $input = $('#wb-site-name__name');

        return $input.val();
    }

}($ || window.jQuery));