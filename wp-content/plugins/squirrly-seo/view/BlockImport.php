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
            <div id="sq_helpimportside" class="sq_helpside"></div>
        <?php } ?>

        <div id="sq_left">
            <form name="settings" action="" method="post" enctype="multipart/form-data">
                <div id="sq_settings_body">
                    <fieldset style="background-color: rgb(35, 40, 45);">
                        <legend id="sq_enable">
                            <span class="sq_legend_title" style="color: rgb(238, 238, 238);"><?php echo __('Import SEO settings from other SEO plugins or themes', _SQ_PLUGIN_NAME_) ?></span>
                            <span style="color: rgb(238, 238, 238);"><?php echo __('If you were already using an SEO plugin, then you can import all the SEO settings in Squirrly. Just follow the steps presented on the right side.', _SQ_PLUGIN_NAME_) ?></span>
                        </legend>

                        <?php $platforms = apply_filters('sq_importList', false); ?>
                        <div class="sq_field_options">
                            <form id="sq_inport_form" name="import" action="" method="post" enctype="multipart/form-data">
                                <ul id="sq_settings_sq_use" class="sq_settings_info">
                                    <span><?php _e('Select the plugin or theme you want to import the Settings from.', _SQ_PLUGIN_NAME_) ?></span>
                                    <li><?php
                                        if (count($platforms) > 0) {
                                            ?>
                                            <select id="sq_import_platform" name="sq_import_platform">
                                                <?php
                                                foreach ($platforms as $path => $settings) {
                                                    ?>
                                                    <option value="<?php echo $path ?>"><?php echo ucfirst(SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->getName($path)); ?></option>
                                                <?php } ?>
                                            </select>

                                            <input type="hidden" name="action" value="sq_importsettings"/>
                                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                            <input type="submit" name="sq_update" style="cursor: pointer" value="<?php _e('Import Settings', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
                                        <?php } else { ?>
                                            <div><?php _e("We couldn't find any SEO plugin or theme to import from."); ?></div>
                                        <?php } ?>
                                    </li>
                                </ul>
                            </form>
                            <form id="sq_inport_form" name="import" action="" method="post" enctype="multipart/form-data">
                                <ul id="sq_settings_sq_use" class="sq_settings_info">
                                    <span><?php _e('Select the plugin or theme you want to import the SEO settings from.', _SQ_PLUGIN_NAME_) ?></span>
                                    <li>
                                        <?php
                                        if (count($platforms) > 0) {
                                            ?>
                                            <select id="sq_import_platform" name="sq_import_platform">
                                                <?php foreach ($platforms as $path => $settings) { ?>
                                                    <option value="<?php echo $path ?>"><?php echo ucfirst(SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->getName($path)); ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="action" value="sq_importseo"/>
                                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                            <input type="submit" name="sq_update" style="cursor: pointer" value="<?php _e('Import SEO', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
                                        <?php } else { ?>
                                            <div><?php _e("We couldn't find any SEO plugin or theme to import from."); ?></div>
                                        <?php } ?>
                                    </li>
                                </ul>
                            </form>

                            <br/>
                            <p class="small"><?php _e('Note! If you import the SEO settings from other plugins or themes, you will lose all the settings that you had in Squirrly SEO. Make sure you backup your settings from the panel below before you do this. ', _SQ_PLUGIN_NAME_) ?></p>
                        </div>
                    </fieldset>
                    <fieldset style="background-color: rgb(35, 40, 45);">
                        <legend id="sq_enable">
                            <span class="sq_legend_title" style="color: rgb(238, 238, 238);"><?php echo __('Backup & Restore Squirrly SEO Settings', _SQ_PLUGIN_NAME_) ?></span>
                            <span style="color: rgb(238, 238, 238);"><?php echo __('You can now download your Squirrly settings in an sql file before you go ahead and import the SEO settings from another plugin. That way, you can always go back to your Squirrly settings. ', _SQ_PLUGIN_NAME_) ?></span>
                        </legend>


                        <div class="sq_field_options">

                            <div class="sq_settings_backup withborder">
                                <p><?php echo __('Backup & Restore Squirrly Settings', _SQ_PLUGIN_NAME_) ?></p>
                                <form action="" method="POST">
                                    <input type="hidden" name="action" value="sq_backup"/>
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                    <input type="submit" class="sq_button" name="sq_backup" value="<?php _e('Backup Settings', _SQ_PLUGIN_NAME_) ?>"/>
                                    <input type="button" class="sq_button sq_restore" name="sq_restore" value="<?php _e('Restore Settings', _SQ_PLUGIN_NAME_) ?>"/>
                                </form>
                            </div>

                            <div class="sq_settings_restore sq_popup" style="display: none">
                                <span class="sq_close">x</span>
                                <span><?php _e('Upload the file with the saved Squirrly Settings', _SQ_PLUGIN_NAME_) ?></span>
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="sq_restore"/>
                                    <input type="file" name="sq_options" id="favicon" style="float: left;"/>
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                    <input type="submit" style="margin-top: 10px;" class="sq_button" value="<?php _e('Restore Backup', _SQ_PLUGIN_NAME_) ?>" onclick="return confirm('<?php _e('Are you sure you want to restore your settings?', _SQ_PLUGIN_NAME_) ?>')"/>
                                </form>
                            </div>

                            <div class="sq_settings_backup_sql">
                                <p><?php echo __('Backup & Restore all the pages optimized with Squirrly SEO', _SQ_PLUGIN_NAME_) ?></p>
                                <form action="" method="POST">
                                    <input type="hidden" name="action" value="sq_backup_sql"/>
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                    <input type="submit" class="sq_button" name="sq_backup" value="<?php _e('Backup SEO', _SQ_PLUGIN_NAME_) ?>"/>
                                    <input type="button" class="sq_button sq_restore_sql" name="sq_restore_sql" value="<?php _e('Restore SEO', _SQ_PLUGIN_NAME_) ?>"/>
                                </form>
                            </div>

                            <div class="sq_settings_restore_sql sq_popup" style="display: none">
                                <span class="sq_close">x</span>
                                <span><?php _e('Upload the file with the saved Squirrly SEO SQL file', _SQ_PLUGIN_NAME_) ?></span>

                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="sq_restore_sql"/>
                                    <input type="file" name="sq_sql" style="float: left;"/>
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                    <input type="submit" style="margin-top: 10px;" class="sq_button" value="<?php _e('Restore Backup', _SQ_PLUGIN_NAME_) ?>" onclick="return confirm('<?php _e('Are you sure you want to restore your settings?', _SQ_PLUGIN_NAME_) ?>')"/>
                                </form>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset style="display:none; background-color: rgb(35, 40, 45);">
                        <legend id="sq_enable">
                            <span class="sq_legend_title" style="color: rgb(238, 238, 238);">Reset All Settings to default</span>
                        </legend>

                        <div class="sq_field_options">


                            <form id="sq_inport_form" name="import" action="" method="post" enctype="multipart/form-data">
                                <ul id="sq_settings_sq_use" class="sq_settings_info">
                                    <span><?php _e('Click to reset all the saved setting to default.', _SQ_PLUGIN_NAME_) ?></span>
                                    <li>
                                        <input type="hidden" name="action" value="sq_resetsettings"/>
                                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                        <input type="submit" name="sq_update" onclick="return confirm('<?php _e('Are you sure you want to remove all the saved settings?', _SQ_PLUGIN_NAME_) ?>')" style="cursor: pointer" value="<?php _e('Reset Settings', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
                                    </li>
                                </ul>
                            </form>

                            <br/>
                            <p class="small"><?php _e('Note! Make sure you backup your data first in case you change your mind.', _SQ_PLUGIN_NAME_) ?></p>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
    <?php
}