(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }

    // Open/Close Helptext
    $(function () {
        var $body = $('body');

        $body.on('click', '.wb-helptext__control', function () {
            var $this = $(this),
                this_control = $this.data('control');

            if (this_control === 'open') {
                $body.addClass('helptext-enable');
            }

            if (this_control === 'close') {
                $body.removeClass('helptext-enable');
            }
        });

    });
}($ || window.jQuery));
// end of file