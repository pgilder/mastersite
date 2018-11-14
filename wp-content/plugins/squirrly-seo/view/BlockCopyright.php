<div id="sq_settings">
    <div class="sq_message sq_error" style="display: none"></div>

    <?php
    SQ_Classes_ObjController::getClass('SQ_Core_BlockSupport')->init();
    ?>
    <div>
        <span class="sq_icon"></span>
        <div id="sq_settings_title"><?php _e('Squirrly Copywriting Options', _SQ_PLUGIN_NAME_); ?> </div>
        <div class="sq_subtitles">
            <p><?php echo __('Referencing other articles, ideas, and relevant Tweets adds value to your original content. This established journalist practice makes the content more trustworthy and helps readers shape a well-rounded understanding of the subject.', _SQ_PLUGIN_NAME_); ?></p>
        </div>
    </div>
    <div id="sq_helpcopyrightside" class="sq_helpside"></div>
    <div id="sq_left">
        <div id="sq_settings_body">

            <fieldset style="background: none !important; box-shadow: none;">
                <div class="sq_subtitles">
                    <p><?php echo __("The toolkit's intended purpose is to help you save time and find the best sources to include in your articles. ", _SQ_PLUGIN_NAME_); ?></p>
                    <p>
                        <iframe src="//www.slideshare.net/slideshow/embed_code/key/nFVwijbEFoUow" width="700" height="565" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="border:1px solid #CCC; border-width:1px; margin-bottom:5px; max-width: 100%;" allowfullscreen></iframe>
                    </p>
                    <p><?php echo __("Squirrly has never encouraged and will never encourage users to create duplicate content.", _SQ_PLUGIN_NAME_); ?></p>
                    <p><?php echo __("Squirrly will not take responsibility if an user copies an entire article from another source.", _SQ_PLUGIN_NAME_); ?></p>

                    <p><?php echo __("Best Practices for Using the Inspiration Box from Squirrly: ", _SQ_PLUGIN_NAME_); ?></p>


                    <ul class="sq_settings_ul">
                        <li><?php echo __("Focus on creating original content. Citing sources should complement your original ideas", _SQ_PLUGIN_NAME_); ?></li>
                        <li><?php echo __("Try to limit yourself to 2 or 3 quotes per article", _SQ_PLUGIN_NAME_); ?></li>
                        <li><?php echo __("Always include a link to your sources", _SQ_PLUGIN_NAME_); ?> </li>
                    </ul>


                    <div class="sq_button">
                        <?php if (SQ_Classes_Tools::getOption('sq_copyright_agreement')) { ?>
                            <a href="post-new.php" target="_blank" style="line-height: 40px; margin-top: 10px; font-size: 15px; max-width: 230px;"><?php _e("Use Squirrly's Inspiration box", _SQ_PLUGIN_NAME_) ?></a>
                        <?php } else { ?>
                            <p>
                                <?php echo __("I've read and understood how to correctly use the Inspiration Box from Squirrly.", _SQ_PLUGIN_NAME_); ?>

                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="sq_copyright_agreement"/>
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(_SQ_NONCE_ID_); ?>"/>
                                    <input type="submit" style="margin: 10px auto; display: block;" class="sq_button" value="<?php _e("I've read and understood how to correctly use the Inspiration Box from Squirrly.", _SQ_PLUGIN_NAME_) ?>"/>
                                </form>
                            </p>
                            <a target="_blank" style="line-height: 40px; background-color:darkgrey; text-shadow: none; border: none; margin-top: 10px; font-size: 15px; max-width: 230px;"><?php _e("Use Squirrly's Inspiration box", _SQ_PLUGIN_NAME_) ?></a>
                        <style>
                            #sq_hside503 a{
                                display: none;
                            }
                        </style>
                        <?php } ?>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

</div>
