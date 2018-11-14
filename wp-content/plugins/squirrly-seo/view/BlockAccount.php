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
} else { ?>
    <div id="sq_settings" class="sq_userinfo">
        <?php SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init(); ?>

        <?php if (SQ_Classes_Tools::getOption('sq_api') <> '') { ?>
            <div>
                <span class="sq_icon"></span>
                <div id="sq_settings_title"><?php _e('Squirrly account information', _SQ_PLUGIN_NAME_); ?> </div>
                <div id="sq_settings_title" style="text-align: right">
                    <input id="sq_goto_dashboard" class="sq_goto_dashboard" type="button" value="<?php _e('Go to dashboard', _SQ_PLUGIN_NAME_) ?> &raquo;"/>
                    <a href="<?php echo _SQ_DASH_URL_ ?>login/?token=<?php echo SQ_Classes_Tools::getToken() ?>&redirect_to=<?php echo _SQ_DASH_URL_ ?>user/dashboard" target="_blank" type="button" title="<?php _e('See ALL of Your Activity so Far', _SQ_PLUGIN_NAME_) ?>"><?php _e('See ALL of Your Activity so Far', _SQ_PLUGIN_NAME_) ?> &raquo;</a>
                    <br style="clear: both;">
                </div>
            </div>
        <?php } ?>

        <?php $menu = SQ_Classes_Tools::getOption('menu');
        if ($menu['show_tutorial']) { ?>
            <div id="sq_helpaccountside" class="sq_helpside"></div>
        <?php } ?>

        <div id="sq_left">

            <?php if (SQ_Classes_Tools::getOption('sq_api') <> '') { ?>
                <div id="sq_settings_body" style="min-height: 400px;">
                    <?php if (SQ_Classes_Tools::getOption('sq_api') <> '') { ?>
                        <fieldset style="background: none; border: none; box-shadow: none;">
                            <div id="sq_userinfo" class="sq_loading"></div>
                        </fieldset>
                        <script type="text/javascript">
                            jQuery(document).ready(function () {
                                jQuery.sq_getUserStatus();
                            });
                        </script>
                    <?php } ?>

                </div>

            <?php } ?>

        </div>
        <div id="sq_sidehelp"></div>
    </div>
    <?php
}