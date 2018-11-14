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
    $date_format = get_option('date_format');
    $time_format = get_option('time_format');
    $timezone = (int)get_option('gmt_offset');
    $last_check = (int)SQ_Classes_Tools::getOption('sq_google_last_checked');
    //Load the summary saved from server
    $info = json_decode(SQ_Classes_Tools::getOption('sq_google_last_info'));
    ?>
    <div id="src_settings">
        <div class="col-md-12 m-b-xs no-p">
            <div class="panel panel-transparent m-t-md">
                <div class="panel-body">
                    <div class="col-md-12">
                        <span class="sq_icon"></span>

                        <div class="sq_serp_settings_button m-t-sm" style="float: right">
                            <button type="button" class="btn btn-default sq_serp_settings p-v-xs" style="cursor: pointer"><?php _e('Settings', _SQ_PLUGIN_NAME_); ?></button>
                        </div>

                        <?php if ($last_check > strtotime('-12 hours')) { ?>
                            <div class="sq_serp_update_button m-t-sm" style="float: right; margin-right: 10px;">
                                <button type="button" class="btn btn-default sq_serp_refresh p-v-xs" style="cursor: pointer"><?php _e('Update ranks', _SQ_PLUGIN_NAME_); ?></button>
                            </div>
                        <?php } ?>

                        <?php if (SQ_Classes_Tools::getIsset('schanges') ||
                            SQ_Classes_Tools::getIsset('ranked') ||
                            SQ_Classes_Tools::getIsset('rank') ||
                            SQ_Classes_Tools::getIsset('keyword') ||
                            SQ_Classes_Tools::getIsset('type') ||
                            SQ_Classes_Tools::getValue('skeyword', '')
                        ) { ?>
                            <div class="sq_serp_settings_button m-t-sm" style="float: right;  margin-right: 10px;">
                                <button type="button" class="btn btn-info p-v-xs" onclick="location.href = '?page=sq_posts';" style="cursor: pointer"><?php echo __('Show All') ?></button>
                            </div>
                        <?php } ?>

                        <div id="sq_posts_title">
                            <?php _e('Advanced Analytics (Business Level)', _SQ_PLUGIN_NAME_); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-md-offset-4 sq_serp_progress" style="display: none">
                <div class="panel info-box panel-white">
                    <div class="panel-body">
                        <div class="info-box-progress">
                            <div class="progress progress-xs progress-squared bs-n">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6  col-md-offset-4 sq_serp_update">
                <div class="panel panel-transparent" style="margin-bottom: 0;">
                    <div class="panel-body">
                        <?php if ($view->getKeywordsFound() == 0 && $last_check <= strtotime('-5 minutes')) { ?>
                            <div class="m-b-xs m-t-md text-center">
                                <button type="button" class="btn btn-success sq_serp_sendnow p-v-xs" style="cursor: pointer"><?php _e('Get Ranks and found Keywords from Squirrly Cloud', _SQ_PLUGIN_NAME_); ?></button>
                            </div>
                        <?php } elseif ((defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) && $last_check < strtotime('-1 hours')) { ?>
                            <div class="m-b-xs m-t-md text-center">
                                <button type="button" class="btn btn-success sq_serp_sendnow p-v-xs" style="cursor: pointer"><?php _e('Get Ranks from Squirrly Cloud', _SQ_PLUGIN_NAME_); ?></button>
                            </div>
                        <?php } elseif ($last_check < strtotime('-12 hours')) { ?>
                            <div class="m-b-xs m-t-md text-center">
                                <button type="button" class="btn btn-success sq_serp_sendnow p-v-xs" style="cursor: pointer"><?php _e('Get Ranks from Squirrly Cloud', _SQ_PLUGIN_NAME_); ?></button>
                            </div>
                        <?php } ?>

                        <?php if (SQ_Classes_Tools::getOption('sq_google_last_checked') > 0) { ?>
                            <div class="m-b-md text-center">
                                <span>
                                    <?php echo __('Last Update', _SQ_PLUGIN_NAME_) . ': <strong>' . date($date_format . ' ' . $time_format, $last_check + $timezone * HOUR_IN_SECONDS) . '</strong>'; ?>
                                </span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 no-m no-p">
            <div class="panel panel-transparent">
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="panel info-box panel-white">
                                    <div class="panel-body">
                                        <div class="info-box-stats">
                                            <a href="<?php echo esc_url(add_query_arg(array('page' => 'sq_posts', 'tab' => 'keywords'), admin_url('admin.php'))) ?>" data-toggle="tooltip" title="<?php echo __('Found Keywords', _SQ_PLUGIN_NAME_) ?>" style="float: left">
                                                <i class="fa fa-search fa-2x pull-left" aria-hidden="true"></i>
                                            </a>
                                            <p class="counter">
                                                <a href="<?php echo esc_url(add_query_arg(array('page' => 'sq_posts', 'tab' => 'keywords'), admin_url('admin.php'))) ?>" data-toggle="tooltip" title="<?php echo __('Found Keywords', _SQ_PLUGIN_NAME_) ?>">
                                                    <?php echo(isset($info->keywords) ? $info->keywords : $view->getKeywordsFound()) ?>
                                                </a>
                                            </p>
                                            <span class="info-box-title" style="padding: 5px 0 0 0;">
                                                <?php echo sprintf(__('Found Keywords (%sSee all keywords%s)', _SQ_PLUGIN_NAME_), '<a href="' . esc_url(add_query_arg(array('page' => 'sq_posts', 'tab' => 'keywords'), admin_url('admin.php'))) . '" data-toggle="tooltip" title="' . __('Show the keywords found with Advanced Analytics', _SQ_PLUGIN_NAME_) . '" style="font-weight: bold;">', '</a>'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="panel info-box panel-white">
                                    <div class="panel-body" style="padding: 20px 15px">
                                        <div class="info-box-stats col-md-5 no-p" style="padding:0 5px 0 0;">
                                            <p class="counter">
                                                <?php
                                                if (isset($info->average_week) && !empty($info->average_week)) {
                                                    $today_average = end($info->average_week);
                                                    $today_average = number_format((int)$today_average[1], 2);
                                                    reset($info->average_week);
                                                } else {
                                                    $today_average = '0';
                                                }

                                                ?>
                                                <a href="<?php echo esc_url(add_query_arg(array('page' => 'sq_posts', 'ranked' => 1), admin_url('admin.php'))) ?>" data-toggle="tooltip" title="<?php echo __('Show only the ranked articles', _SQ_PLUGIN_NAME_) ?>">
                                                    <i class="fa fa-line-chart pull-left" aria-hidden="true"></i>
                                                    <?php echo $today_average ?>
                                                </a>
                                            </p>
                                            <span class="info-box-title" style="display: block; clear: both; padding: 5px 0 0 0;"><?php _e('Today Avg. Ranking', _SQ_PLUGIN_NAME_); ?></span>
                                        </div>
                                        <?php
                                        if (isset($info->average_week) && count($info->average_week) > 1) {
                                            echo $view->getScripts();
                                            foreach ($info->average_week as $key => $average) {
                                                if ($key > 0 && !empty($info->average_week[$key])) {
                                                    $info->average_week[$key][0] = date('d/m/Y', strtotime($info->average_week[$key][0]));
                                                    $info->average_week[$key][1] = (float)$info->average_week[$key][1];
                                                    if ($info->average_week[$key][1] == 0) {
                                                        $info->average_week[$key][1] = 100;
                                                    }
                                                }
                                                $average[1] = (int)$average[1];
                                            }

                                            echo '
                                                <div class="col-md-7" style="padding: 2px 0; border-left: 1px solid #eee; border-bottom: 1px solid #eee;">
                                                    <div id="sq_chart" class="sq_chart no-p" style="width:100%; height: 70px;"></div><script>var sq_chart_val = drawChart("sq_chart", ' . json_encode($info->average_week) . ' ,true); </script>
                                                </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="panel info-box panel-white">
                                    <div class="panel-body">
                                        <div class="info-box-stats">
                                            <p class="counter">
                                                <a href="<?php echo esc_url(add_query_arg(array('page' => 'sq_posts', 'schanges' => 1), admin_url('admin.php'))) ?>" data-toggle="tooltip" title="<?php echo __('Show only the SERP changes', _SQ_PLUGIN_NAME_) ?>">
                                                    <i class="fa fa-arrows-v pull-left" aria-hidden="true"></i>
                                                    <?php echo $view->listTable->getChanges() ?>
                                                </a>
                                            </p>
                                            <span class="info-box-title" style="padding: 5px 0 0 0;"><?php _e('Today SERP Changes', _SQ_PLUGIN_NAME_); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-transparent">
                <div class="panel-body">
                    <div class="col-md-12">
                        <?php echo SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->getNavigationTop() ?>
                        <table class="wp-list-table widefat fixed posts" cellspacing="0">
                            <thead>
                            <tr>
                                <?php echo SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->getHeaderColumns() ?>
                            </tr>
                            </thead>

                            <tfoot>
                            <tr>
                                <?php echo SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->getHeaderColumns() ?>
                            </tr>
                            </tfoot>
                            <tbody id="the-list">
                            <?php echo SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->getRows() ?>
                            </tbody>
                        </table>
                        <?php echo SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->getNavigationBottom() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>