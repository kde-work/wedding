(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }
    
    // Email form
    $(function () {
        var $form = $('.wb-form');
        
        $form.on('submit', function (e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);

            var $this = $(this),
                $body = $('body'),
                form_data = $this.serialize();

            if ($body.hasClass('wb-in-process')) {
                return 0;
            }

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: true,
                dataType: 'text',
                data: form_data,
                beforeSend: function (xhr, ajaxOptions, thrownError) {
                    $body.addClass('wb-in-process');
                },
                success: function (data) {
                    $('.wb-form__message--success').css({
                        'display' : 'flex'
                    });
                    console.log(data);
                },
                complete: function (xhr, ajaxOptions, thrownError) {
                    $body.removeClass('wb-in-process');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('webp_form-@21: '+xhr.status);
                    console.log('webp_form-@22: '+thrownError);
                    $('.wb-form__message--error').css({
                        'display' : 'flex'
                    });
                }
            });
        });
    });
}($ || window.jQuery));
// end of file