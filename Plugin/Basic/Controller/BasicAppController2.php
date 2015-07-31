<?php
App::uses('BasicAppController', 'Basic.Controller');
//App::import('Plugin', 'Basic/Controller/BasicAppController');
/**
 * NOTE: thinking what this class is.
 * Do not inherit this class until I know it.
 */
class BasicAppController2 extends BasicAppController
{
    /**
     * Merges tutorial $components' array and other $components' array
     *
     * @param array $components
     */
    protected function onGetComponents(array &$components = array()){
        self::mergeArrayAny($components, array(
            'Session',
            'Auth' => array(
                'loginRedirect' => array('controller' => 'posts', 'action' => 'index'),
                'logoutRedirect' => array(
                    'controller' => 'pages',
                    'action' => 'display',
                    'home'
                ),
                'authenticate' => array(
                    'Form' => array(
                        'passwordHasher' => 'Blowfish'
                    )
                ),
                'authorize' => array('Controller') // Added
            )
        ));
        parent::onGetComponents($components);
    }

    public function isAuthorized($user) {
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        return false;
    }

    public function beforeFilter() {
        $this->Auth->allow('index', 'view');
        parent::beforeFilter();
    }
}
