(function($) {

    $.fn.sq_blocksettingsseo = function() {
        var $this = this;
        ///////////////////////////////
        var snippet_timeout;


        //Custom title/description
        $this.find('#sq_customize').on('click', function () {
            $this.find('#sq_customize_settings').show();
            $this.find('#sq_snippet_disclaimer').show();
            $this.find('#sq_title_description_keywords').addClass('sq_custom_title');

        });

        $this.find('.sq_checkissues').on('click', function () {
            location.href = '?page=sq_seo&action=sq_checkissues&nonce=' + $this.find('#sq_settings_form').find('input[name=nonce]').val();
        });

        //Listen the title field imput for snippet preview
        $this.find('input[name=sq_fp_title]').on('keyup', function () {
            if (snippet_timeout) {
                clearTimeout(snippet_timeout);
            }

            snippet_timeout = setTimeout(function () {
                $this.sq_submitSettings();
                $.sq_getSnippet();
            }, 1000);

            $.sq_trackLength($(this), 'title');
        });

        //Listen the description field imput for snippet preview
        $this.find('textarea[name=sq_fp_description]').on('keyup', function () {
            if (snippet_timeout) {
                clearTimeout(snippet_timeout);
            }

            snippet_timeout = setTimeout(function () {
                $this.sq_submitSettings();
                $.sq_getSnippet();
            }, 1000);

            $.sq_trackLength($(this), 'description');
        });

        $this.find('input[name=sq_fp_keywords]').on('keyup', function () {
            if (snippet_timeout) {
                clearTimeout(snippet_timeout);
            }

            snippet_timeout = setTimeout(function () {
                $this.sq_submitSettings();
            }, 1000);

        });

        //Squirrly On/Off
        $.sq_getSnippet();

        if (!$.sq_getHash()) {
            $.sq_setHash('seo');
        }

        //Listen the favicon switch
        $this.find('#sq_auto_favicon1').on('click', function () {
            $this.find('#sq_favicon').removeClass('deactivated');
        });
        $this.find('#sq_auto_favicon0').on('click', function () {
            $this.find('#sq_favicon').addClass('deactivated');
        });

        //Listen the favicon switch
        $this.find('#sq_auto_sitemap1').on('click', function () {
            $this.find('#sq_sitemap').removeClass('deactivated');
        });
        $this.find('#sq_auto_sitemap0').on('click', function () {
            $this.find('#sq_sitemap').addClass('deactivated');
        });
        $this.find('#sq_auto_jsonld1').on('click', function () {
            $this.find('#sq_jsonld').removeClass('deactivated');
        });
        $this.find('#sq_auto_jsonld0').on('click', function () {
            $this.find('#sq_jsonld').addClass('deactivated');
        });


        //If select all options in sitemap
        $this.find('#sq_selectall').click(function () {  //on click
            if (this.checked) { // check select status
                $this.find('ul#sq_sitemap_buid').find('li').each(function () { //loop through each checkbox
                    $(this).find('input').attr('checked', true);  //select all checkboxes with class "checkbox1"
                });
            } else {
                $this.find('ul#sq_sitemap_buid').find('li').each(function () { //loop through each checkbox
                    $(this).find('input').attr('checked', false); //deselect all checkboxes with class "checkbox1"
                });
            }
        });

        //Listen the Squirrly Auto seo switch ON
        $this.find('#sq_use_on').on('click', function () {
            $this.find('.sq_seo_switch_condition').show();
            $this.find('#sq_title_description_keywords').removeClass('deactivated');
            $this.find('#sq_seo_allpages').removeClass('deactivated');
            $this.find('#sq_social_media').removeClass('deactivated');

            if ($this.find('input[name=sq_auto_sitemap]:checked').val() === 1) {
                $this.find('#sq_sitemap').removeClass('deactivated');
            }

            if ($this.find('input[name=sq_auto_favicon]:checked').val() === 1) {
                $this.find('#sq_favicon').removeClass('deactivated');
            }

            if ($this.find('input[name=sq_auto_jsonld]:checked').val() === 1) {
                $this.find('#sq_jsonld').removeClass('deactivated');
            }

            var $count = $this.find('.sq_count');
            if (parseInt($count.html()) > 0) {
                var notif = (parseInt($count.html()) - 1);
                if (notif > 0) {
                    $count.html(notif);
                } else {
                    $count.html(notif);
                    $count.hide();
                }
            }
            $this.find('#sq_fix_auto').slideUp('fast');


        });
        //Listen the Squirrly Auto seo switch OFF
        $this.find('#sq_use_off').on('click', function () {
            $this.find('.sq_seo_switch_condition').hide();

            $this.find('#sq_title_description_keywords').addClass('deactivated');
            $this.find('#sq_seo_allpages').addClass('deactivated');
            $this.find('#sq_social_media').addClass('deactivated');
            $this.find('#sq_favicon').addClass('deactivated');
            $this.find('#sq_sitemap').addClass('deactivated');
            $this.find('#sq_jsonld').addClass('deactivated');

            var $count = $this.find('.sq_count');
            if (parseInt($count.html()) >= 0) {
                var notif = (parseInt($count.html()) + 1);
                if (notif > 0) {
                    $count.html(notif).show();
                }
            }
            $this.find('#sq_fix_auto').slideDown('show');
        });

        $this.find('#sq_title_description_keywords').on('click', function () {
            if ($this.find('#sq_title_description_keywords').hasClass('deactivated')) {
                $this.find('#sq_use_on').trigger('click');
                $(this).removeClass('deactivated');
            }
        });

        this.find('#sq_seo_allpages').on('click', function () {
            if ($this.find('#sq_seo_allpages').hasClass('deactivated')) {
                $this.find('#sq_use_on').trigger('click');
                $(this).removeClass('deactivated');
            }
        });


        $this.find('#sq_social_media.deactivated').on('click', function () {
            if ($this.find('#sq_social_media').hasClass('deactivated')) {
                $this.find('#sq_use_on').trigger('click');
                $(this).removeClass('deactivated');
            }
        });
        $this.find('#sq_favicon.deactivated').on('click', function () {
            if ($this.find('#sq_favicon').hasClass('deactivated')) {
                $this.find('#sq_use_on').trigger('click');
                $this.find('#sq_auto_favicon1').trigger('click');
                $(this).removeClass('deactivated');
            }
        });
        $this.find('#sq_sitemap.deactivated').on('click', function () {
            if ($this.find('#sq_sitemap').hasClass('deactivated')) {
                $this.find('#sq_use_on').trigger('click');
                $this.find('#sq_auto_sitemap1').trigger('click');
                $(this).removeClass('deactivated');
            }
        });
        $this.find('#sq_jsonld.deactivated').on('click', function () {
            if ($this.find('#sq_jsonld').hasClass('deactivated')) {
                $this.find('#sq_use_on').trigger('click');
                $this.find('#sq_auto_jsonld1').trigger('click');
                $(this).removeClass('deactivated');
            }
        });

        ///////////////////////////////
        ////////////////////FIX ACTIONS
        //FIX Google settings
        $this.find('#sq_google_index1').on('click', function () {
            var $count = $this.find('.sq_count');

            if (parseInt($count.html()) > 0) {
                var notif = (parseInt($count.html()) - 1);
                if (notif > 0) {
                    $count.html(notif);
                } else {
                    $count.html(notif);
                    $count.hide();
                }
            }
            $this.find('#sq_fix_private').slideUp('show');

        });
        $this.find('#sq_google_index0').on('click', function () {
            var $count = $this.find('.sq_count');

            if (parseInt($count.html()) >= 0) {
                var notif = (parseInt($count.html()) + 1);
                if (notif > 0) {
                    $count.html(notif).show();
                }
            }
            $this.find('#sq_fix_private').slideDown('show');
        });

        //JsonLD switch types
        $this.find('.sq_jsonld_type').on('change', function () {
            $this.find('.sq_jsonld_types').hide();
            $this.find('.sq_jsonld_' + $this.find('select[name=sq_jsonld_type] option:selected').val()).show();

        });
        //////////////////////////////////////////

        //Upload image from library
        $this.find('#sq_json_imageselect').on('click', function (event) {
            var frame;

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if (frame) {
                frame.open();
                return;
            }

            // Create a new media frame
            frame = wp.media({
                title: 'Select Or Upload Media You Want Search Engines to Display in Association with Your Brand/Site',
                button: {
                    text: 'Use this media'
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });


            // When an image is selected in the media frame...
            frame.on('select', function () {

                // Get media attachment details from the frame state
                var attachment = frame.state().get('selection').first().toJSON();

                // Send the attachment URL to our custom image input field.
                $this.find('input[name=sq_jsonld_logo]').val(attachment.url);

            });

            // Finally, open the modal on click
            frame.open();
        });

        $this.find('#sq_auto_facebook1').on('click', function () {
            $this.find('p.sq_select_ogimage').slideDown();
            $this.find('div.sq_select_ogimage_preview').slideDown();

        });
        $this.find('#sq_auto_facebook0').on('click', function () {
            $this.find('p.sq_select_ogimage').slideUp();
            $this.find('div.sq_select_ogimage_preview').slideUp();
        });

        $this.find('div.sq_fp_ogimage_close').on('click', function () {
            $this.find('input[name=sq_fp_ogimage]').val('');
            $this.find('div.sq_fp_ogimage').html('');
            $this.find('div.sq_fp_ogimage_close').hide();
        });


        //Upload image from library
        $this.find('#sq_fp_imageselect').on('click', function (event) {
            var frame;

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if (frame) {
                frame.open();
                return;
            }

            // Create a new media frame
            frame = wp.media({
                title: 'Select or Upload Media Of Your Chosen Persuasion',
                button: {
                    text: 'Use this media'
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });


            // When an image is selected in the media frame...
            frame.on('select', function () {

                // Get media attachment details from the frame state
                var attachment = frame.state().get('selection').first().toJSON();

                // Send the attachment URL to our custom image input field.
                $this.find('input[name=sq_fp_ogimage]').val(attachment.url);
                if (attachment.url !== '') {
                    $this.find('div.sq_fp_ogimage').html('<img src="' + attachment.url + '">');
                    $this.find('div.sq_fp_ogimage_close').show();
                } else {
                    $this.find('div.sq_fp_ogimage').html('');
                    $this.find('div.sq_fp_ogimage_close').hide();
                }
            });

            // Finally, open the modal on click
            frame.open();
        });
    }

    //Submit the settings
    $.fn.sq_submitSettings = function() {
        var $this = this;
        var serialize;

        var sq_sitemap = [];
        serialize = $this.find('input[class=sq_sitemap]').serializeArray();
        $(serialize).each(function () {
            sq_sitemap.push($(this).attr('value'));
        });

        var sq_sitemap_show = [];
        serialize = $this.find('input[class=sq_sitemap_show]').serializeArray();
        $(serialize).each(function () {
            sq_sitemap_show.push($(this).attr('value'));
        });

        var sq_socials = [];
        serialize = $this.find('input[class=sq_socials]').serializeArray();
        $(serialize).each(function () {
            sq_socials[$(this).attr('name').replace(/([a-z_]*)\[([a-z_]*)\]/, '$2')] = $(this).attr('value');
        });
        var sq_codes = [];
        serialize = $this.find('input[class=sq_codes]').serializeArray();
        $(serialize).each(function () {
            sq_codes[$(this).attr('name').replace(/([a-z_]*)\[([a-z_]*)\]/, '$2')] = $(this).attr('value');
        });

        var sq_fb_admins = [];
        serialize = $this.find('input[class=sq_fb_admins]').serializeArray();
        $(serialize).each(function () {
            sq_fb_admins.push($(this).attr('value'));
        });

        var sq_post_types = [];
        serialize = $this.find('input[class=sq_post_types]').serializeArray()
        $(serialize).each(function () {
            sq_post_types.push($(this).attr('value'));
        });

        if (typeof sqQuery !== 'undefined') {
            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_settingsseo_update',
// --
                    sq_use: $this.find('input[name=sq_use]:checked').val(),
                    sq_post_types: sq_post_types,
                    sq_auto_title: $this.find('input[name=sq_auto_title]:checked').val(),
                    sq_auto_description: $this.find('input[name=sq_auto_description]:checked').val(),
                    sq_auto_keywords: $this.find('input[name=sq_auto_keywords]:checked').val(),
                    sq_auto_canonical: $this.find('input[name=sq_auto_canonical]:checked').val(),
                    sq_use_frontend: $this.find('input[name=sq_use_frontend]:checked').val(),


                    sq_auto_meta: $this.find('input[name=sq_auto_meta]:checked').val(),
                    sq_auto_noindex: $this.find('input[name=sq_auto_noindex]:checked').val(),
                    sq_auto_favicon: $this.find('input[name=sq_auto_favicon]:checked').val(),
                    sq_auto_facebook: $this.find('input[name=sq_auto_facebook]:checked').val(),
                    sq_auto_twitter: $this.find('input[name=sq_auto_twitter]:checked').val(),
                    twitter_card_type: $this.find('input[name=twitter_card_type]:checked').val(),

                    sq_keywordtag: $this.find('input[name=sq_keywordtag]:checked').val(),

                    sq_og_locale: $this.find('select[name=sq_og_locale] option:selected').val(),
//--
                    sq_socials: Object.assign({}, sq_socials),
//--
                    sq_auto_sitemap: $this.find('input[name=sq_auto_sitemap]:checked').val(),
                    sq_auto_feed: $this.find('input[name=sq_auto_feed]:checked').val(),
                    sq_sitemap: sq_sitemap,
                    sq_sitemap_show: sq_sitemap_show,
                    sq_sitemap_frequency: $this.find('select[name=sq_sitemap_frequency] option:selected').val(),
                    sq_sitemap_combinelangs: $this.find('input[name=sq_sitemap_combinelangs]:checked').val(),
                    sq_sitemap_ping: $this.find('input[name=sq_sitemap_ping]:checked').val(),
                    sq_sitemap_perpage: $this.find('select[name=sq_sitemap_perpage] option:selected').val(),
// --
                    sq_auto_jsonld: $this.find('input[name=sq_auto_jsonld]:checked').val(),
                    sq_jsonld_type: $this.find('select[name=sq_jsonld_type] option:selected').val(),
                    sq_jsonld_name: $this.find('input[name=sq_jsonld_name]').val(),
                    sq_jsonld_jobTitle: $this.find('input[name=sq_jsonld_jobTitle]').val(),
                    sq_jsonld_logo: $this.find('input[name=sq_jsonld_logo]').val(),
                    sq_jsonld_telephone: $this.find('input[name=sq_jsonld_telephone]').val(),
                    sq_jsonld_contactType: $this.find('select[name=sq_jsonld_contactType] option:selected').val(),
                    sq_jsonld_description: $this.find('textarea[name=sq_jsonld_description]').val(),
//--
                    sq_fp_title: $this.find('input[name=sq_fp_title]').val(),
                    sq_fp_description: $this.find('textarea[name=sq_fp_description]').val(),
                    sq_fp_keywords: $this.find('input[name=sq_fp_keywords]').val(),
                    sq_fp_ogimage: $this.find('input[name=sq_fp_ogimage]').val(),
// --
                    ignore_warn: $this.find('input[name=ignore_warn]:checked').val(),
// --
                    sq_codes: Object.assign({}, sq_codes),
                    sq_fb_admins: Object.assign({}, sq_fb_admins),
// --

                    sq_analytics_google_js: $this.find('select[name=sq_analytics_google_js] option:selected').val(),
                    sq_auto_amp: $this.find('input[name=sq_auto_amp]:checked').val(),
                    // --

                    nonce: sqQuery.nonce
                }
            ).done(function () {
                $.sq_showMessage().addClass('sq_success');
                $.sq_getSnippet();
            }, 'json');
        }
    };

    if ($('#sq_settings').length > 0) {
        $('#sq_settings').sq_blocksettingsseo();
    } else {
        $(document).ready(function () {
            $('#sq_settings').sq_blocksettingsseo();
        });
    }
})(jQuery);