<?php if (SQ_Classes_Tools::isFrontAdmin()) {
    if (!wp_script_is('jquery')) {
        wp_enqueue_script('jquery');
        wp_print_scripts(array('jquery'));
    }
    ?>
    <link rel='stylesheet' href='<?php echo _SQ_THEME_URL_ ?>css/frontmenu.css?ver=<?php SQ_VERSION_ID ?>' type='text/css' media='all'/>
    <script type='text/javascript' src='<?php echo _SQ_THEME_URL_ ?>js/frontmenu.js?ver=<?php SQ_VERSION_ID ?>'></script>
    <link rel='stylesheet' href='<?php echo _SQ_THEME_URL_ ?>css/patterns.css?ver=<?php SQ_VERSION_ID ?>' type='text/css' media='all'/>
    <script type='text/javascript' src='<?php echo _SQ_THEME_URL_ ?>js/patterns.js?ver=<?php SQ_VERSION_ID ?>'></script>
<?php } ?>

<?php
//RUn only is frontend admin and ajax call
if (SQ_Classes_Tools::isAjax() || SQ_Classes_Tools::isFrontAdmin()) {
    if (SQ_Classes_Tools::getOption('sq_api') <> '') {
        if (SQ_Classes_Tools::getOption('sq_use')) {
            if (isset($view->post->hash) && $view->post->hash <> '') {
                if (!is_admin() && !is_network_admin() && SQ_Classes_Tools::getOption('sq_import_opt') == 1) {
                    $import = $view->post->importSEO();
                }
                $patterns = SQ_Classes_Tools::getOption('patterns');

                //Clear the Title and Description for admin use only
                $view->post->sq->title = $view->post->sq->getClearedTitle();
                $view->post->sq->description = $view->post->sq->getClearedDescription();

                if ($view->post->ID > 0 && function_exists('get_sample_permalink')) {
                    list($permalink, $post_name) = get_sample_permalink($view->post->ID);
                    if (strpos($permalink, '%postname%') !== false || strpos($permalink, '%pagename%') !== false) {
                        $view->post->url = str_replace(array('%pagename%', '%postname%'), esc_html($post_name), esc_html(urldecode($permalink)));
                    }
                }

                //Set the preview title and description in case Squirrly SEO is switched off for Title and Description
                $preview_title = (SQ_Classes_Tools::getOption('sq_auto_title') ? $view->post->sq->title : $view->post->post_title);
                $preview_description = (SQ_Classes_Tools::getOption('sq_auto_description') ? $view->post->sq->description : $view->post->post_excerpt);
                $preview_keywords = (SQ_Classes_Tools::getOption('sq_auto_keywords') ? $view->post->sq->keywords : '');

                ?>


                <script>
                    jQuery.sq_patterns_list = jQuery.parseJSON("<?php echo addslashes(SQ_ALL_PATTERNS) ?>");
                    var __sq_save_message = "<?php echo __('Saved!', _SQ_PLUGIN_NAME_); ?>";
                    var __sq_save_message_preview = "<?php echo __('Saved! This is how the preview looks like', _SQ_PLUGIN_NAME_); ?>";
                    var __sq_fp_title = "<?php echo addslashes($view->post->sq->title) ?>";
                </script>
                <div id="sq_settings_body">
                    <div class="container">
                        <div id="sq_optimize_page">
                            <?php
                            $excluded = false;
                            $exclude_post_types = SQ_Classes_Tools::getOption('sq_exclude_post_types');
                            if (!empty($exclude_post_types)) {
                                if (isset($view->post->post_type) && $view->post->post_type <> '' && in_array($view->post->post_type, $exclude_post_types)) {
                                    $excluded = true;
                                }
                            }

                            if (!$excluded) {
                                ?>
                                <div id="sq_message"><?php _e('Activate Squirrly SEO for this page', _SQ_PLUGIN_NAME_) ?>:</div>
                            <?php } else { ?>
                                <div id="sq_message" style="color: red"><?php echo sprintf(__('Post Type (%s) was excluded from %sSquirrly > SEO Settings%s. Squirrly SEO will not load for this post type on the frontend', _SQ_PLUGIN_NAME_), '<strong>' . $view->post->post_type . '</strong>', '<a href="' . admin_url('admin.php?page=sq_seo') . '" target="_blank">', '</a>') ?></div>
                            <?php } ?>

                            <div class="sq_option_content" <?php echo(($excluded) ? 'style="display: none"' : '') ?> >
                                <div class="sq_switch-field sq_option_content">
                                    <input id="sq_doseo_on" type="radio" name="sq_doseo" value="1" <?php echo ($view->post->sq_adm->doseo == 1) ? 'checked="checked"' : ''; ?> >
                                    <label for="sq_doseo_on" class="sq_switch-label-on"><?php _e('Yes', _SQ_PLUGIN_NAME_) ?></label>
                                    <input id="sq_doseo_off" type="radio" name="sq_doseo" value="0" <?php echo ($view->post->sq_adm->doseo == 0) ? 'checked="checked"' : ''; ?> >
                                    <label for="sq_doseo_off" class="sq_switch-label-off"><?php _e('No', _SQ_PLUGIN_NAME_) ?></label>
                                </div>
                            </div>
                        </div>
                        <button id="sq_close">x</button>

                        <!-- ================= Tabs ==================== -->
                        <div class="row b-b">
                            <ul id="sq_tabs" class="sq_tab">
                                <li class="one-forth column active">
                                    <a href="#" class="sq_tablinks tab1" data-tab="tab1"><?php _e('META', _SQ_PLUGIN_NAME_) ?></a>
                                </li>
                                <li class="one-forth column">
                                    <a href="#" class="sq_tablinks tab2" data-tab="tab2"><?php _e('FACEBOOK', _SQ_PLUGIN_NAME_) ?></a>
                                </li>
                                <li class="one-forth column">
                                    <a href="#" class="sq_tablinks tab3" data-tab="tab3"><?php _e('TWITTER', _SQ_PLUGIN_NAME_) ?></a>
                                </li>
                                <li class="one-forth column">
                                    <a href="#" class="sq_tablinks tab4" data-tab="tab4"><?php _e('ADVANCED', _SQ_PLUGIN_NAME_) ?></a>
                                </li>
                            </ul>
                        </div>

                        <!-- =================== Optimize ==================== -->


                        <div id="sq_settings_body_content">
                            <div id="sq_tab_meta" class="sq_tabcontent" style="display: block;">
                                <div id="sq_tab_meta_preview" class="sq_tab_preview">
                                    <div class="row b-b m-b-md">
                                        <div class="eight columns">
                                            <div id="sq_message"><?php _e('How this page will appear on Search Engines', _SQ_PLUGIN_NAME_) ?>:</div>

                                        </div>
                                        <div class="four columns right ">
                                            <div id="sq_blocksnippet_refresh"></div>
                                            <input type="button" class="sq_button sq_edit" value="<?php _e('Edit Snippet', _SQ_PLUGIN_NAME_) ?>"/>
                                        </div>
                                    </div>
                                    <?php if ($preview_title <> __('Auto Draft')) { ?>
                                        <div id="sq_snippet">
                                            <ul id="sq_snippet_ul">
                                                <li id="sq_snippet_title" title="<?php echo $preview_title ?>"><?php echo $preview_title ?></li>
                                                <li id="sq_snippet_url" title="<?php echo urldecode($view->post->url) ?>"><?php echo urldecode($view->post->url) ?></li>
                                                <li id="sq_snippet_description" title="<?php echo $preview_description ?>"><?php echo $preview_description ?></li>
                                                <li id="sq_snippet_keywords"><?php echo $preview_keywords ?></li>
                                            </ul>
                                        </div>
                                    <?php }else{ ?>
                                        <div id="sq_snippet" ><div style="padding: 20px"><?php echo __('Please save the post first to be able to edit the Squirrly SEO Snippet',_SQ_PLUGIN_NAME_) ?></div></div>
                                    <?php } ?>
                                </div>
                                <div class="sq_tab_edit">
                                    <div class="row b-b m-b-md ">
                                        <div class="five columns right ">
                                            <input type="button" class="sq_button sq_cancel" value="<?php _e('Cancel', _SQ_PLUGIN_NAME_) ?>"/>
                                            <input type="button" class="sq_button sq_save" value="<?php _e('Save', _SQ_PLUGIN_NAME_) ?>"/>
                                        </div>
                                    </div>

                                    <div class="sq_showhide">
                                        <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_title') ? '' : 'sq_disabled') ?>" data-option="sq_auto_title" data-value="1">
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('SEO Title', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <div class="input-group <?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? 'sq_pattern_field' : '') ?>">
                                                        <input type="text" autocomplete="off" id="sq_title" name="sq_title" class="form-control input-lg sq-toggle" value="<?php echo SQ_Classes_Tools::clearTitle($view->post->sq_adm->title) ?>" placeholder="<?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? __('Pattern: ', _SQ_PLUGIN_NAME_) . $view->post->sq_adm->patterns->title : '') ?>"/>
                                                        <input type="hidden" id="sq_title_preview" name="sq_title_preview" value="<?php echo $view->post->sq->title ?>">
                                                        <div class="sq-actions">
                                                            <div class="sq-action">
                                                                <span style="display: none" class="sq-value sq-title-value"><?php echo $view->post->sq->title ?></span>
                                                                <span class="sq-action-title" title="<?php echo $view->post->sq->title ?>"><?php _e('Current Title', _SQ_PLUGIN_NAME_) ?>: <span class="sq-title-value"><?php echo $view->post->sq->title ?></span></span>
                                                            </div>
                                                            <?php if (isset($view->post->post_title) && $view->post->post_title <> '') { ?>
                                                                <div class="sq-action">
                                                                    <span style="display: none" class="sq-value"><?php echo $view->post->post_title ?></span>
                                                                    <span class="sq-action-title" title="<?php echo $view->post->post_title ?>"><?php _e('Default Title', _SQ_PLUGIN_NAME_) ?>: <span><?php echo $view->post->post_title ?></span></span>
                                                                </div>
                                                            <?php } ?>
                                                            <?php
                                                            if (!empty($import)) {
                                                                foreach ($import as $plugin => $meta) {
                                                                    if (isset($meta->title) && $meta->title <> '') {
                                                                        ?>
                                                                        <div class="sq-action">
                                                                            <span style="display: none" class="sq-value"><?php echo $meta->title ?></span>
                                                                            <span class="sq-action-title" title="<?php echo $meta->title ?>"><?php echo $plugin . " " . __('Title', _SQ_PLUGIN_NAME_) ?>: <span><?php echo $meta->title ?></span></span>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            <?php if ($view->post->sq_adm->patterns->title <> '') { ?>
                                                                <div class="sq-action">
                                                                    <span style="display: none" class="sq-value"><?php echo $view->post->sq_adm->patterns->title ?></span>
                                                                    <span class="sq-action-title" title="<?php echo $view->post->sq_adm->patterns->title ?>"><?php (SQ_Classes_Tools::getOption('sq_auto_pattern') ? _e('Pattern', _SQ_PLUGIN_NAME_) . ': <span>' . $view->post->sq_adm->patterns->title . '</span>' : '') ?></span>
                                                                </div>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="two right columns small">
                                                            <span id="sq_title_length" class="sq_length" data-maxlength="<?php echo $view->post->sq_adm->title_maxlength ?>">0</span>/<span id="sq_title_maxlength" class="sq_maxlength"><?php echo $view->post->sq_adm->title_maxlength ?></span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_description') ? '' : 'sq_disabled') ?>" data-option="sq_auto_description" data-value="1">
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('META Description', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <div class="input-group <?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? 'sq_pattern_field' : '') ?>">
                                                        <textarea style="color: black;" class="form-control sq-toggle" name="sq_description" id="sq_description" placeholder="<?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? __('Pattern: ', _SQ_PLUGIN_NAME_) . $view->post->sq_adm->patterns->description : '') ?>"><?php echo SQ_Classes_Tools::clearDescription($view->post->sq_adm->description) ?></textarea>
                                                        <div class="sq-actions">
                                                            <div class="sq-action">
                                                                <span style="display: none" class="sq-value sq-description-value"><?php echo $view->post->sq->description ?></span>
                                                                <span class="sq-action-title" title="<?php echo $view->post->sq->description ?>"><?php _e('Current Description', _SQ_PLUGIN_NAME_) ?>: <span class="sq-description-value"><?php echo $view->post->sq->description ?></span></span>
                                                            </div>
                                                            <?php if (isset($view->post->post_excerpt) && $view->post->post_excerpt <> '') { ?>
                                                                <div class="sq-action">
                                                                    <span style="display: none" class="sq-value"><?php echo $view->post->post_excerpt ?></span>
                                                                    <span class="sq-action-title" title="<?php echo $view->post->post_excerpt ?>"><?php _e('Default Description', _SQ_PLUGIN_NAME_) ?>: <span><?php echo $view->post->post_excerpt ?></span></span>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (!empty($import)) {
                                                                foreach ($import as $plugin => $meta) {
                                                                    if (isset($meta->description) && $meta->description <> '') {
                                                                        ?>
                                                                        <div class="sq-action">
                                                                            <span style="display: none" class="sq-value"><?php echo $meta->description ?></span>
                                                                            <span class="sq-action-title" title="<?php echo $meta->description ?>"><?php echo $plugin . " " . __('Description', _SQ_PLUGIN_NAME_) ?>: <span><?php echo $meta->description ?></span></span>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                            } ?>
                                                            <?php if ($view->post->sq_adm->patterns->description <> '') { ?>
                                                                <div class="sq-action">
                                                                    <span style="display: none" class="sq-value"><?php echo $view->post->sq_adm->patterns->description ?></span>
                                                                    <span class="sq-action-title" title="<?php echo $view->post->sq_adm->patterns->description ?>"><?php (SQ_Classes_Tools::getOption('sq_auto_pattern') ? _e('Pattern', _SQ_PLUGIN_NAME_) . ': <span>' . $view->post->sq_adm->patterns->description . '</span>' : '') ?></span>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="two right columns small">
                                                            <span id="sq_description_length" class="sq_length" data-maxlength="<?php echo $view->post->sq_adm->description_maxlength ?>">0</span>/<span id="sq_description_maxlength" class="sq_maxlength"><?php echo $view->post->sq_adm->description_maxlength ?></span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_keywords') ? '' : 'sq_disabled') ?>" data-option="sq_auto_keywords" data-value="1">
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Meta Keywords', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <div id="sq_tags">
                                                        <div class="sq_tag_input">
                                                            <input type="text" autocomplete="off" name="sq_keywords" id="sq_keywords" class="form-control" value="<?php echo SQ_Classes_Tools::clearKeywords($view->post->sq_adm->keywords) ?>" placeholder="<?php _e('+ Add keyword', _SQ_PLUGIN_NAME_) ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_canonical') ? '' : 'sq_disabled') ?>" data-option="sq_auto_canonical" data-value="1">
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Canonical link', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <div class="input-group">
                                                        <input type="text" autocomplete="off" id="sq_canonical" name="sq_canonical" class="form-control input-lg sq-toggle" value="<?php echo urldecode($view->post->sq_adm->canonical) ?>" placeholder="<?php echo __('Found: ', _SQ_PLUGIN_NAME_) . urldecode($view->post->url) ?>"/>

                                                        <div class="sq-actions" data-position="top">
                                                            <?php if (!is_admin() && !is_network_admin()) { ?>
                                                                <div class="sq-action">
                                                                    <span style="display: none" class="sq-value sq-canonical-value"></span>
                                                                    <span class="sq-action-title"><?php _e('Current', _SQ_PLUGIN_NAME_) ?>: <span class="sq-canonical-value"></span></span>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (isset($view->post->url) && $view->post->url <> '') { ?>
                                                                <div class="sq-action">
                                                                    <span style="display: none" class="sq-value"><?php echo urldecode($view->post->url) ?></span>
                                                                    <span class="sq-action-title" title="<?php echo urldecode($view->post->url) ?>"><?php _e('Default Link', _SQ_PLUGIN_NAME_) ?>: <span><?php echo urldecode($view->post->url) ?></span></span>
                                                                </div>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sq_hideshow" style="<?php echo ($view->post->sq_adm->doseo == 0) ? '' : 'display: none'; ?>">
                                        <?php _e('To edit the snippet, you have to activate Squirrly SEO for this page first', _SQ_PLUGIN_NAME_) ?>
                                        <div class="sq_optimize_page_activate"><?php _e('or Click here', _SQ_PLUGIN_NAME_) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div id="sq_tab_facebook" class="sq_tabcontent" style="display: none;">
                                <div id="sq_tab_meta_preview" class="sq_tab_preview">
                                    <div class="row b-b m-b-md">
                                        <div class="eight columns">
                                            <div id="sq_message"><?php _e('How this page appears on Facebook', _SQ_PLUGIN_NAME_) ?>:</div>


                                        </div>
                                        <div class="four columns right ">
                                            <input type="button" class="sq_button sq_edit" value="<?php _e('Edit Open Graph', _SQ_PLUGIN_NAME_) ?>"/>
                                        </div>
                                        <?php
                                        if ($view->post->sq->og_media <> '') {
                                            @list($width, $height) = getimagesize($view->post->sq->og_media);
                                            if ((int)$width > 0 && (int)$width < 500) {
                                                ?>
                                                <div class="twelve columns">
                                                    <div class="sq_message sq_error"><?php _e('The image size must be at least 500 pixels wide', _SQ_PLUGIN_NAME_) ?></div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div id="sq_snippet">
                                        <ul id="sq_snippet_ul">
                                            <?php if ($view->post->sq->og_media <> '') { ?>
                                                <li id="sq_snippet_ogimage">
                                                    <img src="<?php echo $view->post->sq->og_media ?>"></li>
                                            <?php } elseif ($view->post->post_attachment <> '') { ?>
                                                <li id="sq_snippet_ogimage" class="sq_snippet_post_atachment">
                                                    <img src="<?php echo $view->post->post_attachment ?>" title="<?php echo __('This is the Featured Image. You can changin it if you edit the snippet and upload anothe image.', _SQ_PLUGIN_NAME_) ?>">
                                                </li>
                                            <?php } ?>
                                            <li id="sq_snippet_title" title="<?php echo($view->post->sq->og_title <> '' ? $view->post->sq->og_title : $view->post->sq->title) ?>"><?php echo($view->post->sq->og_title <> '' ? $view->post->sq->og_title : $view->post->sq->title) ?></li>
                                            <li id="sq_snippet_description" title="<?php echo($view->post->sq->og_description <> '' ? $view->post->sq->og_description : $view->post->sq->description) ?>"><?php echo($view->post->sq->og_description <> '' ? $view->post->sq->og_description : $view->post->sq->description) ?></li>
                                            <li id="sq_snippet_author"><?php echo str_replace(array('//facebook.com/', '//www.facebook.com/', 'https:', 'http:'), '', $view->post->sq->og_author) ?></li>
                                            <li id="sq_snippet_sitename"><?php echo get_bloginfo('title') ?></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="sq_tab_edit">
                                    <div class="row b-b m-b-md ">
                                        <div class="five columns right ">
                                            <input type="button" class="sq_button sq_cancel" value="<?php _e('Cancel', _SQ_PLUGIN_NAME_) ?>"/>
                                            <input type="button" class="sq_button sq_save" value="<?php _e('Save', _SQ_PLUGIN_NAME_) ?>"/>
                                        </div>
                                    </div>
                                    <div class="sq_showhide">
                                        <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_facebook') ? '' : 'sq_disabled') ?>" data-option="sq_auto_facebook" data-value="1">
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Media Image', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <div class="row">
                                                        <div class="twelve columns sq_media_upload">
                                                            <button id="sq_get_og_media"><?php _e('Upload', _SQ_PLUGIN_NAME_) ?></button>
                                                            <span><?php _e('Image size must be at least 500 pixels wide', _SQ_PLUGIN_NAME_) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <input type="hidden" name="sq_og_media" id="sq_og_media" value="<?php echo $view->post->sq_adm->og_media ?>"/>
                                                        <div style="position: relative; max-width: 470px;">
                                                            <span class="og_image_close">x</span>
                                                            <img id="sq_og_media_preview" src=""/>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('OG Title', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? 'sq_pattern_field' : '') ?>">
                                                        <input type="text" autocomplete="off" name="sq_og_title" id="sq_og_title" value="<?php echo SQ_Classes_Tools::clearTitle($view->post->sq_adm->og_title) ?>"/>
                                                    </div>
                                                    <div class="row">
                                                        <div class="two right columns small">
                                                            <span id="sq_og_title_length" class="sq_length" data-maxlength="<?php echo $view->post->sq_adm->og_title_maxlength ?>">0</span>/<span id="sq_og_title_maxlength" class="sq_maxlength"><?php echo $view->post->sq_adm->og_title_maxlength ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('OG Description', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? 'sq_pattern_field' : '') ?>">
                                                        <textarea style="color: black;" name="sq_og_description" id="sq_og_description"><?php echo SQ_Classes_Tools::clearDescription($view->post->sq_adm->og_description) ?></textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="two right columns small">
                                                            <span id="sq_og_description_length" class="sq_length" data-maxlength="<?php echo $view->post->sq_adm->og_description_maxlength ?>">0</span>/<span id="sq_og_description_maxlength" class="sq_maxlength"><?php echo $view->post->sq_adm->og_description_maxlength ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Author Link', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <input type="text" autocomplete="off" name="sq_og_author" id="sq_og_author" value="<?php echo $view->post->sq_adm->og_author ?>"/>
                                                    <div class="row">
                                                        <div class="nine right columns small">
                                                            <span><?php _e('if there are more authors, separate their facebook links with commas', _SQ_PLUGIN_NAME_) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Page type', _SQ_PLUGIN_NAME_) ?>:</div>
                                                <div class="nine columns">
                                                    <select id="sq_og_type" name="sq_og_type">
                                                        <option <?php echo $view->getPostType('og:type', 'website'); ?> value="website">
                                                            <?php _e('Website', _SQ_PLUGIN_NAME_) ?>
                                                        </option>
                                                        <option <?php echo $view->getPostType('og:type', 'profile'); ?> value="profile">
                                                            <?php _e('Author', _SQ_PLUGIN_NAME_) ?>
                                                        </option>
                                                        <option <?php echo $view->getPostType('og:type', 'article'); ?> value="article">
                                                            <?php _e('Article', _SQ_PLUGIN_NAME_) ?>
                                                        </option>
                                                        <option <?php echo $view->getPostType('og:type', 'book'); ?> value="book">
                                                            <?php _e('Book', _SQ_PLUGIN_NAME_) ?>
                                                        </option>
                                                        <option <?php echo $view->getPostType('og:type', 'music'); ?> value="music">
                                                            <?php _e('Music', _SQ_PLUGIN_NAME_) ?>
                                                        </option>
                                                        <option <?php echo $view->getPostType('og:type', 'product'); ?> value="product">
                                                            <?php _e('Product', _SQ_PLUGIN_NAME_) ?>
                                                        </option>
                                                        <option <?php echo $view->getPostType('og:type', 'video'); ?> value="video">
                                                            <?php _e('Video', _SQ_PLUGIN_NAME_) ?>
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sq_hideshow" style="<?php echo ($view->post->sq_adm->doseo == 0) ? '' : 'display: none'; ?>">
                                        <?php _e('To edit the Open Graph, you have to activate Squirrly SEO for this page first', _SQ_PLUGIN_NAME_) ?>
                                        <div class="sq_optimize_page_activate"><?php _e('or Click here', _SQ_PLUGIN_NAME_) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div id="sq_tab_twitter" class="sq_tabcontent" style="display: none;">
                                <div id="sq_tab_meta_preview" class="sq_tab_preview">
                                    <div class="row b-b m-b-md">
                                        <div class="eight columns">
                                            <div id="sq_message"><?php _e('How this page appears on Twitter', _SQ_PLUGIN_NAME_) ?>:</div>
                                        </div>
                                        <div class="four columns right ">
                                            <input type="button" class="sq_button sq_edit" value="<?php _e('Edit Twitter Card', _SQ_PLUGIN_NAME_) ?>"/>
                                        </div>
                                        <?php
                                        if ($view->post->sq->tw_media <> '') {
                                            @list($width, $height) = getimagesize($view->post->sq->tw_media);
                                            if ((int)$width > 0 && (int)$width < 500) {
                                                ?>
                                                <div class="twelve columns">
                                                    <div class="sq_message sq_error"><?php _e('The image size must be at least 500 pixels wide', _SQ_PLUGIN_NAME_) ?></div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div id="sq_snippet">
                                        <ul id="sq_snippet_ul">
                                            <?php if ($view->post->sq->tw_media <> '') { ?>
                                                <li id="sq_snippet_ogimage">
                                                    <img src="<?php echo $view->post->sq->tw_media ?>"></li>
                                            <?php } elseif ($view->post->post_attachment <> '') { ?>
                                                <li id="sq_snippet_ogimage" class="sq_snippet_post_atachment">
                                                    <img src="<?php echo $view->post->post_attachment ?>" title="<?php echo __('This is the Featured Image. You can changin it if you edit the snippet and upload anothe image.', _SQ_PLUGIN_NAME_) ?>">
                                                </li>
                                            <?php } ?>
                                            <li id="sq_snippet_title" title="<?php echo($view->post->sq->tw_title <> '' ? $view->post->sq->tw_title : $view->post->sq->title) ?>"><?php echo($view->post->sq->tw_title <> '' ? $view->post->sq->tw_title : $view->post->sq->title) ?></li>
                                            <li id="sq_snippet_description" title="<?php echo($view->post->sq->tw_description <> '' ? $view->post->sq->tw_description : $view->post->sq->description) ?>"><?php echo($view->post->sq->tw_description <> '' ? $view->post->sq->tw_description : $view->post->sq->description) ?></li>
                                            <li id="sq_snippet_sitename"><?php echo parse_url(get_bloginfo('url'), PHP_URL_HOST) ?></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="sq_tab_edit">
                                    <div class="row b-b m-b-md ">
                                        <div class="five columns right ">
                                            <input type="button" class="sq_button sq_cancel" value="<?php _e('Cancel', _SQ_PLUGIN_NAME_) ?>"/>
                                            <input type="button" class="sq_button sq_save" value="<?php _e('Save', _SQ_PLUGIN_NAME_) ?>"/>
                                        </div>
                                    </div>
                                    <div class="sq_showhide">

                                        <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_twitter') ? '' : 'sq_disabled') ?>" data-option="sq_auto_twitter" data-value="1">
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Twitter Image', _SQ_PLUGIN_NAME_) ?></div>
                                                <div class="nine columns">
                                                    <div class="row">
                                                        <div class="twelve columns sq_media_upload">
                                                            <button id="sq_get_tw_media"><?php _e('Upload', _SQ_PLUGIN_NAME_) ?></button>
                                                            <span><?php _e('Image size must be at least 500 pixels wide', _SQ_PLUGIN_NAME_) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <input type="hidden" name="sq_tw_media" id="sq_tw_media" value="<?php echo $view->post->sq_adm->tw_media ?>"/>
                                                        <div style="position: relative; max-width: 470px;">
                                                            <span class="tw_image_close">x</span>
                                                            <img id="sq_tw_media_preview" src=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Twitter Card Title', _SQ_PLUGIN_NAME_) ?></div>
                                                <div class="nine columns">
                                                    <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? 'sq_pattern_field' : '') ?>">
                                                        <input type="text" autocomplete="off" name="sq_tw_title" id="sq_tw_title" value="<?php echo SQ_Classes_Tools::clearTitle($view->post->sq_adm->tw_title) ?>"/>
                                                    </div>
                                                    <div class="row">
                                                        <div class="two right columns small">
                                                            <span id="sq_tw_title_length" class="sq_length" data-maxlength="<?php echo $view->post->sq_adm->tw_title_maxlength ?>">0</span>/<span id="sq_tw_title_maxlength" class="sq_maxlength"><?php echo $view->post->sq_adm->tw_title_maxlength ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="three columns sq_text"><?php _e('Twitter Card Description', _SQ_PLUGIN_NAME_) ?></div>
                                                <div class="nine columns">
                                                    <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_pattern') ? 'sq_pattern_field' : '') ?>">
                                                        <textarea style="color: black;" name="sq_tw_description" id="sq_tw_description"><?php echo SQ_Classes_Tools::clearDescription($view->post->sq_adm->tw_description) ?></textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="two right columns small">
                                                            <span id="sq_tw_description_length" class="sq_length" data-maxlength="<?php echo $view->post->sq_adm->tw_description_maxlength ?>">0</span>/<span id="sq_tw_description_maxlength" class="sq_maxlength"><?php echo $view->post->sq_adm->tw_description_maxlength ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sq_hideshow" style="<?php echo ($view->post->sq_adm->doseo == 0) ? '' : 'display: none'; ?>">
                                        <?php _e('To edit the Twitter Card, you have to activate Squirrly SEO for this page first', _SQ_PLUGIN_NAME_) ?>
                                        <div class="sq_optimize_page_activate"><?php _e('or Click here', _SQ_PLUGIN_NAME_) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div id="sq_tab_advanced" class="sq_tabcontent" style="display: none;">
                                <div class="row b-b m-b-md ">
                                    <div class="five columns right ">
                                        <input type="button" class="sq_button sq_save" value="<?php _e('Save', _SQ_PLUGIN_NAME_) ?>"/>
                                    </div>
                                </div>
                                <div class="sq_showhide">
                                    <div class="<?php echo(SQ_Classes_Tools::getOption('sq_auto_noindex') ? '' : 'sq_disabled') ?>" data-option="sq_auto_noindex" data-value="1">

                                        <div class="row">
                                            <div class="five columns sq_text"><?php _e('Let Google Index This Page', _SQ_PLUGIN_NAME_) ?></div>
                                            <div class="seven columns">
                                                <div class="sq_option_content sq_option_content">
                                                    <div class="sq_switch-field">
                                                        <input id="sq_noindex_on" type="radio" name="sq_noindex" value="0" <?php echo($view->post->sq_adm->noindex == 0 ? 'checked="checked"' : ''); ?> >
                                                        <label for="sq_noindex_on" class="sq_switch-label-on"><?php _e('Yes', _SQ_PLUGIN_NAME_) ?></label>
                                                        <input id="sq_noindex_off" type="radio" name="sq_noindex" value="1" <?php echo($view->post->sq_adm->noindex == 1 ? 'checked="checked"' : ''); ?> >
                                                        <label for="sq_noindex_off" class="sq_switch-label-off"><?php _e('No', _SQ_PLUGIN_NAME_) ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="five columns sq_text"><?php _e('Pass Link Juice to This Page', _SQ_PLUGIN_NAME_) ?></div>
                                            <div class="seven columns">
                                                <div class="sq_option_content sq_option_content">
                                                    <div class="sq_switch-field">
                                                        <input id="sq_nofollow_on" type="radio" name="sq_nofollow" value="0" <?php echo($view->post->sq_adm->nofollow == 0 ? 'checked="checked"' : ''); ?> >
                                                        <label for="sq_nofollow_on" class="sq_switch-label-on"><?php _e('Yes', _SQ_PLUGIN_NAME_) ?></label>
                                                        <input id="sq_nofollow_off" type="radio" name="sq_nofollow" value="1" <?php echo($view->post->sq_adm->nofollow == 1 ? 'checked="checked"' : ''); ?> >
                                                        <label for="sq_nofollow_off" class="sq_switch-label-off"><?php _e('No', _SQ_PLUGIN_NAME_) ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="five columns sq_text"><?php _e('Show it in Sitemap.xml', _SQ_PLUGIN_NAME_) ?></div>
                                            <div class="seven columns">
                                                <div class="sq_option_content sq_option_content">
                                                    <div class="sq_switch-field">
                                                        <input id="sq_nositemap_on" type="radio" name="sq_nositemap" value="0" <?php echo($view->post->sq_adm->nositemap == 0 ? 'checked="checked"' : ''); ?> >
                                                        <label for="sq_nositemap_on" class="sq_switch-label-on"><?php _e('Yes', _SQ_PLUGIN_NAME_) ?></label>
                                                        <input id="sq_nositemap_off" type="radio" name="sq_nositemap" value="1" <?php echo($view->post->sq_adm->nositemap == 1 ? 'checked="checked"' : ''); ?> >
                                                        <label for="sq_nositemap_off" class="sq_switch-label-off"><?php _e('No', _SQ_PLUGIN_NAME_) ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sq_hideshow" style="<?php echo ($view->post->sq_adm->doseo == 0) ? '' : 'display: none'; ?>">
                                    <?php _e('To edit, you have to activate Squirrly SEO for this page first.', _SQ_PLUGIN_NAME_) ?>
                                    <div class="sq_optimize_page_activate"><?php _e('or Click here', _SQ_PLUGIN_NAME_) ?></div>
                                </div>
                            </div>
                            <!-- ================ End Tabs ================= -->
                        </div>

                        <input type="hidden" name="sq_url" id="sq_url" value="<?php echo $view->post->url; ?>">
                        <input type="hidden" name="sq_post_id" value="<?php echo (int)$view->post->ID; ?>">
                        <input type="hidden" name="sq_term_taxonomy_id" value="<?php echo (int)$view->post->term_taxonomy_id; ?>">
                        <input type="hidden" name="sq_taxonomy" value="<?php echo (int)$view->post->taxonomy; ?>">

                        <div id="sq_footer">
                            <input type="hidden" readonly="readonly" name="sq_hash" id="sq_hash" value="<?php echo $view->post->hash; ?>">
                            <div class="row">
                                <div class="two columns ">
                                    <img src=" <?php echo _SQ_THEME_URL_ ?>img/logo.png">
                                </div>
                                <div class="ten columns details" style="font-style: italic; margin: 8px 0; color: #999;">
                                    <?php if (false) { ?>
                                        <?php _e('post type', _SQ_PLUGIN_NAME_) ?>:
                                        <strong><?php echo $view->post->post_type ?></strong>,
                                        <?php _e('og type', _SQ_PLUGIN_NAME_) ?>:
                                        <strong><?php echo $view->post->sq->og_type ?></strong>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            } else { ?>

                <div id="sq_settings_body" style="display: none">
                    <div class="container">
                        <button id="sq_close">x</button>
                        <div id="sq_footer">
                            <div class="row">
                                <div class="two columns ">
                                    <img src=" <?php echo _SQ_THEME_URL_ ?>img/logo.png">
                                </div>

                                <div class="nine columns" style="margin: 10px;">
                                    <?php _e("Can't do Custom SEO for this URL", _SQ_PLUGIN_NAME_) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            }
        } else {
            ?>
            <div id="sq_settings_body" style="display: none">
                <div class="container">
                    <div id="sq_footer">
                        <div class="row">
                            <div class="one column">
                                <img src=" <?php echo _SQ_THEME_URL_ ?>img/logo.png">
                            </div>
                            <div class="eleven columns">
                                <div style="margin: 8px 0;">
                                    <?php _e("Enable Squirrly SEO to load Squirrly Snippet", _SQ_PLUGIN_NAME_) ?>
                                    <div class="sq_disabled" data-option="sq_use" data-value="1" style="min-height: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php
        }
    } else {
        ?>

        <div id="sq_settings_body" style="display: none">
            <div class="container">
                <div id="sq_footer">
                    <div class="row">
                        <div class="one column">
                            <img src=" <?php echo _SQ_THEME_URL_ ?>img/logo.png">
                        </div>
                        <div class="eleven columns">
                            <div style="margin: 8px 0;">
                                <?php echo sprintf(__("%sPlease connect to Squirrly first%s", _SQ_PLUGIN_NAME_), '<a href="' . admin_url('?page=sq_dashboard') . '" >', '</a>') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
