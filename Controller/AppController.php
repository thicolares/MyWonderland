<?php
/**
 * The AppController class is the parent class to all of your application’s controllers.
 *
 * @package Controller
 */
class AppController extends Controller {
    
    // var $langs = array(
    //     'pt_br' => 'Português'
    // );

	/**
	 * Components are packages of logic that are shared between controllers.
	 * If you find yourself wanting to copy and paste things between controllers, you might consider wrapping some functionality in a component.
	 *
	 * @var string
	 */
    public $components = array(
        'Session',
        'Cookie',
		'Facebook.Connect',
		//'Acl',
		/**
		 * Default is authorize option is ActionsAuthorize.
		 * In this case, system uses AclComponent to check for permissions on an action level.
		 * learn more: http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#authorization
		 */
		// 'Auth'=> array(
		// 	'authorize' => 'Controller',
		// )
    );


	/**
	 * Helpers are the component-like classes for the presentation layer of your application.
	 * They contain presentational logic that is shared between many views, elements, or layouts.
	 *
	 * @var string
	 */
    public $helpers = array(
        'Html',
		'Time',
        'Form',
        'Session',
        'Text',
        'Js',
        'Layout',
        'AdminIndexList',
		'Paginator',
		'Number',
		'Facebook.Facebook' => array('locale' => 'pt_BR')
    );

    public function getVar($var) {
        return $this->$var;
    }

    public function setVar($var, $value) {
        $this->$var = $value;
    }

	/**
	 * Save handlers that will be trigget with or without DATA
	 *
	 * @author Thiago Colares
	 */
	var $preSaveHandlers = array();
	
	var $successMessage;
	
	var $res;
	
	/**
	 * Save handlers that will be trigget if DATA IS NOT EMPTY
	 *
	 * @author Thiago Colares
	 */
	var $saveHandlers = array(
		array('handler' => 'finalSaveHandler')
	);
			
	/**
	 * Reads an item to edito form
	 *
	 * @var string
	 */
	var $readHandlers = array(
		array('handler' => 'defaultReadHandler')
	);
	
	/**
	 * Do stuffs before peform delete action
	 *
	 * @var string
	 */
	var $deleteHandlers = array(
	);

	/**
	 * Switch the layout according to the prefix 
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    private function _switchLayoutByPrefix(){
        if(isset($this->params['prefix'])){
            switch ($this->params['prefix']) {
                case 'admin':
                    $this->layout = 'admin';
                    break;
                // case 'manager':
                //     $this->layout = 'manager';
                //     break;
                case 'profile':
                    $this->layout = 'profile';
                    break;
                default:
                    $this->layout = 'default';
                    break;
				 
            }   
        } else {
            $this->layout = 'default';
        }
    }


	/**
	 * This function is executed before every action in the controller.
	 * It’s a handy place to check for an active session or inspect user permissions.
	 *
	 * @return void
	 */
    function beforeFilter() {		
// debug($this->Session->read('Auth'));
		// $this->set('user', $this->Auth->user());
		// $this->set('facebook_user', $this->Connect->user());

		// Calling AppController’s callbacks within child controller callbacks for best results
		parent::beforeFilter();
		
		// Switch the layout according to the prefix 
		$this->_switchLayoutByPrefix();
		
		// Actions public for all visitors. This options overcome Authorizations setup
		// $this->Auth->allow('display');
		// 'authorizedActions' => array('index', 'view', 'display')
		//$this->Auth->allow('*');

		// Authorization setup
		// using the default
		//$this->Auth->authError = __('Did you really think you are allowed to see that?');
		// You are not allowed access to this page or perform this action.
		//$this->redirect('/p/servicos');
		
		// Custom fields setup
		// $this->Auth->fields = array('username' => 'email', 'password' => 'password');
		
		// Login / Logout stuffs setup
		// $this->Auth->loginError = __('Username or password are incorrect.');
		// 	    $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login','admin' => false, 'plugin' => false);	
		//         $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => false);
		//         $this->Auth->loginRedirect = array('controller' => 'systems', 'action' => 'dashboard', 'admin' => true, 'plugin' => false);
    }

