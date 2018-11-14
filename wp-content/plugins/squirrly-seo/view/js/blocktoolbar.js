(function($) {
    $.fn.sq_toolbar = function() {
        var $this = this;
        var $hash = $.sq_getHash();

        $this.sq_deselectTabs = function() {
            $this.find('a.nav-tab').each(function () {
                $(this).removeClass('nav-tab-active');
            });
        };

        $this.sq_hideAllSettings= function() {
            $('#sq_settings').find('fieldset').each(function () {
                $(this).hide();
            });
        };

        if ($hash !== '') {
            $this.sq_deselectTabs();

            if ($('.sq_' + $hash).length > 0) {
                $this.sq_hideAllSettings();
                $('.sq_' + $hash).show();
            }

            $this.find('a.nav-tab').each(function () {
                if ($(this).attr('href').indexOf('#' + $hash) !== -1) {
                    $(this).addClass('nav-tab-active');
                }
            });
        }

        $this.find('a.nav-tab').on('click', function () {
            var urlparts = $(this).attr('href').split('#');

            if (urlparts.length >= 2) {
                urlparts.shift();
                var $hash = urlparts.join("#"); //join it back up
            }

            if ($hash !== '') {
                $this.sq_deselectTabs();
                if ($('.sq_' + $hash).length > 0) {
                    $this.sq_hideAllSettings();
                    $('.sq_' + $hash).show();
                }
                $(this).addClass('nav-tab-active');
            }
        });

        $('.sq_social_link').on('click', function () {
            $this.sq_deselectTabs();
            var $hash = 'socials';
            if ($('.sq_' + $hash).length > 0) {
                $this.sq_hideAllSettings();
                $('.sq_' + $hash).show();
            }
            var previewtop = $('#sq_social_media_accounts').offset().top - 100;
            if ($('.edit-post-layout__content').length > 0) {
                $('.edit-post-layout__content').animate({scrollTop: previewtop}, 1000);
            } else {
                $('html,body').animate({scrollTop: previewtop}, 1000);
            }
        });
    };

    if ($('#sq_settings').find('.sq_toolbar').length > 0) {
        $('.sq_toolbar').sq_toolbar();
    } else {
        $(document).ready(function () {
            $('.sq_toolbar').sq_toolbar();
        });
    }

})(jQuery);