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
        <div class="sq_message sq_error" style="display: none"></div>

        <?php
        SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init();
        SQ_Classes_ObjController::getClass('SQ_Core_Loading')->loadJsVars();
        ?>
        <div>
            <span class="sq_icon"></span>
            <div id="sq_settings_title"><?php _e('Squirrly Keyword Research', _SQ_PLUGIN_NAME_); ?> </div>
            <div class="sq_subtitles">
                <p>You can now find <strong>long-tail keywords</strong> that are easy to rank for. Get personalized
                    <strong>competition data</strong> for each keyword you research, thanks to
                    <strong>Squirrly's Market Intelligence Features</strong>.</p>
            </div>
        </div>

        <div id="sq_helpkeywordresearchside" class="sq_helpside"></div>

        <div id="sq_left">
            <?php if (SQ_Classes_Tools::getOption('sq_api') <> '') { ?>
                <div id="sq_settings_body">

                    <?php if (SQ_Classes_Tools::getOption('sq_api') <> '') { ?>
                        <fieldset style="background: none !important; box-shadow: none; border: none; padding: 0 20px;">
                            <div id="sq_krinfo" class="sq_loading"></div>
                        </fieldset>
                        <script type="text/javascript">
                            jQuery(document).ready(function () {
                                jQuery('#sq_settings').sq_getKR();
                            });
                        </script>
                    <?php } ?>

                </div>

            <?php } ?>

        </div>

    </div>
    <?php
}