	function isAuthorized(){
		return true;
	}


	/**
	 * Only user can Edit / Remove its own content. This actions is performerd before delete and before show edit form
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function checkOwner(&$options){
		return;
		// Default user foreignKey
		$field = 'user_id';
		
		// Check if there is a custom foreignKey instead of user_id
		$this->loadModel('User');
		if(isset($this->User->hasOne[Inflector::singularize($this->name)]['foreignKey'])){
			$field = $this->User->hasOne[Inflector::singularize($this->name)]['foreignKey'];
		} elseif (isset($this->User->hasMany[Inflector::singularize($this->name)]['foreignKey'])) {
			$field = $this->User->hasMany[Inflector::singularize($this->name)]['foreignKey'];
		}
		
		// If there is user_id and your are not admin...
		if(
			$this->{Inflector::singularize($this->name)}->hasField($field) 
			&&
			$this->Session->read('Auth.User.role') != 'admin'			
		){
			// You better be owner or beat it!
			$options['conditions'][Inflector::singularize($this->name) . '.' . $field] = $this->Session->read('Auth.User.id');
			return;
		}	
	}

	/**
	 * Define paginate owner conditions
	 *
	 * @return array
	 * @author Thiago Colares
	 */
	protected function getPaginateOwnerConditions() {
        // If is logged, only shows own content
        $conditions = array();
        if(
            $this->{Inflector::singularize($this->name)}->hasField('user_id') 
            &&
            $this->Session->read('Auth.User') // while allow(*) 
            &&
            (
                $this->Session->read('Auth.User.role') != 'admin' 
                &&	
                $this->Session->read('Auth.User.role') != 'manager'
                &&
                $this->Session->read('Auth.User.role') != 'event_owner'
            )
        ){
            $conditions[Inflector::singularize($this->name) . '.user_id'] = $this->Auth->user('id');
        }
		return $conditions;
	}

	/**
	 * Define paginate conditions
	 *
	 * @return array
	 * @author Thiago Colares
	 */
	protected function getPaginateConditions() {
		return array();
	}
	
	/**
	 * Define paginate conditions
	 *
	 * @return array
	 * @author Thiago Colares
	 */
	protected function getPaginateAllConditions() {
		return array_merge($this->getPaginateOwnerConditions(), $this->getPaginateConditions());
		
	}


	/**
	 * Set default paginate order
	 *
	 * @return mixed
	 * @author Thiago Colares
	 */
    protected function getPaginateOrder(){
        if(isset($this->passedArgs['sort']) && isset($this->passedArgs['direction']))
            return array($this->passedArgs['sort'] => $this->passedArgs['direction']);
        else
            return null;
	}
	
//         if (isset($_REQUEST['sort'])) {
// die('e');
//             $sortPieces = explode('__', $_REQUEST['sort']);
//             $countPieces = count($sortPieces);
//             switch ($countPieces) {
//                 case 2:
//                     $sort = $sortPieces[0] . '.' . $sortPieces[1];
//                     break;
//                 case 3:
//                     $sort = $sortPieces[0] . '.' . $sortPieces[2];
//                     break;
//                 default:
//                     $sort = $this->defaultSortField;
//                     break;
//             }
//         } else {
// debug($this->defaultSortField);
//             $sort = $this->defaultSortField;
//         }
// 
//         $dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';
//         return array($sort . ' ' . $dir);
//     }


