<?php

class SQ_Core_BlockSupport extends SQ_Classes_BlockController {

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();
        global $current_user;
        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_feedback':
                $return = array();

                SQ_Classes_Tools::saveOptions('sq_feedback', 1);

                $line = "\n" . "________________________________________" . "\n";
                $from = SQ_Classes_Tools::getOption('sq_support_email');
                if ($from == '') {
                    $from = $current_user->user_email;
                }
                $subject = __('Plugin Feedback', _SQ_PLUGIN_NAME_);
                $face = SQ_Classes_Tools::getValue('feedback');
                $message = SQ_Classes_Tools::getValue('message');

                if ($message <> '' || (int)$face > 0) {
                    switch ($face) {
                        case 1:
                            $face = 'Angry';
                            break;
                        case 2:
                            $face = 'Sad';
                            break;
                        case 3:
                            $face = 'Happy';
                            break;
                        case 4:
                            $face = 'Excited';
                            break;
                        case 5:
                            $face = 'Love it';
                            break;
                    }
                    if ($message <> '')
                        $message = $message . $line;

                    if ($face <> '') {
                        $message .= 'Url:' . get_bloginfo('wpurl') . "\n";
                        $message .= 'Face:' . $face;
                    }


                    $headers[] = 'From: ' . $from . ' <' . $from . '>';

                    //$this->error='buuum';
                    wp_mail(_SQ_SUPPORT_EMAIL_, $subject, $message, $headers);
                    $return['message'] = __('Thank you for your feedback', _SQ_PLUGIN_NAME_);
                    $return['success'] = true;

                } else {
                    $return['message'] = __('No message.', _SQ_PLUGIN_NAME_);
                    $return['error'] = true;
                }

                SQ_Classes_Tools::setHeader('json');
                echo json_encode($return);
                break;

            case 'sq_support':
                $return = array();
                $versions = '';


                $versions .= 'Url:' . get_bloginfo('wpurl') . "\n";
                $versions .= 'Squirrly version: ' . SQ_VERSION_ID . "\n";
                $versions .= 'Wordpress version: ' . WP_VERSION_ID . "\n";
                $versions .= 'PHP version: ' . PHP_VERSION_ID . "\n";

                $line = "\n" . "________________________________________" . "\n";
                $from = SQ_Classes_Tools::getValue('sq_support_email', $current_user->user_email);
                SQ_Classes_Tools::saveOptions('sq_support_email', $from);

                $subject = SQ_Classes_Tools::getValue('subject', __('Plugin Support', _SQ_PLUGIN_NAME_));
                $message = SQ_Classes_Tools::getValue('message');

                if ($message <> '') {
                    $message .= $line;
                    $message .= $versions;

                    $headers[] = 'From: ' . $from . ' <' . $from . '>';

                    //$this->error='buuum';
                    if (wp_mail(_SQ_SUPPORT_EMAIL_, $subject, $message, $headers)) {
                        $return['message'] = __('Message sent. Thank you!', _SQ_PLUGIN_NAME_);
                        $return['success'] = true;
                    } else {
                        $return['message'] = __('Could not send the email. Make sure you can send emails from your blog.', _SQ_PLUGIN_NAME_);
                        $return['error'] = true;
                    }
                } else {
                    $return['message'] = __('No message.', _SQ_PLUGIN_NAME_);
                    $return['error'] = true;
                }

                header('Content-Type: application/json');
                echo json_encode($return);
                break;
        }
        exit();
    }

}
