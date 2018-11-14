<?php

class SQ_Classes_Error extends SQ_Classes_FrontController {

    /** @var array */
    private static $errors, $switch_off;

    /**
     * The error controller for Squirrly
     */
    public function __construct() {
        parent::__construct();

        /* Verify dependences */
        if (!function_exists('get_class')) {
            self::setError(__('Function get_class does not exists! Is required for Squirrly to work properly.', _SQ_PLUGIN_NAME_));
        }
        if (!function_exists('file_exists')) {
            self::setError(__('Function file_exists does not exists! Is required for Squirrly to work properly.', _SQ_PLUGIN_NAME_));
        }

        if (!defined('ABSPATH'))
            self::setError(__('The home directory is not set!', _SQ_PLUGIN_NAME_), 'fatal');

        /* Check the PHP version */
        if (PHP_VERSION_ID < 5000) {
            self::setError(__('The PHP version has to be greater then 4.0', _SQ_PLUGIN_NAME_), 'fatal');
        }
    }

    /**
     * Show the error in wrodpress
     *
     * @param string $error
     * @param boolean $stop
     *
     * @return void;
     */
    public static function setError($error = '', $type = 'notice', $id = '') {
        self::$errors[] = array('id' => $id,
            'type' => $type,
            'text' => $error);
    }

    public static function setMessage($message = '', $id = '') {
        self::$errors[] = array('id' => $id,
            'type' => 'success',
            'text' => $message);
    }

    /**
     * This hook will show the error in WP header
     */
    function hookNotices() {
        if (is_array(self::$errors))
            foreach (self::$errors as $error) {

                switch ($error['type']) {
                    case 'fatal':
                        self::showError(ucfirst(_SQ_PLUGIN_NAME_ . " " . $error['type']) . ': ' . $error['text'], $error['id']);
                        die();
                        break;
                    case 'settings':
                        /* switch off option for notifications */
                        self::showError("<span class='sq_notice_author'>" . _SQ_PLUGIN_NAME_ . "</span> " . $error['text'] . " ", $error['id']);
                        break;

                    case 'helpnotice':
                        if (!SQ_Classes_Tools::getOption('ignore_warn')) {
                            self::$switch_off = "<a href=\"javascript:void(0);\" onclick=\"jQuery.post( ajaxurl, {action: 'sq_warnings_off', nonce: '" . wp_create_nonce(_SQ_NONCE_ID_) . "'}, function() {jQuery('#sq_ignore_warn').attr('checked', true); jQuery('.sq_message').hide(); jQuery('#toplevel_page_squirrly .awaiting-mod').fadeOut('slow');  });\" >" . __("Don't bother me!", _SQ_PLUGIN_NAME_) . "</a>";
                            self::showError("<span class='sq_notice_author'>" . _SQ_PLUGIN_NAME_ . "</span> " . $error['text'] . " " . self::$switch_off, $error['id'], 'sq_helpnotice');
                        }
                        break;

                    case 'success':
                        self::showError("<span class='sq_notice_author'>" . _SQ_PLUGIN_NAME_ . "</span> " . $error['text'] . " ", $error['id'], 'sq_success');
                        break;

                    case 'trial':
                        if (SQ_Classes_Tools::getOption('sq_google_alert_trial')) {
                            self::$switch_off = "<a href=\"javascript:void(0);\" style=\"font-size: 12px;float:right;margin-right: 5px;color: #ddd;\" onclick=\"jQuery.post( ajaxurl, {action: 'sq_google_alert_trial', nonce: '" . wp_create_nonce(_SQ_NONCE_ID_) . "'}, function() {jQuery('.sq_message').fadeOut('slow');});\" >" . __("Don't bother me!", _SQ_PLUGIN_NAME_) . "</a>";
                            self::showError("<span class='sq_notice_author'>" . _SQ_PLUGIN_NAME_ . "</span> " . $error['text'] . " " . self::$switch_off, $error['id'], 'sq_success');
                        }
                        break;
                    default:

                        self::showError("<span class='sq_notice_author'>" . _SQ_PLUGIN_NAME_ . "</span> " . $error['text'], $error['id']);
                }
            }
        self::$errors = array();
    }

    /**
     * Show the notices to WP
     *
     * @return string
     */
    public static function showError($message, $id = '', $type = 'sq_error') {

        if (file_exists(_SQ_THEME_DIR_ . 'Notices.php')) {
            include(_SQ_THEME_DIR_ . 'Notices.php');
        } else {
            echo $message;
        }
    }

}
