<?php

class SQ_Models_Services_JsonLD extends SQ_Models_Abstract_Seo {
    private $_data = array();
    private $_types = array();


    public function __construct() {
        parent::__construct();
        if ($this->_post->sq->doseo) {
            if (function_exists('is_tag')) {
                if (is_tag()) return;
            }
            if (function_exists('is_category')) {
                if (is_category()) return;
            }

            if (class_exists('WooCommerce')) {
                // Generate structured data for Woocommerce 3.
                add_action('woocommerce_before_main_content', array($this, 'generate_website_data'), 31);
                add_action('woocommerce_breadcrumb', array($this, 'generate_breadcrumblist_data'), 11);
                add_action('woocommerce_shop_loop', array($this, 'generate_product_data'), 11);
                add_action('woocommerce_single_product_summary', array($this, 'generate_product_data'), 61);
                add_action('woocommerce_review_meta', array($this, 'generate_review_data'), 21);
                add_action('woocommerce_email_order_details', array($this, 'generate_order_data'), 21, 3);

                remove_action('wp_footer', array(WC()->structured_data, 'output_structured_data'), 10); // Frontend pages
            } else {
                add_filter('sq_json_ld', array($this, 'generate_breadcrumblist_data_blog'), 9);
            }
            add_filter('sq_json_ld', array($this, 'generate_structured_data_blog'), 9);

            add_filter('sq_json_ld', array($this, 'generateJsonLd'));
            add_filter('sq_json_ld', array($this, 'packJsonLd'), 99);
        } else {
            add_filter('sq_json_ld', array($this, 'returnFalse'));
        }


    }


    /**
     * Sanitizes, encodes and outputs structured data.
     *
     * @return array|string
     */
    public function generateJsonLd() {
        $types = $this->get_data_type_for_page();
        return $this->clean($this->get_structured_data($types));
    }

    /**
     * Pack the Structured Data
     */
    public function packJsonLd($data = array()) {
        if (!empty($data)) {
            return '<script type="application/ld+json">' . wp_json_encode($data) . '</script>';
        }

        return false;
    }

    /**
     * Sets data.
     *
     * @param  array $data Structured data.
     * @param  bool $reset Unset data (default: false).
     * @return bool
     */
    public function set_data($data, $reset = false) {
        if (!isset($data['@type']) || !preg_match('|^[a-zA-Z]{1,20}$|', $data['@type'])) {
            return false;
        } else {
            $this->_types[] = strtolower($data['@type']);
            $this->_types = array_unique($this->_types);
        }

        if ($reset && isset($this->_data)) {
            unset($this->_data);
        }

        $this->_data[] = $data;
        return true;
    }


    /**
     * Gets data.
     *
     * @return array
     */
    public function get_data() {
        return $this->_data;
    }


    /**
     * Structures and returns data.
     *
     * List of types available by default for specific request:
     *
     * 'product',
     * 'review',
     * 'breadcrumblist',
     * 'website',
     * 'order',
     *
     * @param  array $types Structured data types.
     * @return array
     */
    public function get_structured_data($types) {
        $data = array();

        // Put together the values of same type of structured data.
        foreach ($this->get_data() as $value) {
            $data[strtolower($value['@type'])][] = $value;
        }

        // Wrap the multiple values of each type inside a graph... Then add context to each type.
        foreach ($data as $type => $value) {
            $data[$type] = count($value) > 1 ? array('@graph' => $value) : $value[0];
            $data[$type] = apply_filters('woocommerce_structured_data_context', array('@context' => 'https://schema.org/'), $data, $type, $value) + $data[$type];
        }

        // If requested types, pick them up... Finally change the associative array to an indexed one.
        $data = $types ? array_values(array_intersect_key($data, array_flip($types))) : array_values($data);

        if (!empty($data)) {
            $data = count($data) > 1 ? array('@graph' => $data) : $data[0];
        }

        return $data;
    }


    /**
     * Get data types for pages.
     *
     * @return array
     */
    public function get_data_type_for_page() {
        if (class_exists('WooCommerce')) {
            $this->_types[] = is_shop() || is_product_category() || is_product() ? 'product' : '';
            $this->_types[] = is_shop() && is_front_page() ? 'website' : '';
            $this->_types[] = is_product() ? 'review' : '';
            $this->_types[] = !is_shop() ? 'breadcrumblist' : '';
            $this->_types[] = 'order';
        }

        return array_filter(apply_filters('sq_structured_data_type_for_page', $this->_types));
    }