	/**
	 * Define index for the first list element
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function getPaginateOffset() {
	    return isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
	}


	/**
	 * Define the page size limite
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    protected function getPaginateLimit() {
		return Configure::read('PageSize');
    }


	/**
	 * Define which fields wil be shown
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function getPaginateFields() {
		if(isset($this->findFields)){
			$fields = array();
			foreach($this->findFields as $key => $field){
				// Just append if its not a taxonomy
				if(!isset($field['taxonomy']) || !$field['taxonomy']){
					$fields[] = $field['field'];
				}
			}
		} else {
			$fields = array(Inflector::singularize($this->name) . '.*');
		}
		return $fields;
	}
	
	// 	    public function getFieldsForSearch() {
	// 	        $fields = array($this->entity . '.*');
	// 
	// 	        if (isset($this->fields)) {
	// 	            foreach ($this->fields as $field) {
	// 	                $namePieces = explode('.', $field['Field']);
	// 	                $virtual = false;
	// 	                if (isset($field['Virtual']))
	// 	                    $virtual = $field['Virtual'];
	// 
	// 	                if ($namePieces[0] != $this->entity && !$virtual)
	// 	                    $fields[] = $field['Field'];
	// 	            }
	// 	        }
	// 
	// 	        return $fields;
	// 	    }
	// 	
	// return 'Registration.*';
	//         return $this->fields;
	

	/**
	 * Define paginate joins
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    protected function getPaginateJoins() {
        return array();
    }


	/**
	 * Organizes all paginate options
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    protected function getPaginateOptions() {
        $options = array();
        $options['conditions'] = $this->getPaginateAllConditions();

        //$options['order'] = $this->getPaginateOrder();

        $options['offset'] = $this->getPaginateOffset();

        $options['limit'] = $this->getPaginateLimit();

        $options['fields'] = $this->getPaginateFields();

        $options['joins'] = $this->getPaginateJoins();

        return $options;
    }

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    protected function getPaginateCountOptions() {
        $options = array();

        $options['conditions'] = $this->getPaginateAllConditions();

        $options['joins'] = $this->getPaginateJoins();

        return $options;
    }

	protected function getPaginateEntity(){
		return Inflector::singularize($this->name);
	}

	/**
	 * Find all items regarding some arguments. 
	 * @access public
	 *
	 * @return array
	 */
	protected function basePaginate() {
		$items = array();
		try {
			$options = array(
				'conditions'	=> $this->getVar('conditions'),
				'joins'			=> $this->getVar('joins'),
				'order'			=> $this->getVar('orders'),
				'offset'		=> $this->getVar('offset'),
				'limit'			=> $this->getVar('limit'),
				/*
					TODO make recusrive optiional
				*/
				//'recursive'		=> -1
			);

			// Get find fields
			$getFieldsForSearchFunction = $this->getVar('getFieldsForSearcFunction');
			if (isset($getFieldsForSearchFunction)) {
				$options['fields'] = $this->{$getFieldsForSearchFunction}();
			} else {
				$options['fields'] = $this->getFieldsForSearch();
			}


				// Renderer
				//$data = $this->{$this->entity}->find('all', $options);
				//debug(array(Inflector::singularize($this->name) => $options));
				// $this->paginate = array(Inflector::singularize($this->name) => $options);
				//debug($this);

			// Only shows status = 1
			if($this->{Inflector::singularize($this->name)}->hasField('status')){
				$options['conditions'][Inflector::singularize($this->name) . '.status'] = 1;
			}
			
			/*
				TODO IT IS NOT NECESSARY. Must filter by event_id only
			*/
			
			// Setting options to paginate
			$this->paginate = $options;
			
			// if(Inflector::singularize($this->name) != $this->name){
			// 	$res = $this->loadModel(Inflector::singularize($this->name));
			// }

			$items = $this->paginate(Inflector::singularize($this->name));
			$success = true;
	    } catch (Exception $e) {
	        $message = $e->getMessage();
	        $success = false;
	    }

	    return compact('items', 'total', 'success', 'message');
	}


    /**
     * Paginate all items regarding some arguments. 
     * @access public
     *
     * @return array
     */
    public function findPaginate($context=null) {
		$this->setVar('entity', $this->getPaginateEntity());
        $this->setVar('conditions', $this->getPaginateAllConditions());
        $this->setVar('joins', $this->getPaginateJoins());
        $this->setVar('orders', $this->getPaginateOrder());
        $this->setVar('limit', $this->getPaginateLimit());
        $this->setVar('offset', $this->getPaginateOffset());
        $this->setVar('fields', $this->getPaginateFields());
        $this->setVar('baseAdditionalElements', $this->additionalElements);
        $this->setVar('getFieldsForSearcFunction', 'getPaginateFields');

        return $this->basePaginate();

		//         if ($context == 'return')
		//             return $result;
		//         else{
		// 	$this->set($result);
		// }

    }


