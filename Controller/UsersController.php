<?php
App::uses('CakeEmail', 'Network/Email');

/**
 * A user is a software agent, who uses the computer or network service.
 *
 * @author Thiago Colares
 */
class UsersController extends AppController {

    public $uses = array('User', 'Role', 'Profile');
	public $components = array('Recaptcha'); 

    public function beforeFilter() {
        parent::beforeFilter();

	    //$this->Auth->allow('initDB'); // We can remove this line after we're finished
	
        $this->Auth->allow('login', 'logout', 'requestResetPassword', 'resetPassword');
		$this->Recaptcha->publickey = ""; 
		$this->Recaptcha->privatekey = "";

        $this->title = __('User');
        $this->titleP = __('Users');

        $this->findFields = array(
            array('field' => 'User.id', 'title' => __('ID')),
            array('field' => 'Profile.name', 'title' => __('Name')),
            array('field' => 'User.email', 'title' => __('Email')),
            array('field' => 'Role.name', 'title' => __('Role')),
            array('field' => 'User.status', 'title' => __('Status')), // render
        );
    }

    /**
     * Join database tables for paginate
     *
     * @return Array Set of all tables to query
     * @author Thiago Colares
     */
    /*
      TODO Check if its really necessary due to new improvments from CakePHP 2.0
     */
    //     protected function getPaginateJoins() {
    //         $joins = parent::getPaginateJoins();
    // $specificJoins = array();
    // $specificJoins = array(
    // 	array(
    // 		'table' => 'roles',
    // 		'alias' => 'Role',
    // 		'type' => 'INNER',
    // 		'conditions' => array(
    // 			'User.role_id = Role.id',
    // 		)
    // 	),
    // );
    //         return array_merge($joins, $specificJoins);
    //     }

    /**
     * Adds a system user and its profile
     *
     * @return void
     * @author Thiago Colares
     */
    public function admin_add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->saveAll($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'), 'default', array('class' => 'alert-message success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set('getProfileTitles', $this->Profile->getProfileTitles());
		
		$roles = $this->Role->find('list');
        /*
          TODO Create a type field on roles table to tell if role is only for system users
         */
		// Just admin users my create user by now
        // if ($this->Session->read('Auth.User.role') != 'admin') {
        //     $roles = $this->Role->find('list', array('conditions' => array('alias NOT' => array('manager', 'admin'))));
        // } else {
        //     $roles = $this->Role->find('list');
        // }
        $this->set(compact('roles'));
    }

    /**
     * Edit a system user and its profile data
     *
     * @return void
     * @author Thiago Colares
     */
    public function admin_edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            //throw new NotFoundException(__('Invalid user'));
            $this->Session->setFlash(__('Invalid user.'));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->saveAll($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->set('getProfileTitles', $this->Profile->getProfileTitles());
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }

