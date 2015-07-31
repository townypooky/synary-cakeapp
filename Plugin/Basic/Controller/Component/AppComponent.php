<?php
/**
 * Abstract class file of AppComponent
 *
 * @author Towny Pooky
 */

App::uses('Component', 'Controller');

/**
 * All components should inherit this class.
 * 
 * @author Towny Pooky
 * @since 0.10
 */
abstract class AppComponent extends Component
{
    /**
     * Additional preparation for each instance
     */
    public function __construct(\ComponentCollection $collection, $settings = array()) {
        $components = array();
        $this->onGetComponents($components);
        $this->components = array_merge_recursive($this->components, $components);
        $helpers = array();
        $this->onGetHelpers($helpers);
        $this->helpers_import($helpers);
        parent::__construct($collection, $settings);
    }
 
    /**
     * Lists up components to use in the instance
     * 
     * @param array
     * @see Controller::$components
     */
    protected function onGetComponents(array &$components=array()){
        // Merge or push a string to the array
    }
    
    
    /**
     * Lists up helpers to use in the instance
     * 
     * @param array
     * @see Controller::$helpers
     */
    protected function onGetHelpers(array &$helpers=array()){
        // Merge or push a string to the array
    }


    /**
     * Applys the controller to the property
     */
    public function initialize(\Controller $controller) {
        parent::initialize($controller);
        $this->controller = $controller;
    }
    
    
    /**
     * Component class doesn't load helpers like Controller does,
     * so it needs this to load them additionally.
     * 
     * @param array $helpers String array of helper names.
     */
    final private function helpers_import(array $helpers=array()){
        foreach($helpers as $helper){
            if(is_string($helper)){
                App::import($helper, 'View/Helper');
            }else{
                App::import($helper[0], $helper[1]);
            }
        }
    }
}