	/**
	 * Generic method for list data in admin_index
	 *
	 * @author Thiago Colares
	 */
	public function admin_index(){
        $this->set('titleP', $this->titleP);
        $this->set('title', $this->title);
        $this->set('findFields', $this->findFields);
		
        //$this->loadModel(Inflector::singularize($this->name));

        // $this->{$this->name}->recursive = 0;
        // $res = $this->paginate($this->name);

		$res = $this->findPaginate('return');

		if($res['success']){
			$this->set('entity',Inflector::singularize($this->name));		
			$this->set('rowActions', $this->rowActions);
	        $this->set('res', $res['items']);
		} else {
			$this->Session->setFlash(__('Error:') . ' ' . $res['message'], 'default', array('class' => 'alert-message error'));
		}

	}

	public function profile_index(){
        $this->set('titleP', $this->titleP);
        $this->set('title', $this->title);
        $this->set('findFields', $this->findFields);
		
        //$this->loadModel(Inflector::singularize($this->name));

        // $this->{$this->name}->recursive = 0;
        // $res = $this->paginate($this->name);

		$res = $this->findPaginate('return');

		if($res['success']){
			$this->set('entity',Inflector::singularize($this->name));		
			$this->set('rowActions', $this->rowActions);
	        $this->set('res', $res['items']);
		} else {
			$this->Session->setFlash(__('Error:') . ' ' . $res['message'], 'default', array('class' => 'alert-message error'));
		}

	}

	
	
	/**
	 * If this content belongs to an user, append this handler to your preSaveHandler array
	 *
	 * @author Thiago Colares
	 */
	protected function appendUserId(){
		// Only if Add!

		if(
			empty($this->request->data[Inflector::singularize($this->name)]['id'])
			&&
			empty($this->request->data[Inflector::singularize($this->name)]['user_id'])
		)
			$this->request->data[Inflector::singularize($this->name)]['user_id'] = $this->Auth->user('id');
		return array('success' => true);
    }


	/**
     * Finally perform a save on database. Default handler
     */
    protected function finalSaveHandler(){
        return $this->_save($this->{Inflector::singularize($this->name)});
    }

	/**
	 * Default pos save handler
	 *
	 * @author Thiago Colares
	 */
	protected function defaultPosSaveHandler(){
		
	}
	
