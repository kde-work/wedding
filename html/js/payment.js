(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }

    var $body = $('body');

    // Click Buy button
    // $(function () {
    //     $body.on('click', '.wp-buy', wb_get_payment_token);
    // });
    function wb_get_payment_token(type) {
        if ($body.hasClass('in-payment')) return;

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'json',
            data: {
                'action' : 'wp-buy',
                'nonce_code' : wedding_budget.nonce,
                'type' : type
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
    }

    // Open Bambora payment
    function wb_open_payment (checkoutToken) {
        var checkout = new Bambora.ModalCheckout(null);
        checkout.on(Bambora.Event.Cancel, function(payload) {
            // window.location.href = payload.declineUrl;
        });
        checkout.on(Bambora.Event.Close, function(payload) {
            window.location.href = payload.acceptUrl;
            // document.location.reload(true);
        });
        checkout.initialize(checkoutToken).then(function() {
            $body.removeClass('in-payment');
            checkout.show();
        });
    }

    $(function () {
        $body.on('click', '.wb-payment-button', function () {
            var $this = $(this),
                this_type = $this.data('type');
            if (this_type === 'trial') {
                window.location.href = '/planlegger/innstillinger/?wb-p=trial';
                return;
            }
            wb_get_payment_token(this_type);
        });
    });

    // Init Payment button
    function wb_payment (type) {
        var $this_script = $('#wp-script-'+type);

        if ($this_script.length) {
            var $button = $this_script.closest('.gw-go-btn');

            $button.addClass('wb-payment-button').addClass('wb-payment-button--'+type);
            $button.data('type', type);
        }
    }

    window.wb_open_payment = wb_open_payment;
    window.wb_payment = wb_payment;

}($ || window.jQuery));