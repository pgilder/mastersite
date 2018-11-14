<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class SQ_Models_SerpCheckerTable extends WP_List_Table {

    protected $serpChecker;
    public $_column_headers;
    public $posts; //save post list for Squirrly call
    private $order_posts;
    /** @var int serp changes */
    private $changes = 0;

    public function __construct() {
        parent::__construct();
        $this->serpChecker = SQ_Classes_ObjController::getClass('SQ_Models_SerpChecker');

        $this->posts = array();
        $this->order_posts = array();
    }

    public function wp_edit_posts_query($q = false) {
        global $current_user;

        if (false === $q)
            $q = $_GET;

        $q['m'] = isset($q['m']) ? (int)$q['m'] : 0;
        $q['cat'] = isset($q['cat']) ? (int)$q['cat'] : 0;
        $post_stati = get_post_stati();

        if (isset($q['author'])) {
            $author = $q['author'];
        } else {
            if (isset($current_user->caps['contributor']) || isset($current_user->caps['author'])) {
                $author = $current_user->ID;
            } else {
                $author = '';
            }
        }

        if (isset($q['type']) && in_array($q['type'], $this->serpChecker->getPostTypes())) {
            $post_type = $q['type'];
            $avail_post_stati = get_available_post_statuses($post_type);
        } else {
            $post_type = array('post', 'page');
            $avail_post_stati = get_available_post_statuses('post');

            $args = array(
                '_builtin' => false,
            );
            $output = 'objects'; // names or objects
            $post_types = get_post_types($args, $output);

            foreach ($post_types as $other_post_type) {
                if ($other_post_type->public) {
                    array_push($post_type, $other_post_type->query_var);
                }
            }
        }
        if (isset($q['post_status']) && in_array($q['post_status'], $post_stati)) {
            $post_status = $q['post_status'];
            $perm = 'readable';
        } else {
            $post_status = 'publish';
            $perm = 'readable';
        }
        if (!isset($q['orderby'])) {
            $_GET['orderby'] = $q['orderby'] = 'rank';
            $_GET['order'] = $q['order'] = 'asc';
        }

        if (isset($q['orderby']))
            $orderby = $q['orderby'];
        elseif (isset($q['post_status']) && in_array($q['post_status'], array('pending', 'draft')))
            $orderby = 'modified';

        if (isset($q['order']))
            $order = $q['order'];
        elseif (isset($q['post_status']) && 'pending' == $q['post_status'])
            $order = 'ASC';


        $per_page = 'edit_post_per_page';
        $posts_per_page = (int)get_user_option($per_page);
        if (empty($posts_per_page) || $posts_per_page < 1) {
            $posts_per_page = 20;
        }

        $posts_per_page = apply_filters($per_page, $posts_per_page);
        $posts_per_page = apply_filters('edit_posts_per_page', $posts_per_page, 'post');

        $query = compact('post_type', 'author', 'post_status', 'perm', 'order', 'orderby', 'meta_key', 'posts_per_page');


        wp($query);
        return $avail_post_stati;
    }

    /**
     * Prepare the current query
     * @param $query WP_Query
     */
    public function prepareQuery($query) {
        $orderby = SQ_Classes_Tools::getValue('orderby', 'rank');

        $post__in = array(0);
        $posts = $this->serpChecker->getPostWithKeywords();
        if ($posts !== false && !empty($posts)) {
            //sort descending
            foreach ($posts as $post) {
                if (!isset($post->meta_value->keyword) ||
                    !isset($post->meta_value->rank) ||
                    !isset($post->meta_value->change) ||
                    !isset($post->meta_value->datetime)) {
                    continue;
                }


                // SQ_Classes_Tools::dump($post);
                if ($post->meta_value->keyword == '') {
                    continue;
                }

                if ((int)$post->meta_value->change <> 0) {
                    ++$this->changes;
                } elseif (SQ_Classes_Tools::getIsset('schanges')) continue;

                if (SQ_Classes_Tools::getIsset('ranked') && $post->meta_value->rank == 0) continue;
                //if rank filter
                if (SQ_Classes_Tools::getIsset('rank') && SQ_Classes_Tools::getValue('rank') <> $post->meta_value->rank) continue;
                //if keyword filter
                if (SQ_Classes_Tools::getIsset('keyword') && strtolower($post->meta_value->keyword) <> strtolower(SQ_Classes_Tools::getValue('keyword'))) continue;
                //if keyword
                if (SQ_Classes_Tools::getValue('skeyword', '') <> '' && strpos(strtolower($post->meta_value->keyword), strtolower(SQ_Classes_Tools::getValue('skeyword', ''))) === false) continue;

                //if rank order
                if ($orderby === 'rank') {
                    $overhundred = 100;
                    if ($post->meta_value->rank > 0) {
                        $this->order_posts[$post->meta_value->rank . '_' . $post->meta_id] = $post->meta_id;
                    } else {
                        ++$overhundred;
                        $this->order_posts[$overhundred . '_' . $post->meta_id] = $post->meta_id;
                    }
                }

                if ($orderby === 'shares') {
                    $post->meta_value->facebook = (isset($post->meta_value->facebook) ? (int)$post->meta_value->facebook : 0);
                    $post->meta_value->plus = (isset($post->meta_value->plus) ? (int)$post->meta_value->plus : 0);
                    $post->meta_value->linkedin = (isset($post->meta_value->linkedin) ? (int)$post->meta_value->linkedin : 0);
                    $post->meta_value->reddit = (isset($post->meta_value->reddit) ? (int)$post->meta_value->reddit : 0);
                    $post->meta_value->stumble = (isset($post->meta_value->stumble) ? (int)$post->meta_value->stumble : 0);
                    $post->meta_value->twitter = (isset($post->meta_value->twitter) ? (int)$post->meta_value->twitter : 0);
                    $post->meta_value->pinterest = (isset($post->meta_value->pinterest) ? (int)$post->meta_value->pinterest : 0);

                    $total = $post->meta_value->facebook +
                        $post->meta_value->plus +
                        $post->meta_value->linkedin +
                        $post->meta_value->reddit +
                        $post->meta_value->stumble +
                        $post->meta_value->twitter +
                        $post->meta_value->pinterest;

                    $this->order_posts[$total . '_' . $post->meta_id] = $post->meta_id;
                }

                if (in_array($orderby, array('optimized', 'facebook', 'plus', 'linkedin', 'reddit', 'stumble', 'twitter', 'pinterest'))) {
                    if (isset($post->meta_value->$orderby)) {
                        $post->meta_value->$orderby = (isset($post->meta_value->$orderby) ? (int)$post->meta_value->$orderby : 0);
                        $this->order_posts[$post->meta_value->$orderby . '_' . $post->meta_id] = $post->meta_id;
                    }
                }

                $post__in[] = $post->post_id;
                $post__in = array_unique($post__in);
            }
        }

        if (SQ_Classes_Tools::getIsset('orderby') && SQ_Classes_Tools::getValue('orderby') === 'type') {
            $query->set('orderby', 'post_type');
        } elseif (in_array($orderby, array('rank', 'shares', 'optimized', 'facebook', 'plus', 'linkedin', 'reddit', 'stumble', 'twitter', 'pinterest'))) {
            add_filter('posts_where', array($this, 'where_posts_meta'));
            ksort($this->order_posts, SORT_NUMERIC);
            add_filter('posts_orderby', array($this, 'order_by_rank'));
        }

        $query->set('post__in', (array)$post__in);


    }

    public function where_posts_meta($where) {
        if (!empty($this->order_posts)) {
            $where .= "AND pm.meta_id IN ('" . join("','", $this->order_posts) . "')";
        }
        return $where;
    }

    public function order_by_rank($orderby) {
        if (!empty($this->order_posts)) {
            $orderby = "CASE WHEN pm.meta_id IN ('" . join("','", $this->order_posts) . "') THEN FIELD(pm.meta_id, '" . join("','", $this->order_posts) . "') ELSE 0 END " . SQ_Classes_Tools::getValue('order', 'DESC');
        }

        return $orderby;
    }

    public function prepareJoin($join) {
        global $wpdb;
        $join .= " INNER JOIN {$wpdb->postmeta} pm ON ({$wpdb->posts}.ID = pm.post_id) AND pm.meta_key= '_src_keyword'";
        return $join;
    }

    public function prepareFields($fields) {
        return $fields . ', pm.meta_value as keyword';
    }

    public function prepare_items() {
        global $avail_post_stati, $wp_query, $per_page, $mode;

        //Prevent conflicts
        remove_all_actions('pre_get_posts');
        add_action('pre_get_posts', array($this, 'prepareQuery'));
        add_filter('posts_join', array($this, 'prepareJoin'));
        add_filter('posts_fields', array($this, 'prepareFields'));


        $avail_post_stati = $this->wp_edit_posts_query();

        $total_items = $wp_query->found_posts;

        $per_page = $this->get_items_per_page('edit_post_per_page');
        $per_page = apply_filters('edit_posts_per_page', $per_page, 'post');

        $total_pages = $wp_query->max_num_pages;

        $mode = empty($_REQUEST['mode']) ? 'list' : $_REQUEST['mode'];

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'per_page' => $per_page
        ));
    }

    public function get_column_info() {
        if (isset($this->_column_headers))
            return $this->_column_headers;

        $columns = $this->get_columns();
        $_sortable = apply_filters("manage_edit-post_sortable_columns", $this->get_sortable_columns());

        $sortable = array();
        foreach ($_sortable as $id => $data) {
            if (empty($data))
                continue;

            $data = (array)$data;
            if (!isset($data[1]))
                $data[1] = false;

            $sortable[$id] = $data;
        }

        $this->_column_headers = array($columns, $sortable);

        return $this->_column_headers;
    }

    public function get_sortable_columns() {
        return array(
            'title' => 'title',
            'type' => 'type',
            'author' => 'author',
            'rank' => 'rank',
            'optimized' => array('optimized', true),
            'facebook' => array('facebook', true),
            'plus' => array('plus', true),
            'linkedin' => array('linkedin', true),
            'reddit' => array('reddit', true),
            'stumble' => array('stumble', true),
            'twitter' => array('twitter', true),
            'pinterest' => array('pinterest', true),
            'shares' => array('shares', true),

            'date' => array('date', true)
        );
    }

    public function print_column_headers($with_id = true) {
        $strcolumn = '';

        list($columns, $sortable) = $this->get_column_info();

        $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $current_url = remove_query_arg('paged', $current_url);

        if (isset($_GET['orderby']))
            $current_orderby = $_GET['orderby'];
        else
            $current_orderby = '';

        if (isset($_GET['order']) && 'desc' == $_GET['order'])
            $current_order = 'desc';
        else
            $current_order = 'asc';

        foreach ($columns as $column_key => $column_display_name) {
            $class = array('manage-column', "column-$column_key");

            $style = '';
            $style = ' style="' . $style . '"';

            if ('cb' == $column_key) {
                $class[] = 'check-column';
                $style = ' style="margin:0;padding:0;width:0px;"';
            }

            if (isset($sortable[$column_key])) {
                list($orderby, $desc_first) = $sortable[$column_key];

                if ($current_orderby == $orderby) {
                    $order = 'asc' == $current_order ? 'desc' : 'asc';
                    $class[] = 'sorted';
                    $class[] = $current_order;
                } else {
                    $order = $desc_first ? 'desc' : 'asc';
                    $class[] = 'sortable';
                    $class[] = $desc_first ? 'asc' : 'desc';
                }

                $column_display_name = '<a href="' . esc_url(add_query_arg(compact('orderby', 'order'), $current_url)) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
            }

            $id = $with_id ? "id='$column_key'" : '';

            if (!empty($class))
                $class = "class='" . join(' ', $class) . "'";

            $strcolumn .= "<th scope='col' $id $class $style>$column_display_name</th>";
        }
        return $strcolumn;
    }

    public function get_columns() {
        $post_type = 'post';

        $posts_columns = array();
        /* translators: manage posts column name */
        $posts_columns['cb'] = '';
        $posts_columns['title'] = __('Title', _SQ_PLUGIN_NAME_);

        if (empty($post_type) || is_object_in_taxonomy($post_type, 'post_tag'))
            $posts_columns['keywords'] = __('Keyword', _SQ_PLUGIN_NAME_);


        $posts_columns['rank'] = __('Google Rank', _SQ_PLUGIN_NAME_);
        $posts_columns['country'] = '';

        $posts_columns['optimized'] = __('Optimized', _SQ_PLUGIN_NAME_);
//        $posts_columns['facebook'] = __('Facebook',_SQ_PLUGIN_NAME_);
//        $posts_columns['plus'] = __('G+',_SQ_PLUGIN_NAME_);
//        $posts_columns['linkedin'] = __('Linkedin',_SQ_PLUGIN_NAME_);
//        $posts_columns['reddit'] = __('Reddit',_SQ_PLUGIN_NAME_);
//        $posts_columns['stumble'] = __('Stumble',_SQ_PLUGIN_NAME_);
//        $posts_columns['twitter'] = __('Twitter',_SQ_PLUGIN_NAME_);
//        $posts_columns['pinterest'] = __('Pinterest',_SQ_PLUGIN_NAME_);

        $posts_columns['shares'] = __('Social Shares', _SQ_PLUGIN_NAME_);

        if (post_type_supports($post_type, 'author', _SQ_PLUGIN_NAME_))
            $posts_columns['author'] = __('Author', _SQ_PLUGIN_NAME_);

        $posts_columns['type'] = __('Type', _SQ_PLUGIN_NAME_);

        $posts_columns['date'] = __('Post Date', _SQ_PLUGIN_NAME_);

//        if (SQ_Classes_Tools::getValue('orderby', 'rank') == 'rank') {
//            $posts_columns['recheck'] = '';
//        }

        return $posts_columns;
    }

    public function display_tablenav($which) {
        if ('top' == $which)
            wp_nonce_field('bulk-' . $this->_args['plural']);

        ob_start();
        /* includes the block from theme directory */
        ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">

            <?php if ('top' == $which) { ?>
                <div class="alignleft actions">
                    <input type="search" id="post-search-input" autofocus name="s" value="<?php echo SQ_Classes_Tools::getValue('skeyword') ?>" onkeypress="if(sq_check_enter(event)){jQuery('#search-submit').trigger('click')}">
                    <input type="submit" id="search-submit" class="button" onclick="location.href = '?page=sq_posts&skeyword=' + encodeURIComponent(jQuery('#post-search-input').val());" value="<?php echo __('Search Keyword', _SQ_PLUGIN_NAME_) ?>">
                    <script>
                        function sq_check_enter(e) {
                            if (e.keyCode == 13) {
                                return true;
                            }
                            return false;
                        }
                    </script>
                </div>
            <?php } ?>
            <?php
            $this->extra_tablenav($which);
            $this->pagination($which);
            ?>

            <br class="clear"/>
        </div>
        <?php
        $strnav = ob_get_contents();
        ob_end_clean();
        return $strnav;
    }

    public function display_rows() {
        global $wp_query;

        $posts = $wp_query->posts;
        //echo $wp_query->request;
        $allrows = array();
        $style = 2;

        if (SQ_Classes_Tools::getValue('orderby', 'rank') <> 'rank') {
            $style = 1;
        }

        add_filter('the_title', 'esc_html');
        if (!empty($posts))
            foreach ($posts as $post) {
                SQ_Classes_Tools::dump($post);
                if (get_post_status($post->ID) == 'publish') {
                    array_push($this->posts, $post->ID);
                }

                if (SQ_Classes_Tools::getIsset('rank')) {
                    $keyword = json_decode($post->keyword);
                    if ($keyword->rank <> SQ_Classes_Tools::getValue('rank')) {
                        continue;
                    }
                }

                if ($style == 1) {
                    $allrows[$post->ID][] = $this->single_row($post);
                } else {
                    $allrows[] = $this->single_row($post);

                }
            }

        remove_action('pre_get_posts', array($this, 'prepareQuery'));
        remove_filter('posts_join', array($this, 'prepareJoin'));
        remove_filter('posts_fields', array($this, 'prepareFields'));

        return $this->packRows($allrows, $style);

    }

    public function packRows($allrows, $style = 1) {
        $strrow = '';
        static $alternate;

        foreach ($allrows as $post_id => $rows) {
            $firstrow = current($rows);
            $alternate = 'alternate' == $alternate ? '' : 'alternate';
            $strrow .= '<tr id="post-' . $post_id . '" class="post-' . $post_id . ' type-post format-standard ' . $alternate . ' hentry">';
            if ($style == 1) {
                $subrow = '<table class="subrow-table">';

                foreach ($rows as $index => $row) {

                    $subrow .= '<tr>';
                    foreach ($row as $key => $column) {
                        if (in_array($key, array('keywords', 'country', 'rank'))) {
                            $subrow .= '<td class = "subrow-column column-' . $key . '"" >' . $column . '</td>';
                            continue;
                        }
                    }
                    $subrow .= '</tr>';

                }
                $subrow .= '</table>';

                $strcolumn = '';
                foreach ($firstrow as $key => $column) {

                    if (!in_array($key, array('keywords', 'country', 'rank'))) {
                        if ($key == 'title') {
                            $strcolumn .= '<td class = "manage-column ' . 'column-' . $key . '">' . $column . '</td>';
                            $strcolumn .= '<td colspan="3">' . $subrow . '</td>';
                        } else {
                            $strcolumn .= '<td class = "manage-column ' . 'column-' . $key . '">' . $column . '</td>';
                        }
                    }
                }


            } else {
                $strcolumn = '';
                foreach ($rows as $key => $column) {
                    $strcolumn .= '<td class = "manage-column ' . 'column-' . $key . '">' . $column . '</td>';
                }

            }

            $strrow .= $strcolumn;
            $strrow .= '</tr>';
        }


        return $strrow;
    }

    /**
     *
     * Return a row from table
     * @param object $a_post
     * @return array
     */
    public function single_row($a_post) {
        global $post;

        $global_post = $post;
        $post = $a_post;
        list($columns) = $this->get_column_info();
        setup_postdata($post);

        if ($post) {
            $post->meta_value = json_decode($post->keyword);
            $post->meta_value->rank = (isset($post->meta_value->rank) ? (int)$post->meta_value->rank : -1);
            $post->meta_value->keyword = (isset($post->meta_value->keyword) ? $post->meta_value->keyword : '');
            $post->meta_value->country = (isset($post->meta_value->country) ? $post->meta_value->country : 'com');
            // Socials
            $post->meta_value->optimized = (isset($post->meta_value->optimized) ? (int)$post->meta_value->optimized : 0);
            $post->meta_value->facebook = (isset($post->meta_value->facebook) ? (int)$post->meta_value->facebook : 0);
            $post->meta_value->plus = (isset($post->meta_value->plus) ? (int)$post->meta_value->plus : 0);
            $post->meta_value->linkedin = (isset($post->meta_value->linkedin) ? (int)$post->meta_value->linkedin : 0);
            $post->meta_value->reddit = (isset($post->meta_value->reddit) ? (int)$post->meta_value->reddit : 0);
            $post->meta_value->stumble = (isset($post->meta_value->stumble) ? (int)$post->meta_value->stumble : 0);
            $post->meta_value->twitter = (isset($post->meta_value->twitter) ? (int)$post->meta_value->twitter : 0);
            $post->meta_value->pinterest = (isset($post->meta_value->pinterest) ? (int)$post->meta_value->pinterest : 0);

            $edit_link = get_edit_post_link($post->ID);
            $title = _draft_or_post_title();
            $post_type_object = get_post_type_object($post->post_type);
            $can_edit_post = current_user_can($post_type_object->cap->edit_post, $post->ID);

            $array = array();

            foreach ($columns as $key => $column) {

                switch ($key) {
                    case 'title':
                        if ($can_edit_post) {
                            $value = '<a class="row-title" href="' . $edit_link . '" title="' . esc_attr(sprintf(__('Edit &#8220;%s&#8221;', _SQ_PLUGIN_NAME_), $title)) . '">' . $title . '</a>' . '<span style="display:block; font-size: 11px">' . urldecode(get_permalink($post->ID)) . '</span>';
                            $actions = array();
                            if ($can_edit_post && 'trash' != $post->post_status) {
                                $actions['edit'] = '<a href="' . get_edit_post_link($post->ID, true) . '" title="' . esc_attr(__('Edit this item', _SQ_PLUGIN_NAME_)) . '">' . __('Edit') . '</a>';
                            }
                            if ($post_type_object->public) {
                                if (in_array($post->post_status, array('pending', 'draft', 'future'))) {
                                    if ($can_edit_post)
                                        $actions['view'] = '<a href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post->ID))) . '" title="' . esc_attr(sprintf(__('Preview &#8220;%s&#8221;'), $title)) . '" rel="permalink" target="_blank">' . __('Preview') . '</a>';
                                } elseif ('trash' != $post->post_status) {
                                    $actions['view'] = '<a href="' . get_permalink($post->ID) . '" title="' . esc_attr(sprintf(__('View &#8220;%s&#8221;', _SQ_PLUGIN_NAME_), $title)) . '" rel="permalink" target="_blank">' . __('View') . '</a>';
                                }
                            }

                            $value .= $this->row_actions($actions);
                        } else {
                            $value = '<strong>' . $title . '</strong>' . '<span style="display:block; font-size: 11px">' . urldecode(get_permalink($post->ID)) . '</span>';
                        }
                        $array[$key] = $value;
                        break;
                    case 'author':
                        //$author = get_userdata($post->post_author);
                        $array[$key] = sprintf('<a href="%s">%s</a>', esc_url(add_query_arg(array('page' => 'sq_posts', 'author' => get_the_author_meta('ID')), admin_url('admin.php'))), get_the_author());
                        break;
                    case 'type':
                        $array[$key] = sprintf('<a href="%s">%s</a>', esc_url(add_query_arg(array('page' => 'sq_posts', 'type' => $post->post_type), admin_url('admin.php'))), ucfirst($post->post_type));
                        break;
                    case 'keywords':
                        $post->meta_value->keyword = str_replace('\\', '', $post->meta_value->keyword);
                        $value = sprintf('<a href="%s">%s</a>', esc_attr(add_query_arg(array('page' => 'sq_posts', 'keyword' => strtolower($post->meta_value->keyword)), admin_url('admin.php'))), $post->meta_value->keyword);

                        $array[$key] = $value;
                        break;
                    case 'country':
                        $array[$key] = strtolower($post->meta_value->country);
                        break;
                    case 'optimized':
                        $array[$key] = '<a href="#" data-toggle="tooltip" data-placement="top"   title="' . sprintf(__('Squirrly Optimization for: \'%s\'', _SQ_PLUGIN_NAME_), str_replace('"', '\'', $post->meta_value->keyword)) . '">' . $post->meta_value->optimized . '%' . '</span>';

                        break;

                    case 'facebook':
                        $array[$key] = $post->meta_value->facebook;
                        break;
                    case 'plus':
                        $array[$key] = $value = $post->meta_value->plus;;
                        break;
                    case 'linkedin':
                        $array[$key] = $post->meta_value->linkedin;
                        break;
                    case 'reddit':
                        $array[$key] = $post->meta_value->reddit;
                        break;
                    case 'stumble':
                        $array[$key] = $post->meta_value->stumble;
                        break;
                    case 'twitter':
                        $array[$key] = $post->meta_value->twitter;
                        break;
                    case 'pinterest':
                        $array[$key] = $post->meta_value->pinterest;
                        break;
                    case 'shares':
                        $value = $post->meta_value->facebook +
//                            $post->meta_value->plus +
//                            $post->meta_value->linkedin +
                            $post->meta_value->reddit +
                            $post->meta_value->stumble +
                            $post->meta_value->twitter +
                            $post->meta_value->pinterest;
                        $title = __("Facebook", _SQ_PLUGIN_NAME_) . ": " . $post->meta_value->facebook . "\n";
                        $title .= __("Twitter", _SQ_PLUGIN_NAME_) . ": " . $post->meta_value->twitter . "\n";
                        //$title .= __("Google+", _SQ_PLUGIN_NAME_) . ": " . $post->meta_value->plus . "\n";
                        //$title .= __("LinkedIn", _SQ_PLUGIN_NAME_) . ": " . $post->meta_value->linkedin . "\n";
                        $title .= __("Reddit", _SQ_PLUGIN_NAME_) . ": " . $post->meta_value->reddit . "\n";
                        $title .= __("Stumble", _SQ_PLUGIN_NAME_) . ": " . $post->meta_value->stumble . "\n";
                        $title .= __("Pinterest", _SQ_PLUGIN_NAME_) . ": " . $post->meta_value->pinterest . "\n";
                        $array[$key] = '<a href="#" data-toggle="tooltip" data-placement="top"   title="' . $title . '">' . $value . '</span>';
                        break;
                    case 'rank':
                        $array[$key] = sprintf('<div id="sq_rank_value' . $post->ID . '" class="col-md-12 text-right"><a  href="%s" data-toggle="tooltip" title="' . sprintf(__('Google Rank for keyword \'%s\' on \'%s\'', _SQ_PLUGIN_NAME_), str_replace('"', '\'', $post->meta_value->keyword), 'google.' . $post->meta_value->country) . '">%s</a></div>', esc_url(add_query_arg(array('page' => 'sq_posts', 'rank' => $post->meta_value->rank), admin_url('admin.php'))), $this->getRankText($post->meta_value->rank, $post->meta_value->change));

                        break;
                    case 'recheck':
                        $array[$key] = sprintf('<button class="btn btn-default sq_rank_refresh" data-id="'.$post->ID.'" data-keyword="'.htmlspecialchars($post->meta_value->keyword).'">%s</button>', __('Update', _SQ_PLUGIN_NAME_));

                        break;
                    case 'date':
                        $value = '';

                        if (isset($post->post_date)) {
                            if ('0000-00-00 00:00:00' == $post->post_date) {
                                $h_time = __('Unpublished', _SQ_PLUGIN_NAME_);
                                $time_diff = 0;
                            } else {
                                $m_time = $post->post_date;
                                $time = get_post_time('G', true, $post);

                                $time_diff = time() - $time;

                                if ($time_diff > 0 && $time_diff < 24 * 60 * 60)
                                    $h_time = sprintf(__('%s ago'), human_time_diff($time));
                                else
                                    $h_time = mysql2date(__('Y/m/d'), $m_time);
                            }
                            $value = $h_time . '<br />';

                            if ('publish' == $post->post_status) {
                                $value .= __('Published', _SQ_PLUGIN_NAME_);
                            } elseif ('future' == $post->post_status) {
                                if ($time_diff > 0)
                                    $value .= '<strong class="attention">' . __('Missed schedule', _SQ_PLUGIN_NAME_) . '</strong>';
                                else
                                    $value .= __('Scheduled', _SQ_PLUGIN_NAME_);
                            } else {
                                $value .= __('Last Modified', _SQ_PLUGIN_NAME_);
                            }
                        }
                        $array[$key] = $value;
                        break;
                    default:
                        $array[$key] = '';
                        break;
                }

            }
        }
        $post = $global_post;
        return $array;
    }

    /**
     * Get the rank text for table
     * @param $rank
     * @param $change
     * @return mixed|string|void
     */
    public function getRankText($rank, $change){
        $detail = '';
        if (!isset($rank) || $rank < 0) {
            return __('In queue', _SQ_PLUGIN_NAME_);
        } else {
            if ($rank == 0) {
                $value = __('Not indexed');
                $detail .= (($change <> 0) ? sprintf('<span class="label label-' . ($change > 0 ? 'primary' : 'danger') . ' m-b-xxs"><i class="fa fa-arrow-%s"></i><span> </span><span>%s</span></span>', ($change > 0 ? 'up' : 'down'), $change) : '');
            } elseif ($rank > 0) {
                $value = '<strong style="font-size: 16px; margin: 0 auto; text-align:right;">' . sprintf('%s', $rank) . '</strong>';
                $detail .= (($change <> 0) ? sprintf('<span class="label label-' . ($change > 0 ? 'primary' : 'danger') . ' m-b-xxs"><i class="fa fa-arrow-%s"></i><span> </span><span>%s</span></span>', ($change > 0 ? 'up' : 'down'), $change) : '');
            }
            return $value . $detail;
        }

    }

    /**
     * @return int Get all changes
     */
    public function getChanges() {
        return $this->changes;
    }
}
