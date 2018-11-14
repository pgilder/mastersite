<div id="sq_settings">
    <?php SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init(); ?>
    <?php if (SQ_Classes_Tools::getOption('sq_api') == '') { ?>
        <span class="sq_icon"></span>

        <div id="sq_settings_title"><?php _e('Connect to Squirrly Data Cloud', _SQ_PLUGIN_NAME_); ?> </div>
        <div id="sq_settings_login">
            <?php SQ_Classes_ObjController::getClass('SQ_Core_Blocklogin')->init(); ?>

            <div class="sq_settings_backup" style="text-align: center; margin: 0 auto !important;">
                <input type="button" class="sq_button sq_restore" name="sq_restore" value="<?php _e('Restore Squirrly Settings', _SQ_PLUGIN_NAME_) ?>"/>
            </div>

            <div class="sq_settings_restore sq_popup" style="display: none">
                <span class="sq_close">x</span>
                <span><?php _e('Upload the file with the saved Squirrly Settings', _SQ_PLUGIN_NAME_) ?></span>
                <form action="#" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="sq_restore"/>
                    <input type="file" name="sq_options" id="favicon" style="float: left;"/>
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                    <input type="submit" style="margin-top: 10px;" class="sq_button" name="sq_restore" value="<?php _e('Restore Backup', _SQ_PLUGIN_NAME_) ?>"/>
                </form>
            </div>
        </div>


        <div class="sq_login_link" style="margin-top: 50px;"><?php _e('Connect to Squirrly and start optimizing your site', _SQ_PLUGIN_NAME_); ?></div>
        <input id="sq_goto_dashboard" class="sq_goto_dashboard" style="display:none;  margin: 0 auto; width: 500px; padding: 0px 10px;" type="button" value="&laquo;<?php _e('START HERE', _SQ_PLUGIN_NAME_) ?> &raquo;"/>

        <?php
    } else {
        ?>
        <div>
            <span class="sq_icon"></span>
            <div id="sq_settings_title"><?php _e('Squirrly dashboard', _SQ_PLUGIN_NAME_); ?> </div>
        </div>

        <?php $menu = SQ_Classes_Tools::getOption('menu');
        if ($menu['show_tutorial']) { ?>
            <div id="sq_helpdashboardside" class="sq_helpside"></div>
            <div id="sq_helpdashboardcontent" class="sq_helpcontent"></div>
        <?php } ?>
    <?php } ?>

    <div class="sq_helpcontent" style="display: none; clear: left; <?php echo (SQ_Classes_Tools::getOption('sq_api') == '') ? 'text-align: center;' : '' ?>">
        <div style="width: 700px; display: inline-block;">
            <div style="font-size: 24px; margin: 30px 0 10px 0; color: #999; line-height: 30px;"><?php echo sprintf(__('%sHelp Center%s - learn more about Squirrly SEO features and unhinge your SEO potential', _SQ_PLUGIN_NAME_), '<strong>', '</strong>') ?></div>

            <div style="padding: 25px 0; font-weight: bold">
                <div class="sq_slidelist_next"><?php echo __("Next Feature") ?> &gt;&gt;</div>
                <div class="sq_slidelist_prev" style="display: none; ">&lt;&lt; <?php echo __("Previous Feature") ?></div>
            </div>

            <ul class="sq_slidelist">
                <li class="active">
                    <div>Integrate Themes and SEO Settings Seamlessly</div>
                    <a href="javascript:void(0);" rel="80734028" style="background-image: url('//image.slidesharecdn.com/importseo-171012105353/95/squirrly-seo-plugin-import-options-integrate-themes-and-seo-settings-seamlessly-1-638.jpg?cb=1507805679')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>The Best Keyword Research Tool for Non-SEO Experts</div>
                    <a href="javascript:void(0);" rel="80644963" style="background-image: url('//image.slidesharecdn.com/squirrlykeywordresearch2-171010101407/95/the-best-keyword-research-tool-for-nonseo-experts-1-638.jpg?cb=1507630478')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Get SEO Advice as You Write with Squirrly's SEO Virtual Assistant</div>
                    <a href="javascript:void(0);" rel="80645001" style="background-image: url('//image.slidesharecdn.com/squirrlyliveassistant1-171010101547/95/get-seo-advice-as-you-write-with-squirrlys-seo-virtual-assistant-1-638.jpg?cb=1507630585')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Inspiration Box by Squirrly SEO - Top-Tier Research Tools for Writers</div>
                    <a href="javascript:void(0);" rel="80644894" style="background-image: url('//image.slidesharecdn.com/inspirationbox-171010101117/95/inspiration-box-by-squirrly-seo-toptier-research-tools-for-writers-1-638.jpg?cb=1507630344')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Get to Know the Squirrly Snippet Tool</div>
                    <a href="javascript:void(0);" rel="80645060" style="background-image: url('//image.slidesharecdn.com/squirrlysnippettool-171010101724/95/get-to-know-the-squirrly-snippet-tool-1-638.jpg?cb=1507630707')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Probably the Most User-Friendly Google SERP Checker in the World</div>
                    <a href="javascript:void(0);" rel="80733633" style="background-image: url('//image.slidesharecdn.com/squirrlyperformanceanalytics-171012104214/95/probably-the-most-userfriendly-google-serp-checker-in-the-world-1-638.jpg?cb=1507804989')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Squirrly's First Page Optimization Option Is Perfect for Non-Expert Users</div>
                    <a href="javascript:void(0);" rel="80733530" style="background-image: url('//image.slidesharecdn.com/squirrlyfirstpageoptimization-171012103902/95/squirrlys-first-page-optimization-option-is-perfect-for-nonexpert-users-1-638.jpg?cb=1507804770')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>The Social Media SEO Trick to Get More Traffic via Social Networks</div>
                    <a href="javascript:void(0);" rel="80733182" style="background-image: url('//image.slidesharecdn.com/squirrlyopengraphandtwittercard-171012102916/95/the-social-media-seo-trick-to-get-more-traffic-via-social-networks-1-638.jpg?cb=1507804284')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Squirrly SEO Plugin's Custom Patterns for Post Types in Wordpress</div>
                    <a href="javascript:void(0);" rel="80599015" style="background-image: url('//image.slidesharecdn.com/patterns-171009083900/95/squirrly-seo-plugins-custom-patterns-for-post-types-in-wordpress-1-638.jpg?cb=1507538812')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Squirrly's Check for SEO Errors Option Is a Website's Must-Have</div>
                    <a href="javascript:void(0);" rel="80733444" style="background-image: url('//image.slidesharecdn.com/squirrlycheckforseoerrors-171012103617/95/squirrlys-check-for-seo-errors-option-is-a-websites-musthave-1-638.jpg?cb=1507804631')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Squirrly SEO Plugin + Sitemap Generator = A Rank-Boosting Combo</div>
                    <a href="javascript:void(0);" rel="80733936" style="background-image: url('//image.slidesharecdn.com/squirrlysitemapxml-171012105128/95/squirrly-seo-plugin-sitemap-generator-a-rankboosting-combo-1-638.jpg?cb=1507805523')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Squirrly SEO Plugin's Favicon Settings</div>
                    <a href="javascript:void(0);" rel="80733492" style="background-image: url('//image.slidesharecdn.com/squirrlyfavicon-171012103752/95/squirrly-seo-plugins-favicon-settings-1-638.jpg?cb=1507804715')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>JSON LD, one of the best Semantic SEO markup solutions</div>
                    <a href="javascript:void(0);" rel="80733592" style="background-image: url('//image.slidesharecdn.com/squirrlyjson-ldstructureddata-171012104102/95/json-ld-one-of-the-best-semantic-seo-markup-solutions-1-638.jpg?cb=1507804894')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Control Ecommerce Social Media Metrics with Squirrly SEO Plugin</div>
                    <a href="javascript:void(0);" rel="80733987" style="background-image: url('//image.slidesharecdn.com/squirrlytrackingtools-171012105250/85/control-ecommerce-social-media-metrics-with-squirrly-seo-plugin-1-320.jpg?cb=1507805609')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Squirrly SEO Plugin's Settings for Posts and Pages</div>
                    <a href="javascript:void(0);" rel="80733727" style="background-image: url('//image.slidesharecdn.com/squirrlysettingsforpostsandpages-171012104449/95/squirrly-seo-plugins-settings-for-posts-and-pages-1-638.jpg?cb=1507805126')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Learn How to Use Squirrly's Google Rank Option</div>
                    <a href="javascript:void(0);" rel="80733569" style="background-image: url('//image.slidesharecdn.com/squirrlygooglerankoption-171012104002/95/learn-how-to-use-squirrlys-google-rank-option-1-638.jpg?cb=1507804834')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>How to Use Squirrly's Measure Your Success Option</div>
                    <a href="javascript:void(0);" rel="80733353" style="background-image: url('//image.slidesharecdn.com/measureyoursuccess1-171012103344/95/how-to-use-squirrlys-measure-your-success-option-1-638.jpg?cb=1507804478')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>How to Check Your robots.txt File from Squirrly SEO Plugin</div>
                    <a href="javascript:void(0);" rel="80733693" style="background-image: url('//image.slidesharecdn.com/squirrlyrobots-171012104338/95/how-to-check-your-robotstxt-file-from-squirrly-seo-plugin-1-638.jpg?cb=1507805056')"></a>
                    <div class="sq_playpause"></div>
                </li>
                <li>
                    <div>Read All About The Site Performance Audit From Squirrly SEO Plugin</div>
                    <a href="javascript:void(0);" rel="80733758" style="background-image: url('//image.slidesharecdn.com/squirrlysiteaudit-171012104554/85/read-all-about-the-site-performance-audit-from-squirrly-seo-plugin-1-320.jpg?cb=1507805205')"></a>
                    <div class="sq_playpause"></div>
                </li>
            </ul>

            <div style="padding: 25px 0; font-weight: bold">
                <div class="sq_slidelist_next"><?php echo __("Next Feature") ?> &gt;&gt;</div>
                <div class="sq_slidelist_prev" style="display: none">&lt;&lt; <?php echo __("Previous Feature") ?></div>
            </div>
        </div>

    </div>
</div>
</div>
