<?php
/**
 * Class file of BasicAppController
 *
 * @author Towny Pooky
 */
App::uses('Controller', 'Controller');
App::uses('Security', 'Controller/Component');
App::uses('PreferenceComponent', 'Prefs.Controller/Component');
App::uses('BasicHelper', 'Basic.Helper');
App::uses('SectionHelper', 'Basic.Helper');

/**
 * Most unchanging, basic Controller class
 *
 * Test classes are highly recommended to inherit this class
 * to avoid gaps when the app is updated.
 *
 * NOTE: please don't modefy this class without enough test.
 *
 * You can add components, helpers, uses in sub classes.
 * Don't write the properties directly to avoid overwriting the values
 * and missing necessary classes.
 *
 * User authorization is supported.
 * View can get the value of $is_login to check the status.
 *
 * This forces SSL for security.
 * @see BasicAppController::onGetActionNamesForSSL()
 *
 * @since 0.11 Added onGetSiteTitle(), onGetPageTitle()
 */
abstract class BasicAppController extends Controller
{
    /**
     * @var array
     * @see BasicAppController::loadUserPreferences()
     */
    public $userPreferences;


    /**
     * Define the website title.
     * A sub class can overwrite the value so that you can
     * easily inherit another project's class.
     *
     * This value affects on $site_title in View.
     *
     * @param string $title
     * @since 0.11   Added onGetSiteTitle()
     */
    protected function onGetSiteTitle(&$title='untitled'){
        if(!defined('IS_TEST_MODE')) return;
        if(!defined('SITE_TEST_TITLE')) return;
        if(!defined('SITE_TITLE')) return;
        $title = IS_TEST_MODE && $this->isPublicPage() ? SITE_TEST_TITLE : SITE_TITLE;
    }

    /**
     * Additional preparation for each instance
     */
    public function __construct($request = null, $response = null) {
        $components = array();
        $this->onGetComponents($components);
        if(!is_array($this->components)) $this->components = array();
        self::mergeArrayAny($this->components, $components);
        $helpers = array();
        $this->onGetHelpers($helpers);
        if(!is_array($this->helpers)) $this->helpers = array();
        self::mergeArrayAny($this->helpers, $helpers);
        $uses = array();
        $this->onGetUses($uses);
        if(!is_array($this->uses)) $this->uses = array();
        self::mergeArrayAny($this->uses, $uses);
        $this->apply_default_layout_on_bootstrap();
        parent::__construct($request, $response);
    }


    /**
     * Does things before filter
     */
    public function beforeFilter(){
        parent::beforeFilter();

        /** @see onGetSiteTitle() **/
        $this->set('site_title', $this->getSiteTitle());
        /** @see onGetPageTitle() **/
        $this->set('page_title', $this->getPageTitle());
        /** @see getController() **/
        $this->set('controller', $this->getController());
        /** @see getAction() **/
        $this->set('action', $this->getAction());
        /** @see updateAuth() **/
        $this->updateAuth();
        /*** @see postit **/
        $this->set('arg1', isset($this->request->params[0]) ? $this->request->params[0] : null);
        $this->set('arg2', isset($this->request->params[1]) ? $this->request->params[1] : null);
        $this->set('arg3', isset($this->request->params[2]) ? $this->request->params[2] : null);
        $this->set('arg4', isset($this->request->params[3]) ? $this->request->params[3] : null);
        /** @see isTest() */
        $this->set('is_test', $this->isTest());


        // Force SSL
        $force_ssl_actions = array();
        $this->onGetActionNamesForSSL($force_ssl_actions);
        if (in_array('*', $force_ssl_actions) || in_array($this->action, $force_ssl_actions)){
            // Force SSL when it gets the action name
            $this->Security->blackHoleCallback = 'forceSSL';
            $this->Security->requireSecure();
        }elseif(env('HTTPS')){ // In another case it should be http ?
            $this->_unforceSSL();
        }

        /**
         * When the user is not logged in on test,
         * it redirect to the front page if the page is public in BasicAppController::isPublicPage()
         *
         * @see BasicAppController::isTest()
         */
        if($this->isTest() && !$this->isLogin() && !$this->isPublicPage()){
            $this->Session->setFlash(__d('basic', 'The page is closed temporarily because of maintenance.'));
            $this->redirect('/');
            return;
        }

        $this->loadUserPreferences(
            $this->is_login ? $this->Preference->getSession() : array()
        );
    }

