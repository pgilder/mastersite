(function ($) {
    $.fn.sq_blocklogin = function () {
        var $this = this;

        $this.sq_autoLogin = function () {
            if (!$this.checkEmail($this.find('#sq_email').val())) {
                $this.find('.sq_error').html(__invalid_email);
                $this.find('#sq_register_email').show();
                $this.find('#sq_register').html(__try_again);
                return false;
            }

            $this.find('#sq_register').html(__connecting);
            $this.find('#sq_register_wait').addClass('sq_minloading');
            $this.find('.sq_message').hide();


            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_register',
                    email: $this.find('#sq_email').val(),
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {

                $this.find('#sq_register_wait').removeClass('sq_minloading');
                if (typeof response.token !== 'undefined') {
                    __token = response.token;

                    if (typeof response.success !== 'undefined') {
                        $('#sq_login_success').html(response.success);
                    }
                    //window.sq_main.load();
                    $this.sq_reload(response);
                } else {
                    if (typeof response.info !== 'undefined') {
                        $this.find('#sq_autologin').hide();
                        $this.find('ul').show();

                        $this.find('.sq_message').html(response.info).show();
                        $this.find('#sq_user').val($this.find('#sq_email').val());
                        $this.find('#sq_password').focus();
                    } else {
                        if (typeof response.error !== 'undefined') {
                            $this.find('.sq_error').html(response.error);
                            $this.find('#sq_register_email').show();
                            $this.find('#sq_register').html(__try_again);
                        }
                    }

                }

            }).fail(function (response) {
                if (response.status === 200 && response.responseText.indexOf('{') > 0) {
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'), response.responseText.lastIndexOf('}'));
                    try {
                        response = $.parseJSON(response.responseText);
                        if (typeof response.info !== 'undefined') {
                            $this.find('#sq_autologin').hide();
                            $this.find('ul').show();

                            $this.find('.sq_message').html(response.info).show();
                            $this.find('#sq_user').val($this.find('#sq_email').val());
                            $this.find('#sq_password').focus();
                        } else {
                            if (typeof response.error !== 'undefined') {
                                $this.find('.sq_error').html(response.error);
                                $this.find('#sq_register_email').show();
                                $this.find('#sq_register').html(__try_again);
                            }
                        }
                    } catch (e) {
                    }

                } else {

                    $this.find('#sq_register_wait').removeClass('sq_minloading');
                    $this.find('.sq_error').html(__error_login);
                    $this.find('#sq_register_email').show();
                    $this.find('#sq_register').html(__try_again);
                }
            }, 'json');
        };

        $this.sq_reload = function (response) {
            if (typeof response.success !== 'undefined') {
                $('#sq_login_success').html(response.success);
            }
            if ($('#postsquirrly').length > 0) {
                $this.remove();
                location.reload();
            } else {
                $this.addClass('sq_login_done');
                $this.html($('#sq_login_success'));

                $this.append($('#sq_goto_dashboard'));
                $('#sq_login_success').show();
                $('#sq_goto_dashboard').show();
                $this.find('.sq_login_link').after($('#sq_goto_dashboard').clone());
                $this.find('.sq_login_link').remove();

                $(".sq_goto_dashboard").on('click', function () {
                    location.href = "?page=sq_dashboard";
                });
            }
        };

        $this.checkEmail = function (email) {
            var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]+$/i;

            if (email !== '') {
                return emailRegEx.test(email);
            }
            return true;
        };

        $this.find('#sq_email').on('keypress', function (event) {
            if (event.keyCode === 13)
                $.sq_autoLogin();

            return event.keyCode !== 13;
        });

        $this.find('#sq_user').on('keypress', function (event) {
            if (event.keyCode === 13)
                $this.find('#sq_login').trigger('click');

            return event.keyCode !== 13;
        });

        $this.find('#sq_password').on('keypress', function (event) {
            if (event.keyCode === 13)
                $this.find('#sq_login').trigger('click');

            return event.keyCode !== 13;
        });

        $this.find('#sq_terms').on('click', function () {
            if ($(this).prop("checked") == true) {
                $this.find('#sq_loginimage').css('opacity', '1');
            } else {
                $this.find('#sq_loginimage').css('opacity', '0.4');
            }
        });


        $this.find('#sq_signin').on('click', function (event) {
            $this.find('#sq_autologin').hide();
            $this.find('ul').show();

            $this.find('#sq_user').val($('#sq_email').val());
            $this.find('#sq_email').focus();
        });

        $this.find('#sq_signup').on('click', function (event) {
            $this.find('#sq_autologin').show();
            $this.find('ul').hide();

            $this.find('#sq_email').focus();
        });

        $this.find('#sq_login').on('click', function () {
            $this.find('#sq_login').addClass('sq_minloading');
            $this.find('#sq_login').attr("disabled", "disabled");
            $this.find('#sq_login').val('');

            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_login',
                    user: $this.find('#sq_user').val(),
                    password: $this.find('#sq_password').val(),
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                if (typeof response.error !== 'undefined')
                    if (response.error === 'invalid_token') {

                        $.post(
                            sqQuery.ajaxurl,
                            {
                                action: 'sq_reset',
                                nonce: sqQuery.nonce
                            }
                        ).done(function (response) {
                            if (typeof response.reset !== 'undefined')
                                if (response.reset === 'success')
                                    location.reload();
                        }, 'json');
                    }
                $this.find('#sq_login').removeAttr("disabled");
                $this.find('#sq_login').val('Login');
                $this.find('#sq_login').removeClass('sq_minloading');

                if (typeof response.token !== 'undefined') {
                    __token = response.token;
                    $this.sq_reload(response);
                } else if (typeof response.error !== 'undefined')
                    $this.find('.sq_error').html(response.error);

            }).fail(function (response) {
                if (response.status === 200 && response.responseText.indexOf('{') > 0) {
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'), response.responseText.lastIndexOf('}'));
                    try {
                        response = $.parseJSON(response.responseText);
                        $this.find('#sq_login').removeAttr("disabled");
                        $this.find('#sq_login').val('Login');
                        $this.find('#sq_login').removeClass('sq_minloading');

                        if (typeof response.token !== 'undefined') {
                            __token = response.token;
                            $this.sq_reload(response);
                        } else if (typeof response.error !== 'undefined')
                            $this.find('.sq_error').html(response.error);

                    } catch (e) {
                    }

                } else {
                    $this.find('#sq_login').removeAttr("disabled");
                    $this.find('#sq_login').val('Login');
                    $this.find('#sq_login').removeClass('sq_minloading');
                    $this.find('.sq_error').html(__error_login);
                }
            }, 'json');
        });

        return $this;
    };



    if ($('#sq_blocklogin').length > 0) {
        $('#sq_blocklogin').sq_blocklogin();
    } else {
        $(document).ready(function () {
            $('#sq_blocklogin').sq_blocklogin();
        });
    }
})(jQuery);