    public function clean($var) {
        if (is_array($var)) {
            return array_map(array($this, 'clean'), $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }

    public function generate_structured_data_blog() {
        $jsonld = SQ_Classes_Tools::getOption('sq_jsonld');
        $jsonld_type = SQ_Classes_Tools::getOption('sq_jsonld_type');
        $socials = SQ_Classes_Tools::getOption('socials');

        if (is_home() || $this->_post->post_type == 'home') {
            $markup['@type'] = $jsonld_type;
            $markup['@id'] = $this->_post->url;
            $markup['url'] = $this->_post->url;

            if (isset($jsonld[$jsonld_type])) {
                foreach ($jsonld[$jsonld_type] as $key => $value) {
                    if ($value <> '') {
                        if ($key == 'contactType' || ($jsonld_type == 'Organization' && $key == 'jobTitle')) {
                            continue;
                        }
                        if ($jsonld_type == 'Organization' && $key == 'telephone') {
                            $markup['contactPoint'] = array(
                                '@type' => 'ContactPoint',
                                'telephone' => $value,
                                'contactType' => $jsonld[$jsonld_type]['contactType'],

                            );
                        }

                        if ($key == 'logo') {
                            if ($jsonld_type == 'Person') {
                                $key = 'image';
                            }
                            $markup[$key] = array(
                                '@type' => 'ImageObject',
                                'url' => $value,
                            );
                        } else {
                            $markup[$key] = $value;
                        }

                    }
                }
            }

            if (!empty($markup)) {
                $jsonld_socials = array();
                if (isset($socials['facebook_site']) && $socials['facebook_site'] <> '') {
                    $jsonld_socials[] = $socials['facebook_site'];
                }
                if (isset($socials['twitter_site']) && $socials['twitter_site'] <> '') {
                    $jsonld_socials[] = $socials['twitter_site'];
                }
                if (isset($socials['instagram_url']) && $socials['instagram_url'] <> '') {
                    $jsonld_socials[] = $socials['instagram_url'];
                }
                if (isset($socials['linkedin_url']) && $socials['linkedin_url'] <> '') {
                    $jsonld_socials[] = $socials['linkedin_url'];
                }
                if (isset($socials['myspace_url']) && $socials['myspace_url'] <> '') {
                    $jsonld_socials[] = $socials['myspace_url'];
                }
                if (isset($socials['twitter']) && $socials['twitter'] <> '') {
                    $jsonld_socials[] = $socials['twitter'];
                }
                if (isset($socials['pinterest_url']) && $socials['pinterest_url'] <> '') {
                    $jsonld_socials[] = $socials['pinterest_url'];
                }
                if (isset($socials['youtube_url']) && $socials['youtube_url'] <> '') {
                    $jsonld_socials[] = $socials['youtube_url'];
                }

                $markup['potentialAction'] = array(
                    '@type' => 'SearchAction',
                    'target' => home_url('?s={search_term_string}'),
                    'query-input' => 'required name=search_term_string',
                );

                if (!empty($jsonld_socials)) {
                    $markup['sameAs'] = $jsonld_socials;
                }
            }
            //add current markup
            $this->set_data($markup);
        } elseif (is_author()) {
            $markup['@type'] = 'Person';
            $markup['@id'] = $this->getAuthor('user_url');
            $markup['url'] = $this->getAuthor('user_url');
            $markup['name'] = $this->getAuthor('display_name');

            //add current markup
            $this->set_data($markup);
        } elseif ($this->_post->sq->og_type == 'website') {
            $markup['@type'] = 'Website';
            $markup['@id'] = $this->_post->url;
            $markup['url'] = $this->_post->url;

            if (isset($this->_post->sq->title)) {
                $markup['name'] = $this->truncate($this->_post->sq->title, 0, $this->_post->sq->jsonld_title_maxlength);
            }

            if (isset($this->_post->sq->description)) {
                $markup['headline'] = $this->truncate($this->_post->sq->description, 0, $this->_post->sq->jsonld_description_maxlength);
            }
            $markup['mainEntityOfPage'] = array(
                '@type' => 'WebPage',
                'url' => $this->_post->url
            );

            if ($this->_post->sq->og_media <> '') {
                $markup['thumbnailUrl'] = $this->_post->sq->og_media;
            }
            if (isset($this->_post->post_date)) {
                $markup['datePublished'] = date('c', strtotime($this->_post->post_date));
            }
            if (isset($this->_post->post_modified)) {
                $markup['dateModified'] = date('c', strtotime($this->_post->post_modified));
            }

            if ($this->_post->sq->og_media <> '') {
                $markup['image'] = array(
                    "@type" => "ImageObject",
                    "url" => $this->_post->sq->og_media,
                    "height" => 500,
                    "width" => 700,
                );
            } else {
                $this->_setMedia($markup);
            }

            $user_url = $this->getAuthor('user_url');
            $display_name = $this->getAuthor('display_name');
            if ($user_url <> '' && $display_name <> '') {
                $markup['author'] = array(
                    "@type" => "Person",
                    "url" => $user_url,
                    "name" => $display_name,
                );
            }

            if ($jsonld_type == 'Organization' && isset($jsonld[$jsonld_type])) {
                $markup['publisher'] = array(
                    "@type" => $jsonld_type,
                    "url" => $this->_post->url,
                    "name" => $this->getAuthor('display_name'),
                );

                foreach ($jsonld[$jsonld_type] as $key => $value) {
                    if ($value <> '') {
                        if ($key == 'contactType' || $key == 'telephone' || $key == 'jobTitle') {
                            continue;
                        }

                        if ($key == 'logo') {
                            $markup['publisher']['logo'] = array(
                                "@type" => "ImageObject",
                                "url" => $value
                            );

                        } else {
                            $markup['publisher'][$key] = $value;
                        }
                    }
                }
            }

            if ($this->_post->sq->keywords <> '') {
                $markup['keywords'] = $this->_post->sq->keywords;
            }
            //add current markup
            $this->set_data($markup);
        }elseif ($this->_post->post_type == 'post' || $this->_post->sq->og_type == 'article') {
            $markup['@type'] = 'Article';
            $markup['@id'] = $this->_post->url;
            $markup['url'] = $this->_post->url;

            if (isset($this->_post->sq->title)) {
                $markup['name'] = $this->truncate($this->_post->sq->title, 0, $this->_post->sq->jsonld_title_maxlength);
            }

            if (isset($this->_post->sq->description)) {
                $markup['headline'] = $this->truncate($this->_post->sq->description, 0, $this->_post->sq->jsonld_description_maxlength);
            }
            $markup['mainEntityOfPage'] = array(
                '@type' => 'WebPage',
                'url' => $this->_post->url
            );

            if ($this->_post->sq->og_media <> '') {
                $markup['thumbnailUrl'] = $this->_post->sq->og_media;
            }
            if (isset($this->_post->post_date)) {
                $markup['datePublished'] = date('c', strtotime($this->_post->post_date));
            }
            if (isset($this->_post->post_modified)) {
                $markup['dateModified'] = date('c', strtotime($this->_post->post_modified));
            }

            if ($this->_post->sq->og_media <> '') {
                $markup['image'] = array(
                    "@type" => "ImageObject",
                    "url" => $this->_post->sq->og_media,
                    "height" => 500,
                    "width" => 700,
                );
            } else {
                $this->_setMedia($markup);
            }

            $user_url = $this->getAuthor('user_url');
            $display_name = $this->getAuthor('display_name');
            if ($user_url <> '' && $display_name <> '') {
                $markup['author'] = array(
                    "@type" => "Person",
                    "url" => $user_url,
                    "name" => $display_name,
                );
//
//                if(isset($jsonld[$jsonld_type]['logo']) && $jsonld[$jsonld_type]['logo'] <> '') {
//                    $markup['author']['image'] = array(
//                        '@type' => 'ImageObject',
//                        'url' => $jsonld[$jsonld_type]['logo'],
//                    );
//                }
            }

            if ($jsonld_type == 'Organization' && isset($jsonld[$jsonld_type])) {
                $markup['publisher'] = array(
                    "@type" => $jsonld_type,
                    "url" => $this->_post->url,
                    "name" => $this->getAuthor('display_name'),
                );

                foreach ($jsonld[$jsonld_type] as $key => $value) {
                    if ($value <> '') {
                        if ($key == 'contactType' || $key == 'telephone' || $key == 'jobTitle') {
                            continue;
                        }

                        if ($key == 'logo') {
                            $markup['publisher']['logo'] = array(
                                "@type" => "ImageObject",
                                "url" => $value
                            );

                        } else {
                            $markup['publisher'][$key] = $value;
                        }
                    }
                }
            }elseif ($jsonld_type == 'Person' && isset($jsonld[$jsonld_type])) {
                    $markup['publisher'] = array(
                        "@type" => $jsonld_type,
                        "name" => $this->getAuthor('display_name'),
                    );

                    foreach ($jsonld[$jsonld_type] as $key => $value) {
                        if ($value <> '') {

                            if ($key == 'logo') {
                                $markup['publisher']['image'] = array(
                                    "@type" => "ImageObject",
                                    "url" => $value
                                );

                            } else {
                                $markup['publisher'][$key] = $value;
                            }
                        }
                    }
            }

            if ($this->_post->sq->keywords <> '') {
                $markup['keywords'] = $this->_post->sq->keywords;
            }
            //add current markup
            $this->set_data($markup);
        }
    }

    /** Set the Image from Feature image
     * @param $markup
     */
    protected function _setMedia(&$markup) {
        $images = $this->getPostImages();
        if (!empty($images)) {
            $image = current($images);
            if (isset($image['src'])) {
                $markup['image'] = array(
                    "@type" => "ImageObject",
                    "url" => $image['src'],
                    "height" => 500,
                    "width" => 700,
                );
                if (isset($image['width'])) {
                    $markup['image']["width"] = $image['width'];
                }
                if (isset($image['height'])) {
                    $markup['image']["height"] = $image['height'];
                }
            }
        }
    }

    /**
     * Get the author
     * @param string $what
     * @return bool|mixed|string
     */
    protected function getAuthor($what = 'user_nicename') {
        if (!isset($this->author)) {
            if (is_author()) {
                $this->author = get_userdata(get_query_var('author'));
            } elseif (is_single() && isset($this->_post->post_author)) {
                $this->author = get_userdata((int)$this->_post->post_author)->data;
            }
        }

        if (isset($this->author)) {

            if ($what == 'user_url' && $this->author->$what == '') {
                return get_author_posts_url($this->author->ID, $this->author->user_nicename);
            }
            if (isset($this->author->$what)) {
                return $this->author->$what;
            }
        }

        return false;
    }


    /**
     * Generates BreadcrumbList structured data.
     *
     * Hooked into `get_post_ancestors` action hook.
     *
     * @param $breadcrumbs Breadcrumb data.
     */
    public function generate_breadcrumblist_data_blog() {
        global $post;
        $crumbs = $markup = array();

        if (isset($post->ID)) {
            $ancestors = get_post_ancestors($post);
            if (!empty($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $crumbs[] = array(
                        strip_tags(get_the_title($ancestor)),
                        get_permalink($ancestor),
                    );
                }
            }

            if (!empty($crumbs)) {
                $markup['@type'] = 'BreadcrumbList';
                $markup['itemListElement'] = array();

                foreach ($crumbs as $key => $crumb) {
                    $markup['itemListElement'][$key] = array(
                        '@type' => 'ListItem',
                        'position' => $key + 1,
                        'item' => array(
                            'name' => $crumb[0],
                            '@id' => $crumb[1]
                        ),
                    );

                }
            }
        }

        $this->set_data($markup);
    }

    /**
     * Generates Product structured data.
     *
     * Hooked into `woocommerce_single_product_summary` action hook.
     * Hooked into `woocommerce_shop_loop` action hook.
     *
     * @param WC_Product $product Product data (default: null).
     */
    public function generate_product_data($product = null) {
        if (!$product instanceof WC_Product) {
            global $product;
        }

        $shop_name = get_bloginfo('name');
        $shop_url = home_url();
        $currency = get_woocommerce_currency();
        $markup = array();
        $markup['@type'] = 'Product';
        if (method_exists($product, 'get_id')) {
            $markup['@id'] = get_permalink($product->get_id());
        }
        $markup['url'] = $markup['@id'];
        if (method_exists($product, 'get_name')) {
            $markup['name'] = $product->get_name();
        } else {
            $markup['name'] = $product->get_title();
        }

        if (apply_filters('woocommerce_structured_data_product_limit', is_product_taxonomy() || is_shop())) {
            $this->set_data(apply_filters('woocommerce_structured_data_product_limited', $markup, $product));
            return;
        }

        $markup_offer = array(
            '@type' => 'Offer',
            'priceCurrency' => $currency,
            'availability' => 'https://schema.org/' . $stock = ($product->is_in_stock() ? 'InStock' : 'OutOfStock'),
            'sku' => (method_exists($product, 'get_sku')) ? $product->get_sku() : '',
            'image' => (method_exists($product, 'get_image_id')) ? wp_get_attachment_url($product->get_image_id()) : '',
            'description' => (method_exists($product, 'get_description') ? $product->get_description() : $product->get_title()),
            'seller' => array(
                '@type' => 'Organization',
                'name' => $shop_name,
                'url' => $shop_url,
            ),
        );

        if ($product->is_type('variable') && method_exists($product, 'get_variation_prices')) {
            $prices = $product->get_variation_prices();

            $markup_offer['priceSpecification'] = array(
                'price' => wc_format_decimal($product->get_price(), wc_get_price_decimals()),
                'minPrice' => wc_format_decimal(current($prices['price']), wc_get_price_decimals()),
                'maxPrice' => wc_format_decimal(end($prices['price']), wc_get_price_decimals()),
                'priceCurrency' => $currency,
            );
        } else {
            $markup_offer['price'] = wc_format_decimal($product->get_price(), wc_get_price_decimals());
        }

        $markup['offers'] = $markup_offer;

        if ($product->get_rating_count()) {
            $markup['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $product->get_average_rating(),
                'ratingCount' => $product->get_rating_count(),
                'reviewCount' => $product->get_review_count(),
            );
        }

        $this->set_data($markup);
    }

    /**
     * Generates Review structured data.
     *
     * Hooked into `generate_review_data` action hook.
     *
     * @param WP_Comment $comment Comment data.
     */
    public function generate_review_data($comment) {
        $markup = array();
        $markup['@type'] = 'Review';
        $markup['@id'] = get_comment_link($comment->comment_ID);
        $markup['datePublished'] = get_comment_date('c', $comment->comment_ID);
        $markup['description'] = get_comment_text($comment->comment_ID);
        $markup['itemReviewed'] = array(
            '@type' => 'Product',
            'name' => get_the_title($comment->comment_post_ID),
        );
        if ($rating = get_comment_meta($comment->comment_ID, 'rating', true)) {
            $markup['reviewRating'] = array(
                '@type' => 'rating',
                'ratingValue' => $rating,
            );

            // Skip replies unless they have a rating.
        } elseif ($comment->comment_parent) {
            return;
        }

        $markup['author'] = array(
            '@type' => 'Person',
            'name' => get_comment_author($comment->comment_ID),
        );

        $this->set_data($markup);
    }

    /**
     * Generates BreadcrumbList structured data.
     *
     *
     * @param WC_Breadcrumb $breadcrumbs Breadcrumb data.
     */
    public function generate_breadcrumblist_data($breadcrumbs) {
        $crumbs = $breadcrumbs->get_breadcrumb();

        $markup = array();
        $markup['@type'] = 'BreadcrumbList';
        $markup['itemListElement'] = array();

        foreach ($crumbs as $key => $crumb) {
            $markup['itemListElement'][$key] = array(
                '@type' => 'ListItem',
                'position' => $key + 1,
                'item' => array(
                    'name' => $crumb[0],
                ),
            );

            if (!empty($crumb[1]) && sizeof($crumbs) !== $key + 1) {
                $markup['itemListElement'][$key]['item'] += array('@id' => $crumb[1]);
            }
        }

        $this->set_data($markup);
    }

    /**
     * Generates WebSite structured data.
     *
     */
    public function generate_website_data() {
        $markup = array();
        $markup['@type'] = 'WebSite';
        $markup['name'] = get_bloginfo('name');
        $markup['url'] = home_url();
        $markup['potentialAction'] = array(
            '@type' => 'SearchAction',
            'target' => home_url('?s={search_term_string}&post_type=product'),
            'query-input' => 'required name=search_term_string',
        );

        $this->set_data($markup);
    }

    /**
     * Generates Order structured data.
     *
     *
     * @param WP_Order $order Order data.
     * @param bool $sent_to_admin Send to admin (default: false).
     * @param bool $plain_text Plain text email (default: false).
     */
    public function generate_order_data($order, $sent_to_admin = false, $plain_text = false) {
        if ($plain_text || !is_a($order, 'WC_Order')) {
            return;
        }

        $shop_name = get_bloginfo('name');
        $shop_url = home_url();
        $order_url = $sent_to_admin ? admin_url('post.php?post=' . absint($order->get_id()) . '&action=edit') : $order->get_view_order_url();
        $order_statuses = array(
            'pending' => 'https://schema.org/OrderPaymentDue',
            'processing' => 'https://schema.org/OrderProcessing',
            'on-hold' => 'https://schema.org/OrderProblem',
            'completed' => 'https://schema.org/OrderDelivered',
            'cancelled' => 'https://schema.org/OrderCancelled',
            'refunded' => 'https://schema.org/OrderReturned',
            'failed' => 'https://schema.org/OrderProblem',
        );

        $markup_offers = array();
        foreach ($order->get_items() as $item) {
            if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
                continue;
            }

            $product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);
            $product_exists = is_object($product);
            $is_visible = $product_exists && $product->is_visible();

            $markup_offers[] = array(
                '@type' => 'Offer',
                'price' => $order->get_line_subtotal($item),
                'priceCurrency' => $order->get_currency(),
                'priceSpecification' => array(
                    'price' => $order->get_line_subtotal($item),
                    'priceCurrency' => $order->get_currency(),
                    'eligibleQuantity' => array(
                        '@type' => 'QuantitativeValue',
                        'value' => apply_filters('woocommerce_email_order_item_quantity', $item['qty'], $item),
                    ),
                ),
                'itemOffered' => array(
                    '@type' => 'Product',
                    'name' => apply_filters('woocommerce_order_item_name', $item['name'], $item, $is_visible),
                    'sku' => $product_exists ? $product->get_sku() : '',
                    'image' => $product_exists ? wp_get_attachment_image_url($product->get_image_id()) : '',
                    'url' => $is_visible ? get_permalink($product->get_id()) : get_home_url(),
                ),
                'seller' => array(
                    '@type' => 'Organization',
                    'name' => $shop_name,
                    'url' => $shop_url,
                ),
            );
        }

        $markup = array();
        $markup['@type'] = 'Order';
        $markup['url'] = $order_url;
        $markup['orderNumber'] = $order->get_order_number();
        $markup['orderDate'] = $order->get_date_created()->format('c');
        $markup['acceptedOffer'] = $markup_offers;
        $markup['discount'] = $order->get_total_discount();
        $markup['discountCurrency'] = $order->get_currency();
        $markup['price'] = $order->get_total();
        $markup['priceCurrency'] = $order->get_currency();
        $markup['priceSpecification'] = array(
            'price' => $order->get_total(),
            'priceCurrency' => $order->get_currency(),
            'valueAddedTaxIncluded' => true,
        );
        $markup['billingAddress'] = array(
            '@type' => 'PostalAddress',
            'name' => $order->get_formatted_billing_full_name(),
            'streetAddress' => $order->get_billing_address_1(),
            'postalCode' => $order->get_billing_postcode(),
            'addressLocality' => $order->get_billing_city(),
            'addressRegion' => $order->get_billing_state(),
            'addressCountry' => $order->get_billing_country(),
            'email' => $order->get_billing_email(),
            'telephone' => $order->get_billing_phone(),
        );
        $markup['customer'] = array(
            '@type' => 'Person',
            'name' => $order->get_formatted_billing_full_name(),
        );
        $markup['merchant'] = array(
            '@type' => 'Organization',
            'name' => $shop_name,
            'url' => $shop_url,
        );
        $markup['potentialAction'] = array(
            '@type' => 'ViewAction',
            'name' => 'View Order',
            'url' => $order_url,
            'target' => $order_url,
        );

        $this->set_data(apply_filters('woocommerce_structured_data_order', $markup, $sent_to_admin, $order), true);
    }
}