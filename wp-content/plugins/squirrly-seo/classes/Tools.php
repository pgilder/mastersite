<?php

/**
 * Handles the parameters and url
 *
 * @author Squirrly
 */
class SQ_Classes_Tools extends SQ_Classes_FrontController {

    /** @var array Options, User Metas, Package and Plugin details */
    public static $options, $usermeta, $package, $plugin = array();
    /** @var integer Count the errors in site */
    static $errors_count;

    /** @var array */
    private static $debug;
    private static $source_code;
    public static $is_ajax = null;

    public function __construct() {
        parent::__construct();

        $maxmemory = self::getMaxMemory();
        if ($maxmemory && $maxmemory < 60) {
            @ini_set('memory_limit', apply_filters('admin_memory_limit', WP_MAX_MEMORY_LIMIT));
        }

        self::$options = $this->getOptions();

        $this->checkDebug(); //dev mode
    }

    public static function getMaxMemory() {
        try {
            $memory_limit = @ini_get('memory_limit');
            if ((int)$memory_limit > 0) {
                if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
                    if ($matches[2] == 'G') {
                        $memory_limit = $matches[1] * 1024 * 1024 * 1024; // nnnM -> nnn MB
                    } elseif ($matches[2] == 'M') {
                        $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
                    } elseif ($matches[2] == 'K') {
                        $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
                    }
                }

                if ((int)$memory_limit > 0) {
                    return number_format($memory_limit / 1024 / 1024, 0, '', '');
                }
            }
        } catch (Exception $e) {
        }

