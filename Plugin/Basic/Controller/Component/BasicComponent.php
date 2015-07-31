<?php
/**
 * Class file of BasicComponent
 *
 * @author Towny Pooky
 * @todo Check what's the difference from AppComponent.
 */

App::uses('ArrayTrait', 'Basic.Util');

/**
 * Basic Component that most of Component classes should inherit
 */
class BasicComponent extends Component
{
    use ArrayTrait;

    /**
     * The caller controller
     *
     * @var Controller
     */
    public $controller;

    /**
     * Apply the caller controller to the component to
     * use $controller in the component class
     *
     * @param Controller $controller
     */
    public function initialize(Controller $controller) {
        parent::initialize($controller);
        $this->controller = $controller;
    }

    /**
     * Additional preparation for each instance
     */
    public function __construct(\ComponentCollection $collection, $settings = array()) {
        $components = array();
        $this->onGetComponents($components);
        self::mergeArrayAny($this->components, $components);
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

