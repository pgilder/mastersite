<div id="src_settings">
    <div class="col-md-12 no-t-m m-b-lg no-p">
        <div class="panel panel-transparent">
            <div class="panel-heading">
                <div id="sq_posts_title">
                    <span class="sq_icon"></span>
                    <?php _e('Advanced Analytics Keywords', _SQ_PLUGIN_NAME_); ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-md-12 m-b-md">
                    <button class="btn btn-success" data-toggle="modal" data-target=".sq_add_keyword"><?php _e('Add new keyword', _SQ_PLUGIN_NAME_); ?></button>
                    <a href="?page=sq_posts" class="btn btn-default"><?php _e('Go to Analytics', _SQ_PLUGIN_NAME_); ?></a>
                </div>

                <div class="col-md-12 m-b-lg">
                    <div class="sq_add_keyword modal fade" role="dialog"
                    ">

                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <input type="hidden" name="action" value="sq_serp_addkeyword"/>
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title"><?php _e('Add a new Keyword', _SQ_PLUGIN_NAME_); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="sq_keyword"><?php _e('Keyword', _SQ_PLUGIN_NAME_); ?></label>
                                        <input type="text" required="required" class="form-control" id="sq_keyword" name="keyword" placeholder="<?php echo __('Enter a Keyword (2-4 words)', _SQ_PLUGIN_NAME_) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="sq_article"><?php _e('Article', _SQ_PLUGIN_NAME_); ?></label>
                                        <input type="text" required="required" class="form-control" id="sq_article" name="article" placeholder="<?php echo __('Article URL ...', _SQ_PLUGIN_NAME_) ?>">
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="sq_save_keyword" class="btn btn-success"><?php _e('Add Keyword', _SQ_PLUGIN_NAME_); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <div class="tablenav top">
                    <?php echo $view->listTable->pagination('top') ?>
                </div>
                <div class="panel panel-white">
                    <div class="panel-body">
                        <?php

                        if (isset($view->keywords) && !empty($view->keywords)) { ?>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo __('Keyword', _SQ_PLUGIN_NAME_); ?></th>
                                    <th><?php echo __('Count', _SQ_PLUGIN_NAME_); ?></th>
                                    <th><?php echo __('Check the rank for it', _SQ_PLUGIN_NAME_); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php


                                foreach ($view->keywords as $key => $row) {
                                    if (!SQ_Classes_Tools::getOption('sq_google_show_ignored') && $row->ignore) {
                                        continue;
                                    }
                                    ?>
                                    <tr>
                                        <th scope="row"><?php echo($view->index + $key + 1) ?></th>
                                        <td><?php echo $row->keyword ?></td>
                                        <td><?php echo $row->count ?></td>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="checker col-md-12 ios-switch switch-sm m-b-xxs">
                                                    <div class="col-md-2 no-p">
                                                        <input type="checkbox" name="sq_keyword[]" data-keyword="<?php echo urlencode(addslashes($row->keyword)) ?>" class="js-switch pull-right ignore-keyword fixed-sidebar-check" style="display: none;" <?php echo(!$row->ignore ? 'checked="checked"' : '') ?> value="1"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>

                                </tbody>
                            </table>

                        <?php } else { ?>
                            <div class="panel-body">
                                <h3 class="text-center"><?php echo $view->error; ?></h3>

                                <div class="col-md-9 m-b-lg"></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="tablenav bottom">
                    <?php echo $view->listTable->pagination('bottom') ?>
                </div>

                <div class="col-md-2 b-t p-v-md no-p-h m-b-sm">
                    <form name="sq_form" method="POST">
                        <button type="submit" class="btn btn-success sq_serp_refresh" style="background-color: #589ee4;"><?php _e('Save Keywords', _SQ_PLUGIN_NAME_); ?></button>
                    </form>
                </div>

                <div class="col-md-6 b-t p-v-md no-p-h m-b-sm">
                    <form name="sq_form" method="POST">
                        <input type="hidden" name="action" value="sq_serp_showignore"/>
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>

                        <div class="checker ios-switch switch-md m-b-xxs p-v-xxs">
                            <input type="hidden" value="0" name="sq_show_ignored">

                            <div class="col-md-1 no-p">
                                <input type="checkbox" name="sq_show_ignored" class="js-switch pull-right fixed-sidebar-check" data-switchery="true" style="display: none;" <?php echo(SQ_Classes_Tools::getOption('sq_google_show_ignored') ? 'checked="checked"' : '') ?> value="1"/>
                            </div>
                            <div class="col-md-6 no-p-h"><?php _e('Show ignored keywords', _SQ_PLUGIN_NAME_); ?></div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="col-md-12 no-m">
    <div class="panel panel-transparent">
        <div class="panel-heading">
            <h2>
                <?php _e('Reload Google Ranks from Squirrly Server', _SQ_PLUGIN_NAME_); ?>
            </h2>
        </div>
        <div class="panel-body">
            <form method="POST">
                <input type="hidden" name="action" value="sq_serp_purgeall"/>
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>

                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="col-md-9">
                            <div class="src_option_content">
                                <button type="submit" class="btn btn-default"><?php _e('Remove Local Ranks', _SQ_PLUGIN_NAME_); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
</div>