<?php
App::uses('RegistrationAppController', 'Registration.Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Broadcasts
 *
 * @author Thiago Colares
 */
class BroadcastsController extends RegistrationAppController {
	
	public $uses = array('Registration.Registration', 'Registration.Broadcast', 'User');
	
    public function beforeFilter() {
        parent::beforeFilter();

		$this->title  = __('Broadcast');
        $this->titleP = __('Broadcasts');

        //$this->Auth->allow('*');

    }
	
	/**
	 * Build checkbox options 
	 *
	 * @author Thiago Colares
	 */
	private function _getSendOptions(){
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
			$formOptions = array();
			foreach($registrations as $registration){
				$formOptions[$registration['PaymentStatus']['id']] = $registration['PaymentStatus']['name'] . " (" . $registration[0]['registrations'] . ")" . (!empty($registration['PaymentEntity']['name']) ? ', ' . $registration['PaymentEntity']['name'] : '');
				
			}

			return $formOptions;
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $filter like N_M. N => old_invite_status; M => current_invite_status. * means disregard 
	 * @return void
	 * @author Thiago Colares
	 */
	private function _getUsers($paymentStatusId){
		
		$options['recursive'] = -1;
		$options['joins'] = array(
            array(
				'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
					'User.id = Registration.user_id',
                )
            ),
            array(
				'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
					'Profile.id = User.id',
                )
            ),
        );

		$options['fields'] = 'User.id, User.email, Profile.name';
		$options['conditions']['Registration.payment_status_id'] = $paymentStatusId;

		return $this->Registration->find('all', $options);
	}


	/**
	 * Replace tokens
	 *
	 * @author Thiago Colares
	 */
	private function _replaceTokens($reg){
		$tokens = array(
			'___NAME___' => array('model' => 'Profile', 'field' => 'name')
		);
		
		foreach($tokens as $token => $value){
			//debug('/\\' . $token . '/');
			$this->request->data['Broadcast']['message'] = preg_replace('/\\' . $token . '/', $reg[$value['model']] [$value['field']], $this->request->data['Broadcast']['message']);
		}
		return $this->request->data['Broadcast']['message'];
		
	}


	/**
	 * Sends an email
	 *
	 * @param string $reg Registrated User 
	 * @return void
	 * @author Thiago Colares
	 */
	private function _sendEmail($reg){
		// debug($this->request->data);
		// debug($reg); die();
		$email = new CakeEmail();
		
		// Replace this->request->data[Broadcast][message]
		$this->_replaceTokens($reg);
		
		$email->viewVars(array(
			'message' => $this->request->data['Broadcast']['message']
		));
		
		$res = $email->template('Registration.broadcast/message','default')
		    ->emailFormat('html')
			->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
			->to($reg['User']['email'])
			->subject('[' . Configure::read('EventTitle') . '] ' .$this->request->data['Broadcast']['subject']) 
			->send();
	}
    
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function admin_index(){
		$formOptions = $this->_getSendOptions();
		$this->set(compact('formOptions'));
		// Preview Broadcast Message before send it
		if ($this->request->is('post') || $this->request->is('put')) {
			// Validate before
			$this->Broadcast->set($this->request->data);
			if ($this->Broadcast->validates()) {
				if(
					$this->request->data['Broadcast']['send'] == true &&
					$this->request->data['Broadcast']['edit'] == false
				){
					
					// Send emails				
					$registrations = $this->_getUsers($this->request->data['Broadcast']['filter']);

					$i = 0;

					if(!empty($registrations)){
						$i++;
						foreach($registrations as $reg){
							$this->_sendEmail($reg);
						}
						$this->Session->setFlash(__('%d e-mails has been sent.', $i), 'default', array('class' => 'alert-message success'));
					} else {
						$this->Session->setFlash(__('No one message has been sent.'), 'default', array('class' => 'alert-message error'));
					}

					$this->request->data = array();
				} else if(
					$this->request->data['Broadcast']['send'] == false &&
					$this->request->data['Broadcast']['edit'] == true
				){
					// Edit message				
					// notheing here, until now
				} else {
					// Confirm page
					$this->request->data['Broadcast']['filter_name'] = $formOptions[$this->request->data['Broadcast']['filter']];
					$this->view = 'admin_preview';
				}
			} else { // if NOT validates
				$this->Session->setFlash(__("Check for validation errors."), 'default', array('class' => 'alert-message error'));				
			}// if validates
		} // if post || put
	}

}