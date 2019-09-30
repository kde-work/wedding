(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }

    var $body = $('body');

    // Click Buy button
    $(function () {
        $body.on('click', '.wp-buy', function () {
            if ($body.hasClass('in-payment')) return;

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: true,
                dataType: 'json',
                data: {
                    'action' : 'wp-buy'
                },
                beforeSend: function (xhr, ajaxOptions, thrownError) {
                    $body.addClass('in-payment');
                },
                success: function (data) {
                    console.log(data);
                    if (data.token !== void 0) {
                        wb_open_payment(data.token);
                    } else {
                        $body.removeClass('in-payment');
                    }

                    if (data.ErrorMessage) {
                        $body.removeClass('in-payment');
                        console.error(data.ErrorMessage);
                    }
                },
                complete: function (xhr, ajaxOptions, thrownError) {
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('wp-buy-@11: '+xhr.status);
                    console.log('wp-buy-@12: '+thrownError);
                }
            });
        });
    });

    // Open Bambora payment
    function wb_open_payment (checkoutToken) {
        var checkout = new Bambora.ModalCheckout(null);
        checkout.on(Bambora.Event.Cancel, function(payload) {
            // window.location.href = payload.declineUrl;
        });
        checkout.on(Bambora.Event.Close, function(payload) {
            document.location.reload(true);
            // window.location.href = payload.acceptUrl;
        });
        checkout.initialize(checkoutToken).then(function() {
            $body.removeClass('in-payment');
            checkout.show();
        });
    }

    window.wb_open_payment = wb_open_payment;

}($ || window.jQuery));