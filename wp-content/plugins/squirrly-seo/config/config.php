<?php

/**
 * The configuration file
 */
define('_SQ_SUPPORT_EMAIL_', 'support@squirrly.co');
if (!defined('_SQ_NONCE_ID_')) {
    if (defined('NONCE_KEY')) {
        define('_SQ_NONCE_ID_', NONCE_KEY);
    } else {
        define('_SQ_NONCE_ID_', md5(date('Y-d')));
    }
}
defined('SQ_DEBUG') || define('SQ_DEBUG', 0);
define('_SQ_MOBILE_ICON_SIZES', '76,120,152');
define('REQUEST_TIME', microtime(true));


if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ((int)@$version[0] * 1000 + (int)@$version[1] * 100 + ((isset($version[2])) ? ((int)$version[2] * 10) : 0)));
}
if (!defined('WP_VERSION_ID') && isset($wp_version)) {
    $version = explode('.', $wp_version);
    define('WP_VERSION_ID', ((int)@$version[0] * 1000 + (int)@$version[1] * 100 + ((isset($version[2])) ? ((int)$version[2] * 10) : 0)));
}
if (!defined('WP_VERSION_ID'))
    define('WP_VERSION_ID', '3000');

if (!defined('SQ_VERSION_ID')) {
    define('SQ_VERSION_ID', (int)number_format(str_replace('.', '', SQ_VERSION), 0, '', ''));
}


/* No path file? error ... */
require_once(dirname(__FILE__) . '/paths.php');

/* Define the record name in the Option and UserMeta tables */
define('SQ_OPTION', 'sq_options');


define('SQ_ALL_PATTERNS', json_encode(array(
    '{{date}}' => __("Displays the date of the post/page once it's published", _SQ_PLUGIN_NAME_),
    '{{title}}' => __("Adds the title of the post/page once it’s published", _SQ_PLUGIN_NAME_),
    '{{page}}' => __("Displays the number of the current page (i.e. 1 of 6)", _SQ_PLUGIN_NAME_),
    '{{parent_title}}' => __('Adds the title of a page\'s parent page', _SQ_PLUGIN_NAME_),
    '{{sitename}}' => __("Adds the site's name to the post description", _SQ_PLUGIN_NAME_),
    '{{sitedesc}}' => __("Adds the tagline/description of your site", _SQ_PLUGIN_NAME_),
    '{{excerpt}}' => __("Will display an excerpt from the post/page (if not customized, the excerpt will be auto-generated)", _SQ_PLUGIN_NAME_),
    '{{excerpt_only}}' => __("Will display an excerpt from the post/page (no auto-generation)", _SQ_PLUGIN_NAME_),
    '{{tag}}' => __("Adds the current tag(s) to the post description", _SQ_PLUGIN_NAME_),
    '{{category}}' => __("Adds the post category (several categories will be comma-separated)", _SQ_PLUGIN_NAME_),
    '{{primary_category}}' => __("Adds the primary category of the post/page", _SQ_PLUGIN_NAME_),
    '{{category_description}}' => __("Adds the category description to the post description", _SQ_PLUGIN_NAME_),
    '{{tag_description}}' => __("Adds the tag description", _SQ_PLUGIN_NAME_),
    '{{term_description}}' => __("Adds the term description", _SQ_PLUGIN_NAME_),
    '{{term_title}}' => __("Adds the term name", _SQ_PLUGIN_NAME_),
    '{{searchphrase}}' => __("Displays the search phrase (if it appears in the post)", _SQ_PLUGIN_NAME_),
    '{{sep}}' => __("Places a separator between the elements of the post description", _SQ_PLUGIN_NAME_),
    '{{modified}}' => __("Replaces the publication date of a post/page with the modified one", _SQ_PLUGIN_NAME_),
    '{{name}}' => __("Displays the author's nicename", _SQ_PLUGIN_NAME_),
    '{{user_description}}' => __("Adds the author's biographical info to the post description", _SQ_PLUGIN_NAME_),
    '{{currentdate}}' => __("Displays the current date", _SQ_PLUGIN_NAME_),
    '{{keyword}}' => __("Adds the post's keyword to the post description", _SQ_PLUGIN_NAME_),
    '{{currentday}}' => __("Adds the current day", _SQ_PLUGIN_NAME_),
    '{{currentmonth}}' => __("Adds the current month", _SQ_PLUGIN_NAME_),
    '{{currentyear}}' => __("Adds the current year", _SQ_PLUGIN_NAME_),
)));


define('SQ_ALL_SEP', json_encode(array(
    'sc-dash' => '-',
    'sc-ndash' => '&ndash;',
    'sc-mdash' => '&mdash;',
    'sc-middot' => '&middot;',
    'sc-bull' => '&bull;',
    'sc-star' => '*',
    'sc-smstar' => '&#8902;',
    'sc-pipe' => '|',
    'sc-tilde' => '~',
    'sc-laquo' => '&laquo;',
    'sc-raquo' => '&raquo;',
    'sc-lt' => '&lt;',
    'sc-gt' => '&gt;',
)));
