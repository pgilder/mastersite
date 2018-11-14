(function ($) {
    $.fn.sq_serpChecker = function () {
        var $this = this;
        var sq_article = 0;

        $this.listenRefresh = function (post_id) {
            $this.find('#sq_rank_column_button_recheck' + post_id).hide();
            $this.find('#sq_rank_value' + post_id).html('').addClass('sq_minloading');
            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_serp_recheck',
                    post_id: post_id,
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                $this.find('#sq_rank_value' + post_id).removeClass('sq_minloading');
                if (typeof response.rank !== 'undefined') {
                    //location.reload();
                    $this.find('#sq_rank_value' + post_id).html(response.rank).removeClass('sq_loading');
                } else {
                    $this.find('#sq_rank_value' + post_id).html('Error').removeClass('sq_minloading');
                }
                $this.find('#sq_rank_column_button_recheck' + post_id).show();

            }).fail(function () {
                $this.find('#sq_rank_value' + post_id).html('Error').removeClass('sq_minloading');
                $this.find('#sq_rank_column_button_recheck' + post_id).show();
            }, 'json');
        };

        $this.processPosts = function () {
            return $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_serp_process',
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                $this.trigger('sq_serp_progress', [response]);
            }).fail(function () {
                $(this).removeClass('sq_minloading');
            }, 'json');
        };

        $this.listenRecheck = function () {
            $this.find('.sq_rank_refresh').on('click', function () {
                var $button = $(this);
                var $id = $(this).data('id');
                var $keyword = $(this).data('keyword');
                $button.addClass('sq_minloading');
                $this.find('#sq_rank_value' + $id).addClass('sq_minloading');
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_serp_refresh_post',
                        id: $id,
                        keyword: $keyword,
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.rank !== 'undefined') {
                        $this.find('#sq_rank_value' + $id).html(response.rank);
                    } else {
                        $.sq_showMessage(response.error).addClass('sq_error');
                    }
                    $button.removeClass('sq_minloading');
                    $this.find('#sq_rank_value' + $id).removeClass('sq_minloading');

                }).fail(function () {
                    $button.removeClass('sq_minloading');
                    $this.find('#sq_rank_value' + $id).removeClass('sq_minloading');

                }, 'json');
            });
        };

        $this.listenButtons = function () {
            $this.find('.sq_serp_sendnow').on('click', function () {
                var $button = $(this);
                $button.addClass('sq_minloading');
                //Start the progress
                $this.processPosts();

                $this.on('sq_serp_progress', function (event, response) {
                    $button.removeClass('sq_minloading');
                    if (typeof response.progress !== 'undefined') {
                        if (response.progress == 100) {
                            location.reload();
                            $this.find('.sq_serp_progress').hide();
                            $this.find('.sq_serp_update').show();
                        } else {
                            $this.find('.sq_serp_progress').find('.progress-bar').css('width', response.progress + '%');
                            $this.find('.sq_serp_progress').show();
                            $this.find('.sq_serp_update').hide();
                            //call the progres again
                            $this.processPosts();
                        }
                    }
                });
            });

            $this.find('.sq_serp_refresh').on('click', function () {
                var $button = $(this);
                $button.addClass('sq_minloading');
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_serp_refresh',
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.refreshed !== 'undefined') {
                        location.reload();
                    }
                }).fail(function () {
                    $(this).removeClass('sq_minloading');
                }, 'json');
            });

            $this.find('.sq_serp_settings').on('click', function () {
                location.href = '?page=sq_settings#ranking';
            });
        };


        $this.listenAddKeyword = function () {
            $this.find('#sq_save_keyword').on('click', function () {
                var $button = $(this);
                if ($this.find('#sq_article').val() == '') {
                    alert('Please add an Article URL first');
                    return;
                }
                if ($this.find('#sq_keyword').val() == '') {
                    alert('Please add the keyword first');
                    return;
                }
                $button.addClass('sq_minloading');

                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_serp_addkeyword',
                        keyword: $this.find('#sq_keyword').val(),
                        post_url: $this.find('#sq_article').val(),
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    $button.removeClass('sq_minloading');
                    if (typeof response.sent !== 'undefined') {
                        location.reload();
                    } else if (typeof response.error !== 'undefined') {
                        alert(response.error);
                    } else {
                        alert('Could not save the keyword');
                    }

                }).fail(function () {
                    $button.removeClass('sq_minloading');
                    alert('Could not save the keyword');
                }, 'json');
            });
        };

        $this.listenIgnore = function () {
            $this.find('.ignore-keyword').on('change', function () {
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_serp_ignore',
                        keyword: $(this).data('keyword'),
                        active: ($(this).prop('checked') ? 1 : 0),
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.saved !== 'undefined') {
                        $.sq_showMessage().addClass('sq_success');
                    }

                }).fail(function () {

                }, 'json');
            });
        };

        $this.listenButtons();
        $this.listenIgnore();
        $this.listenAddKeyword();
        $this.listenRecheck();

        //Refresh the post ID
        $this.on('sq_serp_recheck', function (e, post_id) {
            // console.log(post_id);
            $this.listenRefresh(post_id);
        });

        $this.find('input[name=sq_show_ignored]').on('change', function () {
            $this.find('form[name=sq_form]').submit();
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        elems.forEach(function (html) {
            new Switchery(html);
        });

        $this.find('select[name=sq_mode]').on('change', function () {
            $this.find('.tab-panel').hide();
            $this.find('.src_' + $(this).val()).show();
        });

        $this.find('[data-toggle="tooltip"]').tooltip();

        return $this;
    };


    $(document).ready(function () {
        $('#src_settings').sq_serpChecker();
    });

})(jQuery);
