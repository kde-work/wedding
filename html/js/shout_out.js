(function ($) {
    if (!$) {
        console.error('jQuery and $ not exist');
        return;
    }

    // Insert commercial line
    $(function () {
        var $par = $('.wed-shout-out'),
            $regular_lines = $('.wed-shout-out__item--regular', $par),
            $commercial_line = $('.wed-shout-out__item--commercial-line', $par),
            line_probability = $commercial_line.data('probability') / 100;

        if (Math.random() <= line_probability) {
            $regular_lines.eq(getRandomInRange(0, $regular_lines.length - 2)).before($commercial_line.clone());
            $regular_lines.last().remove();
        }
    });

    function getRandomInRange(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
}($ || window.jQuery));
// end of file