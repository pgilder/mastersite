<?php
if (SQ_Classes_Tools::getOption('sq_api') == '') {
    ?>
    <div id="sq_settings">
        <span class="sq_icon"></span>

        <div id="sq_settings_title"><?php _e('Connect to Squirrly Data Cloud', _SQ_PLUGIN_NAME_); ?> </div>
        <div id="sq_settings_login">
            <?php SQ_Classes_ObjController::getClass('SQ_Core_Blocklogin')->init(); ?>
        </div>


        <input id="sq_goto_dashboard" class="sq_goto_dashboard" style="display:none;  margin: 0 auto; width: 500px; padding: 0px 10px;" type="button" value="&laquo;<?php _e('START HERE', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
    </div>
    <?php
} else {
    $socials = json_decode(json_encode(SQ_Classes_Tools::getOption('socials')));
    $codes = json_decode(json_encode(SQ_Classes_Tools::getOption('codes')));
    ?>
    <script>
        jQuery.sq_patterns_list = jQuery.parseJSON("<?php echo addslashes(SQ_ALL_PATTERNS) ?>");
    </script>
    <div id="sq_settings">
        <?php SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init(); ?>
        <div>
            <span class="sq_icon"></span>
            <div id="sq_settings_title"><?php _e('SEO', _SQ_PLUGIN_NAME_); ?> </div>
            <div id="sq_settings_title">
                <input type="submit" name="sq_update" value="<?php _e('Save SEO', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
                <?php if (!SQ_Classes_Tools::getOption('ignore_warn')) { ?>
                    <div class="sq_checkissues"><?php _e('Check for SEO issues in your site', _SQ_PLUGIN_NAME_); ?></div>
                <?php } ?>
            </div>
        </div>

        <?php SQ_Classes_ObjController::getClass('SQ_Core_BlockToolbar')->init(); ?>

        <?php $menu = SQ_Classes_Tools::getOption('menu');
        if ($menu['show_tutorial']) { ?>
            <div id="sq_helpsettingsseocontent" class="sq_helpcontent"></div>
            <div id="sq_helpsettingsseoside" class="sq_helpside"></div>
        <?php } ?>

        <div id="sq_left">
            <form id="sq_settings_form" name="settings" action="" method="post" enctype="multipart/form-data">
                <div id="sq_settings_body">
                    <fieldset class="sq_seo">
                        <legend>
                            <span class="sq_legend_title"><?php _e('Let Squirrly SEO Optimize This Blog', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo sprintf(__('%sIs Squirrly SEO better than WordPress SEO by Yoast?%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/why_is_squirrly_seo_better_then_wordpress_seo_by_yoast-pagblog-article_id61980-html" target="_blank">', '</a>'); ?></span>

                            <span><?php _e('Activate the built-in SEO settings from Squirrly by switching Yes below. <strong>Works well with Multisites and Ecommerce.</strong>', _SQ_PLUGIN_NAME_); ?></span><br/>
                            <div class="sq_option_content">
                                <div class="sq_switch">
                                    <input id="sq_use_on" type="radio" class="sq_switch-input" name="sq_use" value="1" <?php echo((SQ_Classes_Tools::getOption('sq_use') == 1) ? "checked" : '') ?> />
                                    <label for="sq_use_on" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_use_off" type="radio" class="sq_switch-input" name="sq_use" value="0" <?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? "checked" : '') ?> />
                                    <label for="sq_use_off" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                            </div>
                            <div class="sq_badge_google">
                                <div class="sq_badge-image"></div>
                                <div class="sq_description">
                                    <div class="sq_title">
                                        <?php _e('New SEO Settings For Google 2017', _SQ_PLUGIN_NAME_); ?>
                                    </div>
                                    <div class="sq_link">
                                        <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                    </div>
                                </div>
                            </div>

                            <div class="sq_badge_google">
                                <div class="sq_badge-image"></div>
                                <div class="sq_description">
                                    <div class="sq_title">
                                        <?php _e('Fastest SEO Plugin in 2017', _SQ_PLUGIN_NAME_); ?>
                                    </div>
                                    <div class="sq_link">
                                        <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                    </div>
                                </div>
                            </div>

                        </legend>
                        <div>
                            <ul id="sq_settings_sq_use" class="sq_settings_info">
                                <span><?php _e('What does Squirrly automatically do for SEO?', _SQ_PLUGIN_NAME_); ?></span>
                                <li>
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_canonical')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_canonical1" type="radio" class="sq_switch-input" name="sq_auto_canonical" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_canonical1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_canonical0" type="radio" class="sq_switch-input" name="sq_auto_canonical" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_canonical0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('adds <strong>%scanonical link%s</strong>, <strong>%srel="prev" and rel="next"%s</strong> metas in Header', _SQ_PLUGIN_NAME_), '<a href="https://support.google.com/webmasters/answer/139066" target="_blank">', '</a>', '<a href="https://support.google.com/webmasters/answer/1663744" target="_blank">', '</a>'); ?></span>
                                    </div>
                                </li>

                                <li>
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_meta')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_meta1" type="radio" class="sq_switch-input" name="sq_auto_meta" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_meta1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_meta0" type="radio" class="sq_switch-input" name="sq_auto_meta" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_meta0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php _e('adds the required METAs (<strong>Dublin Core, Language</strong>, etc.)', _SQ_PLUGIN_NAME_); ?></span>
                                    </div>
                                </li>
                                <li>
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_sitemap')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_sitemap1" type="radio" class="sq_switch-input" name="sq_auto_sitemap" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_sitemap1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_sitemap0" type="radio" class="sq_switch-input" name="sq_auto_sitemap" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_sitemap0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('adds the <strong>%sXML Sitemap%s</strong> for search engines: %s', _SQ_PLUGIN_NAME_), '<a href="https://support.google.com/webmasters/answer/156184?rd=1" target="_blank">', '</a>', '<strong><a href="' . SQ_Classes_ObjController::getClass('SQ_Controllers_Sitemaps')->getXmlUrl('sitemap') . '" target="_blank">' . SQ_Classes_ObjController::getClass('SQ_Controllers_Sitemaps')->getXmlUrl('sitemap') . '</a></strong>'); ?></span>
                                    </div>
                                </li>
                                <li>
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_feed')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_feed1" type="radio" class="sq_switch-input" name="sq_auto_feed" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_feed1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_feed0" type="radio" class="sq_switch-input" name="sq_auto_feed" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_feed0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('adds  <strong>Feed style</strong> to your blog feed (eg. %s/feed)', _SQ_PLUGIN_NAME_), home_url()) ?></span>
                                    </div>
                                </li>
                                <li>
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_favicon')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_favicon1" type="radio" class="sq_switch-input" name="sq_auto_favicon" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_favicon1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_favicon0" type="radio" class="sq_switch-input" name="sq_auto_favicon" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_favicon0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('adds the <strong>%sfavicon.ico%s</strong> and the <strong>%sicons for tablets and smartphones%s</strong>', _SQ_PLUGIN_NAME_), '<a href="https://en.wikipedia.org/wiki/Favicon" target="_blank">', '</a>', '<a href="https://developer.apple.com/library/safari/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html" target="_blank">', '</a>'); ?></span>
                                    </div>
                                </li>
                                <li>
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_jsonld')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_jsonld1" type="radio" class="sq_switch-input" name="sq_auto_jsonld" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_jsonld1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_jsonld0" type="radio" class="sq_switch-input" name="sq_auto_jsonld" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_jsonld0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('adds the <strong>%sJson-LD%s</strong> metas for Semantic SEO', _SQ_PLUGIN_NAME_), '<a href="https://en.wikipedia.org/wiki/JSON-LD" target="_blank">', '</a>'); ?></span>
                                    </div>
                                </li>
                                <li>
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_noindex')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_noindex1" type="radio" class="sq_switch-input" name="sq_auto_noindex" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_noindex1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_noindex0" type="radio" class="sq_switch-input" name="sq_auto_noindex" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_noindex0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('adds the <strong>%sNoindex%s</strong>, <strong>%sNofollow%s</strong> metas for your desired pages', _SQ_PLUGIN_NAME_), '<a href="https://support.google.com/webmasters/answer/93710?hl=en" target="_blank">', '</a>', '<a href="https://support.google.com/webmasters/answer/96569?hl=en" target="_blank">', '</a>'); ?></span>
                                    </div>
                                </li>

                                <p class="sq_option_info" style="padding-left:10px; color: darkgrey;"> <?php _e('Note! By switching the  <strong>Json-LD</strong>, <strong>XML Sitemap</strong> and <strong>Favicon</strong> on, you open new options below', _SQ_PLUGIN_NAME_); ?></p>
                            </ul>
                            <div style="text-align: center;">
                                <div class="sq_checkissues"><?php _e('Check for SEO issues in your site', _SQ_PLUGIN_NAME_); ?></div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset id="sq_title_description_keywords" class="sq_seo <?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'deactivated' : ''); ?>">
                        <legend>
                            <span class="sq_legend_title"><?php _e('First Page Optimization', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo sprintf(__('%sThe best SEO approach to Meta information%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/the-best-seo-approach-to-meta-information" target="_blank">', '</a>'); ?></span>
                            <span><?php _e('Optimize the <strong>Titles</strong>', _SQ_PLUGIN_NAME_); ?></span>
                            <?php
                            $auto_option = false;
                            if (SQ_Classes_Tools::getOption('sq_auto_title')) $auto_option = true;
                            ?>
                            <div class="sq_option_content sq_option_content">
                                <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                    <input id="sq_auto_title1" type="radio" class="sq_switch-input" name="sq_auto_title" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                    <label for="sq_auto_title1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_auto_title0" type="radio" class="sq_switch-input" name="sq_auto_title" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                    <label for="sq_auto_title0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                            </div>

                            <span><?php echo sprintf(__('Optimize %sDescriptions%s ', _SQ_PLUGIN_NAME_), '<strong>', '</strong>'); ?></span>

                            <?php
                            $auto_option = false;
                            if (SQ_Classes_Tools::getOption('sq_auto_description')) $auto_option = true;
                            ?>
                            <div class="sq_option_content sq_option_content">
                                <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                    <input id="sq_auto_description1" type="radio" class="sq_switch-input" name="sq_auto_description" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                    <label for="sq_auto_description1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_auto_description0" type="radio" class="sq_switch-input" name="sq_auto_description" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                    <label for="sq_auto_description0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                            </div>

                            <span><?php echo sprintf(__('Optimize %sKeywords%s ', _SQ_PLUGIN_NAME_), '<strong>', '</strong>'); ?></span>

                            <?php
                            $auto_option = false;
                            if (SQ_Classes_Tools::getOption('sq_auto_keywords')) $auto_option = true;
                            ?>
                            <div class="sq_option_content sq_option_content">
                                <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                    <input id="sq_auto_keywords1" type="radio" class="sq_switch-input" name="sq_auto_keywords" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                    <label for="sq_auto_keywords1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_auto_keywords0" type="radio" class="sq_switch-input" name="sq_auto_keywords" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                    <label for="sq_auto_keywords0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                            </div>

                            <span class="withborder"></span>


                            <div class="sq_badge_google">
                                <div class="sq_badge-image"></div>
                                <div class="sq_description">
                                    <div class="sq_title">
                                        <?php _e('Squirrly Snippet G17-True Render', _SQ_PLUGIN_NAME_); ?>
                                    </div>
                                    <div class="sq_link">
                                        <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                    </div>
                                </div>
                            </div>

                        </legend>

                        <div style="min-height: 650px;">
                            <?php
                            if ($pageId = get_option('page_on_front')) {
                                $sq_hash = md5($pageId);
                            } elseif ($post_id = get_option('page_for_posts')) {
                                $sq_hash = md5($pageId);
                            } else {
                                $sq_hash = md5('wp_homepage');
                            }

                            $sq = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getSqSeo($sq_hash);
                            $patterns = SQ_Classes_Tools::getOption('patterns');
                            ?>
                            <ul id="sq_settings_sq_use" class="sq_settings_info">
                                <span><?php _e('First Page Optimization:', _SQ_PLUGIN_NAME_); ?></span>
                                <li>
                                    <div id="sq_customize_settings">
                                        <p class="withborder">
                                            <span class="sq_pattern_field">
                                                <span style="width: 65px;display: inline-block; vertical-align: top;">
                                                    <?php _e('Title:', _SQ_PLUGIN_NAME_); ?>
                                                </span>
                                                <input type="text" name="sq_fp_title" value="<?php echo(($sq->title <> '') ? $sq->title : '') ?>" size="75" placeholder="<?php echo $patterns['home']['title'] ?>"/>
                                            </span>
                                            <span id="sq_title_info"/>
                                            <span id="sq_fp_title_length"><?php _e('Tips: Length 10-75 chars', _SQ_PLUGIN_NAME_); ?></span>
                                        </p>
                                        <p class="withborder">
                                            <span class="sq_pattern_field">
                                                <span class="sq_pattern_field" style="width: 65px;display: inline-block; vertical-align: top;"><?php _e('Description:', _SQ_PLUGIN_NAME_); ?></span>
                                                <textarea name="sq_fp_description" cols="70" rows="5" placeholder="<?php echo $patterns['home']['description'] ?>"><?php echo(($sq->description <> '') ? $sq->description : '') ?></textarea>
                                            </span>
                                            <span id="sq_description_info"/>
                                            <span id="sq_fp_description_length"><?php _e('Tips: Length 70-320 chars', _SQ_PLUGIN_NAME_); ?></span>
                                        </p>
                                        <p class="withborder">
                                            <span style="width: 65px;display: inline-block; vertical-align: top;"><?php _e('Keywords:', _SQ_PLUGIN_NAME_); ?></span><input type="text" name="sq_fp_keywords" value="<?php echo(($sq->keywords <> '') ? $sq->keywords : '') ?>" size="70"/>
                                            <span id="sq_fp_keywords_length"><?php _e('Tips: use 2-4 keywords', _SQ_PLUGIN_NAME_); ?></span>
                                        </p>
                                        <p class="withborder sq_select_ogimage" <?php echo((SQ_Classes_Tools::getOption('sq_auto_facebook')) ? '' : 'style="display:none"') ?>>
                                            <span style="width: 65px;display: inline-block; vertical-align: top;"><?php _e('OG Image:', _SQ_PLUGIN_NAME_); ?></span>
                                            <strong><input type="text" name="sq_fp_ogimage" value="<?php echo(($sq->og_media <> '') ? $sq->og_media : '') ?>" size="60" style="display:none;"/><input id="sq_fp_imageselect" type="button" class="sq_button" value="<?php echo __('Select Open Graph Image', _SQ_PLUGIN_NAME_) ?>"/></strong>
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <span class="sq_option_info"><?php _e('First Page Preview (Title, Description, Keywords)', _SQ_PLUGIN_NAME_); ?></span>
                                    <div id="sq_snippet">
                                        <div id="sq_snippet_name"><?php _e('Squirrly Snippet', _SQ_PLUGIN_NAME_) ?></div>

                                        <ul id="sq_snippet_ul">
                                            <div class="sq_select_ogimage_preview" <?php echo((SQ_Classes_Tools::getOption('sq_auto_facebook')) ? '' : 'style="display:none"') ?>>
                                                <div class="sq_fp_ogimage_close" <?php echo(($sq->og_media <> '') ? '' : 'style="display:none;"') ?>>x</div>
                                                <div class="sq_fp_ogimage"><?php echo(($sq->og_media <> '') ? '<img src="' . $sq->og_media . '" />' : '') ?></div>
                                            </div>
                                            <li id="sq_snippet_title"></li>
                                            <li id="sq_snippet_url"></li>
                                            <li id="sq_snippet_description"></li>
                                        </ul>

                                        <div id="sq_snippet_disclaimer"><?php _e("If you don't see any changes in your Google snippet, check if other SEO themes or plugins affect Squirrly.", _SQ_PLUGIN_NAME_) ?></div>
                                    </div>
                                </li>
                                <li>
                                    <span class="sq_option_info"><?php echo sprintf(__('Use the %s<strong>Squirrly Snippet Tool</strong>%s while editing a Post/Page to customize the Title and the Description.', _SQ_PLUGIN_NAME_), '<a href="http://howto.squirrly.co/sides/squirrly-snippet-tool/" target="_blank" >', '</a>'); ?></span>

                                </li>
                            </ul>
                        </div>
                    </fieldset>
                    <fieldset id="sq_seo_allpages" class="sq_seo <?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'deactivated' : ''); ?>">
                        <legend>
                            <span class="sq_legend_title"><?php _e('SEO for all post/pages', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo sprintf(__('To customize the Title and Description for all the Posts and Pages in your site use the %sSquirrly Snippet Tool%s', _SQ_PLUGIN_NAME_), '<a href="http://howto.squirrly.co/sides/squirrly-snippet-tool/" target="_blank" >', '</a>'); ?></span>

                            <span><?php _e('Add the Post tags in <strong>Keyword META</strong>.', _SQ_PLUGIN_NAME_); ?></span>

                            <?php
                            $auto_option = false;
                            if (SQ_Classes_Tools::getOption('sq_keywordtag')) $auto_option = true;
                            ?>
                            <div class="sq_option_content">
                                <div class="sq_switch">
                                    <input id="sq_keywordtag1" type="radio" class="sq_switch-input" name="sq_keywordtag" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                    <label for="sq_keywordtag1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_keywordtag0" type="radio" class="sq_switch-input" name="sq_keywordtag" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                    <label for="sq_keywordtag0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                            </div>
                            <span class="withborder"></span>
                        </legend>

                        <div style="min-height: 320px;">
                            <?php
                            ?>

                            <p style="font-weight: bold;"><?php _e('Exclude Squirrly from loading in these Post Types', _SQ_PLUGIN_NAME_); ?>:</p>
                            <div style="color: red; margin: 10px 0;"><?php _e("DO NOT CHECK any Post Type if you want Squirrly to load for all posts types. ", _SQ_PLUGIN_NAME_) ?></div>
                            <ul id="sq_post_types">
                                <li>
                                    <input type="checkbox" class="sq_post_types" name="sq_post_types[]" value="post" <?php echo(in_array('post', SQ_Classes_Tools::getOption('sq_exclude_post_types')) ? 'checked="checked"' : ''); ?>><?php _e('Posts', _SQ_PLUGIN_NAME_); ?>
                                </li>
                                <li>
                                    <input type="checkbox" class="sq_post_types" name="sq_post_types[]" value="page" <?php echo(in_array('page', SQ_Classes_Tools::getOption('sq_exclude_post_types')) ? 'checked="checked"' : ''); ?>><?php _e('Pages', _SQ_PLUGIN_NAME_); ?>
                                </li>
                                <?php if (in_array('product', get_post_types())) { //check for ecommerce product ?>
                                    <li>
                                        <input type="checkbox" class="sq_post_types" name="sq_post_types[]" value="product" <?php echo(in_array('product', SQ_Classes_Tools::getOption('sq_exclude_post_types')) ? 'checked="checked"' : ''); ?>><?php _e('Products', _SQ_PLUGIN_NAME_); ?>
                                    </li>
                                <?php } ?>
                                <?php
                                $types = get_post_types();
                                foreach (array('post', 'page', 'revision', 'nav_menu_item', 'shop_order', 'shop_coupon') as $exclude) {
                                    if (in_array($exclude, $types)) {
                                        unset($types[$exclude]);
                                    }
                                }
                                foreach ($types as $type) {
                                    $type_data = get_post_type_object($type);
                                    ?>
                                    <li>
                                        <input type="checkbox" class="sq_post_types" name="sq_post_types[]" value="<?php echo $type ?>" <?php echo(in_array($type, SQ_Classes_Tools::getOption('sq_exclude_post_types')) ? 'checked="checked"' : ''); ?>><?php echo $type_data->labels->name; ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>

                            <br/>
                            <br/>

                            <div class="sq_option_content withborder">
                                <div class="sq_switch">
                                    <input id="sq_use_frontend1" type="radio" class="sq_switch-input" name="sq_use_frontend" value="1" <?php echo((SQ_Classes_Tools::getOption('sq_use_frontend')) ? "checked" : '') ?> />
                                    <label for="sq_use_frontend1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_use_frontend0" type="radio" class="sq_switch-input" name="sq_use_frontend" value="0" <?php echo((!SQ_Classes_Tools::getOption('sq_use_frontend')) ? "checked" : '') ?> />
                                    <label for="sq_use_frontend0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                                <span><?php _e('Load <strong>Squirrly SEO Snippet in Frontend</strong> too.', _SQ_PLUGIN_NAME_); ?></span>
                            </div>

                        </div>
                    </fieldset>

                    <fieldset id="sq_social_media" class="sq_socials <?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'deactivated' : ''); ?>">
                        <legend>
                            <span class="sq_legend_title"><?php _e('Social Media Options', _SQ_PLUGIN_NAME_); ?></span>
                            <p>
                                <span><?php _e('Select the language you\'re using on Social Media', _SQ_PLUGIN_NAME_); ?></span>
                            </p>
                            <div class="abh_select withborder">
                                <select id="sq_og_locale" name="sq_og_locale">
                                    <option value="en_US">English (US)</option>
                                    <option value="af_ZA">Afrikaans</option>
                                    <option value="ak_GH">Akan</option>
                                    <option value="am_ET">Amharic</option>
                                    <option value="ar_AR">Arabic</option>
                                    <option value="as_IN">Assamese</option>
                                    <option value="ay_BO">Aymara</option>
                                    <option value="az_AZ">Azerbaijani</option>
                                    <option value="be_BY">Belarusian</option>
                                    <option value="bg_BG">Bulgarian</option>
                                    <option value="bn_IN">Bengali</option>
                                    <option value="br_FR">Breton</option>
                                    <option value="bs_BA">Bosnian</option>
                                    <option value="ca_ES">Catalan</option>
                                    <option value="cb_IQ">Sorani Kurdish</option>
                                    <option value="ck_US">Cherokee</option>
                                    <option value="co_FR">Corsican</option>
                                    <option value="cs_CZ">Czech</option>
                                    <option value="cx_PH">Cebuano</option>
                                    <option value="cy_GB">Welsh</option>
                                    <option value="da_DK">Danish</option>
                                    <option value="de_DE">German</option>
                                    <option value="el_GR">Greek</option>
                                    <option value="en_GB">English (UK)</option>
                                    <option value="en_IN">English (India)</option>
                                    <option value="en_PI">English (Pirate)</option>
                                    <option value="en_UD">English (Upside Down)</option>
                                    <option value="eo_EO">Esperanto</option>
                                    <option value="es_CL">Spanish (Chile)</option>
                                    <option value="es_CO">Spanish (Colombia)</option>
                                    <option value="es_ES">Spanish (Spain)</option>
                                    <option value="es_LA">Spanish</option>
                                    <option value="es_MX">Spanish (Mexico)</option>
                                    <option value="es_VE">Spanish (Venezuela)</option>
                                    <option value="et_EE">Estonian</option>
                                    <option value="eu_ES">Basque</option>
                                    <option value="fa_IR">Persian</option>
                                    <option value="fb_LT">Leet Speak</option>
                                    <option value="ff_NG">Fulah</option>
                                    <option value="fi_FI">Finnish</option>
                                    <option value="fo_FO">Faroese</option>
                                    <option value="fr_CA">French (Canada)</option>
                                    <option value="fr_FR">French (France)</option>
                                    <option value="fy_NL">Frisian</option>
                                    <option value="ga_IE">Irish</option>
                                    <option value="gl_ES">Galician</option>
                                    <option value="gn_PY">Guarani</option>
                                    <option value="gu_IN">Gujarati</option>
                                    <option value="gx_GR">Classical Greek</option>
                                    <option value="ha_NG">Hausa</option>
                                    <option value="he_IL">Hebrew</option>
                                    <option value="hi_IN">Hindi</option>
                                    <option value="hr_HR">Croatian</option>
                                    <option value="hu_HU">Hungarian</option>
                                    <option value="hy_AM">Armenian</option>
                                    <option value="id_ID">Indonesian</option>
                                    <option value="ig_NG">Igbo</option>
                                    <option value="is_IS">Icelandic</option>
                                    <option value="it_IT">Italian</option>
                                    <option value="ja_JP">Japanese</option>
                                    <option value="ja_KS">Japanese (Kansai)</option>
                                    <option value="jv_ID">Javanese</option>
                                    <option value="ka_GE">Georgian</option>
                                    <option value="kk_KZ">Kazakh</option>
                                    <option value="km_KH">Khmer</option>
                                    <option value="kn_IN">Kannada</option>
                                    <option value="ko_KR">Korean</option>
                                    <option value="ku_TR">Kurdish (Kurmanji)</option>
                                    <option value="la_VA">Latin</option>
                                    <option value="lg_UG">Ganda</option>
                                    <option value="li_NL">Limburgish</option>
                                    <option value="ln_CD">Lingala</option>
                                    <option value="lo_LA">Lao</option>
                                    <option value="lt_LT">Lithuanian</option>
                                    <option value="lv_LV">Latvian</option>
                                    <option value="mg_MG">Malagasy</option>
                                    <option value="mk_MK">Macedonian</option>
                                    <option value="ml_IN">Malayalam</option>
                                    <option value="mn_MN">Mongolian</option>
                                    <option value="mr_IN">Marathi</option>
                                    <option value="ms_MY">Malay</option>
                                    <option value="mt_MT">Maltese</option>
                                    <option value="my_MM">Burmese</option>
                                    <option value="nb_NO">Norwegian (bokmal)</option>
                                    <option value="nd_ZW">Ndebele</option>
                                    <option value="ne_NP">Nepali</option>
                                    <option value="nl_BE">Dutch (België)</option>
                                    <option value="nl_NL">Dutch</option>
                                    <option value="nn_NO">Norwegian (nynorsk)</option>
                                    <option value="ny_MW">Chewa</option>
                                    <option value="or_IN">Oriya</option>
                                    <option value="pa_IN">Punjabi</option>
                                    <option value="pl_PL">Polish</option>
                                    <option value="ps_AF">Pashto</option>
                                    <option value="pt_BR">Portuguese (Brazil)</option>
                                    <option value="pt_PT">Portuguese (Portugal)</option>
                                    <option value="qu_PE">Quechua</option>
                                    <option value="rm_CH">Romansh</option>
                                    <option value="ro_RO">Romanian</option>
                                    <option value="ru_RU">Russian</option>
                                    <option value="rw_RW">Kinyarwanda</option>
                                    <option value="sa_IN">Sanskrit</option>
                                    <option value="sc_IT">Sardinian</option>
                                    <option value="se_NO">Northern Sámi</option>
                                    <option value="si_LK">Sinhala</option>
                                    <option value="sk_SK">Slovak</option>
                                    <option value="sl_SI">Slovenian</option>
                                    <option value="sn_ZW">Shona</option>
                                    <option value="so_SO">Somali</option>
                                    <option value="sq_AL">Albanian</option>
                                    <option value="sr_RS">Serbian</option>
                                    <option value="sv_SE">Swedish</option>
                                    <option value="sw_KE">Swahili</option>
                                    <option value="sy_SY">Syriac</option>
                                    <option value="sz_PL">Silesian</option>
                                    <option value="ta_IN">Tamil</option>
                                    <option value="te_IN">Telugu</option>
                                    <option value="tg_TJ">Tajik</option>
                                    <option value="th_TH">Thai</option>
                                    <option value="tk_TM">Turkmen</option>
                                    <option value="tl_PH">Filipino</option>
                                    <option value="tl_ST">Klingon</option>
                                    <option value="tr_TR">Turkish</option>
                                    <option value="tt_RU">Tatar</option>
                                    <option value="tz_MA">Tamazight</option>
                                    <option value="uk_UA">Ukrainian</option>
                                    <option value="ur_PK">Urdu</option>
                                    <option value="uz_UZ">Uzbek</option>
                                    <option value="vi_VN">Vietnamese</option>
                                    <option value="wo_SN">Wolof</option>
                                    <option value="xh_ZA">Xhosa</option>
                                    <option value="yi_DE">Yiddish</option>
                                    <option value="yo_NG">Yoruba</option>
                                    <option value="zh_CN">Simplified Chinese (China)</option>
                                    <option value="zh_HK">Traditional Chinese (Hong Kong)</option>
                                    <option value="zh_TW">Traditional Chinese (Taiwan)</option>
                                    <option value="zu_ZA">Zulu</option>
                                    <option value="zz_TR">Zazaki</option>
                                </select>

                            </div>
                            <br/>
                            <span><?php echo sprintf(__('%sHow to pop out in Social Media with your links%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/how-to-pop-out-in-social-media-with-your-links." target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sGet busy with Facebook’s new Search Engine functions%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/get-busy-with-facebooks-new-search-engine-functions" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sHow I Added Twitter Cards in My WordPress for Better Inbound Marketing%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/inbound_marketing_twitter_cards-pagblog-article_id62232.html" target="_blank">', '</a>'); ?></span>

                            <div class="sq_badge_google">
                                <div class="sq_badge-image"></div>
                                <div class="sq_description">
                                    <div class="sq_title">
                                        <?php _e('Open Graph G17 - 2017 Settings', _SQ_PLUGIN_NAME_); ?>
                                    </div>
                                    <div class="sq_link">
                                        <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                    </div>
                                </div>
                            </div>

                        </legend>

                        <div>
                            <ul id="sq_settings_sq_use" class="sq_settings_info">
                                <span><?php _e('Squirrly Adds the Best Codes for Open Graph and Twitter Cards', _SQ_PLUGIN_NAME_); ?></span>
                                <li id="sq_option_facebook">
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_facebook')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_img"></div>
                                    <div class="sq_option_content sq_option_content_small">

                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_facebook1" type="radio" class="sq_switch-input" name="sq_auto_facebook" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_facebook1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_facebook0" type="radio" class="sq_switch-input" name="sq_auto_facebook" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_facebook0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('Add the Social Open Graph protocol so that your Facebook shares look good. %sCheck here%s. ', _SQ_PLUGIN_NAME_), '<a href="https://developers.facebook.com/tools/debug/og/object?q=' . urlencode(get_bloginfo('wpurl')) . '" target="_blank" title="Facebook Object Validator">', '</a>'); ?></span>

                                        <p></p>
                                        <div>
                                            <input type="text" name="sq_fbadminapp" value="<?php echo(($socials->fbadminapp <> '') ? $socials->fbadminapp : '') ?>" style="width: 150px;" placeholder="<?php echo __('Facebook App ID', _SQ_PLUGIN_NAME_) ?>"/>
                                            <span><?php echo sprintf(__('Add the %sFacebook App%s ID ', _SQ_PLUGIN_NAME_), '<a href="https://developers.facebook.com/apps/" target="_blank" title="Create a Facebook App">', '</a>') ?></span>
                                        </div>


                                    </div>


                                </li>

                                <span class="withborder" style="min-height: 0;"></span>
                                <li id="sq_option_twitter">
                                    <?php
                                    $auto_option = false;
                                    if (SQ_Classes_Tools::getOption('sq_auto_twitter')) $auto_option = true;
                                    ?>
                                    <div class="sq_option_img"></div>
                                    <div class="sq_option_content sq_option_content_small">
                                        <span style="color: #f7681a; margin-top: 9px; text-align: center; <?php echo(($socials->twitter_site <> '') ? 'display:none' : '') ?>"><?php echo __('You need to add your <strong>Twitter account</strong> below', _SQ_PLUGIN_NAME_); ?></span>
                                        <br/>

                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_twitter1" type="radio" class="sq_switch-input" name="sq_auto_twitter" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_twitter1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_twitter0" type="radio" class="sq_switch-input" name="sq_auto_twitter" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                            <label for="sq_auto_twitter0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo __('Add the <strong>Twitter card</strong> in your tweets. ', _SQ_PLUGIN_NAME_) . ' <a href="https://cards-dev.twitter.com/validator" target="_blank" title="Twitter Card Validator">Check here</a> to validate your site'; ?></span>
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_auto_twittersize1" type="radio" class="sq_switch-input" name="twitter_card_type" value="summary_large_image" <?php echo(($socials->twitter_card_type <> 'summary') ? "checked" : '') ?> />
                                            <label for="sq_auto_twittersize1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_auto_twittersize0" type="radio" class="sq_switch-input" name="twitter_card_type" value="summary" <?php echo(($socials->twitter_card_type == 'summary') ? "checked" : '') ?> />
                                            <label for="sq_auto_twittersize0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo sprintf(__('Use <strong>Twitter %ssummary_large_image%s</strong> for your Twitter Card. ', _SQ_PLUGIN_NAME_), '<a href="https://dev.twitter.com/cards/types/summary-large-image" target="_blank" title="Twitter Large Summary">', '</a> ') . ''; ?></span>

                                    </div>
                                </li>
                                <span class="withborder" style="min-height: 15px;"></span>

                            </ul>

                        </div>
                    </fieldset>
                    <fieldset id="sq_social_media_accounts" class="sq_socials">
                        <legend>
                            <span class="sq_legend_title"><?php _e('Social Media Accounts', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo sprintf(__('%sLink your Google+ profile to the content you create%s', _SQ_PLUGIN_NAME_), '<a href="https://developers.google.com/structured-data/" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sTwitter account is mandatory for <strong>Twitter Card Validation</strong>%s', _SQ_PLUGIN_NAME_), '<a href="https://cards-dev.twitter.com/validator" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sAdd all your social accounts for <strong>JSON-LD Semantic SEO</strong>%s', _SQ_PLUGIN_NAME_), '<a href="http://howto.squirrly.co/sides/squirrly-json-ld-structured-data/" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sSpecify your social profiles to Google%s', _SQ_PLUGIN_NAME_), '<a href="https://plugin.squirrly.co/wordpress-seo/json-ld-generator/" target="_blank">', '</a>'); ?></span>
                        </legend>

                        <div>
                            <ul id="sq_settings_sq_use" class="sq_settings_info">
                                <li>
                                    <p class="withborder withcode">
                                        <span class="sq_icon sq_icon_twitter"></span>
                                        <?php _e('Your Twitter Account:', _SQ_PLUGIN_NAME_); ?>
                                        <br/><strong><input type="text" class="sq_socials" name="sq_socials[twitter_site]" value="<?php echo((isset($socials->twitter_site)) ? $socials->twitter_site : '') ?>" size="60" placeholder="https://twitter.com/"/> (e.g. https://twitter.com/XXXXXXXXXXXXXXXXXX)</strong>
                                    </p>
                                </li>
                                <li>
                                    <p class="withborder withcode">
                                        <span class="sq_icon sq_icon_facebook"></span>
                                        <?php _e('Facebook Profile:', _SQ_PLUGIN_NAME_); ?>
                                        <br/><strong><input type="text" class="sq_socials" name="sq_socials[facebook_site]" value="<?php echo((isset($socials->facebook_site)) ? $socials->facebook_site : '') ?>" size="60" placeholder="https://www.facebook.com/"/> (e.g. https://www.facebook.com/XXXXXXXXXXXXXXXXXX)</strong>
                                    </p>
                                </li>
                                <li>
                                    <p class="withborder withcode">
                                        <span class="sq_icon sq_icon_linkedin"></span>
                                        <?php _e('Linkedin Profile:', _SQ_PLUGIN_NAME_); ?>
                                        <br/><strong><input type="text" class="sq_socials" name="sq_socials[linkedin_url]" value="<?php echo((isset($socials->linkedin_url)) ? $socials->linkedin_url : '') ?>" size="60" placeholder="https://www.linkedin.com/"/> (e.g. https://www.linkedin.com/XXXX/XXXXXXXXXXXXXXXXXX)</strong>
                                    </p>
                                </li>
                                <li>
                                    <p class="withborder withcode">
                                        <span class="sq_icon sq_icon_social_pinterest"></span>
                                        <?php _e('Pinterest Profile:', _SQ_PLUGIN_NAME_); ?>
                                        <br/><strong><input type="text" class="sq_socials" name="sq_socials[pinterest_url]" value="<?php echo((isset($socials->pinterest_url)) ? $socials->pinterest_url : '') ?>" size="60" placeholder="https://www.pinterest.com/"/> (e.g. https://www.pinterest.com/XXXXXXXXXXXXXXXXXX)</strong>
                                    </p>
                                </li>
                                <li>
                                    <p class="withborder withcode">
                                        <span class="sq_icon sq_icon_social_instagram"></span>
                                        <?php _e('Instagram Profile:', _SQ_PLUGIN_NAME_); ?>
                                        <br/><strong><input type="text" class="sq_socials" name="sq_socials[instagram_url]" value="<?php echo((isset($socials->instagram_url)) ? $socials->instagram_url : '') ?>" size="60" placeholder="https://www.instagram.com/"/> (e.g. https://www.instagram.com/XXXXXXXXXXXXXXXXXX)</strong>
                                    </p>
                                </li>
                                <li>
                                    <p class="withborder withcode">
                                        <span class="sq_icon sq_icon_social_youtube"></span>
                                        <?php _e('Youtube Profile:', _SQ_PLUGIN_NAME_); ?>
                                        <br/><strong><input type="text" class="sq_socials" name="sq_socials[youtube_url]" value="<?php echo((isset($socials->youtube_url)) ? $socials->youtube_url : '') ?>" size="60" placeholder="https://www.youtube.com/"/> (e.g. https://www.youtube.com/channel/XXXXXXXXXXXXXXXXXX)</strong>
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </fieldset>

                    <fieldset id="sq_sitemap" class="sq_sitemap <?php echo((!SQ_Classes_Tools::getOption('sq_use') || !SQ_Classes_Tools::getOption('sq_auto_sitemap')) ? 'deactivated' : ''); ?>">
                        <legend>
                            <span class="sq_legend_title"><?php _e('XML Sitemap for Google', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo __('Squirrly Sitemap is the fastest way to tell Google about the pages on your site. <strong>Supports Multisites, Google News, Images, Videos, Custom Post Types, Custom Taxonomies and Ecommerce products</strong>', _SQ_PLUGIN_NAME_) ?></span>
                            <span><?php echo sprintf(__('%sHow to submit your sitemap.xml in Google Webmaster Tool%s', _SQ_PLUGIN_NAME_), '<a href="http://howto.squirrly.co/wordpress-seo/how-to-submit-your-sitemap-xml-in-google-sitemap/" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%s10 Vital To Dos to Feed Your SEO Content Machine After You Post Articles%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/10_vital_to_dos_to_feed_your_seo_content_machine_after_you_post_articles-pagblog-article_id62194-html" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('For Google News Sitemap, ensure that your site is included in %sGoogle News%s', _SQ_PLUGIN_NAME_), '<a href="https://partnerdash.google.com/partnerdash/d/news" target="_blank">', '</a>'); ?></span>
                        </legend>

                        <div>
                            <?php
                            $auto_option = false;
                            if (SQ_Classes_Tools::getOption('sq_sitemap_ping')) $auto_option = true;
                            ?>
                            <ul id="sq_sitemap_option" class="sq_settings_info">
                                <span><?php _e('XML Sitemap Options', _SQ_PLUGIN_NAME_); ?></span>
                                <div class="sq_option_content sq_option_content_small">
                                    <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                        <input id="sq_sitemap_ping1" type="radio" class="sq_switch-input" name="sq_sitemap_ping" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                                        <label for="sq_sitemap_ping1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                        <input id="sq_sitemap_ping0" type="radio" class="sq_switch-input" name="sq_sitemap_ping" value="0" <?php echo(!$auto_option ? "checked" : '') ?> />
                                        <label for="sq_sitemap_ping0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                        <span class="sq_switch-selection"></span>
                                    </div>
                                    <span><?php echo __('Ping your sitemap to Google and Bing when a new post is published', _SQ_PLUGIN_NAME_); ?></span>
                                </div>
                                <?php if (function_exists('pll_get_post_translations')) { ?>
                                    <div class="sq_option_content sq_option_content_small">
                                        <div class="sq_switch sq_seo_switch_condition" style="<?php echo((!SQ_Classes_Tools::getOption('sq_use')) ? 'display:none;' : ''); ?>">
                                            <input id="sq_sitemap_combinelangs1" type="radio" class="sq_switch-input" name="sq_sitemap_combinelangs" value="1" <?php echo(SQ_Classes_Tools::getOption('sq_sitemap_combinelangs') ? "checked" : '') ?> />
                                            <label for="sq_sitemap_combinelangs1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                            <input id="sq_sitemap_combinelangs0" type="radio" class="sq_switch-input" name="sq_sitemap_combinelangs" value="0" <?php echo(!SQ_Classes_Tools::getOption('sq_sitemap_combinelangs') ? "checked" : '') ?> />
                                            <label for="sq_sitemap_combinelangs0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                            <span class="sq_switch-selection"></span>
                                        </div>
                                        <span><?php echo __('Add all languages in the same sitemap', _SQ_PLUGIN_NAME_); ?></span>
                                    </div>
                                <?php } ?>
                                <li>
                                    <?php
                                    $sitemap = SQ_Classes_Tools::getOption('sq_sitemap');
                                    $sitemapshow = SQ_Classes_Tools::getOption('sq_sitemap_show'); ?>
                                    <p><?php _e('Build Sitemaps for', _SQ_PLUGIN_NAME_); ?>:</p>
                                    <ul id="sq_sitemap_buid">
                                        <li class="sq_selectall"><input type="checkbox" id="sq_selectall"/>Select All
                                        </li>
                                        <li>
                                            <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-home" <?php echo(($sitemap['sitemap-home'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Home Page', _SQ_PLUGIN_NAME_); ?>
                                        </li>

                                        <?php
                                        $count_posts = wp_count_posts();
                                        if (isset($count_posts->publish) && $count_posts->publish > 0) { ?>
                                            <li>
                                                <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-news" <?php echo(($sitemap['sitemap-news'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Google News', _SQ_PLUGIN_NAME_); ?>
                                                <a href="https://partnerdash.google.com/partnerdash/d/news" target="_blank">Read first!</a>
                                            </li>
                                            <li>
                                                <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-post" <?php echo(($sitemap['sitemap-post'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Posts', _SQ_PLUGIN_NAME_); ?>
                                            </li>
                                        <?php } ?>
                                        <li>
                                            <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-attachment" <?php echo(($sitemap['sitemap-attachment'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Attachments', _SQ_PLUGIN_NAME_); ?>
                                        </li>
                                        <?php
                                        $sitemap_category = SQ_Classes_ObjController::getClass('SQ_Models_Sitemaps')->getListTerms('sitemap-category');
                                        if (count($sitemap_category) > 1) { ?>
                                            <li>
                                                <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-category" <?php echo(($sitemap['sitemap-category'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Categories', _SQ_PLUGIN_NAME_); ?>
                                            </li>
                                        <?php } ?>

                                        <?php if (SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->isEcommerce()) { //check for ecommerce product ?>
                                            <li>
                                                <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-product" <?php echo(($sitemap['sitemap-product'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Products', _SQ_PLUGIN_NAME_); ?>
                                            </li>
                                        <?php } ?>

                                        <?php
                                        $sitemap_post_tag = SQ_Classes_ObjController::getClass('SQ_Models_Sitemaps')->getListTerms('sitemap-post_tag');
                                        if (count($sitemap_post_tag) > 1) { ?>
                                            <li>
                                                <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-post_tag" <?php echo(($sitemap['sitemap-post_tag'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Tags', _SQ_PLUGIN_NAME_); ?>
                                            </li>
                                        <?php } ?>
                                        <?php
                                        $count_posts = wp_count_posts('page');
                                        if (isset($count_posts->publish) && $count_posts->publish > 0) { ?>
                                            <li>
                                                <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-page" <?php echo(($sitemap['sitemap-page'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Pages', _SQ_PLUGIN_NAME_); ?>
                                            </li>
                                        <?php } ?>

                                        <li>
                                            <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-archive" <?php echo(($sitemap['sitemap-archive'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Archive', _SQ_PLUGIN_NAME_); ?>
                                        </li>
                                        <li>
                                            <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-custom-tax" <?php echo(($sitemap['sitemap-custom-tax'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Custom Taxonomies', _SQ_PLUGIN_NAME_); ?>
                                        </li>
                                        <li>
                                            <input type="checkbox" class="sq_sitemap" name="sq_sitemap[]" value="sitemap-custom-post" <?php echo(($sitemap['sitemap-custom-post'][1] == 1) ? 'checked="checked"' : ''); ?>><?php _e('Custom Posts', _SQ_PLUGIN_NAME_); ?>
                                        </li>
                                    </ul>
                                    <span style="color: red; margin: 10px; line-height: 20px; display: block;"><?php echo sprintf(__('Select only the Post Types that have links in them. Your sitemap will be %s', _SQ_PLUGIN_NAME_), '<a href="' . SQ_Classes_ObjController::getClass('SQ_Controllers_Sitemaps')->getXmlUrl('sitemap') . '" target="_blank">' . SQ_Classes_ObjController::getClass('SQ_Controllers_Sitemaps')->getXmlUrl('sitemap') . '</a></strong>'); ?></span>

                                </li>
                                <li>
                                    <p><?php _e('Include in Sitemaps', _SQ_PLUGIN_NAME_); ?>:</p>
                                    <ul id="sq_sitemap_include">
                                        <li>
                                            <input type="checkbox" class="sq_sitemap_show" name="sq_sitemap_show[]" value="images" <?php echo(($sitemapshow['images'] == 1) ? 'checked="checked"' : ''); ?>><?php _e('<strong>Images</strong> from posts/pages', _SQ_PLUGIN_NAME_); ?>
                                        </li>
                                        <li>
                                            <input type="checkbox" class="sq_sitemap_show" name="sq_sitemap_show[]" value="videos" <?php echo(($sitemapshow['videos'] == 1) ? 'checked="checked"' : ''); ?>><?php _e('<strong>Videos</strong> (embeded and local media)', _SQ_PLUGIN_NAME_); ?>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <p><?php _e('How often do you update your site?', _SQ_PLUGIN_NAME_); ?></p>
                                    <select name="sq_sitemap_frequency">
                                        <option value="hourly" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_frequency') == 'hourly') ? 'selected="selected"' : ''); ?>><?php _e('every hour', _SQ_PLUGIN_NAME_); ?></option>
                                        <option value="daily" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_frequency') == 'daily') ? 'selected="selected"' : ''); ?>><?php _e('every day', _SQ_PLUGIN_NAME_); ?></option>
                                        <option value="weekly" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_frequency') == 'weekly') ? 'selected="selected"' : ''); ?>><?php _e('1-3 times per week', _SQ_PLUGIN_NAME_); ?></option>
                                        <option value="monthly" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_frequency') == 'monthly') ? 'selected="selected"' : ''); ?>><?php _e('1-3 times per month', _SQ_PLUGIN_NAME_); ?></option>
                                        <option value="yearly" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_frequency') == 'yearly') ? 'selected="selected"' : ''); ?>><?php _e('1-3 times per year', _SQ_PLUGIN_NAME_); ?></option>
                                    </select>
                                </li>
                                <li>
                                    <p><?php _e('Feed Pagination: How many Posts per page to show in sitemap?', _SQ_PLUGIN_NAME_); ?></p>
                                    <select name="sq_sitemap_perpage">
                                        <option value="100" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_perpage') == '100') ? 'selected="selected"' : ''); ?>>100</option>
                                        <option value="500" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_perpage') == '500') ? 'selected="selected"' : ''); ?>>500</option>
                                        <option value="1000" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_perpage') == '1000') ? 'selected="selected"' : ''); ?>>1000</option>
                                        <option value="5000" <?php echo((SQ_Classes_Tools::getOption('sq_sitemap_perpage') == '5000') ? 'selected="selected"' : ''); ?>>5000</option>
                                    </select>
                                </li>

                            </ul>
                        </div>
                    </fieldset>

                    <fieldset id="sq_favicon" class="sq_siteicon <?php echo((!SQ_Classes_Tools::getOption('sq_use') || !SQ_Classes_Tools::getOption('sq_auto_favicon')) ? 'deactivated' : ''); ?>">
                        <legend>
                            <span class="sq_legend_title"><?php _e('Change the Website Icon', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php _e('Now, even tablet & smartphone browsers make use of your icons. This makes having a good favicon even more important.', _SQ_PLUGIN_NAME_); ?> </span>
                            <span><?php echo sprintf(__('You can use %shttp://convertico.com/%s to convert your photo to icon and upload it here after that.', _SQ_PLUGIN_NAME_), '<a href="http://convertico.com/" target="_blank">', '</a>'); ?></span>

                        </legend>
                        <div>
                            <?php echo((defined('SQ_MESSAGE_FAVICON')) ? '<span class="sq_message sq_error" style="display: block; padding: 11px 0;">' . SQ_MESSAGE_FAVICON . '</span>' : '') ?>
                            <p>
                                <?php _e('Upload file:', _SQ_PLUGIN_NAME_); ?><br/><br/>
                                <?php
                                if (SQ_Classes_Tools::getOption('sq_auto_favicon') && SQ_Classes_Tools::getOption('favicon') <> '' && file_exists(_SQ_CACHE_DIR_ . SQ_Classes_Tools::getOption('favicon'))) {
                                    if (!get_option('permalink_structure')) {
                                        $favicon = get_bloginfo('wpurl') . '/index.php?sq_get=favicon';
                                    } else {
                                        $favicon = get_bloginfo('wpurl') . '/favicon.icon' . '?' . time();
                                    }
                                    ?>
                                    <img src="<?php echo $favicon ?>" style="float: left; margin-top: 1px;width: 32px;height: 32px;"/>

                                <?php } ?>
                                <input type="file" name="favicon" id="favicon" style="float: left;"/>
                                <input type="submit" name="sq_update" value="<?php _e('Upload', _SQ_PLUGIN_NAME_) ?>" style="float: left; margin-top: 0;"/>
                                <br/>
                            </p>

                            <span class="sq_settings_info"><?php _e('If you don\'t see the new icon in your browser, empty the browser cache and refresh the page.', _SQ_PLUGIN_NAME_); ?></span>
                            <div>
                                <div style="margin-top: 10px"><?php echo __('File types: JPG, JPEG, GIF and PNG.', _SQ_PLUGIN_NAME_); ?></div>
                                <br/><br/>
                                <span><strong style="color:#f7681a"><?php echo __('Does not physically create the favicon.ico file. The best option for Multisites.', _SQ_PLUGIN_NAME_) ?></strong></span>
                            </div>

                        </div>
                    </fieldset>

                    <fieldset id="sq_jsonld" class="sq_jsonld <?php echo((!SQ_Classes_Tools::getOption('sq_use') || !SQ_Classes_Tools::getOption('sq_auto_jsonld')) ? 'deactivated' : ''); ?>">
                        <legend>
                            <span class="sq_legend_title"><?php _e('JSON-LD for Semantic SEO', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo __('Squirrly will automatically add the JSON-LD Structured Data in your site.', _SQ_PLUGIN_NAME_) ?></span>
                            <span><?php echo sprintf(__('%sJSON-LD\'s Big Day at Google%s', _SQ_PLUGIN_NAME_), '<a href="http://www.seoskeptic.com/json-ld-big-day-at-google/" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sGoogle Testing Tool%s', _SQ_PLUGIN_NAME_), '<a href="https://developers.google.com/structured-data/testing-tool/" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sSpecify your social profiles to Google%s', _SQ_PLUGIN_NAME_), '<a href="https://plugin.squirrly.co/wordpress-seo/json-ld-generator/" target="_blank">', '</a>'); ?></span>

                            <div class="sq_badge_google">
                                <div class="sq_badge-image"></div>
                                <div class="sq_description">
                                    <div class="sq_title">
                                        <?php _e('JSON-LD G17 - 2x More Options', _SQ_PLUGIN_NAME_); ?>
                                    </div>
                                    <div class="sq_link">
                                        <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                    </div>
                                </div>
                            </div>

                        </legend>

                        <div>
                            <?php
                            $jsonld = SQ_Classes_Tools::getOption('sq_jsonld');
                            $jsonldtype = SQ_Classes_Tools::getOption('sq_jsonld_type');
                            ?>
                            <ul id="sq_jsonld_option" class="sq_settings_info">
                                <li class="withborder">
                                    <p style="line-height: 30px;"><?php _e('Your site type:', _SQ_PLUGIN_NAME_); ?>
                                        <select name="sq_jsonld_type" class="sq_jsonld_type">
                                            <option value="Organization" <?php echo(($jsonldtype == 'Organization') ? 'selected="selected"' : ''); ?>><?php _e('Organization', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="Person" <?php echo(($jsonldtype == 'Person') ? 'selected="selected"' : ''); ?>><?php _e('Personal', _SQ_PLUGIN_NAME_); ?></option>
                                        </select>
                                    </p>
                                </li>
                                <li class="withborder">
                                    <p>
                                        <span class="sq_jsonld_types sq_jsonld_Organization" style="display: block;float: left;line-height: 30px; <?php echo(($jsonldtype == 'Person') ? 'display:none' : ''); ?>"><?php _e('Your Organization Name:', _SQ_PLUGIN_NAME_); ?></span>
                                        <span class="sq_jsonld_types sq_jsonld_Person" style="width: 105px;display: block;float: left;line-height: 30px; <?php echo(($jsonldtype == 'Organization') ? 'display:none' : ''); ?>"><?php _e('Your Name:', _SQ_PLUGIN_NAME_); ?></span>
                                        <strong><input type="text" name="sq_jsonld_name" value="<?php echo(($jsonld[$jsonldtype]['name'] <> '') ? $jsonld[$jsonldtype]['name'] : '') ?>" size="60" style="width: 300px;"/></strong>
                                    </p>
                                    <p class="sq_jsonld_types sq_jsonld_Person" <?php echo(($jsonldtype == 'Organization') ? 'style="display:none"' : ''); ?>>
                                        <span style="width: 105px;display: block;float: left;line-height: 30px;"><?php _e('Job Title:', _SQ_PLUGIN_NAME_); ?></span>
                                        <strong><input type="text" name="sq_jsonld_jobTitle" value="<?php echo(($jsonld['Person']['jobTitle'] <> '') ? $jsonld['Person']['jobTitle'] : '') ?>" size="60" style="width: 300px;"/></strong>
                                    </p>
                                    <p>
                                        <span class="sq_jsonld_types sq_jsonld_Organization" style="width: 105px; display: block;float: left; line-height: 30px; <?php echo(($jsonldtype == 'Person') ? 'display:none' : ''); ?>"><?php _e('Logo Url:', _SQ_PLUGIN_NAME_); ?></span>
                                        <span class="sq_jsonld_types sq_jsonld_Person" style="width: 105px;display: block;float: left;line-height: 30px; <?php echo(($jsonldtype == 'Organization') ? 'display:none' : ''); ?>"><?php _e('Image Url:', _SQ_PLUGIN_NAME_); ?></span>
                                        <strong><input type="text" name="sq_jsonld_logo" value="<?php echo(($jsonld[$jsonldtype]['logo'] <> '') ? $jsonld[$jsonldtype]['logo'] : '') ?>" size="60" style="width: 247px;"/><input id="sq_json_imageselect" type="button" class="sq_button" value="<?php echo __('Select Image', _SQ_PLUGIN_NAME_) ?>"/></strong>
                                    </p style="line-height: 30px
                                    ;">
                                    <p>
                                        <span style="width: 105px;display: block;float: left;line-height: 30px;"><?php _e('Contact Phone:', _SQ_PLUGIN_NAME_); ?></span>
                                        <strong><input type="text" name="sq_jsonld_telephone" value="<?php echo(($jsonld[$jsonldtype]['telephone'] <> '') ? $jsonld[$jsonldtype]['telephone'] : '') ?>" size="60" style="width: 350px;"/></strong>
                                    </p>
                                    <p class="sq_jsonld_types sq_jsonld_Organization" <?php echo(($jsonldtype == 'Person') ? 'style="display:none"' : ''); ?>>
                                        <span style="width: 105px;display: block;float: left;line-height: 30px;"><?php _e('Contact Type:', _SQ_PLUGIN_NAME_); ?></span>
                                        <select name="sq_jsonld_contactType" class="sq_jsonld_contactType">
                                            <option value="customer service" <?php echo(($jsonld['Organization']['contactType'] == 'customer service') ? 'selected="selected"' : ''); ?>><?php _e('Customer Service', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="technical support" <?php echo(($jsonld['Organization']['contactType'] == 'technical support') ? 'selected="selected"' : ''); ?>><?php _e('Technical Support', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="billing support" <?php echo(($jsonld['Organization']['contactType'] == 'billing support') ? 'selected="selected"' : ''); ?>><?php _e('Billing Support', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="bill payment" <?php echo(($jsonld['Organization']['contactType'] == 'bill payment') ? 'selected="selected"' : ''); ?>><?php _e('Bill Payment', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="sales" <?php echo(($jsonld['Organization']['contactType'] == 'sales') ? 'selected="selected"' : ''); ?>><?php _e('Sales', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="reservations" <?php echo(($jsonld['Organization']['contactType'] == 'reservations') ? 'selected="selected"' : ''); ?>><?php _e('Reservations', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="credit card support" <?php echo(($jsonld['Organization']['contactType'] == 'credit card support') ? 'selected="selected"' : ''); ?>><?php _e('Credit Card Support', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="emergency" <?php echo(($jsonld['Organization']['contactType'] == 'emergency') ? 'selected="selected"' : ''); ?>><?php _e('Emergency', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="baggage tracking" <?php echo(($jsonld['Organization']['contactType'] == 'baggage tracking') ? 'selected="selected"' : ''); ?>><?php _e('Baggage Tracking', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="roadside assistance" <?php echo(($jsonld['Organization']['contactType'] == 'roadside assistance') ? 'selected="selected"' : ''); ?>><?php _e('Roadside Assistance', _SQ_PLUGIN_NAME_); ?></option>
                                            <option value="package tracking" <?php echo(($jsonld['Organization']['contactType'] == 'package tracking') ? 'selected="selected"' : ''); ?>><?php _e('Package Tracking', _SQ_PLUGIN_NAME_); ?></option>
                                        </select>
                                    </p>

                                    <p>
                                        <span style="width: 105px;display: block;float: left;line-height: 30px;"><?php _e('Short Description:', _SQ_PLUGIN_NAME_); ?></span>
                                        <strong><textarea name="sq_jsonld_description" size="60" style="width: 350px; height: 70px;"/><?php echo(($jsonld[$jsonldtype]['description'] <> '') ? $jsonld[$jsonldtype]['description'] : '') ?></textarea>
                                        </strong>
                                    </p>
                                    <p>
                                        <?php
                                        $checksocials = SQ_Classes_Tools::getOption('socials');
                                        if (!$checksocials['facebook_site'] && !$checksocials['twitter_site'] && !$checksocials['instagram_url'] && !$checksocials['linkedin_url'] && !$checksocials['myspace_url'] && !$checksocials['pinterest_url'] && !$checksocials['youtube_url']) { ?>
                                            <input type="button" class="sq_social_link" style="margin-left:120px;background-color: #15b14a;color: white;padding: 5px; cursor: pointer;" value="<?php _e('Add your social accounts for Json-LD', _SQ_PLUGIN_NAME_) ?>"/>
                                        <?php } ?>
                                    </p>
                                </li>
                                <li style="position: relative; font-size: 14px;color: #f7681a;">
                                    <div class="sq_option_img"></div><?php echo __('How the search results will look like once Google grabs your data.', _SQ_PLUGIN_NAME_) ?>
                                </li>

                            </ul>
                        </div>
                    </fieldset>

                    <fieldset id="sq_tracking" class="sq_tracking">
                        <legend>
                            <span class="sq_legend_title"><?php _e('Tracking Tools', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo sprintf(__('%sHow to Get the Most Out of Google Analytics%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/how-to-get-the-most-out-of-google-analytics/" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sWhat is Facebook Pixel?%s', _SQ_PLUGIN_NAME_), '<a href="https://www.facebook.com/business/help/952192354843755" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sA Beginner’s Guide to Facebook Insights%s', _SQ_PLUGIN_NAME_), '<a href="http://mashable.com/2010/09/03/facebook-insights-guide/" target="_blank">', '</a>'); ?></span>

                            <div class="sq_field_options">
                                <div class="sq_badge_google">
                                    <div class="sq_badge-image"></div>
                                    <div class="sq_description">
                                        <div class="sq_title">
                                            <?php _e('Google Tracking G17', _SQ_PLUGIN_NAME_); ?>
                                        </div>
                                        <div class="sq_link">
                                            <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="sq_badge_google">
                                    <div class="sq_badge-image"></div>
                                    <div class="sq_description">
                                        <div class="sq_title">
                                            <?php _e('Facebook Tracking G17', _SQ_PLUGIN_NAME_); ?>
                                        </div>
                                        <div class="sq_link">
                                            <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="sq_badge_google">
                                    <div class="sq_badge-image"></div>
                                    <div class="sq_description">
                                        <div class="sq_title">
                                            <?php _e('Rich Pins G17', _SQ_PLUGIN_NAME_); ?>
                                        </div>
                                        <div class="sq_link">
                                            <a href="https://howto.squirrly.co/wordpress-seo/what-can-you-tell-us-about-squirrly-seo-2016-vs-squirrly-seo-2017/" target="_blank" title="<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>"> (<?php _e('see how this improved since 2016', _SQ_PLUGIN_NAME_); ?>) </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </legend>

                        <div>

                            <?php
                            if (!empty($socials->fb_admins)) {
                                foreach ($socials->fb_admins as $id => $values) { ?>
                                    <p class="withborder withcode" style="padding-bottom: 21px;">
                                        <span class="sq_icon sq_icon_facebookinsights"></span>
                                        <?php echo sprintf(__('Facebook Admin ID (for %sInsights%s ):', _SQ_PLUGIN_NAME_), '<a href="http://www.facebook.com/insights/" target="_blank">', '</a>'); ?>
                                        <br>
                                        <strong>
                                            <input type="text" class="sq_fb_admins" name="sq_fb_admins[]" value="<?php echo(isset($values->id) ? $values->id : $id) ?>" size="15" placeholder="<?php echo __('Facebook ID or https://www.facebook.com/YourProfileName', _SQ_PLUGIN_NAME_) ?>"/> (e.g. &lt;meta property="fb:admins" content="XXXXXXXXXXXXXXXXXX" /&gt;)
                                        </strong>
                                    </p>
                                <?php }
                            } else { ?>
                                <p class="withborder withcode">
                                    <span class="sq_icon sq_icon_facebookinsights"></span>
                                    <?php echo sprintf(__('Facebook Admin ID (for %sInsights%s ):', _SQ_PLUGIN_NAME_), '<a href="http://www.facebook.com/insights/" target="_blank">', '</a>'); ?>
                                    <br>
                                    <strong>
                                        <input type="text" class="sq_fb_admins" name="sq_fb_admins[]" value="" size="15" placeholder="<?php echo __('Facebook ID or https://www.facebook.com/YourProfileName', _SQ_PLUGIN_NAME_) ?>"/> (e.g. &lt;meta property="fb:admins" content="XXXXXXXXXXXXXXXXXX" /&gt;)
                                    </strong>
                                </p>
                            <?php } ?>
                            <p class="add_facebook_id">
                                <input type="button" value="<?php echo __('Add more Facebook Admin IDs', _SQ_PLUGIN_NAME_) ?>" class="sq_button" style="padding: 3px 20px; cursor: pointer" onclick="jQuery('#sq_facebookadmin').slideDown();"/>
                            </p>
                            <p class="withborder withcode" id="sq_facebookadmin" style="display: none">
                                <span class="facebook_app_close" onclick="jQuery('#sq_facebookadmin').hide();">x</span>
                                <span class="sq_icon sq_icon_facebookinsights"></span>
                                <?php echo sprintf(__('Facebook Admin ID (for %sInsights%s ):', _SQ_PLUGIN_NAME_), '<a href="http://www.facebook.com/insights/" target="_blank">', '</a>'); ?>
                                <br>
                                <strong>
                                    <input type="text" autocomplete="off" class="sq_fb_admins" name="sq_fb_admins[]" value="" size="15" placeholder="<?php echo __('Facebook ID or https://www.facebook.com/YourProfileName', _SQ_PLUGIN_NAME_) ?>"/> (e.g. &lt;meta property="fb:admins" content="XXXXXXXXXXXXXXXXXX" /&gt;)
                                </strong>
                            </p>

                            <p class="withborder withcode">
                                <span class="sq_icon sq_icon_googleanalytics"></span>
                                <?php echo sprintf(__('Google  %sAnalytics ID%s:', _SQ_PLUGIN_NAME_), '<a href="https://analytics.google.com/analytics/web/" target="_blank">', '</a>'); ?>
                                <br><strong><input type="text" class="sq_codes" name="sq_codes[google_analytics]" value="<?php echo((isset($codes->google_analytics)) ? $codes->google_analytics : '') ?>" size="15" placeholder="UA-XXXXXXX-XX"/> (e.g. UA-XXXXXXX-XX)</strong>

                                <span style="display: block; margin-left: 65px">
                                    <?php echo __('Google Tracking Mode:', _SQ_PLUGIN_NAME_); ?>
                                    <select name="sq_analytics_google_js">
                                    <option value="analytics" <?php echo((SQ_Classes_Tools::getOption('sq_analytics_google_js') == 'analytics') ? 'selected="selected"' : ''); ?>><?php _e('analytics.js', _SQ_PLUGIN_NAME_); ?></option>
                                    <option value="gtag" <?php echo((SQ_Classes_Tools::getOption('sq_analytics_google_js') == 'gtag') ? 'selected="selected"' : ''); ?>><?php _e('gtag.js', _SQ_PLUGIN_NAME_); ?></option>
                                    </select>
                                </span>
                            </p>
                            <p class="withborder withcode">
                                <span class="sq_icon sq_icon_facebookpixel"></span>
                                <?php echo sprintf(__('Facebook  %sPixel ID%s:', _SQ_PLUGIN_NAME_), '<a href="https://www.facebook.com/ads/manager/pixel/facebook_pixel/" target="_blank">', '</a>'); ?>
                                <br><strong><input type="text" class="sq_codes" name="sq_codes[facebook_pixel]" value="<?php echo((isset($codes->facebook_pixel)) ? $codes->facebook_pixel : '') ?>" size="15"/> (e.g. 1234567890)</strong>
                            </p>


                            <div class="sq_option_content" style="height: 60px">
                                <div class="sq_switch">
                                    <input id="sq_auto_amp1" type="radio" class="sq_switch-input" name="sq_auto_amp" value="1" <?php echo((SQ_Classes_Tools::getOption('sq_auto_amp')) ? "checked" : '') ?> />
                                    <label for="sq_auto_amp1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_auto_amp0" type="radio" class="sq_switch-input" name="sq_auto_amp" value="0" <?php echo((!SQ_Classes_Tools::getOption('sq_auto_amp')) ? "checked" : '') ?> />
                                    <label for="sq_auto_amp0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                                <span><?php echo sprintf(__('Load <strong>%sGoogle Analytics AMP%s</strong> and <strong>%sFacebook Pixel AMP%s</strong> tracking%s(Warning! The tracking works only for AMP Themes.%s)', _SQ_PLUGIN_NAME_), '<a href="https://developers.google.com/analytics/devguides/collection/amp-analytics/" target="_blank">', '</a>', '<a href="https://www.ampproject.org/docs/reference/components/amp-pixel" target="_blank">', '</a>', '<br /><span style="color: red">', '</span>'); ?></span>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset id="sq_measure_success" class="sq_connections">
                        <legend>
                            <span class="sq_legend_title"><?php _e('Measure Your Success', _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo sprintf(__('%sHow to set the Google Webmaster Tool%s', _SQ_PLUGIN_NAME_), '<a href="http://howto.squirrly.co/wordpress-seo/how-to-set-the-google-webmaster-tool/" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sBest practices to help Google find, crawl, and index your site%s', _SQ_PLUGIN_NAME_), '<a href="https://support.google.com/webmasters/answer/35769?hl=en" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sBing Webmaster Tools Help & How-To Center%s', _SQ_PLUGIN_NAME_), '<a href="http://www.bing.com/webmaster/help/help-center-661b2d18" target="_blank">', '</a>'); ?></span>
                            <span><?php echo sprintf(__('%sRich Pins Validator%s', _SQ_PLUGIN_NAME_), '<a href="https://developers.pinterest.com/tools/url-debugger/" target="_blank">', '</a>'); ?></span>

                        </legend>
                        <div>
                            <p class="withborder withcode">
                                <span class="sq_icon sq_icon_googlewt"></span>
                                <?php echo sprintf(__('Google META verification code for %sWebmaster Tool%s:', _SQ_PLUGIN_NAME_), '<a href="https://www.google.com/webmasters" target="_blank">', '</a>'); ?>
                                <br><strong><input type="text" class="sq_codes" name="sq_codes[google_wt]" value="<?php echo((isset($codes->google_wt)) ? $codes->google_wt : '') ?>" size="15"/> (e.g. &lt;meta name="google-site-verification" content="XXXXXXXXXXXXXXXXXX" /&gt;)</strong>
                            </p>

                            <p class="withborder withcode">
                                <span class="sq_icon sq_icon_bingwt"></span>
                                <?php echo sprintf(__('Bing META code (for %sWebmaster Tool%s ):', _SQ_PLUGIN_NAME_), '<a href="http://www.bing.com/toolbox/webmaster/" target="_blank">', '</a>'); ?>
                                <br><strong>
                                    <input type="text" class="sq_codes" name="sq_codes[bing_wt]" value="<?php echo((isset($codes->bing_wt)) ? $codes->bing_wt : '') ?>" size="15"/> (e.g. &lt;meta name="msvalidate.01" content="XXXXXXXXXXXXXXXXXX" /&gt;)</strong>
                            </p>

                            <p class="withborder withcode">
                                <span class="sq_icon sq_icon_alexat"></span>
                                <?php echo sprintf(__('Alexa META code (for %sAlexa Tool%s ):', _SQ_PLUGIN_NAME_), '<a href="http://www.alexa.com/pro/subscription/signup?tsver=0&puid=200" target="_blank">', '</a>'); ?>
                                <br><strong><input type="text" class="sq_codes" name="sq_codes[alexa_verify]" value="<?php echo((isset($codes->alexa_verify)) ? $codes->alexa_verify : '') ?>" size="15"/> (e.g. &lt;meta name="alexaVerifyID" content="XXXXXXXXXXXXXXXXXX" /&gt;)</strong>
                            </p>

                            <p class="withborder withcode">
                                <span class="sq_icon sq_icon_pinterest"></span>
                                <?php echo sprintf(__('Pinterest Website Validator Code: (validate %sRich Pins%s )', _SQ_PLUGIN_NAME_), '<a href="https://developers.pinterest.com/tools/url-debugger/" target="_blank">', '</a>'); ?>
                                <br><strong>
                                    <input type="text" class="sq_codes" name="sq_codes[pinterest_verify]" value="<?php echo((isset($codes->pinterest_verify)) ? $codes->pinterest_verify : '') ?>" size="15"/> (e.g. &lt;meta name="p:domain_verify" content="XXXXXXXXXXXXXXXXXX" /&gt;)</strong>
                            </p>
                        </div>
                    </fieldset>


                    <div id="sq_settings_submit">
                        <input type="hidden" name="action" value="sq_settingsseo_update"/>
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                        <input type="submit" name="sq_update" value="<?php _e('Save SEO', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
                    </div>


                </div>
            </form>
        </div>
    </div>
    <?php
}