	/**
     * Save data to database
	 *
	 * @param string $model 
	 * @param string $indexed If want to pass $data['ModelName'] instead of usual $data. When saving multiple records of same model the records arrays should be just numerically indexed without the model key. 
	 * @return void
	 */
    protected function _save(&$model, $indexed = false) {

        try {

			$res = null;

			if($indexed) {
				$res = $model->saveAll($this->request->data[$model->name]);
			} else {
				$res = $model->saveAll($this->request->data);
			}

            if ($res === true) {
                $success = true;
            } else {
                $success = false;

                $valErrMsg = null;
				/*
					TODO When database throw a unique index error, its not cacthed as invalidFields
				*/
				/*
					TODO those nested if are terrible!! please, build something like handlers, as strategy pattern
				*/

				
                if (!empty($model->dbErrorCode)) {

                    $message = sprintf(__("Error code: %d."), $model->dbErrorCode);

				} elseif (!empty($model->validate)){
					$message = __("Check for validation errors.");
                //} else {
					/**
					 * This part of the code is usefull only to send fields and its error to a json view!
					 * In our case, it will duplicade error message in each field
					 *
					 */
					// $invalidFields = $model->invalidFields();
					// if (!empty($invalidFields[$model->name])) {
					//                         $invalidFields = $model->invalidFields();
					//                         $arrKey = array_keys($invalidFields);
					//                         if (is_array($invalidFields[$arrKey[0]])) {
					//                             $modelName = $arrKey[0];
					//                             $fieldId = array_keys($invalidFields[$modelName]);
					//                             $fieldId = $fieldId[0];
					//                             $fieldName = array_keys($invalidFields[$modelName][$fieldId]);
					//                             $fieldName = $fieldName[0];
					//                             $error = $invalidFields[$modelName][$fieldId][$fieldName];
					//                             $errors[$modelName][$fieldId][$fieldName] = $error;
					//                         } else {
					//                             $field = $arrKey[0];
					//                             $error = $invalidFields[$field];
					//                             $errors[$model->name][$field] = $error;
					//                         }
					//                         // Check if there is db error
					//                         if (isset($this->{$arrKey[0]}->dbErrorCode)) {
					//                             $valErrMsg = sprintf(__("Error code: %d.", true), $this->{$arrKey[0]}->dbErrorCode);
					//                             // Check if there is(are) validation(s) error(s)
					// 	}
					//                 	} elseif(!empty($model->validate)){
					// 	$message = __("Check for validation errors.", true);
					// }	
				}
            }
        } catch (Exception $e) {
            $success = false;
            $message = $e->getMessage() . ': ' . __('Error could not be solved', true);
        }

        return compact('success', 'message', 'errors');
    }
    
	/**
     * Save data to database. Used outside of a form. it puts validation errors manually
	 *
	 * @param string $model 
	 * @param string $indexed If want to pass $data['ModelName'] instead of usual $data. When saving multiple records of same model the records arrays should be just numerically indexed without the model key. 
	 * @return void
	 */
    protected function _aloneSave(&$model, $indexed = false) {

        try {

			$res = null;

			if($indexed) {
				$res = $model->saveAll($this->request->data[$model->name]);
			} else {
				$res = $model->saveAll($this->request->data);
			}

            if ($res === true) {
                $success = true;
            } else {
                $success = false;

                $valErrMsg = null;
				/*
					TODO When database throw a unique index error, its not cacthed as invalidFields
				*/
				/*
					TODO those nested if are terrible!! please, build something like handlers, as strategy pattern
				*/
				if (!empty($model->dbErrorCode)) {
					$message = sprintf(__("Error code: %d.", true), $model->dbErrorCode);
				} else {
//					debug($model->validationErrors);
				    if (!empty($model->validationErrors)) {

				    	$invalidFields = $model->validationErrors;

				    	$arrKey = array_keys($invalidFields);

				     	if (is_array($invalidFields[$arrKey[0]])) {
							if(is_array(current($invalidFields[$arrKey[0]]))){
								$errors[$model->name][$arrKey[0]] = implode('.',current($invalidFields[$arrKey[0]])) . '.';
							} else {
								$errors[$model->name][$arrKey[0]] = implode('.',$invalidFields[$arrKey[0]]) . '.';
							}
							
							// $modelName = $arrKey[0];
							// $fieldId = array_keys($invalidFields[$modelName]);
							// $fieldId = $fieldId[0];
							// $fieldName = array_keys($invalidFields[$modelName][$fieldId]);
							// $fieldName = $fieldName[0];
							// $error = $invalidFields[$modelName][$fieldId][$fieldName];
							// $errors[$modelName][$fieldId][$fieldName] = $error;
				     	} else {
							$errors[$model->name][$arrKey[0]] = $invalidFields[$arrKey[0]] . '.';
							// $field = $arrKey[0];
							// $error = $invalidFields[$field];
							// $errors[$model->name][$field] = $error;
				     	}
						// Check if there is db error
						if (isset($this->{$arrKey[0]}->dbErrorCode)) {
							$errors = sprintf(__("Error code: %d.", true), $this->{$arrKey[0]}->dbErrorCode);
							// Check if there is(are) validation(s) error(s)
						}
						$message = __("Error while saving.", true);
					} elseif(!empty($model->validate)){
						$message = __("Check for validation errors.", true);
				    }	
				}
            }
        } catch (Exception $e) {
            $success = false;
            $message = $e->getMessage() . ': ' . __('Error could not be solved', true);
        }

        return compact('success', 'message', 'errors');
    }
		
