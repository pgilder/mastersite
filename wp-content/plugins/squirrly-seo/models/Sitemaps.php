<?php

/**
 * Squirrly SEO - Sitemap Model
 *
 * Used to get the sitemap format for each type
 *
 * @class        SQ_Models_Sitemaps
 */
class SQ_Models_Sitemaps extends SQ_Models_Abstract_Seo {

    public $args = array();
    public $frequency;
    public $type;
    protected $postmodified;

    public function __construct() {
        //parent::__construct();
        //For sitemap ping
        $this->args['timeout'] = 5;

        $this->frequency = array();
        $this->frequency['hourly'] = array('sitemap-home' => array(1, 'hourly'), 'sitemap-product' => array(1, 'hourly'), 'sitemap-post' => array(1, 'hourly'), 'sitemap-page' => array(0.6, 'hourly'), 'sitemap-category' => array(0.5, 'daily'), 'sitemap-post_tag' => array(0.5, 'daily'), 'sitemap-archive' => array(0.3, 'monthly'), 'sitemap-author' => array(0.3, 'daily'), 'sitemap-custom-tax' => array(0.3, 'hourly'), 'sitemap-custom-post' => array(1, 'hourly'), 'sitemap-attachment' => array(0.3, 'hourly'));
        $this->frequency['daily'] = array('sitemap-home' => array(1, 'daily'), 'sitemap-product' => array(0.8, 'daily'), 'sitemap-post' => array(0.8, 'daily'), 'sitemap-page' => array(0.6, 'weekly'), 'sitemap-category' => array(0.5, 'weekly'), 'sitemap-post_tag' => array(0.5, 'daily'), 'sitemap-archive' => array(0.3, 'monthly'), 'sitemap-author' => array(0.3, 'weekly'), 'sitemap-custom-tax' => array(0.3, 'weekly'), 'sitemap-custom-post' => array(0.8, 'daily'), 'sitemap-attachment' => array(0.3, 'weekly'));
        $this->frequency['weekly'] = array('sitemap-home' => array(1, 'weekly'), 'sitemap-product' => array(0.8, 'weekly'), 'sitemap-post' => array(0.8, 'weekly'), 'sitemap-page' => array(0.6, 'monthly'), 'sitemap-category' => array(0.3, 'monthly'), 'sitemap-post_tag' => array(0.5, 'weekly'), 'sitemap-archive' => array(0.3, 'monthly'), 'sitemap-author' => array(0.3, 'weekly'), 'sitemap-custom-tax' => array(0.3, 'weekly'), 'sitemap-custom-post' => array(0.8, 'weekly'), 'sitemap-attachment' => array(0.3, 'monthly'));
        $this->frequency['monthly'] = array('sitemap-home' => array(1, 'monthly'), 'sitemap-product' => array(0.8, 'weekly'), 'sitemap-post' => array(0.8, 'monthly'), 'sitemap-page' => array(0.6, 'monthly'), 'sitemap-category' => array(0.3, 'monthly'), 'sitemap-post_tag' => array(0.5, 'monthly'), 'sitemap-archive' => array(0.3, 'monthly'), 'sitemap-author' => array(0.3, 'monthly'), 'sitemap-custom-tax' => array(0.3, 'monthly'), 'sitemap-custom-post' => array(0.8, 'monthly'), 'sitemap-attachment' => array(0.3, 'monthly'));
        $this->frequency['yearly'] = array('sitemap-home' => array(1, 'monthly'), 'sitemap-product' => array(0.8, 'weekly'), 'sitemap-post' => array(0.8, 'monthly'), 'sitemap-page' => array(0.6, 'yearly'), 'sitemap-category' => array(0.3, 'yearly'), 'sitemap-post_tag' => array(0.5, 'monthly'), 'sitemap-archive' => array(0.3, 'yearly'), 'sitemap-author' => array(0.3, 'yearly'), 'sitemap-custom-tax' => array(0.3, 'yearly'), 'sitemap-custom-post' => array(0.8, 'monthly'), 'sitemap-attachment' => array(0.3, 'monthly'));
    }

