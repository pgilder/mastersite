<?php

/**
 * The main class for core blocks
 *
 */
class SQ_Classes_BlockController {

    /** @var object of the model class */
    protected $model;

    /** @var boolean */
    public $flush = true;

    /** @var object of the view class */
    protected $view;

    /** @var name of the  class */
    private $name;

    public function __construct() {
        /* get the name of the current class */
        $this->name = get_class($this);

        /* create the model and view instances */
        $this->model = SQ_Classes_ObjController::getClass(str_replace('Core', 'Models', $this->name));
    }

    /**
     * load sequence of classes
     *
     * @return mixed
     */
    public function init() {
        /* check if there is a hook defined in the block class */
        SQ_Classes_ObjController::getClass('SQ_Classes_HookController')->setBlockHooks($this);

        if ($this->flush) {
            $this->hookHead();
            echo $this->getView();
        } else {
            return $this->getView();
        }
    }


    /**
     * Get the block view
     *
     * @param null $view
     * @return mixed
     */
    public function getView($view = null) {
        if (!isset($view)) {
            if ($class = SQ_Classes_ObjController::getClassPath($this->name)) {
                $view = $class['name'];
            }
        }

        if (isset($view)) {
            $this->view = SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController');
            return $this->view->getView($view, $this);
        }

        return '';
    }


    /**
     * Called as menu callback to show the block
     *
     */
    public function show() {
        $this->flush = true;
        echo $this->init();
    }


    public function preloadSettings() {
        if(SQ_Classes_Tools::getOption('sq_api') <> '') {
            echo '<script type="text/javascript">
                   var __blog_url = "' . get_bloginfo('url') . '";
                   var __token = "' . SQ_Classes_Tools::getOption('sq_api') . '";
                   var __language = "' . get_bloginfo('language') . '";
                   var __api_url = "' . _SQ_API_URL_ . '";
                    jQuery(document).ready(function () {
                         jQuery.sq_getHelp("' . str_replace(array("sq_core_block", "sq_controller_block_"), "", strtolower($this->name)) . '", "content"); 
                    });
             </script>';
        }
    }

    /**
     * This function is called from Ajax class as a wp_ajax_action
     *
     */
    protected function action() {
        // check to see if the submitted nonce matches with the
        // generated nonce we created
        if (function_exists('wp_verify_nonce'))
            if (!wp_verify_nonce(SQ_Classes_Tools::getValue('nonce'), _SQ_NONCE_ID_))
                die('Invalid request!');
    }

    /**
     * This function will load the media in the header for each class
     *
     * @return void
     */
    protected function hookHead() {
        if ($class = SQ_Classes_ObjController::getClassPath($this->name)) {
            SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia($class['name']);
        }
    }

}
