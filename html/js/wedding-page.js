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

    function wb_get_template() {
        var $input = $('.wb-new-page__radio:checked');

        if ($input.length) {
            return $input.val();
        } else {
            return 1;
        }
    }

}($ || window.jQuery));