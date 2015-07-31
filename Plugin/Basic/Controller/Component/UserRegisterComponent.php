<?php
/**
 * Class file of BasicComponent
 *
 * @author Towny Pooky
 * @todo Check what's the difference from AppComponent.
 */

App::uses('AppComponent', 'Basic.Controller/Component');

/**
 * Component for user registration
 * 
 * @author Towny Pooky
 * @since 0.10
 */
class UserRegisterComponent extends AppComponent
{
    /**
     * Adds components to user authorization
     */
    protected function onGetComponents(array &$components = array()) {
        parent::onGetComponents($components);
        $components = array_merge_recursive($components, array(
            'Session', 'Auth'
        ));
    }
    

    /**
     * If it gets POST, which is correct for User->save(),
     * this method may register the user and publish a session
     * and then redirect to the front page.
     * 
     * CAUTION: this method won't work when it's called by another component
     * since CakePHP 2.6.1 or previous cannot specify the caller controller
     * through the component. (I'm not proud that much ....)
     * 
     * @param callable $onsuccess Called when registration succeeds.
     * The arguments are the following: <pre>
     *    callback(
     *       UserRegisterComponent $component, // === $this
     *       array $data, // Array for User->save()
     *       ... // Elements of $args in order
     *     )</pre>
     * If the callback returns false, this won't redirect to the front page
     * and won't display the session message.
     *
     * @param callable $onfailure Called when registration won't go.
     * The arguments are all the same as $onsuccess
     *
     * @param callable $onbefore Called when POST is not given.
     * The arguments are the following: <pre>
     *    callback(
     *       UserRegisterComponent $component, // === $this
     *       ... // Elements of $args in order
     *     )</pre>
     *
     * @param array $args Arguments for $onsuccess, $onfailure, $onbefore
     * @since 0.10
     */
    public function add(callable $onsuccess=null, callable $onfailure=null, callable $onbefore=null, array $args=array()){
        if(is_object($this->controller) && $this->controller->request->is('post')){
            $user_model = ClassRegistry::init('User');
            $user_model->create();
            $this->controller->request->data['User']['role'] = 'readonly';
            $this->controller->request->data['User']['screen_name'] = 'No name';
            $this->controller->request->data['User']['created'] = date('Y-m-d H:i:s', TIME_START);
            $this->controller->request->data['User']['modified'] = date('Y-m-d H:i:s', TIME_START);

            if($user_model->save($this->controller->request->data)){
                if(is_callable($onsuccess)){
                    $result = call_user_func_array($onsuccess,
                            array_merge(array($this, $this->controller->request->data), $args));
                    if($result === false) return;
                }
                $this->Session->setFlash(__('The user has been saved'));
                $this->controller->redirect(array('action' => 'index'));
            }else{
                if(is_callable($onfailure)){
                    $result = call_user_func_array($onfailure,
                            array_merge(array($this, $this->controller->request->data), $args));
                    if($result === false) return;
                }
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }else if(is_callable($onbefore)){
            call_user_func_array($onbefore,
                    array_merge(array($this), $args));
        }
    }
}
