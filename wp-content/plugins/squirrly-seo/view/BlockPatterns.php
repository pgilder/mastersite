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
    ?>
    <div id="sq_settings">
        <?php SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init(); ?>
        <div>
            <span class="sq_icon"></span>
            <div id="sq_settings_title"><?php _e('SEO Patterns', _SQ_PLUGIN_NAME_); ?> </div>
        </div>

        <?php SQ_Classes_ObjController::getClass('SQ_Core_BlockToolbar')->init(); ?>

        <?php $menu = SQ_Classes_Tools::getOption('menu');
        if ($menu['show_tutorial']) { ?>
            <div id="sq_helppatternsside" class="sq_helpside"></div>
        <?php } ?>

        <div id="sq_left">
            <form name="settings" action="" method="post" enctype="multipart/form-data">
                <div id="sq_settings_body">

                    <fieldset id="sq_auto_pattern" style="background-color: rgb(35, 40, 45);" class="<?php echo((!SQ_Classes_Tools::getOption('sq_auto_pattern')) ? 'deactivated' : ''); ?>">
                        <legend id="sq_enable">
                            <span class="sq_legend_title" style="color: rgb(238, 238, 238);">Post Patterns</span>
                            <span><?php _e('Use Squirrly Patterns', _SQ_PLUGIN_NAME_); ?></span>
                            <div class="sq_option_content">
                                <div class="sq_switch">
                                    <input id="sq_auto_pattern_on" type="radio" class="sq_switch-input" name="sq_auto_pattern" value="1" <?php echo((SQ_Classes_Tools::getOption('sq_auto_pattern') == 1) ? "checked" : '') ?> />
                                    <label for="sq_auto_pattern_on" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                    <input id="sq_auto_pattern_off" type="radio" class="sq_switch-input" name="sq_auto_pattern" value="0" <?php echo((!SQ_Classes_Tools::getOption('sq_auto_pattern')) ? "checked" : '') ?> />
                                    <label for="sq_auto_pattern_off" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                    <span class="sq_switch-selection"></span>
                                </div>
                            </div>
                            <br/>

                            <span><?php echo __("Control how post types are displayed on your site and within search engine results and social media feeds.", _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo __("In Squirrly, each post type in your site comes with a predefined posting pattern when displayed onto your website. However, based on your site's purpose and needs, you can also decide what information these patterns will include.", _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo __("Once you set up a pattern for a particular post type, only the content required by your custom sequence will be displayed.", _SQ_PLUGIN_NAME_); ?></span>
                            <span><?php echo sprintf(__("Squirrly lets you see how the customized patterns will apply when posts/pages are shared across social media or search engine feeds. You just need to go to the %sSquirrly SEO Snippet%s box, press <strong>Edit Snippet</strong> and you'll get a live preview after you customize the meta information.", _SQ_PLUGIN_NAME_), '<a href="http://howto.squirrly.co/sides/squirrly-snippet-tool/" target="_blank">', '</a>'); ?></span>

                        </legend>
                        <div id="sq_field_options">
                            <ul class="sq_settings_info">
                                <span><?php _e('Set the custom patterns for each post type', _SQ_PLUGIN_NAME_) ?></span>

                                <li>
                                    <select id="sq_post_types" name="sq_post_types">
                                        <?php foreach (SQ_Classes_Tools::getOption('patterns') as $pattern => $type) { ?>
                                            <option value="<?php echo $pattern ?>"><?php echo ucfirst(str_replace(array('tax-', '_'), array('', ' '), $pattern)); ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="button" value="<?php _e('+ Add Post Type', _SQ_PLUGIN_NAME_) ?>" class="sq_button" title="<?php echo __('Add a post type from your Wordpress website', _SQ_PLUGIN_NAME_) ?>" onclick="jQuery('#sq_add_post_types').toggle();"/>
                                </li>
                                <li id="sq_add_post_types" style="display: none">
                                    <span><?php _e('Add Post Type', _SQ_PLUGIN_NAME_) ?></span>
                                    <select id="sq_select_post_types" name="sq_select_post_types">
                                        <option value="" selected="selected"></option>

                                        <?php
                                        $types = get_post_types();
                                        $excludes = array('revision', 'nav_menu_item');
                                        foreach ($types as $pattern => $type) {
                                            if (in_array($type, array_keys(SQ_Classes_Tools::getOption('patterns')))) {
                                                continue;
                                            } elseif (in_array($type, $excludes)) {
                                                continue;
                                            }
                                            ?>
                                            <option value="<?php echo $pattern ?>"><?php echo ucfirst(str_replace(array('tax-', '_'), array('', ' '), $pattern)); ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="submit" name="sq_update" class="sq_button" style="font-size: 12px; margin: 0; padding: 0px 10px;" value="<?php _e('Add', _SQ_PLUGIN_NAME_) ?>"/>
                                </li>
                                <li>
                                    <?php
                                    foreach (SQ_Classes_Tools::getOption('patterns') as $pattern => $type) {
                                        ?>
                                        <script>
                                            jQuery.sq_patterns_list = jQuery.parseJSON("<?php echo addslashes(SQ_ALL_PATTERNS) ?>");
                                        </script>
                                        <div class="show_hide sq<?php echo $pattern ?>" style="display: none">
                                            <span class="sq_remove" data-id="<?php echo $pattern ?>" data-confirm="<?php echo sprintf(__('Are you sure you want to remove the post type: %s', _SQ_PLUGIN_NAME_), ucfirst(str_replace(array('tax-', '_'), array('', ' '), $pattern))); ?>"><?php _e('Remove Post Type', _SQ_PLUGIN_NAME_) ?></span>

                                            <div class="withborder">
                                                <table style="max-width: 600px;">
                                                    <tr>
                                                        <td><?php echo __('Title', _SQ_PLUGIN_NAME_) ?>:</td>
                                                        <td class="sq_pattern_field">
                                                            <input type="text" name="sq_patterns[<?php echo $pattern ?>][title]" value="<?php echo $type['title'] ?>" size="45"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo __('Description', _SQ_PLUGIN_NAME_) ?>:</td>
                                                        <td class="sq_pattern_field">
                                                            <input type="text" name="sq_patterns[<?php echo $pattern ?>][description]" value="<?php echo $type['description'] ?>" size="45"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo __('Separator', _SQ_PLUGIN_NAME_) ?>:</td>
                                                        <td>
                                                            <select name="sq_patterns[<?php echo $pattern ?>][sep]">
                                                                <?php
                                                                $seps = json_decode(SQ_ALL_SEP, true);

                                                                foreach ($seps as $sep => $code) {
                                                                    ?>
                                                                    <option value="<?php echo $sep ?>" <?php echo ($type['sep'] == $sep) ? 'selected="selected"' : '' ?>><?php echo $code ?></option><?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php _e('Let Google Index it', _SQ_PLUGIN_NAME_) ?>:</td>
                                                        <td>
                                                            <div class="sq_option_content">
                                                                <div class="sq_switch">
                                                                    <input id="sq_patterns_noindex_<?php echo $pattern ?>1" type="radio" class="sq_switch-input" name="sq_patterns[<?php echo $pattern ?>][noindex]" value="0" <?php echo(($type['noindex'] == 0) ? "checked" : '') ?> />
                                                                    <label for="sq_patterns_noindex_<?php echo $pattern ?>1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                                                    <input id="sq_patterns_noindex_<?php echo $pattern ?>0" type="radio" class="sq_switch-input" name="sq_patterns[<?php echo $pattern ?>][noindex]" value="1" <?php echo(($type['noindex'] == 1) ? "checked" : '') ?> />
                                                                    <label for="sq_patterns_noindex_<?php echo $pattern ?>0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                                                    <span class="sq_switch-selection"></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php _e('Pass Link Juice', _SQ_PLUGIN_NAME_) ?>:</td>
                                                        <td>
                                                            <div class="sq_option_content">
                                                                <div class="sq_switch">
                                                                    <input id="sq_patterns_nofollow_<?php echo $pattern ?>1" type="radio" class="sq_switch-input" name="sq_patterns[<?php echo $pattern ?>][nofollow]" value="0" <?php echo(($type['nofollow'] == 0) ? "checked" : '') ?> />
                                                                    <label for="sq_patterns_nofollow_<?php echo $pattern ?>1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                                                    <input id="sq_patterns_nofollow_<?php echo $pattern ?>0" type="radio" class="sq_switch-input" name="sq_patterns[<?php echo $pattern ?>][nofollow]" value="1" <?php echo(($type['nofollow'] == 1) ? "checked" : '') ?> />
                                                                    <label for="sq_patterns_nofollow_<?php echo $pattern ?>0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                                                    <span class="sq_switch-selection"></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php _e('Do SEO', _SQ_PLUGIN_NAME_) ?>:</td>
                                                        <td>
                                                            <div class="sq_option_content">
                                                                <?php if (!isset($type['disable'])) {
                                                                    $type['disable'] = 0;
                                                                } ?>
                                                                <div class="sq_switch">
                                                                    <input id="sq_patterns_disable_<?php echo $pattern ?>1" type="radio" class="sq_switch-input" name="sq_patterns[<?php echo $pattern ?>][disable]" value="0" <?php echo(($type['disable'] == 0) ? "checked" : '') ?> />
                                                                    <label for="sq_patterns_disable_<?php echo $pattern ?>1" class="sq_switch-label sq_switch-label-off"><?php _e('Yes', _SQ_PLUGIN_NAME_); ?></label>
                                                                    <input id="sq_patterns_disable_<?php echo $pattern ?>0" type="radio" class="sq_switch-input" name="sq_patterns[<?php echo $pattern ?>][disable]" value="1" <?php echo(($type['disable'] == 1) ? "checked" : '') ?> />
                                                                    <label for="sq_patterns_disable_<?php echo $pattern ?>0" class="sq_switch-label sq_switch-label-on"><?php _e('No', _SQ_PLUGIN_NAME_); ?></label>
                                                                    <span class="sq_switch-selection"></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <?php

                                    }
                                    ?>
                                </li>
                                <li>
                                    <table style="max-width: 600px;">
                                        <tbody>
                                        <?php
                                        $all_patterns = json_decode(SQ_ALL_PATTERNS, true);
                                        foreach ($all_patterns as $pattern => $details) { ?>
                                            <tr>
                                                <td><code><?php echo $pattern ?></code></td>
                                                <td><?php echo $details ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </li>
                            </ul>
                        </div>
                    </fieldset>
                    <div id="sq_settings_submit">
                        <input type="hidden" name="action" value="sq_savepatters"/>
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                        <input type="submit" name="sq_update" style="cursor: pointer" value="<?php _e('Save SEO', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}