    /**
     * Applys the user preference to the current access
     *
     * @param array $preferences
     * @see Prefs.PreferenceComponent::getSession()
     */
    protected function loadUserPreferences(array $preferences){
        if(isset($preferences['lang'])){
            Configure::write('Config.language', $preferences['lang']);
        }
        $this->userPreferences = empty($preferences) ? array() : $preferences;
        $this->set('user_preferences', $this->userPreferences);
    }

    /** Does things after action and before View */
    public function beforeRender(){
        parent::beforeRender();
        $this->set('model', $this->getModel());
    }


    /** @var boolean Whether the current user is logged in **/
    private $is_login = false;
    /** @var boolean Whether the current user is an administrator **/
    private $is_admin = false;
    /** @var boolean Whether the current user is a project user */
    private $is_project_user = false;
    /** @var string|null Name of the current user **/
    private $username = null;
    /** @var int User ID of the current user **/
    private $user_id = 0;
    /** @return boolean Whether the current user is logged in **//
    public function isLogin(){ return $this->is_login; }
    /** @return boolean Whether the current user is an administrator **/
    public function isAdmin(){ return $this->is_admin; }
    /** @return string|null Name of the current user **/
    public function getCurrentUserName(){ return $this->username; }
    /** @return int User ID of the current user **/
    public function getCurrentUserId(){ return $this->user_id; }

    /**
     * Force SSL
     *
     * NOTE: don't call this except from beforeFilter()
     */
    public function forceSSL() {
        /** Hacks Sakura **/
        if(isset($_SERVER['HTTP_X_SAKURA_FORWARDED_FOR'])) return;
        $this->redirect('https://' . env('SERVER_NAME') . $this->here);
    }
    /**
     * Force HTTP
     *
     * NOTE: don't call this except from beforeFilter()
     */
    public function _unforceSSL() {
        $this->redirect('http://' . env('SERVER_NAME') . $this->here);
    }

    /**
     * Define the page title in the web site.
     * A sub class can overwrite the value so that you can
     * easily inherit another project's class.
     *
     * This value affects on $page_title in View.
     *
     * @param string $title
     * @since 0.11   Added onGetPageTitle()
     */
    protected function onGetPageTitle(&$title=''){
        // Overwrite the string
    }

    /**
     * List up the action names to force SSL
     *
     * NOTE: this won't work only when IS_TEST_MODE is defined and its value is true,
     * plus, Configure::read('Basic.httpIsOkay') has an integer.
     *
     * @param array $names Method names in Controller to forece SSL or `*` for all.
     */
    protected function onGetActionNamesForSSL(array &$names){
        if(defined('IS_TEST_MODE') && IS_TEST_MODE && is_int(Configure::read('Basic.httpIsOkay'))){
            $name = array();
        }else {
            $names = array('*');
        }
    }
 
    /**
     * Lists up components to use in the instance
     * 
     * @param array
     * @see Controller::$components
     */
    protected function onGetComponents(array &$components){
        self::mergeArrayAny($components, array(
            'Session', 'Security',
            'Prefs.Preference',
            'RequestHandler'
        ));
    }
    
    
    /**
     * Lists up helpers to use in the instance
     * 
     * @param array
     * @see Controller::$helpers
     */
    protected function onGetHelpers(array &$helpers){
        self::mergeArrayAny($helpers, array(
            'BootForm', 'BootHtml', 'Basic.Basic',
            'Basic.Section'
        ));
    }
    
    
    /**
     * Lists up models to use in the instance
     * 
     * @param array
     * @see Controller::$helpers
     */
    protected function onGetUses(array &$uses){
        // Merge or push a string to the array
    }


    /**
     * Merge arrays
     *
     * @param array... $source_arr Array to merge
     */
    final protected static function mergeArrayAny(array &$source_arr){
        $args = func_get_args();
        array_shift($args);
        for($i=0, $k=count($args); $i<$k; $i++){
            if(!is_array($args[$i])) continue;
            $source_arr = array_merge_recursive($source_arr, $args[$i]);
        }
    }

