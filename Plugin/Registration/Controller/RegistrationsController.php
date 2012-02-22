<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('RegistrationAppController', 'Registration.Controller');
/**
 * Registration Controller
 *
 * PHP version 5
 */
class RegistrationsController extends RegistrationAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Registrations';

	public $entity = "Registration";
    
    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = array('Registration.Registration','Registration.RegistrationMainVariation', 'Role', 'Registration.PaymentMethod');
    
    /**
     * Paginate configuration
     *
     * @var array
     * @access public
     */
    var $paginate = array(
        /**
        * @todo turn into a enviroment variable!
        */
        'limit' => 25, 
        //'contain'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->title  = __('Registration');
        $this->titleP = __('Registrations');
        
        $this->Auth->allow('index'
			//'updateRegistrationCode'
		);
        
        $this->preSaveHandlers = array(
            array('handler' => 'setAdminRegistrationTypesHandler'),
            array('handler' => 'setAdminPaymentMethodsHandler'),
            array('handler' => 'setAdminPaymentStatusesHandler'),
        );
        
        $this->saveHandlers = array(
            array('handler' => 'addRoleHandler'),
            array('handler' => 'addAdminPaymentHandler'),
            array('handler' => 'finalSaveHandler'),
            array('handler' => 'saveRegistrationItemHandler'),
            array('handler' => 'sendWelcomeConsolidationLinkHandler'),
        );
        
        $this->readHandlers = array(
            array('handler' => 'checkPagSeguroPaymentHandler'),
            array('handler' => 'defaultReadHandler')
        );
        
        $this->findReadFields = array(
            array('field' => 'User.*'), 
            array('field' => 'Profile.*'), 
            array('field' => 'Registration.*'),
            array('field' => 'RegistrationItem.*')
        );
        
//        $this->saveHandlers = array(
//		    array('handler' => 'setStatusHandler'),
//	        array('handler' => 'finalSaveHandler')	
//		);
        
        $this->findFields = array(
			array('field' => 'Registration.id', 'title' => __('ID'), 'hide' => true),
            array('field' => 'User.id', 'title' => __('ID'), 'hide' => true),
			array('field' => 'Profile.name', 'title' => __('Name')),
			array('field' => 'Profile.mobile_ddd', 'title' => __('DDD')),
			array('field' => 'Profile.mobile', 'title' => __('Mobile')),
			array('field' => 'User.email', 'title' => __('E-Mail')),
            array('field' => 'Profile.main_doc', 'title' => __('Document')),
            array('field' => 'PaymentEntity.name', 'title' => __('Payment Entity')),
            array('field' => 'PaymentMethod.name', 'title' => __('Payment Method')),
            array('field' => 'PaymentStatus.name', 'title' => __('Payment Status'))
        );
        
        $this->Registration->recursive = -1;
    }
    
    protected function checkPagSeguroPaymentHandler(){
        $registrationId = $this->request->pass[0];
        
        $options['joins'] = array(
            array(
                'table' => 'payment_statuses',
                'alias' => 'PaymentStatus',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.payment_status_id = PaymentStatus.id'
                )
            ),
        );
        
        $options['conditions']['Registration.payment_entity_id'] = 2;
        $options['conditions']['Registration.user_id'] = $registrationId;
        $options['recursive'] = -1;
        $options['fields'] = array('PaymentStatus.*');
        
        $registration = $this->Registration->find('first', $options);
        
        if($registration)
            $this->set('registration', $registration);
        
        return array('success' => true);
    }
    
    protected function setAdminPaymentMethodsHandler(){
        $options['joins'] = array(
            array(
                'table' => 'payment_entity_payment_methods',
                'alias' => 'PaymentEntityPaymentMethod',
                'type' => 'INNER',
                'conditions' => array(
                    'PaymentEntityPaymentMethod.payment_method_id = PaymentMethod.id'
                )
            )
        );
        
        //Organização do evento
        $options['conditions']['PaymentEntityPaymentMethod.payment_entity_id'] = 1;
        $options['fields'] = array('PaymentEntityPaymentMethod.id', 'PaymentMethod.name');
        
        $items = $this->PaymentMethod->find('all', $options);
        $res = array();
        foreach($items as $item){
            $res[$item['PaymentEntityPaymentMethod']['id']] = $item['PaymentMethod']['name'];
        }
        
        $this->set('paymentMethods', $res);
        
        return array('success' => true);
    }
    
    protected function setAdminPaymentStatusesHandler(){        
        //Organização do evento
        $options['conditions']['PaymentStatus.payment_entity_id'] = 1;
        
        $res = $this->Registration->PaymentStatus->find('list', $options);
        
        $this->set('paymentStatuses', $res);
        
        return array('success' => true);
    }
    
    protected function getPaginateJoins() {
        $defaultJoins = parent::getPaginateJoins();
        
        $joins = array(
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.user_id = Profile.id'
                )
            ),
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.user_id = User.id'
                )
            ),
            array(
                'table' => 'payment_entities',
                'alias' => 'PaymentEntity',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.payment_entity_id = PaymentEntity.id'
                )
            ),
            array(
                'table' => 'payment_statuses',
                'alias' => 'PaymentStatus',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.payment_status_id = PaymentStatus.id'
                )
            ),
            array(
                'table' => 'payment_entity_payment_methods',
                'alias' => 'PaymentEntityPaymentMethod',
                'type' => 'LEFT',
                'conditions' => array(
                    'Registration.payment_entity_payment_method_id = PaymentEntityPaymentMethod.id'
                )
            ),
            array(
                'table' => 'payment_methods',
                'alias' => 'PaymentMethod',
                'type' => 'LEFT',
                'conditions' => array(
                    'PaymentEntityPaymentMethod.payment_method_id = PaymentMethod.id'
                )
            ),
        );
        
        return array_merge($defaultJoins, $joins);
    }
    
    protected function setRegistrationTypesHandler(){
        $options['joins'] = array(
            array(
                'table' => 'registration_types',
                'alias' => 'RegistrationType',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationMainVariation.registration_type_id = RegistrationType.id'
                )
            ),
            array(
                'table' => 'registration_periods',
                'alias' => 'RegistrationPeriod',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationMainVariation.registration_period_id = RegistrationPeriod.id'
                )
            )
        );
        
        $now = date("Y-m-d H:i:s");
        
        $options['conditions']['RegistrationPeriod.begin_date <'] = $now;
        $options['conditions']['RegistrationPeriod.end_date >='] = $now;
        
        $options['conditions']['RegistrationType.off_site'] = 0;
        
        $options['fields'] = array('RegistrationMainVariation.id', 'RegistrationMainVariation.price', 'RegistrationType.name', 'RegistrationPeriod.end_date');
        
        $items = $this->RegistrationMainVariation->find('all', $options);

        $res = array();
        
        foreach($items as $item){
            $key = $item['RegistrationMainVariation']['id'];
			// There is a cakephp helper called: http://book.cakephp.org/2.0/en/core-libraries/helpers/number.html
			// it may be usefull in view
            $value = $item['RegistrationType']['name'].' <span class="label warning" style="font-size:80%; font-weight:normal; background-color: #46A546">R$ '.number_format($item['RegistrationMainVariation']['price'], 2, ',', '.') . '</span>';
            
            $res[$key] = $value;
        }
        
        $this->set('main_subscriptions_array', $res);
        $this->set('last_day', date('d/m/Y \à\s H:i', strtotime($items[0]['RegistrationPeriod']['end_date'])));
        
        return array('success' => true);
    }
    
    protected function setAdminRegistrationTypesHandler(){
        $options['joins'] = array(
            array(
                'table' => 'registration_types',
                'alias' => 'RegistrationType',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationMainVariation.registration_type_id = RegistrationType.id'
                )
            ),
            array(
                'table' => 'registration_periods',
                'alias' => 'RegistrationPeriod',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationMainVariation.registration_period_id = RegistrationPeriod.id'
                )
            )
        );
        
        $options['fields'] = array('RegistrationMainVariation.id', 'RegistrationMainVariation.price', 'RegistrationType.name', 'RegistrationPeriod.end_date', 'RegistrationPeriod.begin_date');
        
        $options['order'] = array('RegistrationPeriod.begin_date DESC');
        
        $items = $this->RegistrationMainVariation->find('all', $options);

        $res = array();
        
        foreach($items as $item){
            $key = $item['RegistrationMainVariation']['id'];
			// There is a cakephp helper called: http://book.cakephp.org/2.0/en/core-libraries/helpers/number.html
			// it may be usefull in view
            $value = $item['RegistrationType']['name'].' - R$ '.number_format($item['RegistrationMainVariation']['price'], 2, ',', '.');
            $endDate = date('d/m/Y \à\s H:i', strtotime($item['RegistrationPeriod']['end_date']));
            $beginDate = date('d/m/Y \à\s H:i', strtotime($item['RegistrationPeriod']['begin_date']));
            
            $period = $beginDate.__(' to ').$endDate;
            
            $res[$period][$key] = $value;
        }
        
        $this->set('main_subscriptions_array', $res);
        //$this->set('last_day', date('d/m/Y \à\s H:i', strtotime($items[0]['RegistrationPeriod']['end_date'])));
        
        return array('success' => true);
    }
    
	/**
	 * Before load form, check if the user is alreayd logged in
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function isAlreadyLoggedin(){
		if ($this->Session->read('Auth.User')) {
            $role = $this->Role->read(null, $this->Auth->user('role_id'));
            CakeSession::write('Auth.User.role', $role['Role']['alias']);

            $url = null;
            switch ($role['Role']['alias']) {
                case 'admin':
                case 'manager':
					$this->Session->setFlash(__('If you pretend to register an other person, user "Add Register (Consolidate)" at Administrative Panel.'), 'default', array('class' => 'notice'));
                    $url = array('plugin' => false, 'controller' => 'systems', 'action' => 'dashboard', 'prefix' => 'admin', 'admin' => true);
                    break;
                case 'registered':
					$this->Session->setFlash(__('You has already registered!'), 'default', array('class' => 'notice'));
                    $url = array('plugin' => false, 'controller' => 'systems', 'action' => 'dashboard', 'prefix' => 'profile', 'profile' => true);
                    break;
                default:
                    $url = $this->Auth->redirect();
                    break;
            }
            $this->redirect($url);
        }
		return array('success' => true);
	}
	
	

    public function index(){
        $this->preSaveHandlers = array(
			array('handler' => 'isAlreadyLoggedin'),
            array('handler' => 'setRegistrationTypesHandler')
        );
        
        $this->saveHandlers = array(
            array('handler' => 'addRoleHandler'),
            array('handler' => 'addPaymentHandler'),
			array('handler' => 'checkRegistrationMainvariation'),
            array('handler' => 'finalSaveHandler'),
            array('handler' => 'saveRegistrationItemHandler'),
			array('handler' => 'sendWelcomeEmailHandler'),
            array('handler' => 'loginAndRedirectHandler')
        );
        
        $this->readHandlers = array(
            array('handler' => 'defaultReadHandler')
        );
        
        $this->redirectURLHandler = '/';
        
        $this->admin_add();
    }
    
	/**
	 * Before save or validate, check is registration has been set!
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	/*
		TODO I should be done at model, but It has not worked... I don't know why
	*/
	protected function checkRegistrationMainvariation(){
		//$this->Registration->RegistrationItem->set( $this->request->data );
		//if ($this->Registration->RegistrationItem->validates()) {
		if(
			!isset($this->request->data['RegistrationItem']['registration_main_variation_id'])
			||
			empty($this->request->data['RegistrationItem']['registration_main_variation_id'])
		){
			$this->Registration->RegistrationItem->validationErrors['registration_main_variation_id'] = __('You must choose registration option.');
			return array('success' => false, 'message' => __("Check for validation errors."));		
		} else {
			return array('success' => true);
		}
	}



    protected function beginTransactionHandler(){
        $this->dataSource = $this->Registration->getDataSource();
        $this->dataSource->begin($this->Registration);
    }
    
    protected function commitTransactionHandler(){
        $this->dataSource->commit($this->Registration);
    }
    
    protected function saveRegistrationItemHandler(){
        $registration_id = $this->Registration->id;
        
        $this->request->data['RegistrationItem']['registration_id'] = $registration_id;
        
        $res = $this->Registration->RegistrationItem->save($this->request->data);
        
        if($res) return array('success' => true);
        else return array('success' => true);
    }

	/**
	 * Default payment settings
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function addPaymentHandler(){
		$this->request->data['Registration'][0]['payment_entity_id'] = 2; // PagSeguro
		//$this->request->data['Registration'][0]['payment_entity_payment_methods'] = 0;
		$this->request->data['Registration'][0]['payment_status_id'] = 9; // not initiated
        return array('success' => true);
    }
    
    protected function addAdminPaymentHandler(){
        if(!empty($this->request->data['Registration'][0]['payment_entity_payment_method_id']) && !empty($this->request->data['Registration'][0]['payment_status_id'])){
            $this->request->data['Registration'][0]['payment_entity_id'] = 1; // Organização do evento
        }else{
            unset($this->request->data['Registration'][0]['payment_entity_payment_method_id']);
            unset($this->request->data['Registration'][0]['payment_status_id']);
        }
            

        return array('success' => true);
    }
    

    protected function addRoleHandler(){
        $options['conditions']['Role.alias'] = 'registered';
        $role = $this->Role->find('first', $options);
        $this->request->data['User']['role_id'] = $role['Role']['id'];
        
        return array('success' => true);
    }


    /**
     * Finally perform a save on database. Final handler
     */
    protected function finalSaveHandler(){
        $res = $this->_save($this->Registration->User);
        return $res;
    }
    
    protected function getReadJoins() {
        $defaultJoins = parent::getReadJoins();
        
        $joins = array(
            array(
                'table' => 'registrations',
                'alias' => 'Registration',
                'type' => 'INNER',
                'conditions' => array(
                    'User.id = Registration.user_id'
                )
            ),
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.user_id = Profile.id'
                )
            ),
            array(
                'table' => 'registration_items',
                'alias' => 'RegistrationItem',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.id = RegistrationItem.registration_id'
                )
            )
        );
        
        return array_merge($defaultJoins, $joins);
    }


    protected function defaultReadHandler(){
		// $this->{$this->name}->recursive = 1; // Is it necessary!?
		$options['conditions'] = $this->getReadConditions(); 
		$options['conditions']['User.id'] = $this->request->pass[0];
		$options['joins'] = $this->getReadJoins(); 
		$options['fields'] = $this->getReadFields();
        $options['recursive'] = -1;
        
		$this->request->data = $this->Registration->User->find('first', $options);
        
        $regData = $this->request->data['Registration'];
        unset($this->request->data['Registration']);
        unset($this->request->data['User']['password']);
        
        $this->request->data['Registration'][] = $regData;
        
        if(!$this->request->data){
			return array('success' => false, 'message' => __('Invalid ID'));
		} else {
			return array('success' => true);
		}
	}
    
    protected function loginAndRedirectHandler(){
        $this->Session->setFlash(
            // __("Registration was successful. Wait for instructions to make payment and secure your entrance.") . '<br>' .  
            'Bem vindo ou Bem vinda! Esperamos ver você muito em breve no <strong>VII Fórum Brasileiro de Educação Ambiental</strong>!' . '<br>' .  
            __("Registration was successful. <strong>Now you must to make the payment to ensure your entry.</strong>"),
            'default', array('class' => 'alert-message success')
        );
        $options['conditions']['User.id'] = $this->Registration->User->getInsertID();
        $options['recursive'] = -1;

		// Update ref_code
		$this->Registration->id = $this->Registration->getInsertID();
		$this->request->data['Registration']['ref_code'] = $this->_refCode($this->Registration->getInsertID(),$this->Registration->User->getInsertID());
		$this->Registration->save($this->data);

        $user = $this->Registration->User->find('first', $options);
        $this->Auth->login($user['User']);
        $this->redirect('/profile/payments');
    }    

	/**
	 * Welcome e-mail to user
	 *
	 */
    protected function sendWelcomeEmailHandler(){
		$email = new CakeEmail();

		$email->viewVars(array(
			'name' => $this->request->data['Profile']['name']
		));

		$res = $email->template('Registration.registration/welcome','default')
		    ->emailFormat('html')
			->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
			->to($this->request->data['User']['email'])
			->subject('[' . Configure::read('EventTitle') . '] ' . __('Welcome! See Instructions to Complete Your Registration') ) 
			->send();

		return array('success' => true);
    }


	/**
	 * Send new password link to user added by consolidation
	 *
	 */
    protected function sendWelcomeConsolidationLinkHandler(){
        if($this->request->data['User']['id']){
            return array('success' => true);
        }
		// Preparing data

		// Expires in two days
		$expiration_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+2, date("Y")));

		$token = Security::hash(microtime(), null, true);

		$email = $this->request->data['User']['email'];

		// Update data
		$this->request->data['User']['expiration_date'] = $expiration_date;
		$this->request->data['User']['activation_key'] = $token;
	
		$email = new CakeEmail();

		$email->viewVars(array(
			'name' => $this->request->data['Profile']['name'],
			'email' => $this->request->data['User']['email'],
			'activation_key' => $this->request->data['User']['activation_key']
		));

		$res = $email->template('Registration.registration/welcome_consolidation','default')
		    ->emailFormat('html')
			->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
			->to($this->request->data['User']['email'])
			->subject('[' . Configure::read('EventTitle') . '] ' . __('Welcome! See Instructions to Complete Your Registration') ) 
			->send();

		return array('success' => true);
    }


	/**
	 * Return how many registrations per entity and its status
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function admin_paymentStatusReport(){
		$options = array();
		$options['recursive'] = -1;
		$options['joins'] = array(
            array(
                'table' => 'payment_statuses',
                'alias' => 'PaymentStatus',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.payment_status_id = PaymentStatus.id'
                )
            ),
            array(
                'table' => 'payment_entities',
                'alias' => 'PaymentEntity',
                'type' => 'INNER',
                'conditions' => array(
                    'PaymentStatus.payment_entity_id = PaymentEntity.id'
                )
            ),
        );
		$options['group'] = 'Registration.payment_status_id';
		$options['conditions'] = 'Registration.status';
        $options['fields'] = 'PaymentStatus.id,PaymentStatus.name, PaymentEntity.id,PaymentEntity.name, COUNT(*) as registrations';

		$registrations = $this->Registration->find('all', $options);
		$tmp = array();
		foreach($registrations as $registration){
			$tmp[$registration['PaymentEntity']['id']]['name'] = $registration['PaymentEntity']['name'];
			$tmp[$registration['PaymentEntity']['id']]['status'][] = array(
				'status' => $registration['PaymentStatus']['name'],
				'registrations' => $registration[0]['registrations']
			);
		}
		return $tmp;
	}
	
	
	/**
	 * Generetates a Referation Code to Registration register
	 *
	 * @return string
	 * @author Thiago Colares
	 */
	private function _refCode($userId, $registrationId){
		return
		//strtoupper(Security::hash(Configure::read('EventTitle'), 'CRC32', true)) . '-' .  // Nome do Evento
		strtoupper(Security::hash($userId . $registrationId . time(),'CRC32', true)) . sprintf("%07s", $registrationId); // User ID
	}
	
	
	/**
	 * Update reference code to all registrations
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function updateRegistrationCode($token = null){
		if($token === 'H4d8v5t1u7O8fDgZQIY43nK24dc3gQ18vvmp6p2D2eM7qi516Ki7o7o07hPEU6ewu8YJ266b2eBO8Dc214K9rt3J5s'){
			$this->Registration->recursive = -1;
			$registrations = $this->Registration->find('all');
			foreach($registrations as $reg){
				$this->Registration->id = $reg['Registration']['id'];
				$this->request->data['Registration']['id'] = $reg['Registration']['id'];
				$this->request->data['Registration']['ref_code'] = $this->_refCode($reg['Registration']['id'],$reg['Registration']['user_id']);
				// debug($this->request->data);
				$this->Registration->save($this->data);
			}
		}
	}

}