	/**
	 * Generic action that adds a new item
	 *
	 * @return void
	 */
	function admin_add() {
        $this->set('titleP', $this->titleP);
        $this->set('title', $this->title);

		$this->res['success'] = true;
		
		// Handlers to be trigged with or without data
        foreach ($this->preSaveHandlers as $item) {
	        $this->res = $this->{$item['handler']}();
	        if($this->res['success'] != true)
	            break;
        }

        if($this->res['success'] === true){
			if ($this->request->is('post')){
		
				// Handlers to be trigged IF DATA IS NOT EMPTY
				foreach ($this->saveHandlers as $item) {
		            $this->res = $this->{$item['handler']}();
		            if($this->res['success'] != true)
                        break;
		        }

				/*
					TODO Make $this->res generic
				*/
				
				if($this->res['success'] == true){
					if(isset($this->successMessage)){
						$this->Session->setFlash($this->successMessage, 'default', array('class' => 'alert-message success'));
					} else {
						$this->Session->setFlash(__("%s has been successfully added.", $this->title), 'default', array('class' => 'alert-message success'));
					}

                    if(empty($this->redirectURLHandler)){
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->redirect($this->redirectURLHandler);
                    }
                } else {
                    $this->Session->setFlash(__('Error while saving') . ' ' . $this->title . '. ' . $this->res['message'], 'default', array('class' => 'alert-message error'));
                }


            }
        } else {
            $this->Session->setFlash(__('Error while loading form') . ' ' . $this->title . '. ' . $this->res['message'], 'default', array('class' => 'alert-message error'));
            /**
            * @todo show possible errors and solutions
            */
        }
    }

	/**
	 * Generic action that adds a new item
	 *
	 * @return void
	 */
	function profile_add() {
		if(!isset($this->redirectURLHandler))
			$this->redirectURLHandler = array('profile' => true, 'action' => 'index');
		$this->admin_add();
	}
	
	/**
 	 * Remove for real! :) 
	 *
	 * @return void
	 */
	/*
		TODO Use $this->request->pass[0], instead of $id
	*/
	function profile_delete($id = null) {
		$this->admin_delete($id);
	}
	
	/**
	 * Generic action that edits a new item
	 *
	 * @return void
	 */
	function profile_edit($id = null) {
		if(!isset($this->redirectURLHandler))
			$this->redirectURLHandler = array('profile' => true, 'action' => 'index');
		$this->admin_edit($id);
	}



