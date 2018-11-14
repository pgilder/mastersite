(function ($) {

    $.fn.sq_Briefcase = function () {
        var $this = this;
        var $popupAddKeyword = $this.find('.sq_add_keyword_dialog');
        var $popupAddLabel = $this.find('.sq_add_labels_dialog');
        var $popupEditLabel = $this.find('.sq_edit_label_dialog');

        $this.sq_getResearchLang = function (id) {
            if (typeof id !== 'undefined' && id !== '') {
                try {
                    if (id.indexOf('_') !== -1)
                        return id.substring(id.lastIndexOf('_') + 1);
                } catch (e) {
                    return 'us';
                }
            }

            return 'us';
        };


        $this.listenAdd = function () {
            $popupAddKeyword.find('#sq_save_keyword').on('click', function () {
                var $button = $(this);
                var $keyword = $popupAddKeyword.find('input#sq_keyword').val();

                var $labels = [];
                $popupAddKeyword.find('input[name="sq_labels"]:checked').each(function () {
                    $labels.push(this.value);
                });

                $button.addClass('sq_minloading');
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_briefcase_addkeyword',
                        keyword: $keyword,
                        labels: $labels,
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.saved !== 'undefined') {
                        location.reload();
                        $button.removeClass('sq_minloading');
                    } else {
                        $button.removeClass('sq_minloading');
                        $.sq_showMessage(response.error).addClass('sq_error');

                    }
                }).fail(function () {
                    $button.removeClass('sq_minloading');
                }, 'json');
            });

            $popupAddLabel.find('#sq_labelcolor').wpColorPicker();
            $popupAddLabel.find('#sq_save_label').on('click', function () {
                var $button = $(this);
                var $name = $popupAddLabel.find('input#sq_labelname').val();
                var $color = $popupAddLabel.find('input#sq_labelcolor').val();

                $button.addClass('sq_minloading');
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_briefcase_addlabel',
                        name: $name,
                        color: $color,
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.saved !== 'undefined') {
                        location.reload();
                        $button.removeClass('sq_minloading');
                    } else {
                        $button.removeClass('sq_minloading');
                        $.sq_showMessage(response.error).addClass('sq_error');

                    }
                }).fail(function () {
                    $button.removeClass('sq_minloading');
                }, 'json');
            });

            $this.find('.sq_save_keyword_labels').on('click', function () {
                var $popup = $(this).parents('.sq_label_manage_popup:last');
                var $button = $(this);
                var $keyword_id = $(this).data('id');

                var $labels = [];
                $popup.find('input[name="sq_labels"]:checked').each(function () {
                    $labels.push(this.value);
                });

                $button.addClass('sq_minloading');
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_briefcase_keywordlabel',
                        keyword_id: $keyword_id,
                        labels: $labels,
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.saved !== 'undefined') {
                        location.reload();
                        $button.removeClass('sq_minloading');
                    } else {
                        $button.removeClass('sq_minloading');
                        $.sq_showMessage(response.error).addClass('sq_error');

                    }
                }).fail(function () {
                    $button.removeClass('sq_minloading');
                }, 'json');
            });
        };

        $this.listenEdit = function () {
            $popupEditLabel.find('#sq_labelcolor').wpColorPicker();

            $this.find('.sq_edit_label').on('click', function () {
                $('#element .wp-picker-clear').trigger('click');
                $popupEditLabel.find('input#sq_labelid').val($(this).attr('data-id'));
                $popupEditLabel.find('input#sq_labelname').val($(this).attr('data-name'));
                $popupEditLabel.find('input#sq_labelcolor').val($(this).attr('data-color'));
                $popupEditLabel.find('input#sq_labelcolor').trigger('change');
                $popupAddKeyword.modal('hide');
                $popupAddLabel.modal('hide');
                $popupEditLabel.modal('show');
            });

            $popupEditLabel.find('#sq_save_label').on('click', function () {
                var $button = $(this);
                var $id = $popupEditLabel.find('#sq_labelid').val();
                var $name = $popupEditLabel.find('#sq_labelname').val();
                var $color = $popupEditLabel.find('#sq_labelcolor').val();

                $button.addClass('sq_minloading');
                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_briefcase_editlabel',
                        id: $id,
                        name: $name,
                        color: $color,
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.saved !== 'undefined') {
                        location.reload();
                        $button.removeClass('sq_minloading');
                    } else {
                        $button.removeClass('sq_minloading');
                        $.sq_showMessage(response.error).addClass('sq_error');

                    }
                }).fail(function () {
                    $button.removeClass('sq_minloading');
                }, 'json');
            });
        };

        $this.listenResearch = function () {
            $this.find('ul.sq_research_lang').on('mouseleave', function () {
                $this.find('ul.sq_research_lang').scrollTop(0);
            });
            $this.find('li.sq_research_lang').on('click', function () {
                $this.find('li.sq_research_lang').removeClass('selected');
                $(this).addClass('selected');
                $this.find("li.sq_research_first").html($(this).html());
                $this.find("li.sq_research_first").attr('id', $(this).attr('id'));
                $.sq_setCookie('sq_research_lang', $(this).attr('id'));

                $this.find('input[name=sq_kr_country]').val($this.sq_getResearchLang($(this).attr('id')));
                $this.find('ul.sq_research_lang').scrollTop(0);

            });

            if ($.sq_getCookie('sq_research_lang') !== '') {
                $('#' + $.sq_getCookie('sq_research_lang')).addClass('selected');
                $('#' + $.sq_getCookie('sq_research_lang')).trigger('click');
                $this.find('input[name=sq_kr_country]').val($this.sq_getResearchLang($.sq_getCookie('sq_research_lang')));
            } else {
                $this.find('ul.sq_research_lang').find('#sq_research_lang_us').addClass('selected');
                $this.find('input[name=sq_kr_country]').val('us');
            }

            $this.find('#sq_doresearch').on('click', function () {
                var $button = $(this);
                var $keyword = $this.find('input[name=sq_kr_keyword]').val();
                var $country =  $this.find('input[name=sq_kr_country]').val();
                $button.addClass('sq_minloading');

                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_briefcase_doresearch',
                        keyword: $keyword,
                        country: $country,
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.error !== 'undefined') {
                        if (response.error === 'limit_exceeded') {
                            $.sq_showMessage(response.error).addClass('sq_error');
                        } else {
                            $.sq_showMessage(response.error, 10000).addClass('sq_error');
                        }
                        $button.removeClass('sq_minloading');
                    } else {
                        location.reload();
                    }

                }).fail(function () {
                    $button.removeClass('sq_minloading');
                }, 'json');
            });
        };

        $this.listenCount = function () {
            $this.find('.sq_open_subrow').on('click', function () {
                var $button = $(this);
                var $id = $(this).data('id');
                var $keyword = $(this).data('keyword');
                $this.find('.fa_showmore').removeClass('fa-sort-asc');

                if ($('#sq_subrow_' + $id).is(':visible')) {
                    $('#sq_subrow_' + $id).hide();
                    return;
                }

                $button.addClass('sq_minloading');

                $.post(
                    sqQuery.ajaxurl,
                    {
                        action: 'sq_briefcase_article',
                        keyword: $keyword,
                        nonce: sqQuery.nonce
                    }
                ).done(function (response) {
                    if (typeof response.error !== 'undefined') {
                        if (response.error === 'limit_exceeded') {
                            $.sq_showMessage(response.error).addClass('sq_error');
                        } else {
                            $.sq_showMessage(response.error, 10000).addClass('sq_error');
                        }
                    } else if (typeof response.articles !== 'undefined') {
                        $('.sq_subrow').hide();
                        $('#sq_subrow_' + $id).find('td').html(response.articles);
                        $('#sq_subrow_' + $id).show();

                        $this.listenRecheck();
                    }
                    $('#sq_row_' + $id).find('.fa_showmore').addClass('fa-sort-asc');

                    $button.removeClass('sq_minloading');
                }).fail(function () {
                    $button.removeClass('sq_minloading');
                }, 'json');
            });
        };

        $this.listenDelete = function () {
            $this.find('.sq_delete_keyword').on('click', function () {
                if (confirm('Are you sure ?')) {
                    var $button = $(this);
                    var $id = $(this).data('id');
                    $button.addClass('sq_minloading');
                    $.post(
                        sqQuery.ajaxurl,
                        {
                            action: 'sq_briefcase_deletekeyword',
                            id: $id,
                            nonce: sqQuery.nonce
                        }
                    ).done(function (response) {
                        if (typeof response.deleted !== 'undefined') {
                            $this.find('#sq_row_' + $id).remove();
                            $this.find('#sq_subrow_' + $id).remove();
                        } else {
                            $.sq_showMessage(response.error).addClass('sq_error');
                        }
                        $button.removeClass('sq_minloading');
                    }).fail(function () {
                        $button.removeClass('sq_minloading');
                    }, 'json');
                }
            });

            $this.find('.sq_delete_label').on('click', function () {
                if (confirm('Are you sure ?')) {
                    var $button = $(this).parents('.sq_saved_label:last');
                    var $id = $(this).data('id');
                    $button.addClass('sq_minloading');
                    $.post(
                        sqQuery.ajaxurl,
                        {
                            action: 'sq_briefcase_deletelabel',
                            id: $id,
                            nonce: sqQuery.nonce
                        }
                    ).done(function (response) {
                        if (typeof response.deleted !== 'undefined') {
                            $button.remove();
                            $this.find('label[data-id=' + $id + ']').remove();
                            $this.find('.sq_circle_label[data-id=' + $id + ']').remove();
                        } else {
                            $.sq_showMessage(response.error).addClass('sq_error');
                        }
                        $button.removeClass('sq_minloading');
                    }).fail(function () {
                        $button.removeClass('sq_minloading');
                    }, 'json');
                }
            });
        };

        $this.listenRecheck = function () {
            $this.find('.sq_rank_refresh').on('click', function () {
                var $button = $(this);
                var $id = $(this).data('id');
                var $keyword = $(this).data('keyword');
                $this.find('#sq_rank_value' + $id).addClass('sq_minloading');
                $button.addClass('sq_minloading');
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
                        $('#sq_rank_value' + $id).html(response.rank);
                        if (typeof response.datetime !== 'undefined') {
                            $('#sq_rank_value' + $id).attr('title', response.datetime);
                        }
                    } else {
                        $.sq_showMessage(response.error).addClass('sq_error');
                    }
                    $this.find('#sq_rank_value' + $id).removeClass('sq_minloading');
                    $button.removeClass('sq_minloading');
                }).fail(function () {
                    $this.find('#sq_rank_value' + $id).removeClass('sq_minloading');
                    $button.removeClass('sq_minloading');
                }, 'json');
            });
        };

        $this.listenAdd();
        $this.listenEdit();
        $this.listenCount();
        $this.listenResearch();
        $this.listenDelete();



        $this.find('.sq_research_selectit').on('click', function () {
            $(this).addClass('sq_minloading');
            var $keyword = $(this).data('keyword');
            $.sq_setCookie('sq_keyword', $keyword);

            location.href = $(this).data('post');
        });





        $('#sq_settings .sq_filter_label input[type=checkbox]').click(function () {
            $('#sq_settings .sq_filter_label input[type=checkbox]').each(function () {
                if (!$(this).is(':checked')) {
                    $(this).next('label').removeClass('sq_active');
                }
            });

            if ($(this).is(':checked')) {
                $(this).next('label').addClass('sq_active');
            }
        });
        $('#sq_settings .sq_add_keyword_dialog input[type=checkbox]').click(function () {
            $('#sq_settings .sq_add_keyword_dialog input[type=checkbox]').each(function () {
                if (!$(this).is(':checked')) {
                    $(this).next('label').removeClass('sq_active');
                }
            });

            if ($(this).is(':checked')) {
                $(this).next('label').addClass('sq_active');
            }
        });


        $('#sq_settings .sq_label_manage_popup input[type=checkbox]').click(function () {
            var $popup = $(this).parents('.sq_label_manage_popup:last');
            $popup.find('input[type=checkbox]').each(function () {
                if (!$(this).is(':checked')) {
                    $(this).next('label').removeClass('sq_active');
                }
            });

            if ($(this).is(':checked')) {
                $(this).next('label').addClass('sq_active');
            }
        });

        return $this;
    };


    if ($('#sq_settings').length > 0) {
        $('#sq_settings').sq_Briefcase();
    } else {
        $(document).ready(function () {
            $('#sq_settings').sq_Briefcase();
        });
    }

})(jQuery);