    /**
     * Get the default Model of the controller
     *
     * @return Model
     */
    protected function getModel(){
        $model_class = $this->modelClass;
        return is_string($model_class) && isset($this->$model_class) ? $this->$model_class : null;
    }


    /**
     * Get the website title
     *
     * @since 0.11 Added onGetSiteTitle()
     * @final Don't modefy this
     * @return string Website title in string
     */
    final protected function getSiteTitle(){
        $site_title = 'untitled';
        $this->onGetSiteTitle($site_title);
        return $site_title;
    }


    /**
     * Get the page title of the website
     *
     * @since 0.11 Added onGetPageTitle()
     * @final Don't modefy this
     * @return string Page title in string
     */
    final protected function getPageTitle(){
        $page_title = '';
        $this->onGetPageTitle($page_title);
        return $page_title;
    }

    /**
     * Get the current controller name
     *
     * @since 0.11 Added
     * @final Don't modefy this
     * @return string Name of the controller
     */
    final protected function getController(){
        return $this->request->params['controller'];
    }


    /**
     * Get the current action (method) name
     *
     * @since 0.11 Added
     * @final Don't modefy this
     * @return string Name of the action
     */
    final protected function getAction(){
        return $this->request->params['action'];
    }

    /**
     * Update cached data by the session
     * The values are affected by the properties of this class and View.
     *
     * @since 0.11 Added
     */
    protected function updateAuth(){
        $user = $this->Session->read('Auth.User');
        $this->is_login = is_array($user);
        $this->values(array(
            'is_login'=>$this->is_login,
            'is_project_user'=>$this->is_login && in_array($user['role'], array('admin', 'author')),
            'is_admin' => $this->is_login && $user['role'] === 'admin',
            'user_id' => $this->is_login ? $user['id'] : null,
            'username' => $this->is_login ? $user['username'] : null
        ));
        /** @deprecated 201502262104 */
        $this->set('islogin', $this->is_login);
        $this->set('isadmin', $this->is_admin);
    }


    /**
     * Register the values to the property and View
     *
     * @param array $data Keys are a property name and values are its value.
     */
    final protected function values(array $data){
        foreach($data as $name=>$value){
            $this->$name = $value;
            $this->set($name, $value);
        }
    }

    /**
     * Whether the current page is available to display without authorization.
     * This method makes sence only on test.
     *
     * @return bool
     */
    protected function isPublicPage() {
        $pares = array(
            array('index', 'index'),
            array('users', 'login'),
            array('register', 'invite')
        );
        foreach ($pares as $pare){
            if ($this->getController() === $pare[0] && $this->getAction() === $pare[1]) return true;
        }
        return false;
    }

    /**
     * Apply $layout in Controller if <code>Configure::write('default_layout', 'name of *.ctp');</code>
     * is written in <code>bootstrap.php</code>.
     *
     * Especially for plugin views, this is very useful.
     */
    private function apply_default_layout_on_bootstrap(){
        $default_layout = Configure::read('default_layout');
        if(is_string($default_layout)) $this->layout = $default_layout;
    }


    /**
     * Get the version of the current plugin or app.
     *
     * The version should be written on the file in the root directory
     * for each. The file name should be "VERSION" without any extension.
     *
     * @return string Value like version_compare()
     * @see version_compare()
     */
    public function version(){
        $class_file = (new ReflectionObject($this))->getFilename();
        $version_file = dirname(dirname($class_file)) . DS . 'VERSION';
        $unknown_version = '0.0.0';
        if(file_exists($version_file)){
            $version = @file_get_contents($version_file, false);
            if(!is_string($version)) $version = $unknown_version;
        }else{
            $version = $unknown_version;
        }

        $this->set('controller_version', $version);
        return $version;
    }


    /**
     * Whether it's currently on test.
     *
     * This value is affected by <code>Configure::write('Basic.isTest', '0-2')</code>
     * on <code>App/Config/bootstrap.php</code>.
     * 0-2 means 0 or 1 or 2 in integer, not string like that.
     * @return bool
     */
    protected function isTest(){
        $v = Configure::read('Basic.isTest');
        return is_int($v) && $v > 0;
    }

}