        return false;

    }

    public static function getUsedMemory() {
        return number_format(memory_get_usage() / 1024 / 1024, 0);
    }

    /**
     * This hook will save the current version in database
     *
     * @return void
     */
    function hookInit() {
        $this->loadMultilanguage();
        add_filter("plugin_row_meta", array($this, 'hookExtraLinks'), 10, 4);
        //add setting link in plugin
        add_filter('plugin_action_links', array($this, 'hookActionlink'), 5, 2);

        if (self::getOption('sq_name') <> '') {
            if (isset($_SERVER['REQUEST_URI']) && function_exists('get_plugin_data')) {
                if (strpos($_SERVER['REQUEST_URI'], '/plugins.php') !== false) {
                    $data = get_plugin_data(_SQ_ROOT_DIR_ . '/squirrly.php');
                    if (isset($data['Name'])) {
                        self::$plugin['name'] = $data['Name'];
                        add_filter('pre_kses', array('SQ_Classes_Tools', 'changeString'), 1, 1);
                    }
                }
            }
        }
    }

    /**
     * Check if Dev Kit is installed
     * @return mixed|false
     */
    public static function updatePluginData() {
        $package_file = _SQ_ROOT_DIR_ . '/package.json';
        if (!file_exists($package_file)) {
            return false;
        }

        /* load configuration blocks data from core config files */
        $config = json_decode(file_get_contents($package_file), 1);
        if (isset($config['package'])) {
            self::$package = $config['package'];

            if (isset(self::$package['settings']) && !empty(self::$options)) {
                self::$options = @array_merge(self::$options, self::$package['settings']);

                if (isset(self::$package['name']) && self::$package['name'] <> '') {
                    self::$options['sq_name'] = self::$package['name'] . ' - Squirrly Special';
                }

                self::saveOptions();
                wp_redirect(admin_url('admin.php?page=sq_dashboard'));
            }
        }


        //remove the package after activation
        @unlink($package_file);
    }

    public static function changeString($string) {
        if (isset(self::$plugin['name']) && self::getOption('sq_name') <> '') {
            return str_replace(self::$plugin['name'], self::getOption('sq_name'), $string);
        }
        return $string;
    }

    /**
     * Add a link to settings in the plugin list
     *
     * @param array $links
     * @param type $file
     * @return array
     */
    public function hookActionlink($links, $file) {
        if ($file == _SQ_PLUGIN_NAME_ . '/squirrly.php') {
            $link = '<a href="' . admin_url('admin.php?page=sq_dashboard') . '">' . __('Getting started', _SQ_PLUGIN_NAME_) . '</a>';
            array_unshift($links, $link);
        }

        return $links;
    }

    /**
     * Adds extra links to plugin  page
     */
    public function hookExtraLinks($meta, $file, $data, $status) {
        if ($file == _SQ_PLUGIN_NAME_ . '/squirrly.php') {
            echo '<style>
                .ml-stars{display:inline-block;color:#ffb900;position:relative;top:3px}
                .ml-stars svg{fill:#ffb900}
                .ml-stars svg:hover{fill:#ffb900}
                .ml-stars svg:hover ~ svg{fill:none}
            </style>';

            $meta[] = "<a href='https://howto.squirrly.co/wordpress-seo/' target='_blank'>" . __('Documentation', _SQ_PLUGIN_NAME_) . "</a>";
            $meta[] = "<a href='https://wordpress.org/support/plugin/squirrly-seo/reviews/#new-post' target='_blank' title='" . __('Leave a review', _SQ_PLUGIN_NAME_) . "'><i class='ml-stars'><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg></i></a>";
        }
        return $meta;
    }


    public static function getToken() {
        if (get_transient('sq_token')) {
            $token = get_transient('sq_token');
        } else {
            $token = md5(self::generatePassword(32));
            set_transient('sq_token', md5($token));
        }

        return $token;
    }

    /**
     * Load the Options from user option table in DB
     *
     * @return array
     */
    public static function getOptions($action = '') {
        $default = array(
            'sq_ver' => 0,
            'sq_api' => '',
            'sq_logo' => _SQ_THEME_URL_ . 'img/settings/menu_icon_16.png',
            'sq_name' => '',
            'sq_checkedissues' => 0,
            'sq_areissues' => 0,
            'sq_use' => 1,
            'sq_use_frontend' => 1,
            'sq_laterload' => 0,
            'sq_post_types' => array(
                'post', 'page', 'product', 'shopp_page_shopp-products'
            ),
            'sq_exclude_post_types' => array(),
            'sq_support_email' => '',
            // --
            'sq_auto_canonical' => 1,
            'sq_auto_sitemap' => 0,
            'sq_auto_feed' => 0,
            'sq_auto_noindex' => 1,
            'sq_auto_jsonld' => 0,
            'sq_auto_amp' => 0,
            'sq_jsonld_type' => 'Organization',
            'sq_jsonld' => array(
                'Organization' => array(
                    'name' => '',
                    'logo' => '',
                    'telephone' => '',
                    'contactType' => '',
                    'description' => ''
                ),
                'Person' => array(
                    'name' => '',
                    'logo' => '',
                    'telephone' => '',
                    'jobTitle' => '',
                    'description' => ''
                )),
            'sq_sitemap_ping' => 0,
            'sq_sitemap_show' => array(
                'images' => 1,
                'videos' => 0,
            ),
            'sq_sitemap_perpage' => 200,
            'sq_sitemap_frequency' => 'weekly',
            'sq_sitemap_combinelangs' => 1,
            'sq_sitemap' => array(
                'sitemap' => array('sitemap.xml', 1),
                'sitemap-home' => array('sitemap-home.xml', 1),
                'sitemap-news' => array('sitemap-news.xml', 0),
                'sitemap-product' => array('sitemap-product.xml', 1),
                'sitemap-post' => array('sitemap-posts.xml', 1),
                'sitemap-page' => array('sitemap-pages.xml', 1),
                'sitemap-category' => array('sitemap-categories.xml', 1),
                'sitemap-post_tag' => array('sitemap-tags.xml', 1),
                'sitemap-archive' => array('sitemap-archives.xml', 1),
                'sitemap-author' => array('sitemap-authors.xml', 0),
                'sitemap-custom-tax' => array('sitemap-custom-taxonomies.xml', 0),
                'sitemap-custom-post' => array('sitemap-custom-posts.xml', 0),
                'sitemap-attachment' => array('sitemap-attachment.xml', 0),
            ),
            'sq_auto_robots' => 1,
            'sq_robots_permission' => array(
                'User-agent: *',
                'Disallow: */trackback/',
                'Disallow: */xmlrpc.php',
                'Disallow: /wp-*.php',
                'Disallow: /cgi-bin/',
                'Disallow: /wp-admin/',
                'Allow: */wp-content/uploads/',),
            'sq_auto_meta' => 1,
            'sq_auto_favicon' => 0,
            'favicon' => '',
            'sq_auto_twitter' => 0,
            'sq_auto_facebook' => 0,
            'sq_og_locale' => 'en_US',
            // --
            'sq_auto_title' => 1,
            'sq_auto_description' => 1,
            'sq_auto_keywords' => 1,
            // --
            'active_help' => '',
            'ignore_warn' => 0,
            'sq_copyright_agreement' => 0,
            'sq_keyword_help' => 1,
            'sq_keyword_information' => 0,
            'sq_url_fix' => 1,
            'sq_img_licence' => 1,
            //Ranking Option
            'sq_google_country' => 'com',
            'sq_google_language' => 'en',
            'sq_google_country_strict' => 0,
            'sq_google_ranksperhour' => 0,
            'sq_google_serpsperhour' => 50,
            'sq_google_last_checked' => 0,
            'sq_google_last_info' => 0,
            'sq_google_show_ignored' => 0,
            'sq_google_serp_active' => 0,
            'sq_google_serp_trial' => 0,
            'sq_google_alert_trial' => 1,
            // --
            'sq_affiliate_link' => '',
            'sq_sla' => 1,
            'sq_keywordtag' => 1,
            'sq_local_images' => 1,
            'sq_force_savepost' => 0,
            //--
            'sq_dashboard' => 0,
            'sq_analytics' => 0,
            'sq_analytics_google_js' => 'analytics',
            'menu' => array(
                'show_account_info' => 1,
                'show_affiliate' => 1,
                'show_panel' => 1,
                'show_tutorial' => 1,
                'show_audit' => 1,
                'show_ads' => 1,
            ),
            'sq_metas' => array(
                'title_maxlength' => 75,
                'description_maxlength' => 320,
                'og_title_maxlength' => 75,
                'og_description_maxlength' => 110,
                'tw_title_maxlength' => 75,
                'tw_description_maxlength' => 280,
                'jsonld_title_maxlength' => 75,
                'jsonld_description_maxlength' => 110,
            ),
            'socials' => array(
                'fb_admins' => array(),
                'fbconnectkey' => "",
                'fbadminapp' => "",

                'facebook_site' => "",
                'twitter_site' => "",
                'twitter' => "",
                'instagram_url' => "",
                'linkedin_url' => "",
                'myspace_url' => "",
                'pinterest_url' => "",
                'youtube_url' => "",
                'twitter_card_type' => "summary",
                'plus_publisher' => ""
            ),
            'codes' => array(
                'google_wt' => "",
                'google_analytics' => "",
                'facebook_pixel' => "",

                'bing_wt' => "",
                'pinterest_verify' => "",
                'alexa_verify' => "",
            ),
            'sq_auto_pattern' => 1,
            'patterns' => array(
                'home' => array(
                    'sep' => '|',
                    'title' => '{{sitename}} {{page}} {{sep}} {{sitedesc}}',
                    'description' => '{{excerpt}} {{page}} {{sep}} {{sitename}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'post' => array(
                    'sep' => '|',
                    'title' => '{{title}} {{page}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'page' => array(
                    'sep' => '|',
                    'title' => '{{title}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'category' => array(
                    'sep' => '|',
                    'title' => '{{category}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{category_description}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'tag' => array(
                    'sep' => '|',
                    'title' => '{{tag}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'tax-post_format' => array(
                    'sep' => '|',
                    'title' => '{{term_title}} ' . __('Format', _SQ_PLUGIN_NAME_) . ' {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'tax-category' => array(
                    'sep' => '|',
                    'title' => '{{term_title}} ' . __('Category', _SQ_PLUGIN_NAME_) . ' {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'tax-post_tag' => array(
                    'sep' => '|',
                    'title' => '{{term_title}} ' . __('Tag', _SQ_PLUGIN_NAME_) . ' {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'tax-product_cat' => array(
                    'sep' => '|',
                    'title' => '{{term_title}} ' . __('Category', _SQ_PLUGIN_NAME_) . ' {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'tax-product_tag' => array(
                    'sep' => '|',
                    'title' => '{{term_title}} ' . __('Tag', _SQ_PLUGIN_NAME_) . ' {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'tax-product_shipping_class' => array(
                    'sep' => '|',
                    'title' => '{{term_title}} ' . __('Shipping Option', _SQ_PLUGIN_NAME_) . ' {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'profile' => array(
                    'sep' => '|',
                    'title' => '{{name}}, ' . __('Author at', _SQ_PLUGIN_NAME_) . ' {{sitename}} {{page}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'shop' => array(
                    'sep' => '|',
                    'title' => '{{title}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'product' => array(
                    'sep' => '|',
                    'title' => '{{title}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'archive' => array(
                    'sep' => '|',
                    'title' => '{{date}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'search' => array(
                    'sep' => '|',
                    'title' => __('You searched for', _SQ_PLUGIN_NAME_) . ' {{searchphrase}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 1,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                'attachment' => array(
                    'sep' => '|',
                    'title' => '{{title}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
                '404' => array(
                    'sep' => '|',
                    'title' => __('Page not found', _SQ_PLUGIN_NAME_) . ' {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 1,
                    'nofollow' => 1,
                    'disable' => 0,
                ),
                'custom' => array(
                    'sep' => '|',
                    'title' => '{{title}} {{page}} {{sep}} {{sitename}}',
                    'description' => '{{excerpt}}',
                    'noindex' => 0,
                    'nofollow' => 0,
                    'disable' => 0,
                ),
            )
        );
        $options = json_decode(get_option(SQ_OPTION), true);

        if ($action == 'reset') {
            $init['sq_ver'] = $options['sq_ver'];
            $init['sq_api'] = $options['sq_api'];
            return $init;
        }

        if (is_array($options)) {
            $options = @array_merge($default, $options);
            return $options;
        }

        return $default;
    }

    /**
     * Get the option from database
     * @param $key
     * @return mixed
     */
    public static function getOption($key) {
        if (!isset(self::$options[$key])) {
            self::$options = self::getOptions();

            if (!isset(self::$options[$key])) {
                self::$options[$key] = false;
            }
        }

        return apply_filters('sq_option_' . $key, self::$options[$key]);
    }

    //Get the package info in case of custom details
    public static function getPackageInfo($key) {
        if (isset(self::$package[$key])) {
            return self::$package[$key];
        }

        return false;
    }

    /**
     * Get user metas
     * @param null $user_id
     * @return array|mixed
     */
    public static function getUserMetas($user_id = null) {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }

        $default = array('sq_auto_sticky' => 0,);

        $usermeta = get_user_meta($user_id);
        $usermetatmp = array();
        if (is_array($usermeta)) foreach ($usermeta as $key => $values) {
            $usermetatmp[$key] = $values[0];
        }
        $usermeta = $usermetatmp;

        if (is_array($usermeta)) {
            $usermeta = @array_merge($default, $usermeta);
        } else {
            $usermeta = $default;
        }
        self::$usermeta = $usermeta;
        return $usermeta;
    }

    /**
     * Get use meta
     * @param $value
     * @return bool
     */
    public static function getUserMeta($value) {
        if (!isset(self::$usermeta[$value])) {
            self::getUserMetas();
        }

        if (isset(self::$usermeta[$value])) {
            return apply_filters('sq_usermeta_' . $value, self::$usermeta[$value]);
        }

        return false;
    }

    /**
     * Save user meta
     * @param $key
     * @param $value
     * @param null $user_id
     */
    public static function saveUserMeta($key, $value, $user_id = null) {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }
        self::$usermeta[$key] = $value;
        update_user_meta($user_id, $key, $value);
    }

    /**
     * Delete User meta
     * @param $key
     * @param null $user_id
     */
    public static function deleteUserMeta($key, $user_id = null) {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }
        unset(self::$usermeta[$key]);
        delete_user_meta($user_id, $key);
    }

    /**
     * Send completed tasks from tutorial
     * @return array
     */
    public static function getBriefOptions() {
        $jsonld = self::getOption('sq_jsonld');
        $socials = self::getOption('socials');
        $codes = self::getOption('codes');

        return array(
            'sq_version' => SQ_VERSION_ID,
            'sq_use' => self::getOption('sq_use'),
            'sq_token' => self::getToken(),
            'sq_rest' => (function_exists('rest_get_url_prefix') ? rest_get_url_prefix() : ''),
            'sq_checkedissues' => self::getOption('sq_checkedissues'),
            'sq_areissues' => self::getOption('sq_areissues'),
            'sq_auto_canonical' => self::getOption('sq_auto_canonical'),
            'sq_auto_meta' => self::getOption('sq_auto_meta'),
            'sq_auto_sitemap' => self::getOption('sq_auto_sitemap'),
            'sq_auto_jsonld' => (self::getOption('sq_auto_jsonld') && ($jsonld['Organization']['name'] <> '' || $jsonld['Person']['name'] <> '')),
            'sq_sitemap_ping' => SQ_Classes_Tools::getOption('sq_sitemap_ping'),
            'sq_auto_robots' => SQ_Classes_Tools::getOption('sq_auto_robots'),
            'sq_auto_favicon' => (SQ_Classes_Tools::getOption('sq_auto_favicon') && SQ_Classes_Tools::getOption('favicon') <> ''),
            'sq_auto_twitter' => SQ_Classes_Tools::getOption('sq_auto_twitter'),
            'sq_auto_facebook' => SQ_Classes_Tools::getOption('sq_auto_facebook'),
            'sq_auto_seo' => 1,
            'sq_auto_title' => (int)(SQ_Classes_Tools::getOption('sq_auto_title') == 1),
            'sq_auto_description' => (int)(SQ_Classes_Tools::getOption('sq_auto_description') == 1),
            'sq_auto_keywords' => (int)(SQ_Classes_Tools::getOption('sq_auto_keywords') == 1),
            'sq_auto_noindex' => (int)(SQ_Classes_Tools::getOption('sq_auto_noindex') == 1),
            'sq_google_wt' => (int)($codes['google_wt'] <> ''),
            'sq_google_analytics' => (int)($codes['google_analytics'] <> ''),
            'sq_google_serpsperhour' => (int)SQ_Classes_Tools::getOption('sq_google_serpsperhour'),
            'sq_facebook_insights' => (int)(!empty($socials['sq_facebook_insights'])),
            'sq_bing_wt' => (int)($codes['bing_wt'] <> ''),
            'sq_pinterest' => (int)($codes['pinterest_verify'] <> ''),
            'sq_alexa' => (int)($codes['alexa_verify'] <> ''),
            'sq_keyword_help' => SQ_Classes_Tools::getOption('sq_keyword_help'),
            'sq_keyword_information' => SQ_Classes_Tools::getOption('sq_keyword_information'),
            'sq_google_language' => SQ_Classes_Tools::getOption('sq_google_language'),
            'sq_google_country' => SQ_Classes_Tools::getOption('sq_google_country'),
            'sq_google_country_strict' => SQ_Classes_Tools::getOption('sq_google_country_strict'),
            'sq_keywordtag' => SQ_Classes_Tools::getOption('sq_keywordtag'),
            'sq_local_images' => SQ_Classes_Tools::getOption('sq_local_images'),
        );

    }

    /**
     * Save the Options in user option table in DB
     *
     * @return void
     */
    public static function saveOptions($key = null, $value = '') {
        if (isset($key)) {
            self::$options[$key] = $value;
        }

        update_option(SQ_OPTION, json_encode(self::$options));
    }

    /**
     * Set the header type
     * @param string $type
     */
    public static function setHeader($type) {
        if (SQ_Classes_Tools::getValue('sq_debug') == 'on') {
            if (self::isAjax()) {
                header("Content-type: text/html");
            }
            return;
        }

        switch ($type) {
            case 'json':
                header('Content-Type: application/json');
                break;
            case 'ico':
                header('Content-Type: image/x-icon');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case'text':
                header("Content-type: text/plain");
                break;
            case'html':
                header("Content-type: text/html");
                break;
        }
    }

    /**
     * Get a value from $_POST / $_GET
     * if unavailable, take a default value
     *
     * @param string $key Value key
     * @param mixed $defaultValue (optional)
     * @param bool $htmlcode
     * @param bool $keep_newlines
     * @return mixed Value
     */
    public static function getValue($key, $defaultValue = false, $htmlcode = false, $keep_newlines = false) {
        if (!isset($key) OR empty($key) OR !is_string($key))
            return false;
        $ret = (isset($_POST[$key]) ? (is_string($_POST[$key]) ? rawurldecode($_POST[$key]) : $_POST[$key]) : (isset($_GET[$key]) ? (is_string($_GET[$key]) ? rawurldecode($_GET[$key]) : $_GET[$key]) : $defaultValue));

        if (is_string($ret) === true && $htmlcode === false) {
            if ($keep_newlines && function_exists('sanitize_textarea_field')) {
                $ret = sanitize_textarea_field($ret);
            } else {
                $ret = sanitize_text_field($ret);
            }
        }

        return !is_string($ret) ? $ret : stripslashes($ret);
    }

    /**
     * Check if the parameter is set
     *
     * @param string $key
     * @return boolean
     */
    public static function getIsset($key) {
        if (!isset($key) OR empty($key) OR !is_string($key))
            return false;
        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
     * Show the notices to WP
     *
     * @return string
     */
    public static function showNotices($message, $type = 'sq_notices') {
        if (file_exists(_SQ_THEME_DIR_ . 'Notices.php')) {
            ob_start();
            include(_SQ_THEME_DIR_ . 'Notices.php');
            $message = ob_get_contents();
            ob_end_clean();
        }

        return $message;
    }

    /**
     * Load the multilanguage support from .mo
     */
    private function loadMultilanguage() {
        if (!defined('WP_PLUGIN_DIR')) {
            load_plugin_textdomain(_SQ_PLUGIN_NAME_, _SQ_PLUGIN_NAME_ . '/languages/');
        } else {
            load_plugin_textdomain(_SQ_PLUGIN_NAME_, null, _SQ_PLUGIN_NAME_ . '/languages/');
        }
    }

    /**
     * Connect remote with CURL if exists
     */
    public static function sq_remote_get($url, $param = array(), $options = array()) {
        $parameters = '';
        $cookies = array();
        $cookie_string = '';

        $url_domain = parse_url($url);
        if (isset($url_domain['host'])) {
            $url_domain = $url_domain['host'];
        } else {
            $url_domain = '';
        }

        if (isset($param)) {
            foreach ($param as $key => $value) {
                if (isset($key) && $key <> '' && $key <> 'timeout') {
                    $parameters .= ($parameters == "" ? "" : "&") . $key . "=" . $value;
                }
            }
        }

        if ($parameters <> '') {
            $url .= ((strpos($url, "?") === false) ? "?" : "&") . $parameters;
        }

        //send the cookie for preview
        $server_domain = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            $server_domain = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $server_domain = $_SERVER['SERVER_NAME'];
        }

        if ($url_domain == $server_domain && strpos($url, 'preview=true') !== false) {
            foreach ($_COOKIE as $name => $value) {
                if (strpos($name, 'wordpress') !== false || strpos($name, 'wp') !== false || strpos($name, 'slimstat') !== false || strpos($name, 'sforum') !== false) {
                    $cookies[] = new WP_Http_Cookie(array('name' => $name, 'value' => $value));
                    $cookie_string .= "$name=$value;";
                }
            }
        }

        $options['timeout'] = (isset($options['timeout'])) ? $options['timeout'] : 30;
        if (!isset($options['cookie_string'])) {
            $options['cookies'] = $cookies;
            $options['cookie_string'] = $cookie_string;
        }
        $options['sslverify'] = false;


        if (function_exists('curl_init') && !ini_get('open_basedir')) {
            $response = self::sq_curl($url, $options, 'get');
        } else {
            if (!$response = self::sq_wpcall($url, $options, 'get')) {
                return false;
            }
        }

        return $response;
    }

    /**
     * Connect remote with CURL if exists
     */
    public static function sq_remote_post($url, $param = array(), $options = array()) {
        $parameters = '';
        $cookies = array();
        $cookie_string = '';

        $url_domain = parse_url($url);
        if (isset($url_domain['host'])) {
            $url_domain = $url_domain['host'];
        } else {
            $url_domain = '';
        }

        if (isset($param)) {
            foreach ($param as $key => $value) {
                if (isset($key) && $key <> '' && $key <> 'timeout') {
                    $parameters .= ($parameters == "" ? "" : "&") . $key . "=" . $value;
                }
            }
        }

        if ($parameters <> '') {
            $url .= ((strpos($url, "?") === false) ? "?" : "&") . $parameters;
        }

        //send the cookie for preview
        $server_domain = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            $server_domain = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $server_domain = $_SERVER['SERVER_NAME'];
        }

        if ($url_domain == $server_domain && strpos($url, 'preview=true') !== false) {
            foreach ($_COOKIE as $name => $value) {
                $cookies[] = new WP_Http_Cookie(array('name' => $name, 'value' => $value));
                $cookie_string .= "$name=$value;";
            }
        }

        $options['timeout'] = (isset($options['timeout'])) ? $options['timeout'] : 30;
        if (!isset($options['cookie_string'])) {
            $options['cookies'] = $cookies;
            $options['cookie_string'] = $cookie_string;
        }
        $options['sslverify'] = false;

        if (function_exists('curl_init') && !ini_get('open_basedir')) {
            $response = self::sq_curl($url, $options, 'post');
        } else {
            if (!$response = self::sq_wpcall($url, $options, 'post')) {
                return false;
            }
        }


        return $response;
    }

    /**
     * Call remote UR with CURL
     * @param string $url
     * @param array $param
     * @return string
     */
    private static function sq_curl($url, $options, $method = 'get') {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        //--
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //--
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);

        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        if (isset($options['sslverify'])) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        if (isset($options['followlocation'])) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        }

        if ($options['cookie_string'] <> '') {
            curl_setopt($ch, CURLOPT_COOKIE, $options['cookie_string']);
        }

        if (isset($options['User-Agent']) && $options['User-Agent'] <> '') {
            curl_setopt($ch, CURLOPT_USERAGENT, $options['User-Agent']);
        }
        $response = curl_exec($ch);
        $response = self::cleanResponce($response);

        self::dump('CURL', $method, $url, $options, $ch, $response); //output debug

        if (curl_errno($ch) == 1 || $response === false) { //if protocol not supported
            if (curl_errno($ch)) {
                self::dump(curl_getinfo($ch), curl_errno($ch), curl_error($ch));
            }
            curl_close($ch);
            $response = self::sq_wpcall($url, $options); //use the wordpress call
        } else {
            curl_close($ch);
        }

        return $response;
    }

    /**
     * Use the WP remote call
     * @param string $url
     * @param array $param
     * @return string
     */
    private static function sq_wpcall($url, $options, $method = 'get') {
        if ($method == 'post') {
            $response = wp_remote_post($url, $options);
        } else {
            $response = wp_remote_get($url, $options);
        }
        if (is_wp_error($response)) {
            self::dump($response);
            return false;
        }

        $response = self::cleanResponce(wp_remote_retrieve_body($response)); //clear and get the body
        self::dump('wp_remote_get', $method, $url, $options, $response); //output debug
        return $response;
    }

    /**
     * Connect remote with CURL if exists
     */
    public static function sq_remote_head($url) {
        $response = array();

        if (function_exists('curl_exec')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_exec($ch);

            $response['headers']['content-type'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $response['response']['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $response;
        } else {
            return wp_remote_head($url, array('timeout' => 30));
        }

        return false;
    }

    /**
     * Get the Json from responce if any
     * @param string $response
     * @return string
     */
    private static function cleanResponce($response) {
        return trim($response, '()');
    }

    /**
     * Check for SEO blog bad settings
     */
    public static function checkErrorSettings($count_only = false) {
        if (current_user_can('manage_options')) {

            $fixit = "<a href=\"javascript:void(0);\"  onclick=\"%sjQuery(this).closest('div').fadeOut('slow'); if(parseInt(jQuery('.sq_count').html())>0) { var notif = (parseInt(jQuery('.sq_count').html()) - 1); if (notif > 0) {jQuery('.sq_count').html(notif); }else{ jQuery('.sq_count').html(notif); jQuery('.sq_count').hide(); } } jQuery.post(ajaxurl, { action: '%s', nonce: '" . wp_create_nonce(_SQ_NONCE_ID_) . "'});\" >" . __("Fix it for me!", _SQ_PLUGIN_NAME_) . "</a>";

            /* IF SEO INDEX IS OFF */
            if (self::getAutoSeoSquirrly()) {
                self::$errors_count++;
                if (!$count_only) {
                    SQ_Classes_Error::setError(__('Activate the Squirrly SEO for your blog (recommended)', _SQ_PLUGIN_NAME_) . " <br />" . sprintf($fixit, "jQuery('#sq_use_on').trigger('click');", "sq_fixautoseo") . "", 'settings', 'sq_fix_auto');
                }
            }

            //check only when in seo settings
            self::$source_code = self::sq_remote_get(get_bloginfo('url'), array(), array('timeout' => 5, 'followlocation' => true));
            if (self::$source_code <> '') {
                /* IF TITLE DUPLICATES */
                if (self::getDuplicateTitle()) {
                    self::$errors_count++;
                    if (!$count_only) {
                        SQ_Classes_Error::setError(__('You have META Title Duplicates. Disable the Squirrly Title Optimization or disable the other SEO Plugins', _SQ_PLUGIN_NAME_) . " <br />" . sprintf($fixit, "jQuery('#sq_auto_title0').attr('checked', true); jQuery('#sq_automatically').attr('checked', true);", "sq_fix_titleduplicate") . "", 'settings', 'sq_fix_descduplicate');
                    }
                }

                /* IF DESCRIPTION DUPLICATES */
                if (self::getDuplicateDescription()) {
                    self::$errors_count++;
                    if (!$count_only) {
                        SQ_Classes_Error::setError(__('You have META Description Duplicates. Disable the Squirrly Description Optimization or disable the other SEO Plugins', _SQ_PLUGIN_NAME_) . " <br />" . sprintf($fixit, "jQuery('#sq_auto_description0').attr('checked', true); jQuery('#sq_automatically').attr('checked', true);", "sq_fix_descduplicate") . "", 'settings', 'sq_fix_descduplicate');
                    }
                }

                /* IF OG DUPLICATES */
                if (self::getDuplicateOG()) {
                    self::$errors_count++;
                    if (!$count_only) {
                        SQ_Classes_Error::setError(__('You have Open Graph META Duplicates. Disable the Squirrly SEO Open Graph or disable the other SEO Plugins', _SQ_PLUGIN_NAME_) . " <br />" . sprintf($fixit, "jQuery('#sq_auto_facebook0').attr('checked', true);", "sq_fix_ogduplicate") . "", 'settings', 'sq_fix_ogduplicate');
                    }
                }

                /* IF TWITTER CARD DUPLICATES */
                if (self::getDuplicateTC()) {
                    self::$errors_count++;
                    if (!$count_only) {
                        SQ_Classes_Error::setError(__('You have Twitter Card META Duplicates. Disable the Squirrly SEO Twitter Card or disable the other SEO Plugins', _SQ_PLUGIN_NAME_) . " <br />" . sprintf($fixit, "jQuery('#sq_auto_twitter0').attr('checked', true);", "sq_fix_tcduplicate") . "", 'settings', 'sq_fix_tcduplicate');
                    }
                }
            }

            /* IF SEO INDEX IS OFF */
            if (self::getPrivateBlog()) {
                self::$errors_count++;
                if (!$count_only) {
                    SQ_Classes_Error::setError(__('You\'re blocking google from indexing your site!', _SQ_PLUGIN_NAME_) . " <br />" . sprintf($fixit, "jQuery('#sq_google_index1').attr('checked',true);", "sq_fixprivate") . "", 'settings', 'sq_fix_private');
                }
            }

            if (self::getBadLinkStructure()) {
                self::$errors_count++;
                if (!$count_only) {
                    SQ_Classes_Error::setError(__('It is highly recommended that you include the %postname% variable in the permalink structure. <br />Go to Settings > Permalinks and add /%postname%/ in Custom Structure', _SQ_PLUGIN_NAME_) . " <br /> ", 'settings');
                }
            }

            if (self::getDefaultTagline()) {
                self::$errors_count++;
                if (!$count_only) {
                    SQ_Classes_Error::setError(__('It is highly recommended to change or remove the default Wordpress Tagline. <br />Go to Settings > General > Tagline', _SQ_PLUGIN_NAME_) . " <br /> ", 'settings');
                }
            }

            if (self::$errors_count == 0) {
                self::saveOptions('sq_areissues', 0);
                SQ_Classes_Error::setError(__('Great! We didn\'t find any issue in your site.', _SQ_PLUGIN_NAME_) . " <br /> ", 'success');
            } else {
                self::saveOptions('sq_areissues', 1);
            }
        }
    }

    /**
     * Check if the automatically seo si active
     * @return bool
     */
    private static function getAutoSeoSquirrly() {
        return (!self::getOption('sq_use'));
    }

    /**
     * Check for META duplicates
     * @return boolean
     */
    private static function getDuplicateOG() {
        if (!function_exists('preg_match_all')) {
            return false;
        }

        if (self::getOption('sq_use') && self::getOption('sq_auto_facebook')) {

            if (self::$source_code <> '') {
                preg_match_all("/<meta[\s+]property=[\"|\']og:url[\"|\'][\s+](content|value)=[\"|\']([^>]*)[\"|\'][^>]*>/i", self::$source_code, $out);
                if (!empty($out) && isset($out[0]) && is_array($out[0])) {
                    return (sizeof($out[0]) > 1);
                }
            }
        }

        return false;
    }

    /**
     * Check for META duplicates
     * @return boolean
     */
    private static function getDuplicateTC() {
        if (!function_exists('preg_match_all')) {
            return false;
        }

        if (self::getOption('sq_use') && self::getOption('sq_auto_twitter')) {

            if (self::$source_code <> '') {
                preg_match_all("/<meta[\s+]name=[\"|\']twitter:card[\"|\'][\s+](content|value)=[\"|\']([^>]*)[\"|\'][^>]*>/i", self::$source_code, $out);
                if (!empty($out) && isset($out[0]) && is_array($out[0])) {
                    return (sizeof($out[0]) > 1);
                }
            }
        }

        return false;
    }

    /**
     * Check for META duplicates
     * @return boolean
     */
    private static function getDuplicateDescription() {
        if (!function_exists('preg_match_all')) {
            return false;
        }
        $total = 0;

        if (self::getOption('sq_use') && self::$options['sq_auto_description'] == 1) {
            if (self::$source_code <> '') {
                preg_match_all("/<meta[^>]*name=[\"|\']description[\"|\'][^>]*content=[\"]([^\"]*)[\"][^>]*>/i", self::$source_code, $out);
                if (!empty($out) && isset($out[0]) && is_array($out[0])) {
                    $total += sizeof($out[0]);
                }
                preg_match_all("/<meta[^>]*content=[\"]([^\"]*)[\"][^>]*name=[\"|\']description[\"|\'][^>]*>/i", self::$source_code, $out);
                if (!empty($out) && isset($out[0]) && is_array($out[0])) {
                    $total += sizeof($out[0]);
                }
            }
        }

        return ($total > 1);
    }

    /**
     * Check for META duplicates
     * @return boolean
     */
    private static function getDuplicateTitle() {
        if (!function_exists('preg_match_all')) {
            return false;
        }
        $total = 0;

        if (self::getOption('sq_use') && self::$options['sq_auto_title'] == 1) {
            if (self::$source_code <> '') {
                preg_match_all("/<title[^>]*>(.*)?<\/title>/i", self::$source_code, $out);

                if (!empty($out) && isset($out[0]) && is_array($out[0])) {
                    $total += sizeof($out[0]);
                }
                preg_match_all("/<meta[^>]*name=[\"|\']title[\"|\'][^>]*content=[\"|\']([^>\"]*)[\"|\'][^>]*>/i", self::$source_code, $out);
                if (!empty($out) && isset($out[0]) && is_array($out[0])) {
                    $total += sizeof($out[0]);
                }
            }
        }

        return ($total > 1);
    }

    /**
     * Check if the blog is in private mode
     * @return bool
     */
    public static function getPrivateBlog() {
        return ((int)get_option('blog_public') == 0);
    }

    /**
     * Check if the blog has a bad link structure
     * @return bool
     */
    private static function getBadLinkStructure() {
        global $wp_rewrite;
        if (function_exists('apache_get_modules')) {
            //Check if mod_rewrite is installed in apache
            if (!in_array('mod_rewrite', apache_get_modules()))
                return false;
        }

        $home_path = get_home_path();
        $htaccess_file = $home_path . '.htaccess';

        if ((!file_exists($htaccess_file) && @is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks()) || @is_writable($htaccess_file)) {
            $link = get_option('permalink_structure');
            if ($link == '')
                return true;
        }
    }

    private static function getDefaultTagline() {
        $blog_description = get_bloginfo('description');
        $default_blog_description = 'Just another WordPress site';
        $translated_blog_description = __('Just another WordPress site');
        return $translated_blog_description === $blog_description || $default_blog_description === $blog_description;
    }

    /**
     * Support for i18n with wpml, polyglot or qtrans
     *
     * @param string $in
     * @return string $in localized
     */
    public static function i18n($in) {
        if (function_exists('langswitch_filter_langs_with_message')) {
            $in = langswitch_filter_langs_with_message($in);
        }
        if (function_exists('polyglot_filter')) {
            $in = polyglot_filter($in);
        }
        if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
            $in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);
        }
        $in = apply_filters('localization', $in);
        return $in;
    }

    /**
     * Convert integer on the locale format.
     *
     * @param int $number The number to convert based on locale.
     * @param int $decimals Precision of the number of decimal places.
     * @return string Converted number in string format.
     */
    public static function i18n_number_format($number, $decimals = 0) {
        global $wp_locale;
        $formatted = number_format($number, absint($decimals), $wp_locale->number_format['decimal_point'], $wp_locale->number_format['thousands_sep']);
        return apply_filters('number_format_i18n', $formatted);
    }

    public static function clearTitle($title) {
        if ($title <> '') {
            $title = str_replace(array("\n", "&nbsp;"), " ", $title);

            $title = self::i18n(trim(esc_html(ent2ncr(strip_tags($title)))));

            $title = preg_replace('/\s{2,}/', ' ', $title);
        }
        return $title;
    }

    public static function clearDescription($description) {
        if ($description <> '') {
            if (function_exists('preg_replace')) {
                $search = array("'<script[^>]*?>.*?<\/script>'si", // strip out javascript
                    "/<form.*?<\/form>/si",
                    "/<iframe.*?<\/iframe>/si",
                );
                $description = preg_replace($search, "", $description);
                $search = array(
                    "/[\n\r]/si",
                    "/&nbsp;/si",
                    "/\s{2,}/",
                );
                $description = preg_replace($search, " ", $description);
            }

            $description = self::i18n(trim(esc_html(ent2ncr(strip_tags($description)))));
        }

        return $description;
    }

    public static function clearKeywords($keywords) {
        return self::clearTitle($keywords);
    }

    public static function getBrowserInfo() {
        $ub = '';
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";
        if (!function_exists('preg_match'))
            return false;

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'IE';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?' . join('|', $known) . ')[/ ]+(?[0-9.|a-zA-Z.]*)#';

        if (strpos($u_agent, 'MSIE 7.0;') !== false) {
            $version = 7.0;
        }

        if ($version == null || $version == "") {
            $version = "0";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    /**
     *
     * @param string $url
     * @return array | false
     */
    public static function getSnippet($url) {
        if ($url == '' || !function_exists('preg_match')) {
            return false;
        }


        $post_id = 0;
        $snippet = array();

        if ($post_id == 0) {
            $post_id = url_to_postid($url);
        } elseif ($url == get_bloginfo('url')) {
            if (!$post_id = get_option('page_on_front'))
                ($post_id = get_option('page_for_posts'));
        }


        if ($post_id > 0) {
            if ($post = SQ_Classes_ObjController::getClass('SQ_Controllers_Menu')->setPostByID($post_id)) {
                $snippet['title'] = (self::getOption('sq_auto_title') ? $post->sq->title : $post->post_title);
                $snippet['description'] = (self::getOption('sq_auto_description') ? $post->sq->description : $post->post_excerpt);
                $snippet['url'] = $url;
            }
        } elseif ($url == home_url()) {
            $post = SQ_Classes_ObjController::getDomain('SQ_Models_Domain_Post');
            $post->debug = 'isHomePage';
            $post->post_type = 'home';
            $post->hash = md5('wp_homepage');
            $post->url = home_url();

            $post = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->setPost($post)->getPost();
            $snippet['title'] = (self::getOption('sq_auto_title') ? $post->sq->title : get_bloginfo('name'));
            $snippet['description'] = (self::getOption('sq_auto_description') ? $post->sq->description : get_bloginfo('description'));
            $snippet['url'] = $url;
        } else {
            $length = array('title' => 66,
                'description' => 240,
                'url' => 45);

            self::$source_code = self::sq_remote_get($url, array(), array('timeout' => 10, 'followlocation' => true));

            $title_regex = "/<title[^>]*>([^<>]*)<\/title>/si";
            preg_match($title_regex, self::$source_code, $title);

            if (is_array($title) && count($title) > 0) {
                $snippet['title'] = $title[1];
                $snippet['title'] = self::i18n(trim(strip_tags($snippet['title'])));
            }

            $description_regex = '/<meta[^>]*(name|property)=["\']description["\'][^>]*content="([^"<>]+)"[^<>]*>/si';
            preg_match($description_regex, self::$source_code, $description);
            if (is_array($description) && count($description) > 0) {
                $snippet['description'] = self::i18n(trim(strip_tags($description[2])));

                if (strlen($snippet['description']) > $length['description'])
                    $snippet['description'] = substr($snippet['description'], 0, ($length['description'] - 1)) . '...';
            }

            $snippet['url'] = urldecode($url);
            if (strlen($snippet['url']) > $length['url'])
                $snippet['url'] = substr($snippet['url'], 0, ($length['url'] - 1)) . '...';

        }

        return $snippet;

    }

    /**
     * Check if debug is called
     */
    private function checkDebug() {
        //if debug is called
        if (SQ_DEBUG && self::getIsset('sq_debug')) {
            if (self::getValue('sq_debug') === 'on') {
                if (function_exists('register_shutdown_function')) {
                    register_shutdown_function(array($this, 'showDebug'));
                }
            }
        }
    }

    /**
     * Store the debug for a later view
     */
    public static function dump() {
        if (self::getValue('sq_debug') !== 'on') {
            return;
        }

        $output = '';
        $callee = array('file' => '', 'line' => '');
        if (function_exists('func_get_args')) {
            $arguments = func_get_args();
            $total_arguments = count($arguments);
        } else
            $arguments = array();


        $run_time = number_format(microtime(true) - REQUEST_TIME, 3);
        if (function_exists('debug_backtrace'))
            list($callee) = debug_backtrace();

        $output .= '<fieldset style="background: #FFFFFF; border: 1px #CCCCCC solid; padding: 5px; font-size: 9pt; margin: 0;">';
        $output .= '<legend style="background: #EEEEEE; padding: 2px; font-size: 8pt;">' . $callee['file'] . ' Time: ' . $run_time . ' @ line: ' . $callee['line']
            . '</legend><pre style="margin: 0; font-size: 8pt; text-align: left;">';

        $i = 0;
        foreach ($arguments as $argument) {
            if (count($arguments) > 1)
                $output .= "\n" . '<strong>#' . (++$i) . ' of ' . $total_arguments . '</strong>: ';

            // if argument is boolean, false value does not display, so ...
            if (is_bool($argument))
                $argument = ($argument) ? 'TRUE' : 'FALSE';
            else
                if (is_object($argument) && function_exists('array_reverse') && function_exists('class_parents'))
                    $output .= implode("\n" . '|' . "\n", array_reverse(class_parents($argument))) . "\n" . '|' . "\n";

            $output .= htmlspecialchars(print_r($argument, TRUE))
                . ((is_object($argument) && function_exists('spl_object_hash')) ? spl_object_hash($argument) : '');
        }
        $output .= "</pre>";
        $output .= "</fieldset>";

        self::$debug[] = $output;
    }

    /**
     * Add Memory Debug in the footer
     */
    public function hookFooter() {
        if (self::getValue('sq_debug') === 'on') {
            self::dump(self::getMaxMemory(), self::getUsedMemory());
        }
    }

    /**
     * Show the debug dump
     */
    public static function showDebug() {
        echo "Debug result: <br />" . '<div id="wpcontent">' . @implode('<br />', self::$debug) . '</div>';
    }

    public static function generatePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $password;
    }

    public function sq_activate() {
        set_transient('sq_activate', true);
        set_transient('sq_rewrite', true);
        set_transient('sq_import', true);
    }

    public function sq_deactivate() {
        try {
            //clear the cron job
            wp_clear_scheduled_hook('sq_processCron');

            $args = array();
            $args['type'] = 'deact';
            SQ_Classes_Action::apiCall('sq/user/log', $args, 5);

            remove_filter('rewrite_rules_array', array(SQ_Classes_ObjController::getClass('SQ_Core_BlockSettingsSeo'), 'rewrite_rules'));
        } catch (Exception $e) {

        }
    }

    public static function emptyCache($post_id = null) {
        if (function_exists('w3tc_pgcache_flush')) {
            w3tc_pgcache_flush();
        }
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        if (function_exists('wp_cache_post_edit') && isset($post_id)) {
            wp_cache_post_edit($post_id);
        }

        if (class_exists("WpFastestCache")) {
            $wpfc = new WpFastestCache();
            $wpfc->deleteCache();
        }
    }

    public static function checkUpgrade() {
        if (self::getOption('sq_ver') == 0 || self::getOption('sq_ver') < SQ_VERSION_ID) {
            if (self::getOption('sq_ver') < 8200) {
                //Delete the old versions table
                global $wpdb;
                $wpdb->query("UPDATE " . $wpdb->postmeta . " SET `meta_key` = '_sq_fp_title' WHERE `meta_key` = 'sq_fp_title'");
                $wpdb->query("UPDATE " . $wpdb->postmeta . " SET `meta_key` = '_sq_fp_description' WHERE `meta_key` = 'sq_fp_description'");
                $wpdb->query("UPDATE " . $wpdb->postmeta . " SET `meta_key` = '_sq_fp_ogimage' WHERE `meta_key` = 'sq_fp_ogimage'");
                $wpdb->query("UPDATE " . $wpdb->postmeta . " SET `meta_key` = '_sq_fp_keywords' WHERE `meta_key` = 'sq_fp_keywords'");
                $wpdb->query("UPDATE " . $wpdb->postmeta . " SET `meta_key` = '_sq_post_keyword' WHERE `meta_key` = 'sq_post_keyword'");

                self::createTable();

                //Import the SEO from the old format to the new format
                $seo = SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->importDBSeo('squirrly-seo');
                if (!empty($seo)) {
                    foreach ($seo as $sq_hash => $metas) {
                        SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->db_insert(
                            (isset($metas['url']) ? $metas['url'] : ''),
                            $sq_hash,
                            (int)$metas['post_id'],
                            maybe_serialize($metas),
                            gmdate('Y-m-d H:i:s'));
                    }
                }

                //Import the settings from the old format to the new format
                SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->importDBSettings('squirrly-seo');
            }


            self::saveOptions('sq_ver', SQ_VERSION_ID);

            self::getOptions();
        }
    }

    public static function isAjax() {
        if (isset(self::$is_ajax)) {
            return self::$is_ajax;
        }

        self::$is_ajax = false;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            self::$is_ajax = true;
        } else {
            $url = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : false);
            if ($url && (strpos($url, str_replace(get_bloginfo('url'), '', admin_url('admin-ajax.php', 'relative'))) !== false)) {
                self::$is_ajax = true;
            }
        }
        return self::$is_ajax;
    }

    public static function isPluginInstalled($name) {
        switch ($name) {
            case 'instapage':
                return defined('INSTAPAGE_PLUGIN_PATH');
                break;
            case 'quick-seo':
                return defined('QSS_VERSION') && defined('_QSS_ROOT_DIR_');
                break;
            case 'premium-seo-pack':
                return defined('PSP_VERSION') && defined('_PSP_ROOT_DIR_');
                break;
        }
    }

    public static function isFrontAdmin() {
        return (!is_admin() && is_user_logged_in());
    }

    public static function createTable() {
        global $wpdb;
        $sq_table_query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . strtolower(_SQ_DB_) . ' (
                      `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                      `blog_id` INT(10) NOT NULL,
                      `post_id`  bigint(20) NOT NULL DEFAULT 0 ,
                      `URL` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                      `url_hash` VARCHAR(32) NOT NULL,
                      `seo` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                      `date_time` DATETIME NOT NULL,
                      PRIMARY KEY(id),
                      UNIQUE url_hash(url_hash) USING BTREE,
                      INDEX post_id(post_id) USING BTREE, 
                      INDEX blog_id_url_hash(blog_id, url_hash) USING BTREE
                      )  CHARACTER SET utf8 COLLATE utf8_general_ci';

        if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            if (function_exists('dbDelta')) {
                dbDelta($sq_table_query, true);
                $count = $wpdb->get_row("SELECT count(*) as count
                              FROM information_schema.columns 
                              WHERE table_name = '" . $wpdb->prefix . strtolower(_SQ_DB_) . "'
                              AND column_name = 'post_id';");

                if ($count->count == 0) {
                    $wpdb->query("ALTER TABLE " . $wpdb->prefix . strtolower(_SQ_DB_) . " ADD COLUMN `post_id` bigint(20) NOT NULL DEFAULT 0");
                }
            }
        }
    }

    public static function getBusinessLink() {
        if (!self::getOption('sq_google_serp_active')) {
            return _SQ_DASH_URL_ . 'login/?token=' . self::getToken() . '&redirect_to=' . _SQ_DASH_URL_ . 'user/plans?pid=31';
        } else {
            return admin_url('admin.php?page=sq_posts');
        }

    }

    public static function isGutenberg() {
        if (function_exists('is_gutenberg_page')) {
            return @is_gutenberg_page();
        }
        return false;
    }
}