    /**
     * Add the Sitemap Index
     * @global  $polylang
     * @return array
     */
    public function getHomeLink() {
        $homes = array();
        $homes['contains'] = array();
        global $polylang;

        if (isset($polylang) && method_exists($polylang, 'get_languages_list') && method_exists($polylang, 'get_home_url') && SQ_Classes_Tools::getOption('sq_sitemap_combinelangs')) {
            foreach ($polylang->get_languages_list() as $term) {
                $xml = array();
                $xml['loc'] = esc_url($polylang->get_home_url($term));
                $xml['lastmod'] = trim(mysql2date('Y-m-d\TH:i:s+00:00', date('Y-m-d', strtotime(get_lastpostmodified('gmt'))), false));
                $xml['changefreq'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')]['sitemap-home'][1];
                $xml['priority'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')]['sitemap-home'][0];
                $homes[] = $xml;
            }
        } else {
            $xml = array();
            $xml['loc'] = home_url();
            $xml['lastmod'] = trim(mysql2date('Y-m-d\TH:i:s+00:00', date('Y-m-d', strtotime(get_lastpostmodified('gmt'))), false));
            $xml['changefreq'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')]['sitemap-home'][1];
            $xml['priority'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')]['sitemap-home'][0];
            if ($post_id = get_option('page_on_front')) {
                if (SQ_Classes_Tools::$options['sq_sitemap_show']['images'] == 1) {
                    if ($images = $this->getPostImages($post_id, true)) {
                        array_push($homes['contains'], 'image');
                        $xml['image:image'] = array();
                        foreach ($images as $image) {
                            if (empty($image['src'])) {
                                continue;
                            }


                            $xml['image:image'][] = array(
                                'image:loc' => $image['src'],
                                'image:title' => $this->clearTitle($image['title']),
                                'image:caption' => $this->clearDescription($image['description']),
                            );
                        }
                    }
                }
            }
            $homes[] = $xml;
            unset($xml);
        }

        return $homes;
    }

    /**
     * Add posts/pages in sitemap
     * @return array
     */
    public function getListPosts() {
        global $wp_query, $sq_query;

        $wp_query = new WP_Query($sq_query);
        $posts = array();
        $posts['contains'] = array();
        if (have_posts()) {
            while (have_posts()) {
                the_post();

                //do not incude password protected pages in sitemap
                if (post_password_required()) {
                    continue;
                }

                if (SQ_Classes_Tools::getOption('sq_sitemap_combinelangs')) {
                    if (function_exists('pll_get_post_translations')) {
                        $translates = pll_get_post_translations(get_post()->ID);

                        if (!empty($translates)) {
                            foreach ($translates as $post_id) {

                                if ($post = SQ_Classes_ObjController::getClass('SQ_Controllers_Menu')->setPostByID(get_post($post_id))) {
                                    if ($post->sq->nositemap) {
                                        continue;
                                    }

                                    $posts[] = $this->_getPostXml($post);
                                }
                            }
                            //the polylang has both posts so continue
                            continue;
                        }
                    }
                }

                if ($post = SQ_Classes_ObjController::getClass('SQ_Controllers_Menu')->setPostByID(get_post())) {
                    if ($post->sq->nositemap) {
                        continue;
                    }

                    $posts[] = $this->_getPostXml($post);
                }


            }
        }

        foreach ($posts as $post) {
            if (array_key_exists('image:image', $post)) {
                array_push($posts['contains'], 'image');
            }
            if (array_key_exists('video:video', $post)) {
                array_push($posts['contains'], 'video');
            }
        }

        return $posts;
    }

    public function getListAttachments() {
        global $wp_query, $sq_query;

        $wp_query = new WP_Query($sq_query);
        $posts = array();
        $posts['contains'] = array();
        if (have_posts()) {
            while (have_posts()) {
                the_post();

                //do not incude password protected pages in sitemap
                if (post_password_required()) {
                    continue;
                }

                if ($post = SQ_Classes_ObjController::getClass('SQ_Controllers_Menu')->setPostByID(get_post())) {
                    if ($post->sq->nositemap) {
                        continue;
                    }
                    $xml = $this->_getPostXml($post);
                    if (strpos($xml['loc'], '?') !== false) {
                        $xml['loc'] = wp_get_attachment_url($post->ID);
                    }
                    $posts[] = $xml;
                }


            }
        }

        foreach ($posts as $post) {
            if (array_key_exists('image:image', $post)) {
                array_push($posts['contains'], 'image');
            }
            if (array_key_exists('video:video', $post)) {
                array_push($posts['contains'], 'video');
            }
        }

        return $posts;
    }

    private function _getPostXml($post) {
        $xml = array();
        $xml['loc'] = esc_url(get_permalink());
        $xml['lastmod'] = trim(mysql2date('Y-m-d\TH:i:s+00:00', $this->lastModified(), false));
        $xml['changefreq'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$this->type][1];
        $xml['priority'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$this->type][0];

        if (SQ_Classes_Tools::$options['sq_sitemap_show']['images'] == 1) {
            if ($images = $this->getPostImages($post->ID, true)) {
                $xml['image:image'] = array();
                foreach ($images as $image) {
                    if (empty($image['src'])) {
                        continue;
                    }

                    $xml['image:image'][] = array(
                        'image:loc' => $image['src'],
                        'image:title' => $this->clearTitle($image['title']),
                        'image:caption' => $this->clearDescription($image['description']),
                    );
                }
            }
        }

        if (SQ_Classes_Tools::$options['sq_sitemap_show']['videos'] == 1) {
            $images = $this->getPostImages($post->ID, true);
            if (isset($images[0]['src']) && $videos = $this->getPostVideos($post->ID)) {
                $xml['video:video'] = array();
                foreach ($videos as $video) {
                    if ($video == '') {
                        continue;
                    }

                    $xml['video:video'][$post->ID] = array(
                        'video:player_loc' => $video,
                        'video:thumbnail_loc' => $images[0]['src'],
                        'video:title' => $this->clearTitle($post->sq->title),
                        'video:description' => $this->clearDescription($post->sq->description),
                    );

                    //set the first keyword for this video
                    $keywords = $post->sq->keywords;
                    $keywords = preg_split('/,/', $keywords);
                    if (is_array($keywords)) {
                        $xml['video:video'][$post->ID]['video:tag'] = $keywords[0];
                    }
                }
            }
        }
        return $xml;
    }

    /**
     * Add the post news in sitemap
     * If the site is registeres for google news
     * @return array
     */
    public function getListNews() {
        global $wp_query, $sq_query;
        $wp_query = new WP_Query($sq_query);

        $posts = array();
        $posts['contains'] = array();

        if (have_posts()) {
            while (have_posts()) {
                the_post();

                if ($post = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->setPost(get_post())->getPost()) {
                    if ($post->sq->nositemap) {
                        continue;
                    }

                    $xml = array();

                    $xml['loc'] = esc_url(get_permalink());
                    $language = convert_chars(strip_tags(get_bloginfo('language')));
                    $language = substr($language, 0, strpos($language, '-'));
                    if ($language == '')
                        $language = 'en';

                    $xml['news:news'][$post->ID] = array(
                        'news:publication' => array(
                            'news:name' => $this->sanitizeString(get_bloginfo('name')),
                            'news:language' => $language
                        )
                    );
                    $xml['news:news'][$post->ID]['news:publication_date'] = trim(mysql2date('Y-m-d\TH:i:s+00:00', $this->lastModified(), false));
                    $xml['news:news'][$post->ID]['news:title'] = $this->sanitizeString($post->sq->title);
                    $xml['news:news'][$post->ID]['news:keywords'] = $post->sq->keywords;


                    if (SQ_Classes_Tools::$options['sq_sitemap_show']['images'] == 1) {
                        if ($images = $this->getPostImages($post->ID, true)) {
                            array_push($posts['contains'], 'image');
                            $xml['image:image'] = array();
                            foreach ($images as $image) {
                                if (empty($image['src'])) {
                                    continue;
                                }

                                $xml['image:image'][] = array(
                                    'image:loc' => $image['src'],
                                    'image:title' => $this->clearTitle($image['title']),
                                    'image:caption' => $this->clearDescription($image['description']),
                                );
                            }
                        }
                    }

                    if (SQ_Classes_Tools::$options['sq_sitemap_show']['videos'] == 1) {
                        $images = $this->getPostImages($post->ID, true);
                        if (isset($images[0]['src']) && $videos = $this->getPostVideos($post->ID)) {
                            array_push($posts['contains'], 'video');
                            $xml['video:video'] = array();
                            foreach ($videos as $video) {
                                if ($video == '') {
                                    continue;
                                }


                                $xml['video:video'][$post->ID] = array(
                                    'video:player_loc' => $video,
                                    'video:thumbnail_loc' => $images[0]['src'],
                                    'video:title' => $this->clearTitle($post->sq->title),
                                    'video:description' => $this->clearDescription($post->sq->description),
                                );

                                //set the first keyword for this video
                                $keywords = $post->sq->keywords;
                                $keywords = preg_split('/,/', $keywords);
                                if (is_array($keywords)) {
                                    $xml['video:video'][$post->ID]['video:tag'] = $keywords[0];
                                }
                            }
                        }
                    }
                    $posts[] = $xml;
                    unset($xml);
                }
            }
        }

        return $posts;
    }

    /**
     * Add the Taxonomies in sitemap
     * @param string $type
     * @return array
     */
    public function getListTerms($type = null) {
        if (!isset($type)) {
            $type = $this->type;
        }

        $terms = $array = array();
        $array['contains'] = array();
        if ($type == 'sitemap-custom-tax') {
            $taxonomies = $this->excludeTypes(get_taxonomies(), array('category', 'post_tag', 'nav_menu', 'link_category', 'post_format'));
            if (!empty($taxonomies)) {
                $taxonomies = array_unique($taxonomies);
            }
            foreach ($taxonomies as $taxonomy) {
                $array = array_merge($array, $this->getListTerms($taxonomy));

            }
        } else {
            $terms = get_terms(str_replace('sitemap-', '', $type));
        }

        if (!isset(SQ_Classes_Tools::$options['sq_sitemap'][$type])) {
            $type = 'sitemap-custom-tax';
        }

        if (!empty($terms)) {
            foreach ($terms AS $term) {
                $xml = array();
                if (!isset($term->taxonomy)) {
                    continue;
                }
                $term->lastmod = (isset($term->lastmod) ? $term->lastmod : time());
                $xml['loc'] = esc_url(get_term_link($term, $term->taxonomy));
                $xml['lastmod'] = date('Y-m-d\TH:i:s+00:00', $term->lastmod);
                $xml['changefreq'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$type][1];
                $xml['priority'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$type][0];

                $array[] = $xml;
            }
        }

        return $array;
    }

    /**
     * Add the authors in sitemap
     * @return array
     */
    public function getListAuthors() {
        $array = array();
        $authors = apply_filters('sq-sitemap-authors', $this->type);

        if (!empty($authors)) {
            foreach ($authors AS $author) {
                $xml = array();

                $xml['loc'] = get_author_posts_url($author->ID, $author->user_nicename);
                if (isset($author->lastmod) && $author->lastmod <> '')
                    $xml['lastmod'] = date('Y-m-d\TH:i:s+00:00', strtotime($author->lastmod));
                $xml['changefreq'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$this->type][1];
                $xml['priority'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$this->type][0];

                $array[] = $xml;
            }
        }
        return $array;
    }

    /**
     * Add the archive in sitemap
     * @return array
     */
    public function getListArchive() {
        $array = array();
        $archives = apply_filters('sq-sitemap-archive', $this->type);
        if (!empty($archives)) {
            foreach ($archives as $archive) {
                $xml = array();

                $xml['loc'] = get_month_link($archive->year, $archive->month);
                if (isset($archive->lastmod) && $archive->lastmod <> '')
                    $xml['lastmod'] = date('Y-m-d\TH:i:s+00:00', strtotime($archive->lastmod));

                $xml['changefreq'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$this->type][1];
                $xml['priority'] = $this->frequency[SQ_Classes_Tools::getOption('sq_sitemap_frequency')][$this->type][0];

                $array[] = $xml;
            }
        }

        return $array;
    }

    private function sanitizeString($string) {
        return esc_html(ent2ncr(strip_tags($string)));
    }

    /**
     * Get the last modified date for the specific post/page
     *
     * @global WP_Post $post
     * @param string $sitemap
     * @param string $term
     * @return string
     */
    public function lastModified($sitemap = 'post_type', $term = '') {
        if ('post_type' == $sitemap) :
            global $post;

            if (isset($post->ID)) {
                if (empty($this->postmodified[$post->ID])) {
                    $postmodified = get_post_modified_time('Y-m-d H:i:s', true, $post->ID);
                    $options = get_option('post_types');

                    if (!empty($options[$post->post_type]['update_lastmod_on_comments']))
                        $lastcomment = get_comments(array(
                            'status' => 'approve',
                            'number' => 1,
                            'post_id' => $post->ID,
                        ));

                    if (isset($lastcomment[0]->comment_date_gmt))
                        if (mysql2date('U', $lastcomment[0]->comment_date_gmt) > mysql2date('U', $postmodified))
                            $postmodified = $lastcomment[0]->comment_date_gmt;

                    $this->postmodified[$post->ID] = $postmodified;
                }
                return $this->postmodified[$post->ID];
            }

            return '0000-00-00 00:00:00';
        elseif (!empty($term)) :

            if (is_object($term)) {
                if (!isset($this->termmodified[$term->term_id])) {
                    // get the latest post in this taxonomy item, to use its post_date as lastmod
                    $posts = get_posts(array(
                            'post_type' => 'any',
                            'numberposts' => 1,
                            'no_found_rows' => true,
                            'update_post_meta_cache' => false,
                            'update_post_term_cache' => false,
                            'update_cache' => false,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => $term->taxonomy,
                                    'field' => 'slug',
                                    'terms' => $term->slug
                                )
                            )
                        )
                    );
                    $this->termmodified[$term->term_id] = isset($posts[0]->post_date_gmt) ? $posts[0]->post_date_gmt : '';
                }
                return $this->termmodified[$term->term_id];
            } else {
                $obj = get_taxonomy($term);
                return get_lastdate('gmt', $obj->object_type);
            }

        else :

            return '0000-00-00 00:00:00';

        endif;
    }

    /**
     * Check if the current page is the home page
     * @global  $polylang
     * @param integer $id
     * @return boolean
     */
    public function is_home($id) {
        $home = array();
        $id = get_option('page_for_posts');

        if (!empty($id)) {
            global $polylang;
            if (isset($polylang)) {
                $home = $polylang->get_translations('post', $id);
            } else {
                $home = array($id);
            }
        }

        return in_array($id, $home);
    }

    /**
     * Excude types from array
     * @param array $types
     * @param array $exclude
     * @return array
     */
    public function excludeTypes($types, $exclude) {
        foreach ($exclude as $value) {
            if (in_array($value, $types)) {
                unset($types[$value]);
            }
        }
        return $types;
    }

}