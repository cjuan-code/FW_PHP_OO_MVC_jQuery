<?php 

include(UTILS_PATH . 'common.inc.php');

class router {
    private $uriModule;
    private $uriFunction;
    private $nameModule;
    static $_instance;

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }// end_if
        return self::$_instance;
    }// end_getInstance

    function __construct() {

        if(isset($_GET['page'])) {
            $this -> uriModule = ($_GET['page']);
        } else {
            $this -> uriModule = 'home';
        }

        if(isset($_GET['op'])) {
            $this -> uriFunction = ($_GET['op']);
        } else {
            $this -> uriFunction = 'list';
        }

    }// end_construct

    function routingStart() {
        try {
            call_user_func(array($this -> loadModule(), $this -> loadFunction()));
        }catch(Exception $e) {
            common::loadError();
            // include('error404.html');
        }// end_catch
    }// end_routingStart
    
    private function loadModule() {
        if (file_exists('resources/modules.xml')) {
            $modules = simplexml_load_file('resources/modules.xml');
            foreach ($modules as $row) {
                //////
                if (in_array($this -> uriModule, (Array) $row -> uri)) {
                    $path = MODULES_PATH . $row -> name . '/controller/controller_' . (String) $row -> name . '.class.php';
                   
                    if (file_exists($path)) {
                        require_once($path);
                        $controllerName = 'controller_' . (String) $row -> name;
                        $this -> nameModule = (String) $row -> name;
                        return new $controllerName;
                    }// end_if
                }// end_if
            }// end_foreach
        }// end_if
        throw new Exception('Not Module found.');
    }// loadModule
    
    private function loadFunction() {
        $path = MODULES_PATH . $this -> nameModule . '/resources/functions.xml';
        if (file_exists($path)) {
            $functions = simplexml_load_file($path);
            foreach ($functions as $row) {
                if (in_array($this -> uriFunction, (Array) $row -> uri)) {
                    return (String) $row -> name;
                }// end_if
            }// end_foreach
        }// end_if
        throw new Exception('Not Function found.');
    }// end_loadFunction


}

router::getInstance() -> routingStart();

?>