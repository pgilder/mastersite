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
        <div class="col-md-12 no-t-m m-b-lg no-p">
            <div class="panel panel-transparent">
                <div class="panel-heading">
                    <span class="sq_briefcase_icon"></span>
                    <div id="sq_posts_title">
                        <?php if (SQ_Classes_Tools::getIsset('skeyword')) { ?>
                            <div class="sq_serp_settings_button m-t-sm" style="float: right;  margin-right: 10px;">
                                <button type="button" class="btn btn-info p-v-xs" onclick="location.href = '?page=sq_briefcase';" style="cursor: pointer"><?php echo __('Show All') ?></button>
                            </div>
                        <?php } ?>

                        <?php _e('Briefcase Keywords', _SQ_PLUGIN_NAME_); ?>

                        <?php if (!SQ_Classes_Tools::getOption('sq_google_serp_active')) { ?>
                            <?php if (isset($view->keywords) && !empty($view->keywords)) { ?>
                                <a href="<?php echo SQ_Classes_Tools::getBusinessLink() ?>" class="btn btn-warning"><?php _e('See the Google Ranks for these Keywords', _SQ_PLUGIN_NAME_); ?></a>
                            <?php } ?>
                        <?php } else { ?>
                            <a href="?page=sq_posts" class="btn btn-default"><?php _e('Go to  Analytics', _SQ_PLUGIN_NAME_); ?></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="panel-body">

                    <div class="col-md-12">
                        <form method="get" style="float: right">
                            <input type="search" id="post-search-input" autofocus name="skeyword" value="<?php echo htmlspecialchars(SQ_Classes_Tools::getValue('skeyword')) ?>"/>
                            <input type="hidden" name="page" value="<?php echo SQ_Classes_Tools::getValue('page', 'sq_briefcase') ?>"/>
                            <input type="submit" class="button" value="<?php echo __('Search Keyword', _SQ_PLUGIN_NAME_) ?>"/>
                            <div class="sq_filter_label" style="clear: both; margin: 3px 0; max-width: 400px;">
                                <span style="float: left;font-size: 11px;margin-right: 5px; line-height: 22px; cursor: pointer" onclick="jQuery('.sq_add_labels_dialog').modal('show')"><?php echo __('Filter by labels', _SQ_PLUGIN_NAME_) ?>:</span>
                                <?php if (isset($view->labels) && !empty($view->labels)) {
                                    $keyword_labels = SQ_Classes_Tools::getValue('slabel', array());
                                    foreach ($view->labels as $label) {
                                        ?>
                                        <input type="checkbox" name="slabel[]" id="search_checkbox_<?php echo $label->id ?>" style="display: none;" value="<?php echo $label->id ?>" <?php echo(in_array($label->id, $keyword_labels) ? 'checked' : '') ?> />
                                        <label for="search_checkbox_<?php echo $label->id ?>" class="sq_circle_label fa <?php echo(in_array($label->id, $keyword_labels) ? 'sq_active' : '') ?>" data-id="<?php echo $label->id ?>" style="background-color: <?php echo $label->color ?>" title="<?php echo $label->name ?>"></label>
                                        <?php

                                    }
                                } ?>
                            </div>
                        </form>

                        <button class="btn btn-success" onclick="jQuery('.sq_add_keyword_dialog').modal('show')"><?php _e('Add new keyword', _SQ_PLUGIN_NAME_); ?></button>
                        <button class="btn btn-warning" onclick="jQuery('.sq_add_labels_dialog').modal('show')"><?php _e('Manage Labels', _SQ_PLUGIN_NAME_); ?></button>
                    </div>

                    <div class="col-md-12 m-b-lg">
                        <div class="sq_add_keyword_dialog modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?php _e('Add a new Keyword', _SQ_PLUGIN_NAME_); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="sq_keyword"><?php _e('Keyword', _SQ_PLUGIN_NAME_); ?></label>
                                            <input type="text" class="form-control" id="sq_keyword" placeholder="<?php echo __('Enter a Keyword (2-4 words)', _SQ_PLUGIN_NAME_) ?>"/>
                                        </div>
                                        <div class="form-group m-t-md">
                                            <label style="display: block; margin-right: 5px;line-height: 25px;"><?php _e('Select Labels', _SQ_PLUGIN_NAME_); ?></label>
                                            <?php if (isset($view->labels) && !empty($view->labels)) {
                                                foreach ($view->labels as $label) {
                                                    ?>
                                                    <input type="checkbox" name="sq_labels" id="checkbox_<?php echo $label->id ?>" style="display: none;" value="<?php echo $label->id ?>"/>
                                                    <label for="checkbox_<?php echo $label->id ?>" class="sq_checkbox_label fa" data-id="<?php echo $label->id ?>" style="background-color: <?php echo $label->color ?>" title="<?php echo $label->name ?>"><?php echo $label->name ?></label>
                                                    <?php
                                                }
                                            } else { ?>
                                                <button class="btn btn-warning" onclick="jQuery('.sq_add_labels_dialog').modal('show')"><?php _e('Add new Label', _SQ_PLUGIN_NAME_); ?></button>

                                            <?php } ?>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="sq_save_keyword" class="btn btn-success"><?php _e('Add Keyword', _SQ_PLUGIN_NAME_); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sq_add_labels_dialog modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?php _e('Add New Label', _SQ_PLUGIN_NAME_); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="sq_labelname"><?php _e('Label Name', _SQ_PLUGIN_NAME_); ?></label>
                                            <input type="text" class="form-control" id="sq_labelname" maxlength="35"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="sq_labelcolor" style="display: block;"><?php _e('Label Color', _SQ_PLUGIN_NAME_); ?></label>
                                            <input type="text" id="sq_labelcolor"/>
                                        </div>


                                    </div>
                                    <div class="modal-footer" style="border-bottom: 1px solid #ddd;">
                                        <button type="button" id="sq_save_label" class="btn btn-success"><?php _e('Add Label', _SQ_PLUGIN_NAME_); ?></button>
                                    </div>
                                    <div class="modal-header">
                                        <h4 class="modal-title"><?php _e('Your Labels', _SQ_PLUGIN_NAME_); ?></h4>
                                    </div>
                                    <div class="modal-body" style="min-height: 50px; display: table; width: 100%;">
                                        <?php if (isset($view->labels) && !empty($view->labels)) {
                                            foreach ($view->labels as $label) {
                                                ?>
                                                <div class="sq_saved_label" style="background-color: <?php echo $label->color ?>" title="<?php echo $label->name ?>">
                                                    <span class="sq_delete_label fa fa-remove" data-id="<?php echo $label->id ?>"></span>
                                                    <span class="sq_edit_label fa fa-pencil" data-id="<?php echo $label->id ?>" data-name="<?php echo $label->name ?>" data-color="<?php echo $label->color ?>"></span>
                                                    <span class="sq_saved_label_name"><?php echo $label->name ?></span>

                                                </div>
                                                <?php
                                            }
                                        } ?>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="sq_edit_label_dialog modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?php _e('Edit Label', _SQ_PLUGIN_NAME_); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="sq_labelname"><?php _e('Label Name', _SQ_PLUGIN_NAME_); ?></label>
                                            <input type="text" class="form-control" id="sq_labelname" maxlength="35"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="sq_labelcolor"><?php _e('Label Color', _SQ_PLUGIN_NAME_); ?></label>
                                            <input type="text" id="sq_labelcolor"/>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" id="sq_labelid"/>
                                        <button type="button" id="sq_save_label" class="btn btn-success"><?php _e('Save Label', _SQ_PLUGIN_NAME_); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tablenav top">
                            <?php echo $view->listTable->pagination('top') ?>
                        </div>
                        <div class="panel panel-white">

                            <div class="panel-body">
                                <?php if (isset($view->keywords) && !empty($view->keywords)) { ?>

                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 40%;"><?php echo __('Keyword', _SQ_PLUGIN_NAME_) ?></th>
                                            <th style="width: 5%;" align="right"><?php echo __('Used', _SQ_PLUGIN_NAME_) ?></th>
                                            <th style="width: 37%;"><?php echo __('Data', _SQ_PLUGIN_NAME_) ?></th>
                                            <th style="width: 8%;"></th>
                                            <th style="width: 2%;"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php


                                        foreach ($view->keywords as $key => $row) {

                                            $research = '';
                                            $keyword_labels = array();
                                            if ($row->research <> '') {
                                                $research = json_decode($row->research);
                                            }
                                            ?>
                                            <tr id="sq_row_<?php echo $row->id ?>">
                                                <td scope="row"><?php echo($view->index + $key + 1) ?></td>
                                                <td>
                                                    <?php if (!empty($row->labels)) {
                                                        foreach ($row->labels as $label) {
                                                            $keyword_labels[] = $label->lid;
                                                            ?>
                                                            <span class="sq_circle_label fa" style="background-color: <?php echo $label->color ?>" data-id="<?php echo $label->lid ?>" title="<?php echo $label->name ?>"></span>
                                                            <?php
                                                        }
                                                    } ?>
                                                    <span style="display: block; clear: left; float: left"><?php echo $row->keyword ?></span>

                                                    <span onclick="jQuery('#sq_label_manage_popup<?php echo $key ?>').modal('show')" title="<?php _e('Assign Labels to this Keyword', _SQ_PLUGIN_NAME_); ?>" class="sq_label_manage fa fa-plus-square"></span>
                                                    <div id="sq_label_manage_popup<?php echo $key ?>" class="sq_label_manage_popup modal fade" role="dialog">
                                                        <div class="modal-dialog" style="width: 600px;">
                                                            <div class="modal-content">
                                                                <div class="modal-header m-b-md">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title"><?php echo sprintf(__('Select Labels for: %s', _SQ_PLUGIN_NAME_), '<strong style="font-size: 115%">' . $row->keyword . '</strong>'); ?></h4>
                                                                </div>
                                                                <div class="modal-body" style="min-height: 50px; display: table; margin: 0 20px 10px 20px;">
                                                                    <?php if (isset($view->labels) && !empty($view->labels)) {
                                                                        foreach ($view->labels as $label) {
                                                                            ?>
                                                                            <input type="checkbox" name="sq_labels" id="popup_checkbox_<?php echo $key ?>_<?php echo $label->id ?>" style="display: none;" value="<?php echo $label->id ?>" <?php echo(in_array($label->id, $keyword_labels) ? 'checked' : '') ?> />
                                                                            <label for="popup_checkbox_<?php echo $key ?>_<?php echo $label->id ?>" class="sq_checkbox_label fa <?php echo(in_array($label->id, $keyword_labels) ? 'sq_active' : '') ?>" style="background-color: <?php echo $label->color ?>" title="<?php echo $label->name ?>"><?php echo $label->name ?></label>
                                                                            <?php
                                                                        }

                                                                    } else { ?>
                                                                        <button class="btn btn-warning" onclick="jQuery('.sq_add_labels_dialog').modal('show')" data-dismiss="modal"><?php _e('Add new Label', _SQ_PLUGIN_NAME_); ?></button>

                                                                    <?php } ?>
                                                                </div>
                                                                <?php if (isset($view->labels) && !empty($view->labels)) { ?>
                                                                    <div class="modal-footer">
                                                                        <button data-id="<?php echo $row->id ?>" class="sq_save_keyword_labels btn btn-success"><?php _e('Save Labels', _SQ_PLUGIN_NAME_); ?></button>
                                                                    </div>
                                                                <?php } ?>

                                                            </div>
                                                        </div>

                                                    </div>
                                                </td>

                                                <td class="sq_open_subrow" align="right" style="cursor: pointer" data-id="<?php echo $row->id ?>" data-keyword="<?php echo htmlspecialchars($row->keyword) ?>"><?php echo($row->count > 0 ? $row->count . ' <i class="fa fa-sort-desc fa_showmore" style="color: rgb(88, 158, 228)"></i>' : '0') ?> </td>
                                                <td>
                                                    <ul class="sq_row_stats"><?php
                                                        if ($research <> '') {
                                                            if ($research->sc) {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-users" style="color: <?php echo $research->sc->color ?>" title="<?php echo __('Competition', _SQ_PLUGIN_NAME_) ?>"></i>
                                                                    <span style="color: <?php echo $research->sc->color ?>" title="<?php echo __('Competition', _SQ_PLUGIN_NAME_) ?>"><?php echo($research->sc->text <> '' ? $research->sc->text : __('-', _SQ_PLUGIN_NAME_)) ?></span>
                                                                </li>
                                                                <?php
                                                            }
                                                            if ($research->sv) {
                                                                if (isset($research->sv->absolute) && is_numeric($research->sv->absolute)) {
                                                                    $research->sv->absolute = number_format($research->sv->absolute, 0, '', '.');
                                                                }
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-search" style="color: <?php echo $research->sv->color ?>" title="<?php echo __('SEO Search Volume', _SQ_PLUGIN_NAME_) ?>"></i>
                                                                    <span style="color: <?php echo $research->sv->color ?>" title="<?php echo __('SEO Search Volume', _SQ_PLUGIN_NAME_) ?>"><?php echo(isset($research->sv->absolute) ? $research->sv->absolute : __('-', _SQ_PLUGIN_NAME_)) ?></span>
                                                                </li>
                                                                <?php
                                                            }
                                                            if ($research->tw) {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-comments-o" style="color: <?php echo $research->tw->color ?>" title="<?php echo __('Recent discussions', _SQ_PLUGIN_NAME_) ?>"></i>
                                                                    <span style="color: <?php echo $research->tw->color ?>" title="<?php echo __('Recent discussions', _SQ_PLUGIN_NAME_) ?>"><?php echo($research->tw->text <> '' ? $research->tw->text : __('-', _SQ_PLUGIN_NAME_)) ?></span>
                                                                </li>
                                                                <?php
                                                            }
                                                            if ($research->td) {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-bar-chart" style="color: <?php echo $research->td->color ?>" title="<?php echo __('Trending', _SQ_PLUGIN_NAME_) ?>"></i>
                                                                    <span style="color: <?php echo $research->td->color ?>" title="<?php echo __('Trending', _SQ_PLUGIN_NAME_) ?>"><?php echo($research->td->text <> '' ? $research->td->text : __('-', _SQ_PLUGIN_NAME_)) ?></span>
                                                                </li>
                                                                <?php
                                                            }

                                                            ?>
                                                            <li>(<?php echo($row->country ? $row->country : 'us') ?>)</li>
                                                            <li>
                                                                <i class="fa fa-refresh" style="cursor: pointer" onclick="jQuery('.sq_kr_countries').modal('show'); jQuery('#sq_kr_keyword').html(jQuery(this).data('keyword')); jQuery('input[name=sq_kr_keyword]').val(jQuery(this).data('keyword'));" data-dismiss="modal" data-keyword="<?php echo htmlspecialchars($row->keyword) ?>" title="<?php echo __('Refresh Keyword Research', _SQ_PLUGIN_NAME_); ?>"></i>
                                                            </li>

                                                        <?php } else { ?>
                                                            <li>
                                                                <button class="btn btn-success" onclick="jQuery('.sq_kr_countries').modal('show'); jQuery('#sq_kr_keyword').html(jQuery(this).data('keyword')); jQuery('input[name=sq_kr_keyword]').val(jQuery(this).data('keyword'));" data-dismiss="modal" data-keyword="<?php echo htmlspecialchars($row->keyword) ?>"><?php echo __('Do a research', _SQ_PLUGIN_NAME_); ?></button>
                                                            </li>
                                                            <?php
                                                        }

                                                        //echo $row->research
                                                        ?></ul>
                                                </td>
                                                <td>
                                                    <button class="btn btn-info sq_research_selectit" data-post="<?php echo admin_url('post-new.php') ?>" data-keyword="<?php echo htmlspecialchars($row->keyword) ?>"><?php echo __('Use Keyword', _SQ_PLUGIN_NAME_) ?></button>
                                                </td>
                                                <td style="position: relative">
                                                    <button type="button" class="btn btn-default sq_delete_keyword" style="position: absolute; left: -10px;" data-id="<?php echo $row->id ?>" title="<?php _e('Delete', _SQ_PLUGIN_NAME_); ?>">x</button>
                                                </td>
                                            </tr>
                                            <tr id="sq_subrow_<?php echo $row->id ?>" class="sq_subrow" style="display: none">
                                                <td colspan="6" style="padding: 0 !important;"></td>
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

                                <div class="sq_kr_countries modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title"><?php _e('Keyword Research', _SQ_PLUGIN_NAME_); ?>:
                                                    <span style="font-weight: bold; font-size: 110%" id="sq_kr_keyword"></span>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <label for="sq_kr_countries_list"><?php _e('Select Country', _SQ_PLUGIN_NAME_); ?></label>
                                                <ul id="sq_kr_countries_list" class="sq_research_lang">
                                                    <?php echo $view->countries ?>
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" id="sq_doresearch" class="btn btn-success "><?php _e('Do Research', _SQ_PLUGIN_NAME_); ?></button>
                                                <input type="hidden" name="sq_kr_keyword">
                                                <input type="hidden" name="sq_kr_country">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tablenav bottom">
                            <?php echo $view->listTable->pagination('bottom') ?>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
<?php } ?>