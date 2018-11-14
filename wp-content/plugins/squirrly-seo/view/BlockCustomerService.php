<div id="sq_settings">
    <div class="sq_message sq_error" style="display: none"></div>
    <div>
        <span class="sq_icon"></span>
        <div id="sq_settings_title"><?php _e('Squirrly Customer Service', _SQ_PLUGIN_NAME_); ?> </div>

    </div>
    <div id="sq_left">
        <div id="sq_settings_body">
            <fieldset>
                <legend>
                    <span class="sq_legend_title"><?php _e('Support Channels', _SQ_PLUGIN_NAME_); ?></span>
                    <span><?php echo sprintf(__('%sHowto.squirrly.co%s > Knowledge Base. Find out how to get the most out of Squirrly SEO', _SQ_PLUGIN_NAME_), '<a href="https://howto.squirrly.co/wordpress-seo" target="_blank">', '</a>'); ?></span>
                    <span><?php echo sprintf(__('%sPlugin.squirrly.co%s >> Case studies, ideas on how to better use Squirrly SEO for Content Marketing', _SQ_PLUGIN_NAME_), '<a href="https://plugin.squirrly.co" target="_blank">', '</a>'); ?></span>
                    <span><?php echo sprintf(__('%sFacebook page%s >> You can send us your support requests in private messages', _SQ_PLUGIN_NAME_), '<a href="https://www.facebook.com/Squirrly.co" target="_blank">', '</a>'); ?></span>
                    <span><?php echo sprintf(__('%sSupport Community%s >> Join us on Facebook', _SQ_PLUGIN_NAME_), '<a href="https://www.facebook.com/groups/SquirrlySEOCustomerService/" target="_blank">', '</a>'); ?></span>
                    <span><?php echo sprintf(__('%sLive Chat%s >> on Youtube. Thursday 4 PM', _SQ_PLUGIN_NAME_), '<a href="https://www.youtube.com/c/GetGrowthTV/live" target="_blank">', '</a>'); ?></span>
                    <span><?php echo sprintf(__('%sNew Lessons%s >> Mon. and Tue. on Twitter', _SQ_PLUGIN_NAME_), '<a href="https://twitter.com/squirrlyhq" target="_blank">', '</a>'); ?></span>
                    <span><?php echo sprintf(__('%sEmail Support%s >> 8 AM to 4 PM (London Time): Mon-Fri..', _SQ_PLUGIN_NAME_), '<a href="mailto:' . _SQ_SUPPORT_EMAIL_ . '" target="_blank">', '</a>'); ?></span>

                </legend>

                <div id="sq_customerservice">
                    <div id="sq_post_type_option" class="withborder">
                        <p style="font-weight: bold;"><?php _e('Need Help with Squirrly SEO?', _SQ_PLUGIN_NAME_); ?>:</p>
                        <ul class="sq_options_support_popup">

                            <li>
                                <div class="withborder">
                                    <p id="sq_support_msg">
                                        <span style="margin-left: 15px"><?php echo __('From')?>:</span>
                                        <input class="sq_small_input" name="sq_support_email" value="<?php
                                        $current_user = wp_get_current_user();
                                        echo (SQ_Classes_Tools::getOption('sq_support_email') <> '' ? SQ_Classes_Tools::getOption('sq_support_email') : $current_user->user_email) ;
                                        ?>"/>
                                    </p>
                                    <p id="sq_support_msg">
                                        <textarea class="sq_big_input" name="sq_support_message"></textarea></p>
                                    <div id="sq_options_support_error"></div>
                                    <p>
                                        <input id="sq_support_submit" type="button" value="<?php _e('Send Question', _SQ_PLUGIN_NAME_) ?>" style="padding: 9px 30px;background-color: #589ee4;color: white;text-shadow: 1px 1px #333;font-size: 14px;    cursor: pointer;">
                                    </p>
                                    <p >
                                        <span style="margin-left: 15px">
                                            <?php echo sprintf(__("By clicking Send Question you're agreeing to %sSquirrly Terms%s", _SQ_PLUGIN_NAME_), '<a href="https://www.squirrly.co/terms-of-use#page-block-lvtsrakgujgzxgvi" target="_blank" >', '</a>'); ?>
                                        </span>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <div class="withborder">
                                    <p>
                                        <strong>Find out who we are, Contact our Squirrly team and See Our Company Details</strong>
                                    </p>
                                    <a href="https://www.squirrly.co/more" target="_blank"><img src="<?php echo _SQ_THEME_URL_ . 'img/settings/team.png' ?>" alt="Squirrly Team" style="max-width: 520px;"/></a>
                                </div>
                            </li>

                            <li>
                                <div class="withborder">
                                    <p><strong>Squirrly is registered in the UK as:</strong></p>

                                    <p>Squirrly Limited</p>
                                    <p>Company registration number: <strong>08198658</strong></p>
                                    <p>Incorporation Date: <strong>03 Sept 2012</strong></p>
                                </div>
                                <div class="withborder">
                                    <p><strong>Registered Address for UK:</strong></p>
                                    <p>20-22 Wenlock Road</p>
                                    <p>London, N1 7GU</p>
                                    <p>England</p>
                                </div>
                            </li>

                        </ul>
                    </div>

                </div>
            </fieldset>

        </div>
    </div>

</div>
