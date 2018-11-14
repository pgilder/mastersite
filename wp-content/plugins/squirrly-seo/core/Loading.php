<?php

class SQ_Core_Loading extends SQ_Classes_BlockController {

    public function hookHead() {
        parent::hookHead();
        $this->loadJsVars();
    }

    /**
     * Load the JS for API
     */
    public function loadJsVars() {
        global $sq_postID;

        echo '<script>
                    var SQ_DEBUG = ' . (int)SQ_DEBUG . ';
                    (function($){
                        $.sq_config = {
                            sq_use: ' . (int)SQ_Classes_Tools::getOption('sq_use') . ',
                            token: "' . SQ_Classes_Tools::getOption('sq_api') . '",
                            user_url: "' . home_url() . '",
                            sq_baseurl: "' . _SQ_STATIC_API_URL_ . '",
                            sq_uri: "' . SQ_URI . '", 
                            sq_apiurl: "' . _SQ_API_URL_ . '",
                            language: "' . get_bloginfo('language') . '",
                            sq_version: "' . SQ_VERSION_ID . '",
                            sq_wpversion: "' . WP_VERSION_ID . '",
                            sq_phpversion: "' . PHP_VERSION_ID . '",
                            sq_seoversion: "' . ((int)SQ_Classes_Tools::getOption('sq_sla') + 1) . '",
                            keyword_information: ' . (int)SQ_Classes_Tools::getOption('sq_keyword_information') . ',
                            sq_keywordtag: ' . (int)SQ_Classes_Tools::getOption('sq_keywordtag') . ',
                            frontend_css: "' . _SQ_THEME_URL_ . 'css/frontend' . (SQ_DEBUG ? '' : '.min') . '.css",
                            postID: "' . $sq_postID . '",
                            prevNonce: "' . wp_create_nonce('post_preview_' . $sq_postID) . '",
                            is_gutenberg: "' . (SQ_Classes_Tools::isGutenberg() ? 1 : 0) . '",
                            __infotext: ["' . __('Recent discussions:', _SQ_PLUGIN_NAME_) . '", "' . __('SEO Search Volume:', _SQ_PLUGIN_NAME_) . '", "' . __('Competition:', _SQ_PLUGIN_NAME_) . '", "' . __('Trend:', _SQ_PLUGIN_NAME_) . '"],
                            __keyword: "' . __('Keyword:', _SQ_PLUGIN_NAME_) . '",
                            __date: "' . __('date', _SQ_PLUGIN_NAME_) . '",
                            __saved: "' . __('Saved!', _SQ_PLUGIN_NAME_) . '",
                            __readit: "' . __('Read it!', _SQ_PLUGIN_NAME_) . '",
                            __insertit: "' . __('Insert it!', _SQ_PLUGIN_NAME_) . '",
                            __reference: "' . __('Reference', _SQ_PLUGIN_NAME_) . '",
                            __insertasbox: "' . __('Insert as box', _SQ_PLUGIN_NAME_) . '",
                            __addlink: "' . __('Insert Link', _SQ_PLUGIN_NAME_) . '",
                            __notrelevant: "' . __('Not relevant?', _SQ_PLUGIN_NAME_) . '",
                            __insertparagraph: "' . __('Insert in your article', _SQ_PLUGIN_NAME_) . '",
                            __ajaxerror: "' . __(':( An error occurred while processing your request. Please try again', _SQ_PLUGIN_NAME_) . '",
                            __krerror: "' . __('Keyword Research takes too long to get the results. Click to try again', _SQ_PLUGIN_NAME_) . '",
                            __nofound: "' . __('No results found!', _SQ_PLUGIN_NAME_) . '",
                            __morewords: "' . __('Enter one more word to find relevant results', _SQ_PLUGIN_NAME_) . '",
                            __toolong: "' . __('Takes too long to check this keyword ...', _SQ_PLUGIN_NAME_) . '",
                            __doresearch: "' . __('Do a research!', _SQ_PLUGIN_NAME_) . '",
                            __morekeywords: "' . __('Do more research!', _SQ_PLUGIN_NAME_) . '",
                            __sq_photo_copyright: "' . __('[ ATTRIBUTE: Please check: %s to find out how to attribute this image ]', _SQ_PLUGIN_NAME_) . '",
                            __has_attributes: "' . __('Has creative commons attributes', _SQ_PLUGIN_NAME_) . '",
                            __no_attributes: "' . __('No known copyright restrictions', _SQ_PLUGIN_NAME_) . '",
                            __noopt: "' . __('You haven`t used Squirrly SEO to optimize your article. Do you want to optimize for a keyword before publishing?', _SQ_PLUGIN_NAME_) . '",
                            __limit_exceeded: "' . __('Keyword Research limit exceeded', _SQ_PLUGIN_NAME_) . '",
                            __subscription_expired: "' . __('Your Subscription has Expired', _SQ_PLUGIN_NAME_) . '",
                            __add_keywords: "' . __('Add 20 Keyword Researches', _SQ_PLUGIN_NAME_) . '",
                            __no_briefcase: "' . __('There are no keywords saved in briefcase yet', _SQ_PLUGIN_NAME_) . '",
                            __fulloptimized: "' . __('Congratulations! Your article is 100% optimized!', _SQ_PLUGIN_NAME_) . '",
                            __toomanytimes: "' . __('appears too many times. Try to remove %s of them', _SQ_PLUGIN_NAME_) . '",
                            __writemorewords: "' . __('write %s more words', _SQ_PLUGIN_NAME_) . '",
                            __keywordinintroduction: "' . __('Add the keyword in the %s of your article', _SQ_PLUGIN_NAME_) . '",
                            __clicktohighlight: "' . __('Click to keep the highlight on', _SQ_PLUGIN_NAME_) . '",
                            __introduction: "' . __('introduction', _SQ_PLUGIN_NAME_) . '",
                            __morewordsafter: "' . __('Write more words after the %s keyword', _SQ_PLUGIN_NAME_) . '",
                            __orusesynonyms: "' . __('or use synonyms', _SQ_PLUGIN_NAME_) . '",
                            __addmorewords: "' . __('add %s more word(s)', _SQ_PLUGIN_NAME_) . '",
                            __removewords: "' . __('or remove %s word(s)', _SQ_PLUGIN_NAME_) . '",
                            __addmorekeywords: "' . __('add %s  more keyword(s)', _SQ_PLUGIN_NAME_) . '",
                            __addminimumwords: "' . __('write %s  more words to start calculating', _SQ_PLUGIN_NAME_) . '",
                            __add_to_briefcase: "' . __('Add to Briefcase', _SQ_PLUGIN_NAME_) . '",
                            __add_keyword_briefcase: "' . __('Add Keyword to Briefcase', _SQ_PLUGIN_NAME_) . '",
                            __usekeyword: "' . __('Use Keyword', _SQ_PLUGIN_NAME_) . '",
                            __new_post_title: "' . __('Auto Draft') . '"
                        };
                  
                        
                        if (typeof sq_script === "undefined"){
                            var sq_script = document.createElement(\'script\');
                            sq_script.src = "' . _SQ_STATIC_API_URL_ . SQ_URI . '/js/squirrly' . (SQ_DEBUG ? '' : '.min') . '.js?ver=' . SQ_VERSION_ID . '";
                            var site_head = document.getElementsByTagName ("head")[0] || document.documentElement;
                            site_head.insertBefore(sq_script, site_head.firstChild);
                        }
                        
                        window.onerror = function(){
                            $.sq_config.sq_windowerror = true;
                        };
                        
                        $(document).ready(function() {
                            $("#sq_preloading").html("");
                        });
                    })(jQuery);
                     
                     </script>';

    }

}
