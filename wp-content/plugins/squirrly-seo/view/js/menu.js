(function ($) {
    /**
     * Set the Cookie
     *
     * @param name string cookie name
     * @param value string cookie value
     * @return void
     */
    $.sq_setCookie = function (name, value) {
        document.cookie = name + "=" + value + "; expires=" + (60 * 24 * 2) + "; path=/";
        return false;
    };

    /**
     * Get the Cookie
     *
     * @param name string cookie name
     * @return void
     */
    $.sq_getCookie = function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    };

    $.sq_getHash = function () {
        var urlparts = location.href.split('#');

        if (urlparts.length >= 2) {
            urlparts.shift();
            return urlparts.join("#"); //join it back up

        }

        return false;
    };

    $.sq_setHash = function (hash) {
        location.href += '#' + hash;
    };

    $.sq_getHashParam = function (key) {
        var urlparts = location.href.split('#');

        if (urlparts.length >= 2) {
            urlparts.shift();
            var queryString = urlparts.join("#"); //join it back up
            var results = new RegExp('[\\?&#]*' + key + '=([^&#]*)').exec(queryString);
            if (results) {
                return results[1] || 0;
            }
        }

        return false;
    };

    $.sq_setHashParam = function (key, val) {
        var urlparts = location.href.split('#');

        if (urlparts.length >= 2) {
            var add = true;
            var urlBase = urlparts.shift(); //get first part, and remove from array
            var queryString = urlparts.join("#"); //join it back up

            var prefix = encodeURIComponent(key) + '=';
            var pars = queryString.split(/[&;]/g);
            for (var i = pars.length; i-- > 0;) {//reverse iteration as may be destructive
                if (pars[i].lastIndexOf(prefix, 0) !== -1 || pars[i] === '') {
                    pars[i] = pars[i].replace(pars[i], prefix + val);
                    add = false;
                    break;
                }
            }

            add && pars.push(prefix + val);

            location.href = urlBase + '#' + pars.join('&');
        } else {
            location.href += '#' + key + '=' + val;
        }
    };

    $.sq_deleteHashParam = function (key) {
        var urlparts = location.href.split('#');

        if (urlparts.length >= 2) {
            var urlBase = urlparts.shift(); //get first part, and remove from array
            var queryString = urlparts.join("#"); //join it back up

            var prefix = encodeURIComponent(key) + '=';
            var pars = queryString.split(/[&;]/g);
            for (var i = pars.length; i-- > 0;) {             //reverse iteration as may be destructive
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {   //idiom for string.startsWith
                    pars.splice(i, 1);
                    break;
                }
            }

            location.href = urlBase + '#' + pars.join('&');
        }
    };

    $.fn.sq_blockmenu = function () {
        var $this = this;
///////////////////////////////
        window.addEventListener("hashchange", function () {
            window.scrollTo(window.scrollX, window.scrollY - 50);
        });

        //Go to dashboard listener
        $this.find(".sq_goto_dashboard").on('click', function () {
            location.href = "?page=sq_dashboard";
        });

        $this.find('input[name=sq_update]').on('click', function () {
            $this.find('#sq_settings_form').submit();
        });

        $this.find('input[name=sq_restore]').on('click', function () {
            $this.find('.sq_settings_restore').show();
        });

        $this.find('input[name=sq_restore_sql]').on('click', function () {
            $this.find('.sq_settings_restore_sql').show();
        });

        $this.find('.sq_settings_restore').find('.sq_close').on('click', function () {
            $this.find('.sq_settings_restore').find('textarea').val('');
            $this.find('.sq_settings_restore').hide();
        });


        $this.find('.sq_settings_restore_sql').find('.sq_close').on('click', function () {
            $this.find('.sq_settings_restore_sql').find('textarea').val('');
            $this.find('.sq_settings_restore_sql').hide();
        });

        //switch click
        $this.find('#sq_settings_form').find('input[type=radio]').on('change', function () {
            $this.sq_submitSettings();
        });

        //Custom title/description
        $this.find('#sq_customize').on('click', function () {
            $this.find('#sq_customize_settings').show();
            $this.find('#sq_snippet_disclaimer').show();
            $this.find('#sq_title_description_keywords').addClass('sq_custom_title');
        });

        //Login
        $this.find('.sq_login_link').on('click', function () {
            var previewtop = $this.find('#sq_settings_login').offset().top - 100;
            $('html,body').animate({scrollTop: previewtop}, 1000);
        });

        $this.find('.sq_remove').on('click', function () {
            if (confirm($(this).data('confirm'))) {
                $('.sq' + $(this).data('id')).remove();
                $("#sq_post_types option[value='" + $(this).data('id') + "']").remove();
                $("#sq_post_types").val(1).trigger('change');
            }
        });

    };


    //Show the title length in post editor
    $.sq_trackLength = function (field, type) {
        var min = 0;
        var max = 0;
        if (typeof field === 'undefined')
            return;

        if (type === 'title' || type === 'wp_title') {
            min = 10;
            max = 75;
        } else if (type === 'description') {
            min = 70;
            max = 320;
        }
        if (min > 0 && min > field.val().length)
            $('#sq_' + type + '_info').html(__snippetshort);
        else if (max > 0 && max < field.val().length)
            $('#sq_' + type + '_info').html(__snippetlong);
        else if (max > 0) {
            $('#sq_' + type + '_info').html(field.val().length + '/' + max);
        }
    };

    //get the snippet in settings and post editor
    $.sq_getSnippet = function (url, show_url) {
        if ($('#sq_snippet').length == 0) {
            return;
        }

        if (typeof url === 'undefined') {
            url = '';
        }
        if (typeof show_url === 'undefined') {
            show_url = '';
        }
        $('#sq_snippet_ul').addClass('sq_minloading');

        $('#sq_snippet_title').html('');
        $('#sq_snippet_url').html('');
        $('#sq_snippet_description').html('');
        $('#sq_snippet_keywords').hide();
        $('#sq_snippet').show();
        $('#sq_snippet_update').hide();
        $('#sq_snippet_customize').hide();
        $('#ogimage_preview').hide();

        setTimeout(function () {
            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_get_snippet',
                    url: url,
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                $('#sq_snippet_ul').removeClass('sq_minloading');
                $('#sq_snippet_update').show();
                $('#sq_snippet_customize').show();
                $('#sq_snippet_keywords').show();
                $('#ogimage_preview').show();
                if (response) {
                    $('#sq_snippet_title').html(response.title);
                    if (show_url !== '')
                        $('#sq_snippet_url').html('<a href="' + url + '" target="_blank">' + show_url + '</a>');
                    else
                        $('#sq_snippet_url').html(response.url);

                    $('#sq_snippet_description').html(response.description);
                }
            }).fail(function () {
                $('#sq_snippet_ul').removeClass('sq_minloading');
                $('#sq_snippet_update').show();
            }, 'json');
        }, 500);
    };

    //Show user status in Squirrly > Account info
    $.sq_getUserStatus = function () {
        $('#sq_userinfo').addClass('sq_loading');
        $('#sq_userstatus').addClass('sq_loading');

        $.getJSON(
            __api_url + 'sq/user/status/?callback=?',
            {
                token: __token,
                lang: (document.getElementsByTagName("html")[0].getAttribute("lang") || window.navigator.language)
            }
        ).done(function (response) {
            $.sq_checkResponse(response);

            $('#sq_userinfo').removeClass('sq_loading').removeClass('sq_error');
            $('#sq_userstatus').removeClass('sq_loading').removeClass('sq_error');
            if (typeof response.info !== 'undefined' && response.info !== '') {
                $('#sq_userinfo').html(response.info);
            }
            if (typeof response.stats !== 'undefined' && response.stats !== '') {
                $('#sq_userstatus').html(response.stats);
            }
            if (typeof response.data !== 'undefined' && typeof response.data.user_registered_date !== 'undefined') {
                var currentDate = new Date();
                var day = currentDate.getDate();
                if (day.toString().length === 1)
                    day = '0' + day.toString();
                var month = currentDate.getMonth() + 1;
                if (month.toString().length === 1)
                    month = '0' + month.toString();
                var year = currentDate.getFullYear();
                var currDate = year + '-' + month + '-' + day;
                var passed = ((new Date(currDate).getTime() - new Date(response.data.user_registered_date).getTime()) / (24 * 60 * 60 * 1000));
                ;
                if (passed <= 3 && $('#sq_survey').length > 0)
                    $('#sq_survey').show();
            }
        }).fail(function () {
            // $('#sq_userinfo').removeClass('sq_loading');
            $('#sq_userinfo').html('');
            $('#sq_userstatus').html('');
        });
        $('#sq_survey').show();
    };

    //Recheck the user rank in Squirrly > Performance analytics
    $.sq_recheckRank = function (post_id) {
        $('.sq_rank_column_button_recheck').hide();
        $('#sq_rank_value' + post_id).html('').addClass('sq_loading');
        $.post(
            sqQuery.ajaxurl,
            {
                action: 'sq_recheck',
                post_id: post_id,
                nonce: sqQuery.nonce
            }
        ).done(function (response) {
            if (typeof response.rank !== 'undefined') {
                $('#sq_rank_value' + post_id).html(response.rank).removeClass('sq_loading');
            } else {
                $('#sq_rank_value' + post_id).html('Error').removeClass('sq_loading');
            }
            setTimeout(function () {
                $('.sq_rank_column_button_recheck').show();
            }, 10000)


        }).fail(function () {
            $('#sq_rank_value' + post_id).html('Error').removeClass('sq_loading');
            $('.sq_rank_column_button_recheck').show();
        }, 'json');
    };

    //Show user status in Squirrly > Account info
    $.sq_getSlides = function (category) {
        if ($('#sq_help' + category + 'slides').length == 0) {
            return;
        }

        $.getJSON(
            __api_url + 'sq/help/slides?callback=?',
            {
                category: category,
                lang: (document.getElementsByTagName("html")[0].getAttribute("lang") || window.navigator.language)
            }
        ).done(function (response) {
            $('#sq_help' + category + 'slides').removeClass('sq_loading').removeClass('sq_error');
            if (typeof response.html !== 'undefined' && response.html !== '') {
                $('#sq_help' + category + 'slides').html(response.html).show();
            }
        });
    };

    $.sq_getHelp = function (category, zone) {
        var loadingAjax = true;

        if (zone == 'content' && $('#sq_help' + category + zone).length == 0) {
            $.sq_getHelp(category, 'side');
            return;
        } else {
            if ($('#sq_help' + category + zone).length == 0) {
                return;
            }
        }

        $('#sq_help' + category + zone).addClass('sq_loading');
        $.getJSON(
            __api_url + 'sq/help/?callback=?',
            {
                token: __token,
                user_url: __blog_url,
                category: category,
                zone: zone,
                lang: (document.getElementsByTagName("html")[0].getAttribute("lang") || window.navigator.language)
            }
        ).done(function (response) {
            $.sq_checkResponse(response);
            loadingAjax = false;

            $('#sq_help' + category + zone).removeClass('sq_loading').removeClass('sq_error');
            if (typeof response.html !== 'undefined' && response.html !== '') {
                $('#sq_help' + category + zone).html(response.html).show();
                if (typeof response.remained_here !== 'undefined') {
                    var active_help = category;
                    if (response.remained_here > 0) {
                        if (category === 'settingsseo') {
                            active_help = 'settings';
                        }
                    } else {
                        active_help = '';
                    }

                    $.post(
                        sqQuery.ajaxurl,
                        {
                            action: 'sq_active_help',
                            active_help: active_help,
                            nonce: sqQuery.nonce
                        });
                }
            }
            if (typeof response.side !== 'undefined' && response.side !== '') {
                $('#sq_help' + category + 'side').html(response.side).show();
            }
        }).fail(function () {
            loadingAjax = false;
            $('#sq_help' + category + zone).removeClass('sq_loading');
            $('#sq_help' + category + 'content').html('Lost connection with the server. Please make sure you whitelisted the IP from https://api.squirrly.co').show();
        });

        setTimeout(function () {
            if (loadingAjax) {
                $('#sq_help' + category + zone).removeClass('sq_loading').addClass('sq_error').show();
                $('#sq_help' + category + 'content').html('Lost connection with the server. Please make sure you whitelisted the IP from https://api.squirrly.co');
            }
        }, 20000);

    };

    $.sq_checkResponse = function (response) {
        if (typeof response.error !== 'undefined') {
            if (response.error === 'invalid_token') {
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_reset', nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.reset !== 'undefined')
                        if (response.reset === 'success')
                            location.href = "?page=sq_dashboard";
                }, 'json');
            }
        }
    };

    $.sq_showMessage = function (text, time) {
        if (typeof text === 'undefined') {
            text = 'Saved!';
        }

        $("#sq_settings").prepend('<div class="sq_savenotice sq_absolute" ><span>' + text + '</span></div>');

        if (typeof time === 'undefined') {
            time = 2000;
        }
        setTimeout(function () {
            $('.sq_savenotice').remove();
        }, time);


        return $("#sq_settings").find('.sq_savenotice').find('span');
    };

    if ($('#sq_settings').length > 0) {
        $('#sq_settings').sq_blockmenu();
    } else {
        $(document).ready(function () {
            $('#sq_settings').sq_blockmenu();
        });
    }
})(jQuery);