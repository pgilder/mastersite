(function ($) {
    var sq_script = 'kr';

    $.fn.sq_getKR = function () {
        var $this = this;

        if (typeof $.sq_config !== 'undefined') {
            var loadingAjax = true;
            $this.find('#sq_krinfo').addClass('sq_loading');
            $.getJSON(
                __api_url + 'sq/kr/?callback=?',
                {
                    token: $.sq_config.token,
                    user_url: __blog_url,
                    country: 'com',
                    lang: (__language || document.getElementsByTagName("html")[0].getAttribute("lang") || window.navigator.language)
                }
            ).done(function (response) {
                loadingAjax = false;

                var head = document.getElementsByTagName('head')[0],
                    script = document.createElement('script'),
                    link = document.createElement('link');

                //Insert the js
                script.src = $.sq_config.sq_baseurl + $.sq_config.sq_uri + '/js/kr' + (SQ_DEBUG ? '' : '.min') + '.js';
                script.type = 'text/javascript';

                script.onreadystatechange = script.onload = function () {
                    var state = script.readyState;
                    if (!state || /loaded|complete/.test(state)) {
                        $('#sq_research').show()
                    }
                };

                head.appendChild(script);

                $this.find('#sq_krinfo').removeClass('sq_loading').removeClass('sq_error');
                if (typeof response.html !== 'undefined' && response.html !== '') {
                    $this.find('#sq_krinfo').html(response.html);
                }

            }).fail(function () {
                loadingAjax = false;
                $this.find('#sq_krinfo').find('fieldset').removeClass('sq_loading');
                $this.find('#sq_krinfo').html('Lost connection with the server. Please make sure you whitelisted the IP from https://api.squirrly.co').show();
            });

            setTimeout(function () {
                if (loadingAjax) {
                    $this.find('#sq_krinfo').removeClass('sq_loading').addClass('sq_error').show();
                    $this.find('#sq_krinfo').html('Lost connection with the server. Please make sure you whitelisted the IP from https://api.squirrly.co');
                }
            }, 60000);
        } else {
            $this.find('#sq_krinfo').html('Squirrly Library loading error. Please contact us at support@squirrly.co');
        }
    };
})(jQuery);