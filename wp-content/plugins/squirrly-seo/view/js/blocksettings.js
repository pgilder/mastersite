(function ($) {

    $.fn.sq_blocksettings = function () {
        var $this = this;

        if (!$.sq_getHash()) {
            $.sq_setHash('advanced');
        }

        $this.find('#sq_selectall').click(function (event) {  //on click
            if (this.checked) { // check select status
                $this.find('#sq_post_type_option input').each(function () { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"
                });
            } else {
                $this.find('#sq_post_type_option input').each(function () { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
        });

    };

    //Submit the settings
    $.fn.sq_submitSettings = function() {
        var $this = this;

        var sq_post_types = [];
        var serialize = $this.find('input[class=sq_post_types]').serializeArray()
        $(serialize).each(function () {
            sq_post_types.push($(this).attr('value'));
        });

        $.post(
            sqQuery.ajaxurl,
            {
                action: 'sq_settings_update',
                sq_post_types: sq_post_types,
                sq_keyword_help: $this.find('input[name=sq_keyword_help]:checked').val(),
                sq_keyword_information: $this.find('input[name=sq_keyword_information]:checked').val(),
                sq_force_savepost: $this.find('input[name=sq_force_savepost]:checked').val(),
// --
                sq_google_country: $this.find('select[name=sq_google_country] option:selected').val(),
                sq_google_country_strict: $this.find('input[name=sq_google_country_strict]:checked').val(),
                sq_google_ranksperhour: $this.find('select[name=sq_google_ranksperhour] option:selected').val(),
                sq_google_serpsperhour: $this.find('select[name=sq_google_serpsperhour] option:selected').val(),
// --
                sq_sla: $this.find('input[name=sq_sla]:checked').val(),
                sq_local_images: $this.find('input[name=sq_local_images]:checked').val(),
                sq_url_fix: $this.find('input[name=sq_url_fix]:checked').val(),
// --

                sq_robots_permission: $this.find('textarea[name=sq_robots_permission]').val(),
                sq_auto_robots: $this.find('input[name=sq_auto_robots]:checked').val(),
                nonce: sqQuery.nonce
            }
        ).done(function () {
            $.sq_showMessage().addClass('sq_success');
        }, 'json');

    };

    if ($('#sq_settings').length > 0) {
        $('#sq_settings').sq_blocksettings();
    } else {
        $(document).ready(function () {
            $('#sq_settings').sq_blocksettings();
        });
    }

})(jQuery);