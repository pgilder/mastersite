(function ($) {
    $.fn.sq_blockdashboard = function () {
        var $this = this;

        if ($this.find('#sq_settings_login').length > 0) {
            $this.find('#sq_settings_login').after($('.sq_helpcontent'));
        }
        $this.find('.sq_helpcontent').show();
        $this.find('.sq_slidelist a,.sq_slidelist div.sq_playpause').on('click', function () {
            var li = $(this).parent('li');
            li.addClass('sq_loading');
            li.find('a').hide();
            li.find('div').hide();
            if (li.find('iframe').length == 0) {
                li.append('<iframe src="//www.slideshare.net/slideshow/embed_code/' + li.find('a').attr('rel') + '" width="680" height="530" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="display:none; max-width: 100%;  max-height: 100%;" allowfullscreen> </iframe>');
            }
            li.find('iframe').show();
            li.append('<span>x</span>');

            li.find('span').on('click', function () {
                var li = $(this).parent('li');
                li.find('a').show();
                li.find('div').show();
                li.find('iframe').hide();
                $(this).remove();
            });
        });
        $this.find('.sq_slidelist_next').on('click', function () {
            var $current = $this.find('.sq_slidelist li.active');
            $this.find('.sq_slidelist_prev').show();
            if ($current.next().length > 0) {
                $(this).show();
                $current.removeClass('active');
                $current.next().addClass('active');
            }else{
                $(this).hide();
            }
        });

        $this.find('.sq_slidelist_prev').on('click', function () {
            var $current = $this.find('.sq_slidelist li.active');
            $this.find('.sq_slidelist_next').show();
            if ($current.prev().length > 0) {
                $(this).show();
                $current.removeClass('active');
                $current.prev().addClass('active');
            }else{
                $(this).hide();
            }
        });

    };

    if ($('#sq_settings').length > 0) {
        $('#sq_settings').sq_blockdashboard();
    } else {
        $(document).ready(function () {
            $('#sq_settings').sq_blockdashboard();
        });
    }
})(jQuery);