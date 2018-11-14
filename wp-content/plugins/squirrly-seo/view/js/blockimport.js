(function ($) {

    $.fn.sq_blockimport = function () {
        var $this = this;

        if (!$.sq_getHash()) {
            $.sq_setHash('import');
        }

    };

    if ($('#sq_settings').length > 0) {
        $('#sq_settings').sq_blockimport();
    } else {
        $(document).ready(function () {
            $('#sq_settings').sq_blockimport();
        });
    }

})(jQuery);