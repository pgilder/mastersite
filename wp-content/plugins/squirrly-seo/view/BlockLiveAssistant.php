<div id="sq_settings">
    <div class="sq_message sq_error" style="display: none"></div>

    <?php
    SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init();
    ?>
    <div>
        <span class="sq_icon"></span>
        <div id="sq_settings_title"><?php _e('Squirrly Live Assistant', _SQ_PLUGIN_NAME_); ?> </div>
        <div class="sq_subtitles">
            <p><?php echo __('Using the Live Assistant from Squirrly SEO is like having a consultant sitting right next to you. It helps you get a 100% optimized page for both Humans and Search Engines.', _SQ_PLUGIN_NAME_) ?></p>
        </div>
    </div>
    <div id="sq_helpliveassistantside" class="sq_helpside"></div>
    <div id="sq_left">
        <div id="sq_settings_body">

            <fieldset style="background: none !important; box-shadow: none;">
                <div class="sq_subtitles">
                    <p><?php echo __('You just have to type in the keyword you want the page to be optimized for.', _SQ_PLUGIN_NAME_) ?></p>
                    <p>
                        <iframe src="//www.slideshare.net/slideshow/embed_code/key/B8cE31uYNptTYm" width="700" height="565" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="border:1px solid #CCC; border-width:1px; margin-bottom:5px; max-width: 100%;" allowfullscreen></iframe>
                    </p>
                    <div class="sq_button">
                        <a href="post-new.php" target="_blank" style="margin-top: 10px; font-size: 15px; max-width: 210px;"><?php _e('Use Squirrly Live Assistant', _SQ_PLUGIN_NAME_) ?></a>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

</div>
