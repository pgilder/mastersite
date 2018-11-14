<?php

/**
 * The main class for controllers
 *
 */
class SQ_Classes_FrontController {

    /** @var object of the model class */
    public $model;

    /** @var boolean */
    public $flush = true;

    /** @var object of the view class */
    public $view;

    /** @var name of the  class */
    private $name;

    public function __construct() {

        /* Load error class */
        SQ_Classes_ObjController::getClass('SQ_Classes_Error');
        /* Load Tools */
        SQ_Classes_ObjController::getClass('SQ_Classes_Tools');


        /* get the name of the current class */
        $this->name = get_class($this);

        /* load the model and hooks here for wordpress actions to take efect */
        /* create the model and view instances */
        $this->model = SQ_Classes_ObjController::getClass(str_replace('Controllers', 'Models', $this->name));

        //IMPORTANT TO LOAD HOOKS HERE
        /* check if there is a hook defined in the controller clients class */
        SQ_Classes_ObjController::getClass('SQ_Classes_HookController')->setHooks($this);

        /* Load the Submit Actions Handler */
        SQ_Classes_ObjController::getClass('SQ_Classes_Action');
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController');

        //abstract classes
        SQ_Classes_ObjController::getClass('SQ_Models_Abstract_Domain');
        SQ_Classes_ObjController::getClass('SQ_Models_Abstract_Models');
        SQ_Classes_ObjController::getClass('SQ_Models_Abstract_Seo');
    }

    public function getClass(){
        return $this->name;
    }

    /**
     * load sequence of classes
     * Function called usualy when the controller is loaded in WP
     *
     * @return mixed
     */
    public function init() {
        /* load the blocks for this controller */
        SQ_Classes_ObjController::getClass('SQ_Classes_ObjController')->getBlocks($this->name);

        if ($this->flush) {
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

    /**
     * initialize settings
     * Called from index
     *
     * @return void
     */
    public function runAdmin() {
        /* show the admin menu and post actions */
        SQ_Classes_ObjController::getClass('SQ_Controllers_Menu');
    }

    /**
     * Run fron frontend
     */
    public function runFrontend() {
        //Load Frontend only if Squirrly SEO is enabled
        SQ_Classes_ObjController::getClass('SQ_Controllers_Frontend');

        /* show the topbar admin menu and post actions */
        SQ_Classes_ObjController::getClass('SQ_Controllers_Menu');

        /* call the API for save posts */
        SQ_Classes_ObjController::getClass('SQ_Controllers_Api');

    }

    /**
     * first function call for any class
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
    public function hookHead() {
        if (!is_admin()) {
            return;
        }

        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->init();
        if ($class = SQ_Classes_ObjController::getClassPath($this->name)) {
            SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia($class['name']);
        }
    }

}