        $roles = $this->Role->find('list');
        $this->set(compact('roles'));
    }

    /**
     * Delete a system user and its profile data from database
     *
     * @param string $id 
     * @return void
     * @author Thiago Colares
     */
    /*
      TODO Should we do delete logical or physically?
     */
    public function admin_delete($id = null) {
        if (!$this->request->is('get')) {
            //throw new MethodNotAllowedException();
            $this->Session->setFlash(__('Method not allowed.'));
            $this->redirect(array('action' => 'index'));
        }

        $this->User->id = $id;
        if (!$this->User->exists()) {
            //throw new NotFoundException(__('Invalid user'));
            $this->Session->setFlash(__('Invalid user.'));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->User->delete($this->request->data('User.id'), true)) {
            $this->Session->setFlash(__('User deleted.'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted.'));
        /*
          TODO Show errors
         */
        $this->redirect(array('action' => 'index'));
    }

    /**
     * Perform login on system
     *
     * @return void
     * @author Thiago Colares
     */
    public function login() {
        // Check if is already logged
		if($this->Session->read('FB.Me')){
			$user = $this->User->findByFacebookId($this->Session->read('FB.Me.id'));
			if(!$user){
				$this->User->create();
				$this->request->data['User']['username'] = $this->Session->read('FB.Me.username');
				$this->request->data['User']['facebook_id'] = $this->Session->read('FB.Me.id');
				$this->request->data['User']['role_id'] = 3;
				$this->request->data['Profile']['name'] = $this->Session->read('FB.Me.name');
				$this->request->data['Profile']['facebook_data'] = json_encode($this->Session->read('FB.Me'));
				if($this->_save($this->User)){
					$this->request->data['User']['id'] = $this->User->id;
				}
			} else {
				$this->request->data = $user;
			}
			$this->Auth->login($this->request->data);
			//            $this->redirect($url);
		}

//                $this->Session->setFlash($this->Auth->loginError, 'default', array('class' => 'alert-message error'), 'auth');

    }


	/**
	 * By typing the current password. User can set a new password.
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	/*
		TODO Build a handler set! :) / Strategy
	*/
    public function profile_resetPassword() {
        if ($this->request->is('post')) {
	        $user = $this->User->findById($this->Auth->User('id'));
			if(!empty($user)){
				if($this->request->data['User']['password'] == $this->request->data['User']['confirm_password']){
					if(!empty($user['User']['password'])){							
						if ($user['User']['password'] == AuthComponent::password($this->request->data['User']['current_password'])) {
							if(strlen(trim($this->request->data['User']['password'])) >= 6){
								$this->request->data['User']['id'] = $this->Auth->User('id');
				                if ($this->User->save($this->request->data)) {
				                    $this->Session->setFlash(__('Password has been reset.', true), 'default', array('class' => 'alert-message success'));
				                    $this->redirect(array(
				                    	'plugin' => false,
										'action' => 'dashboard',
										'controller' => 'systems'
				                    ));
				                } else {
				                    $this->Session->setFlash(__('Password could not be reset. Please, try again.'), 'default', array('class' => 'alert-message error'));
				                }
							} else {
								$this->Session->setFlash(__('Password must have at least 6 characters. Please, try again.'), 'default', array('class' => 'alert-message error'));							
							}
			            } else {
			                $this->Session->setFlash(__('Current password did not match. Please, try again.'), 'default', array('class' => 'alert-message error'));
			            }
					} else {
		                $this->Session->setFlash(__('Password could not be empty! Please, try again.'), 'default', array('class' => 'alert-message error'));						
					}
				} else {
					$this->Session->setFlash(__('New password and its confirmation did not match. Please, try again.'), 'default', array('class' => 'alert-message error'));
				}
			} else {
				$this->Session->setFlash(__('User could no be found.'), 'default', array('class' => 'alert-message error'));
			}
		
		}

    }


	/**
	 * Send Reset Password Email
	 *
	 */
	private function _sendResetPasswordEmail(){
		$email = new CakeEmail();
		//__('no-reply') . '@' . env('HTTP_HOST');
		// $email->template('contact')
		//     ->emailFormat('html')
		$email->viewVars(array(
			'name' => $this->request->data['Profile']['name'],
			'email' => $this->request->data['User']['email'],
			'activation_key' => $this->request->data['User']['activation_key']
		));
		// 			->to($this->request->data['User']['email'])
		$res = $email->template('request_reset_password','default')
		    ->emailFormat('html')
			->from('no-reply@viiforumeducacaoambiental.org.br')
			->to($this->request->data['User']['email'])
			->subject('[' . Configure::read('EventTitle') . '] ' . __('Reset Password Requestment'))
			->send();
			
		return array('success' => true);
	}


	/**
	 * Forgot password? Ask for reset password!
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	/*
		TODO Build handlers
	*/
	public function requestResetPassword(){
		if ($this->request->is('post')) {
			if(!$this->Recaptcha->valid($this->request)){
				$this->Session->setFlash(__('The code informed at the end of this form was mistyped!'), 'default', array('class' => 'alert-message error')); 	
				$this->set('captchaError', true);
			} else {
				// Is this user exists?
				$options['recursive'] = 0;
				$options['conditions']['email'] = $this->data['User']['email'];
				$this->request->data = $this->User->find('first', $options);

				if(!$this->request->data){
					$this->Session->setFlash(__('E-mail could not be found.'), 'default', array('class' => 'alert-message notice'));
	        	} else {
					// Prepare to send a reset e-mail
					// Expires in two days
			        $expiration_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+2, date("Y")));

					$token = Security::hash(microtime(), null, true);

					$email = $this->request->data['User']['email'];
				
					// Update data
					$this->User->id = $this->request->data['User']['id'];
					$updateData['User']['expiration_date'] = $expiration_date;
					$updateData['User']['activation_key'] = $token;
				
					if(!$this->User->save($updateData)){
						$this->Session->setFlash(__('An error occurred. Please try again or contact the administrator.'), 'default', array('class' => 'alert-message error'));
					} else {	
						
						$this->request->data['User']['expiration_date'] = $expiration_date;
						$this->request->data['User']['activation_key'] = $token;
						
						$res = $this->_sendResetPasswordEmail();
						if(!$res['success']){
							$this->Session->setFlash(__('Error occurred while sending an e-mail. Please try again or contact the administrator.'), 'default', array('class' => 'alert-message error'));
						} else {
							$this->Session->setFlash(
								__('An E-mail has been sent to <strong>%s</strong> with instructions to recover your password. ', $this->request->data['User']['email']),
								'default',
								array('class' => 'alert-message success')
							);
						}
	      			}
	  			}
			} // repatcha
		} // is post
	}
	
	
	/**
	 * Reset Password
	 *
	 */
	/*
		TODO Build handlers
	*/
	function resetPassword(){
		if(
			!isset($this->request->pass[0]) // Email
			||
			!isset($this->request->pass[1]) // Token
		){
			$this->Session->setFlash(__('Invalid Link. Have you entered the right link?'), 'default', array('class' => 'alert-message error'));
			$this->redirect('/users/requestResetPassword');
		} else {
			$options['conditions']['User.email'] = $this->request->pass[0];
			$options['conditions']['User.activation_key'] = $this->request->pass[1];
			$options['conditions']['User.expiration_date >'] = date('Y-m-d H:i:s');
			$user = $this->User->find('first', $options);

			if(!$user){
				$this->Session->setFlash(__('Invalid Verification Code. Make sure that your code has not expired.'), 'default', array('class' => 'alert-message error'));
				$this->redirect('/users/requestResetPassword');
			}else{
			  //$this->set('recaptchaPublicKey', $this->recaptchaPublicKey);

				if ($this->request->is('post')) {
					if(!$this->Recaptcha->valid($this->request)){
						$this->Session->setFlash(__('The code informed at the end of this form was mistyped!'), 'default', array('class' => 'alert-message error')); 	
						$this->set('captchaError', true);
					} else {
						$user['User']['password'] = $this->request->data['User']['password'];
						$user['User']['confirm_password'] = $this->request->data['User']['confirm_password'];
						$user['User']['activation_key'] = '';
						$user['User']['expiration_date'] = '';

						if ($this->User->save($user)) {
							$this->Session->setFlash(__('Your password has successfully changed!'), 'default', array('class' => 'alert-message success'));
							$this->redirect('/login');
						}else{
							$this->Session->setFlash(__('Error occurred when chanding your password.'), 'default', array('class' => 'alert-message error'));
						}//if ($this->User->save($user)) 
						
					}//if (!$recaptcha_resp) 
					
				}//if($this->data)

			}//if(!$user)

		}//if(!isset($email) || !isset($token))
			

		
	}


    /**
     * Perform logout from system
     *
     * @return void
     * @author Thiago Colares
     */
    public function logout() {
        $this->Session->setFlash(__('You have successfully logged out.'), 'default', array('class' => 'alert-message success'));
		$this->Session->delete('FB');
		$this->redirect($this->Auth->logout());
    }


	public function add(){
		
	}

    /**
     * Set of action that, until now, ar useless
     */
    //     public function index() {
    //         $this->User->recursive = 0;
    //         $this->set('users', $this->paginate());
    //     }
    // 
    //     public function view($id = null) {
    //         $this->User->id = $id;
    //         if (!$this->User->exists()) {
    //             throw new NotFoundException(__('Invalid user'));
    //         }
    //         $this->set('user', $this->User->read(null, $id));
    //     }
    // 
    //     public function add() {
    // // Check if is already logged
    // if ($this->Session->read('Auth.User')) {
    // 	$this->Session->setFlash(__('You are already an User.'), 'default', array('class' => 'notice'));
    // 	return $this->redirect($this->Auth->redirect());
    // 	    }
    // 	
    //         if ($this->request->is('post')) {
    //             $this->User->create();
    //             if ($this->User->save($this->request->data)) {
    //                 $this->Session->setFlash(__('The user has been saved'));
    //                 $this->redirect(array('action' => 'index'));
    //             } else {
    //                 $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
    //             }
    //         }
    //     }
    // 
    //     public function edit($id = null) {
    //         $this->User->id = $id;
    //         if (!$this->User->exists()) {
    //             throw new NotFoundException(__('Invalid user'));
    //         }
    //         if ($this->request->is('post') || $this->request->is('put')) {
    //             if ($this->User->save($this->request->data)) {
    //                 $this->Session->setFlash(__('The user has been saved'));
    //                 $this->redirect(array('action' => 'index'));
    //             } else {
    //                 $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
    //             }
    //         } else {
    //             $this->request->data = $this->User->read(null, $id);
    //             unset($this->request->data['User']['password']);
    //         }
    //     }
    // 
    //     public function delete($id = null) {
    //         if (!$this->request->is('post')) {
    //             throw new MethodNotAllowedException();
    //         }
    //         $this->User->id = $id;
    //         if (!$this->User->exists()) {
    //             throw new NotFoundException(__('Invalid user'));
    //         }
    //         if ($this->User->delete()) {
    //             $this->Session->setFlash(__('User deleted'));
    //             $this->redirect(array('action'=>'index'));
    //         }
    //         $this->Session->setFlash(__('User was not deleted'));
    //         $this->redirect(array('action' => 'index'));
    //     }




	function initDB() {
	    $role = $this->User->Role;
	    //Allow admins to everything
	    $role->id = 1;
	    $this->Acl->allow($role, 'controllers');

	    //allow managers to posts and widgets
	    $role->id = 2;
	    $this->Acl->deny($role, 'controllers');
	
	    $role->id = 3;
	    $this->Acl->deny($role, 'controllers');
	    $this->Acl->allow($role, 'controllers/Posts');
	    // $this->Acl->allow($group, 'controllers/Widgets');
	    // 
	    // //allow users to only add and edit on posts and widgets
	    // $group->id = 3;
	    // $this->Acl->deny($group, 'controllers');
	    // $this->Acl->allow($group, 'controllers/Posts/add');
	    // $this->Acl->allow($group, 'controllers/Posts/edit');
	    // $this->Acl->allow($group, 'controllers/Widgets/add');
	    // $this->Acl->allow($group, 'controllers/Widgets/edit');
	    //we add an exit to avoid an ugly "missing views" error message
	    echo "all done";
	    exit;
	}


}

?>