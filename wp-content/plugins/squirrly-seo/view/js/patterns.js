(function ($) {
    $.fn.sq_patterns = function () {
        var $this = this;

        var settings = {
            field: false,
            sq_pattern_icon: $('<div class="sq_pattern_icon" title="Click to edit/add more patterns"></div>'),
            sq_pattern_list: $('<div class="sq_pattern_list" style="display: none"><ul><li class="sq_notyle">Click to insert a pattern:</li></ul></div>'),
        };

        if ($this.find('input[type=text]').length > 0) {
            settings.field = $this.find('input[type=text]');
        } else if ($this.find('textarea').length > 0) {
            settings.field = $this.find('textarea');
        }

        $this.listenIcon = function () {
            if (!$this.find('.sq_pattern_icon').length) {
                $this.append(settings.sq_pattern_icon);

                settings.sq_pattern_icon.on('click', function () {
                    if (!$this.find('.sq_pattern_list').length) {
                        $this.append(settings.sq_pattern_list);

                        if (typeof $.sq_patterns_list !== 'undefined') {
                            for (var pattern in $.sq_patterns_list) {
                                $this.find('.sq_pattern_list').find('ul').append('<li title="' + $.sq_patterns_list[pattern] + '">' + pattern + '</li>');
                            }
                            $this.selectPattern();
                        }

                        settings.sq_pattern_list.find('li').on('click', function () {
                            $this.selectPattern($(this).html());
                        });

                        settings.field.on('change', function () {
                            $this.selectPattern();
                        });
                    }

                    settings.sq_pattern_list.toggle();

                    if (settings.sq_pattern_list.is(':visible')) {
                        settings.sq_pattern_icon.addClass('sq_opened');

                    } else {
                        settings.sq_pattern_icon.removeClass('sq_opened');
                    }

                });
            }
        };

        $this.selectPattern = function (pattern) {
            var words = [];
            if (typeof pattern !== 'undefined') {
                if (settings.field.val().length == 0 && settings.field.attr('placeholder')) {
                    words = settings.field.attr('placeholder').split(' ');
                    if (words.length > 0) {
                        for (var i = 0; i < words.length; i++) {
                            if (words[i].match(/{{[a-z_]+}}/g)) {
                                settings.field.val(settings.field.val() + ' ' +words[i]);
                            }
                        }
                    }
                }

                if (pattern !== '{{sep}}' && settings.field.val().indexOf(pattern) !== -1) {
                    settings.field.val(settings.field.val().replace(' ' + pattern, ''));
                    settings.field.val(settings.field.val().replace('  ', ' '));
                } else {
                    settings.field.val(settings.field.val() + ' ' + pattern);
                }
            }

            if (settings.field.val().length > 1) {
                words = settings.field.val().split(' ');
            } else if (settings.field.attr('placeholder')) {
                words = settings.field.attr('placeholder').split(' ');
            }


            settings.sq_pattern_list.find('li').each(function () {
                $(this).removeClass('sq_patterns_selected');
            });

            if (words.length > 0) {
                for (var i = 0; i < words.length; i++) {
                    if (words[i].match(/{{[a-z_]+}}/g)) {
                        settings.sq_pattern_list.find('li').each(function () {
                            if (words[i] === $(this).html()) {
                                $(this).addClass('sq_patterns_selected');
                            }
                        });
                    }
                }
            }
        };

        if (settings.field) {
            $this.listenIcon();
        }

        return $this;

    };

    $(document).ready(function () {
        $('.sq_pattern_field').each(function () {
            $(this).sq_patterns();
        });
    });
})(jQuery);