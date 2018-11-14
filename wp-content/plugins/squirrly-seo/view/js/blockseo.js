(function ($) {
    //Submit the settings
    $.fn.sq_stickyListen = function () {
        var $this = this;
        var $sticky = $this.find('.sq_auto_sticky');

        $sticky.find('input').on('click', function () {
            var $input = $(this);

            if ($input.is(":checked")) {
                $this.trigger('sq_sticky', [true]);
            } else {
                $this.trigger('sq_sticky', [false]);
            }

            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_setstickysla',
                    sq_auto_sticky: ($input.is(":checked") ? 1 : 0),
                    nonce: sqQuery.nonce
                }
            );
        });

        $this.on("sq_task_loaded", function () {
            if ($sticky.find('input').is(":checked")) {
                $this.trigger('sq_sticky', [true]);
            }
        });
    };

    $(document).ready(function () {
        $('#sq_blockseo').sq_stickyListen();
    });

})(jQuery);