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
    <div id="sq_settings">
        <div class="sq_message sq_error" style="display: none"></div>
        <?php
        SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init();
        ?>
        <div>
            <span class="sq_icon"></span>
            <div id="sq_settings_title"><?php _e('Squirrly Site Audit', _SQ_PLUGIN_NAME_); ?> </div>

        </div>
        <div id="sq_left">
            <div id="sq_settings_body">
                <fieldset>
                    <legend>
                        <span class="sq_legend_title"><?php _e('What the Audit offers:', _SQ_PLUGIN_NAME_); ?></span>
                        <span><?php echo sprintf(__('%sTracks all the areas of your Content Marketing Strategy:%s: Blogging, Traffic, SEO, Social Signals, Links, Authority. Every single week, you get a new report by email.', _SQ_PLUGIN_NAME_), '<strong>', '</strong>'); ?></span>
                        <span><?php echo sprintf(__('%sIt gives you professional advice on how to fix issues in those 6 areas%s. You can easily find out how to improve your content marketing strategy.', _SQ_PLUGIN_NAME_), '<strong>', '</strong>'); ?></span>
                        <span><?php echo sprintf(__('%sMonitors your progress, week by week.%s You’ll get to see if your site audit has improved since you last checked it. ', _SQ_PLUGIN_NAME_), '<strong>', '</strong>'); ?></span>
                        <span><?php echo sprintf(__('%sAnalyze any single article.%s See how it improves over time.', _SQ_PLUGIN_NAME_), '<strong>', '</strong>'); ?></span>
                        <span><?php echo sprintf(__('%sLearn More About The Site Audit%s', _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/site-seo-audit-tool" target="_blank">', '</a>'); ?></span>
                        <span><?php echo sprintf(__('%sRequest an Audit Now%s', _SQ_PLUGIN_NAME_), '<a href="' . _SQ_DASH_URL_ . 'login/?token=' . SQ_Classes_Tools::getToken() . '" target="_blank">', '</a>'); ?></span>

                    </legend>

                    <div>
                        <div id="sq_post_type_option" class="withborder" style="min-height: 540px;">
                            <p style="font-weight: bold;"><?php _e('"Your current site audit score:', _SQ_PLUGIN_NAME_); ?>:</p>
                            <ul style="margin-top: 50px;">
                                <li>
                                    <?php if (isset($view->blog->score) && $view->blog->score > 0) { ?>
                                        <p class="sq_audit_score"><?php echo __('Score', _SQ_PLUGIN_NAME_) . ': <span>' . $view->blog->score . '/100</span>'; ?></p>
                                        <p class="sq_audit_date"><?php echo __('Date', _SQ_PLUGIN_NAME_) . ': <span>' . date(get_option('date_format'), strtotime($view->blog->datetime)) . '</span>'; ?></p>
                                        <p class="sq_settings_bigbutton" style="margin-bottom:35px;">
                                            <a href="<?php echo _SQ_DASH_URL_ . 'login/?token=' . SQ_Classes_Tools::getToken() ?>" target="_blank"><?php _e('See the Audit', _SQ_PLUGIN_NAME_) ?> &raquo;</a>
                                        </p>
                                    <?php } else { ?>
                                        <p><?php _e('It seems that no audit has been generated yet. You can request an audit down below. It should be ready in 5-10 minutes.', _SQ_PLUGIN_NAME_); ?>:</p>
                                        <p class="sq_settings_bigbutton" style="margin-bottom:35px;">
                                            <a href="<?php echo _SQ_DASH_URL_ . 'login/?token=' . SQ_Classes_Tools::getToken() ?>" target="_blank"><?php _e('Request an Audit Now', _SQ_PLUGIN_NAME_) ?> &raquo;</a>
                                        </p>
                                    <?php } ?>
                                </li>
                                <?php if (isset($view->blog->score) && $view->blog->score == 0) { ?>
                                    <li>
                                        <p>
                                            <?php _e('This is an example of a Site Audit', _SQ_PLUGIN_NAME_); ?>:
                                        </p>
                                        <p>
                                            <a href="<?php echo _SQ_DASH_URL_ . 'login/?token=' . SQ_Classes_Tools::getToken() ?>" target="_blank">
                                                <img src="https://ps.w.org/squirrly-seo/trunk/screenshot-7.png" alt="" style="max-width: 520px">
                                            </a>
                                        </p>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>

                    </div>
                </fieldset>

            </div>
        </div>

    </div>
    <?php
}