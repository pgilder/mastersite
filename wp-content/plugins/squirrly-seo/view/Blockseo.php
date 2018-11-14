<?php
$auto_option = false;
if (SQ_Classes_Tools::getUserMeta('sq_auto_sticky')) $auto_option = true;
?>
<div class="sq_box" style="display: none">
    <div id="sq_blockseo" style="display: none">
        <div class="sq_header"><?php _e('Squirrly Live Assistant', _SQ_PLUGIN_NAME_); ?>

            <span id="sq_seo_refresh">
                <?php echo __('Update', _SQ_PLUGIN_NAME_) ?>
            </span>
            <div style="float: right">
                <div class="sq_auto_sticky" title="<?php echo __('Split Window', _SQ_PLUGIN_NAME_) ?>">
                    <input id="sq_auto_sticky1" type="checkbox" name="sq_auto_sticky" value="1" <?php echo($auto_option ? "checked" : '') ?> />
                    <label for="sq_auto_sticky1"><span class="sq_switch_img"></label>
                </div>
            </div>
        </div>

        <div class="sq_tasks"></div>
    </div>
</div>
<style>

</style>