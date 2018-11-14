<?php

/**
 * The class creates object for plugin classes
 */
class SQ_Classes_ObjController {

    /** @var array of instances */
    public static $instances;

    /** @var array from core config */
    private static $config;


    public static function getClass($className, $args = array()) {

        if ($class = self::getClassPath($className)) {
            if (!isset(self::$instances[$className])) {
                /* check if class is already defined */
                if (!class_exists($className) || $className == get_class()) {
                    self::includeClass($class['dir'], $class['name']);

                    //check if abstract
                    $check = new ReflectionClass($className);
                    $abstract = $check->isAbstract();
                    if (!$abstract) {
                        self::$instances[$className] = new $className();
                        if (!empty($args)) {
                            call_user_func_array(array(self::$instances[$className], '__construct'), $args);
                        }
                        return self::$instances[$className];
                    } else {
                        self::$instances[$className] = true;
                    }
                } else {

                }
            } else
                return self::$instances[$className];
        }
        return false;
    }

    private static function includeClass($classDir, $className) {

        if (file_exists($classDir . $className . '.php'))
            try {
                include_once($classDir . $className . '.php');
            } catch (Exception $e) {
                throw new Exception('Controller Error: ' . $e->getMessage());
            }
    }

    public static function getDomain($className, $args = array()) {
        if ($class = self::getClassPath($className)) {

            /* check if class is already defined */
//            if (!isset(self::$instances[$className])) {
//
//                self::$instances[$className] = new $className($args);
//                return self::$instances[$className];
//            }else
//                return self::$instances[$className];
            self::includeClass($class['dir'], $class['name']);
            return new $className($args);
        }
        throw new Exception('Could not create domain: ' . $className);
    }


    /**
     * Check if the class is correctly set
     *
     * @param string $className
     * @return boolean
     */
    private static function checkClassPath($className) {
        $path = preg_split('/[_]+/', $className);
        if (is_array($path) && count($path) > 1) {
            if (in_array(_SQ_NAMESPACE_, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the path of the class and name of the class
     *
     * @param string $className
     * @return array | boolean
     * array(
     * dir - absolute path of the class
     * name - the name of the file
     * }
     */
    public static function getClassPath($className) {
        $dir = '';


        if (self::checkClassPath($className)) {
            $path = preg_split('/[_]+/', $className);
            for ($i = 1; $i < sizeof($path) - 1; $i++)
                $dir .= strtolower($path[$i]) . '/';

            $class = array('dir' => _SQ_ROOT_DIR_ . '/' . $dir,
                'name' => $path[sizeof($path) - 1]);

            if (file_exists($class['dir'] . $class['name'] . '.php')) {
                return $class;
            }
        }
        return false;
    }


    /**
     * Deprecated since 8.0.2
     * Get the instance of the specified class
     *
     * @param string $className
     * @param bool $core TRUE is the class is a core class or FALSE if it is from classes directory
     *
     * @return bool|object of the class
     */
    public static function getController($className, $core = true) {
        if (strpos($className, 'Controllers') === false) {
            $className = _SQ_NAMESPACE_ . '_Controllers_' . str_replace(_SQ_NAMESPACE_ . '_', '', $className);
        }
        return self::getClass($className);
    }

    /**
     * Deprecated since 8.0.2
     * Get the instance of the specified model class
     *
     * @param string $className
     *
     * @return bool|object of the class
     */
    public static function getModel($className) {
        if (strpos($className, 'Model') === false) {
            $className = _SQ_NAMESPACE_ . '_Models_' . str_replace(_SQ_NAMESPACE_ . '_', '', $className);
        }
        return self::getClass($className);
    }

    /**
     * Get the instance of the specified block from core directory
     *
     * @param string $className
     *
     * @return bool|object of the class
     */
    public static function getBlock($className) {
        if (strpos($className, 'Core') === false) {
            $className = _SQ_NAMESPACE_ . '_Core_' . str_replace(_SQ_NAMESPACE_ . '_', '', $className);
        }
        return self::getClass($className);
    }

    /**
     * Get all core classes from config.json in core directory
     * eg.SQ_Controllers_Post
     * @param string $for
     */
    public function getBlocks($for) {
        /* if config allready in cache */
        if (!isset(self::$config)) {
            $config_file = _SQ_CORE_DIR_ . 'config.json';
            if (!file_exists($config_file))
                return;

            /* load configuration blocks data from core config files */
            self::$config = json_decode(file_get_contents($config_file), 1);;
        }
        //print_r(self::$config);
        if (is_array(self::$config))
            foreach (self::$config['blocks']['block'] as $block) {
                if ($block['active'] == 1)
                    if (isset($block['controllers']['controller']))
                        if (!is_array($block['controllers']['controller'])) {
                            /* if the block should load for the current controller */
                            if ($for == $block['controllers']['controller']) {
                                SQ_Classes_ObjController::getClass($block['name'])->init();
                            }
                        } else {
                            foreach ($block['controllers']['controller'] as $controller) {
                                /* if the block should load for the current controller */
                                if ($for == $controller) {
                                    SQ_Classes_ObjController::getClass($block['name'])->init();
                                }
                            }
                        }
            }

    }

}