	/**
	 * Define read conditions for edit form
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    protected function getReadConditions() {
        return array();
    }

	/**
	 * Define read joins for edit form
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    protected function getReadJoins() {
        return array();
    }


	/**
	 * Define which fields wil be shown on edit form
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function getReadFields() {
		if(isset($this->findReadFields)){
			$fields = array();
			foreach($this->findReadFields as $key => $field){
                $fields[] = $field['field'];
			}
		} else {
			$fields = array(Inflector::singularize($this->name) . '.*');
		}
		return $fields;
	}
	
	
	/**
	 * Default handler that reads a item for edit form
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function defaultReadHandler(){
		// $this->{$this->name}->recursive = 1; // Is it necessary!?
		$options['conditions'] = $this->getReadConditions(); 
		$options['conditions'][Inflector::singularize($this->name) . '.id'] = $this->request->pass[0];
		$this->checkOwner($options);
		$options['joins'] = $this->getReadJoins(); 
		$options['fields'] = $this->getReadFields();
		$this->request->data = $this->{Inflector::singularize($this->name)}->find('first', $options);

        if(!$this->request->data){
			return array('success' => false, 'message' => __('Invalid ID'));
		} else {
			return array('success' => true);
		}
	}
	

	/**
	 * Generic action that edits a new item
	 *
	 * @return void
	 */
	function admin_edit($id = null) {

		$this->set('titleP', $this->titleP);
        $this->set('title', $this->title);

		// Handlers to be trigged with or without data
        foreach ($this->preSaveHandlers as $item) {
	        $res = $this->{$item['handler']}();
	        if($res['success'] != true)
	            break;
        }

		// Save action
		if ($this->request->is('post') || $this->request->is('put')) {

			$res['success'] = true;

			// Handlers to be trigged IF DATA IS NOT EMPTY
			foreach ($this->saveHandlers as $item) {
	            $res = $this->{$item['handler']}();
	            if($res['success'] != true)
                    break;
	        }

            if($res['success'] == true){
				$this->Session->setFlash(__("Changes has been successfully saved.", $this->title), 'default', array('class' => 'alert-message success'));
				if(empty($this->redirectURLHandler)){
					$this->redirect(array('action' => 'index'));
				} else {
					$this->redirect($this->redirectURLHandler);
				}
            } else {
                $this->Session->setFlash(__('Error while saving',true) . ' ' . $this->title . '<br>' . $res['message'], 'default', array('class' => 'alert-message error'));
            }

		// Read action
        } else {
	        foreach ($this->readHandlers as $item) {
		        $res = $this->{$item['handler']}();
		        if($res['success'] != true)
		            break;
	        }
			if($res['success'] != true){
                $this->Session->setFlash(__('Error reading item',true) . ' ' . $this->title . '<br>' . $res['message'], 'default', array('class' => 'alert-message error'));
				if(empty($this->redirectURLHandler)){
					$this->redirect(array('action' => 'index'));
				} else {
					$this->redirect($this->redirectURLHandler);
				}
            }
		}
	}
	
	/**
	 * Remove for real! :) 
	 *
	 * @param int $id 
	 * @return void
	 */
	function admin_delete($id = null) {

		$res['success'] = true;
		foreach ($this->deleteHandlers as $item) {
            $res = $this->{$item['handler']}();
            if($res['success'] != true)
                break;
        }

		if(empty($this->redirectURLHandler)){
			$redUrl = DS . $this->request->prefix . DS . $this->params['controller']  . DS . 'index';
		} else {
			$redUrl = $this->redirectURLHandler;
		}

        if($res['success'] == true){
			/*
				TODO handlers
			*/
			// Id can not be null or empty
			if (empty($id)) {
			    $this->Session->setFlash(__('ID can not be null.'), 'default', array('class' => 'alert-message error'));
			    $this->redirect($redUrl);
			}

			// Check if item exist before try to delete
			/*
				TODO Is it necessary? Just try to delete and check error!
			*/
			// $this->{Inflector::singularize($this->name)}->id = $id;
			// 	        if (!$this->{Inflector::singularize($this->name)}->exists()) {
			$options = array();
			$options['conditions'][Inflector::singularize($this->name) . '.id'] = $this->request->pass[0];			
			$this->checkOwner($options);
	        if (!$this->{Inflector::singularize($this->name)}->find('first', $options)) {
				//throw new NotFoundException(__('Invalid user'));
				$this->Session->setFlash(__('Invalid %s.', Inflector::singularize($this->name)));
				$this->redirect($redUrl);
	        }

			// Perform deletion
			/*
				TODO Make cascade option customizable on deleting
			*/
			if ($this->{Inflector::singularize($this->name)}->delete($id)) {
			    $this->Session->setFlash(__('Item has been successfully deleted.'), 'default', array('class' => 'alert-message success'));
			    $this->redirect($redUrl);
			}

			// Ops! Error!
		    $this->Session->setFlash(__('Item was no deleted. Error while deleting item.'), 'default', array('class' => 'alert-message error'));

        } else {
            $this->Session->setFlash(__('Error while deleting',true) . ' ' . $this->title . '<br>' . $res['message'], 'default', array('class' => 'alert-message error'));
        }
		/*
			TODO Show errors
		*/
	    $this->redirect($redUrl);

	}
}