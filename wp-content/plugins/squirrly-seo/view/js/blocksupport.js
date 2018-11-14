(function ($) {

    $.fn.sq_blocksupport = function () {
        var $this = this;

        $this.find('#sq_options_support').find('span').on('click', function () {
            $this.find('.sq_options_support_popup').show();
            $this.find('.sq_options_feedback_popup').hide();
        });
        $this.find('#sq_options_close').on('click', function () {
            $this.find('.sq_options_support_popup').hide();
        });

        $this.find('#sq_options_feedback').find('span').on('click', function () {
            $this.find('.sq_options_feedback_popup').show();
            $this.find("#sq_options_feedback").find('.sq_push').hide();
            $this.find('.sq_options_support_popup').hide();
        });

        $this.find("#sq_options_feedback_close").on('click', function () {
            $this.find('.sq_options_feedback_popup').hide();
        });

        $this.find("#sq_feedback_0").on('click', function () {
            $this.find('#sq_feedback_msg').show();
        });

        $this.find("#sq_feedback_1").on('click', function () {
            $this.find('#sq_feedback_msg').show();
        });

        $this.find("#sq_feedback_2").on('click', function () {
            $this.find("#sq_feedback_submit").trigger('click');
            for (i = 0; i < 5; i++) $this.find('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
            $this.find('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_2');

        });

        $this.find("#sq_feedback_3").on('click', function () {
            $this.find("#sq_feedback_submit").trigger('click');
            for (i = 0; i < 5; i++) $this.find('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
            $this.find('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_3');
        });

        $this.find("#sq_feedback_4").on('click', function () {
            $this.find("#sq_feedback_submit").trigger('click');
            for (i = 0; i < 5; i++) $this.find('#sq_options_feedback').find('.sq_icon').removeClass('sq_label_feedback_' + i);
            $this.find('#sq_options_feedback').find('.sq_icon').addClass('sq_label_feedback_4');

        });

        $this.find("#sq_feedback_submit").on('click', function () {
            $this.find('#sq_feedback_msg').hide();
            $this.find('#sq_options_feedback_error').html('<p class="sq_minloading" style="margin:0 auto; padding:2px;"></p>');
            $this.find('#sq_feedback_submit').attr("disabled", "disabled");


            document.cookie = "sq_feedback_face=" + $("input[name=sq_feedback_face]:radio:checked").val() + "; expires=" + (60 * 12) + "; path=/";

            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_feedback',
                    feedback: $this.find("input[name=sq_feedback_face]:radio:checked").val(),
                    sq_support_email: $this.find("input[name=sq_support_email]").val(),
                    message: $this.find("textarea[name=sq_feedback_message]").val(),
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                $this.find('#sq_feedback_submit').removeAttr("disabled");
                $this.find('#sq_feedback_submit').val('Send feedback');
                $this.find("textarea[name=sq_feedback_message]").val('');

                if (typeof response.message !== 'undefined') {
                    $this.find('#sq_options_feedback_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                } else
                    $this.find('#sq_options_feedback_error').removeClass('sq_error').html('');


            }).fail(function (response) {
                if (response.status === 200 && response.responseText.indexOf('{') > 0) {
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'), response.responseText.lastIndexOf('}'));
                    try {
                        response = $.parseJSON(response.responseText);
                        $this.find('#sq_feedback_submit').removeAttr("disabled");
                        $this.find('#sq_feedback_submit').val('Send feedback');
                        $this.find("textarea[name=sq_feedback_message]").val('');

                        if (typeof response.message !== 'undefined') {
                            $this.find('#sq_options_feedback_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                        } else
                            $this.find('#sq_options_feedback_error').removeClass('sq_error').html('');
                    } catch (e) {
                    }

                } else {
                    $this.find('#sq_feedback_submit').removeAttr("disabled");
                    $this.find('#sq_feedback_submit').val('Send feedback');
                    $this.find('#sq_feedback_submit').removeClass('sq_minloading');
                    $this.find('#sq_options_feedback_error').addClass('sq_error').removeClass('sq_message').html('Could not send the feedback');
                }
            }, 'json');
        });

        $this.find("#sq_support_submit").on('click', function () {
            $this.find('#sq_options_support_error').html('<p class="sq_minloading" style="margin:0 auto; padding:2px;"></p>');
            $this.find('#sq_support_submit').attr("disabled", "disabled");

            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_support',
                    sq_support_email: $this.find("input[name=sq_support_email]").val(),
                    message: $this.find("textarea[name=sq_support_message]").val(),
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                $this.find('#sq_support_submit').removeAttr("disabled");
                $this.find("textarea[name=sq_support_message]").val('');

                if (typeof response.message !== 'undefined') {
                    $this.find('#sq_options_support_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                } else
                    $this.find('#sq_options_support_error').removeClass('sq_error').html('');


            }).fail(function (response) {
                if (response.status === 200 && response.responseText.indexOf('{') > 0) {
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'), response.responseText.lastIndexOf('}'));
                    try {
                        response = $.parseJSON(response.responseText);
                        $this.find('#sq_support_submit').removeAttr("disabled");
                        $this.find("textarea[name=sq_support_message]").val('');

                        if (typeof response.message !== 'undefined') {
                            $this.find('#sq_options_support_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                        } else
                            $this.find('#sq_options_support_error').removeClass('sq_error').html('');
                    } catch (e) {
                    }

                } else {
                    $this.find('#sq_support_submit').removeAttr("disabled");
                    $this.find('#sq_support_submit').val('Send feedback');
                    $this.find('#sq_support_submit').removeClass('sq_minloading');
                    $this.find('#sq_options_support_error').addClass('sq_error').removeClass('sq_message').html('Could not send the feedback');
                }
            }, 'json');
        });


        /////////////////For Survey
        $this.find("#sq_survey_submit").on('click', function () {
            $this.find('#sq_survey_error').html('<p class="sq_minloading" style="margin:0 auto; padding:2px;"></p>');
            $this.find('#sq_survey_submit').attr("disabled", "disabled");

            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_support',
                    sq_support_email: $this.find("input[name=sq_support_email]").val(),
                    subject: 'Survey message',
                    message: $this.find("textarea[name=sq_survey_message]").val(),
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                $this.find('#sq_survey_submit').removeAttr("disabled");
                $this.find("textarea[name=sq_survey_message]").val('');

                if (typeof response.message !== 'undefined') {
                    $this.find('#sq_survey_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                } else
                    $this.find('#sq_survey_error').removeClass('sq_error').html('');

                if (typeof response.success !== 'undefined') {
                    $.post(
                        sqQuery.ajaxurl,
                        {
                            action: 'sq_hide_survey',
                            sq_hide_survey: '1',
                            nonce: sqQuery.nonce
                        }
                    );
                }

            }).fail(function (response) {
                if (response.status === 200 && response.responseText.indexOf('{') > 0) {
                    response.responseText = response.responseText.substr(response.responseText.indexOf('{'), response.responseText.lastIndexOf('}'));
                    try {
                        response = $.parseJSON(response.responseText);
                        $this.find('#sq_survey_submit').removeAttr("disabled");
                        $this.find("textarea[name=sq_survey_message]").val('');

                        if (typeof response.message !== 'undefined') {
                            $this.find('#sq_survey_error').removeClass('sq_error').addClass('sq_message').html(response.message);
                        } else
                            $this.find('#sq_survey_error').removeClass('sq_error').html('');
                    } catch (e) {
                    }

                } else {
                    $this.find('#sq_survey_submit').removeAttr("disabled");
                    $this.find('#sq_survey_submit').val('Send feedback');
                    $this.find('#sq_survey_submit').removeClass('sq_minloading');
                    $this.find('#sq_survey_error').addClass('sq_error').removeClass('sq_message').html('Could not send your message');
                }
            }, 'json');
        });

        $("#sq_survey_close").on('click', function () {
            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_hide_survey',
                    sq_hide_survey: '1',
                    nonce: sqQuery.nonce
                }
            ).done(function () {
                $this.find('#sq_survey').hide();
            }, 'json');
        });

    };

    if ($('#sq_options').length > 0) {
        $('#sq_options').sq_blocksupport();
    } else {
        $(document).ready(function () {
            $('#sq_options').sq_blocksupport();

            //For Customer Support Page
            $('#sq_customerservice').sq_blocksupport();
        });
    }


})(jQuery);