<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('RegistrationAppController', 'Registration.Controller');

App::import('Lib', 'PagSeguroLibrary/PagSeguroLibrary');

/**
 * Payment stuffs + PagSeguro
 *
 * @package default
 * @author Thiago Colares
 */
class PaymentsController extends RegistrationAppController {

    public $name = 'Payments';
    public $uses = array('User', 'Registration.Registration', 'Registration.RegistrationMainVariation', 'Registration.PaymentLog');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('payment_pagseguro_notification', 'updateFromPagSeguro','fixRegistrationsFromPagSeguro', 'fixPaymentLogFromPagSeguro', 'sendBackdatedPaymentReceipt');
	}

    private function _getRegistrationInfo($registrationId) {
        $options['recursive'] = -1;
        $options['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.user_id = User.id'
                )
            ),
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'User.id = Profile.id'
                )
            ),
        );

        $options['fields'] = array('Registration.*', 'User.*', 'Profile.*');
        $options['conditions']['Registration.id'] = $registrationId;

        $res = $this->Registration->find('first', $options);


        $options = array();
        $options['joins'] = array(
            array(
                'table' => 'registration_main_variations',
                'alias' => 'RegistrationMainVariation',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationItem.registration_main_variation_id = RegistrationMainVariation.id'
                )
            ),
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


        $options['fields'] = array('RegistrationItem.*', 'RegistrationMainVariation.*', 'RegistrationType.*', 'RegistrationPeriod.end_date');
        $options['conditions']['RegistrationItem.registration_id'] = $registrationId;

        $items = $this->Registration->RegistrationItem->find('all', $options);
        
        $now = strtotime(date('Y-m-d H:i:s'));
        $this->temp['canPay'] = true;
        
        foreach($items as &$item){
            //$begin_date = strtotime($item['RegistrationPeriod']['begin_date']);
            $end_date = strtotime($item['RegistrationPeriod']['end_date']);
            if(($res['Registration']['payment_status_id'] == 9 || $res['Registration']['payment_status_id'] == 7) && $now > $end_date)
                $this->_updateMainRegistration($res['Registration']['id'], $item);
        }
        
        if($this->temp['canPay']){
            $res['RegistrationItems'] = $items;

            return $res;
        }else{
            $this->redirect(array('action' => 'index', 'profile' => true));
        }
    }
    
    private $temp;
    
    private function _updateMainRegistration($registrationId, &$registrationItem){
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
        
        $now = date('Y-m-d H:i:s');
        
        
        $options['conditions']['RegistrationPeriod.begin_date <'] = $now;
        $options['conditions']['RegistrationPeriod.end_date >='] = $now;
        $options['conditions']['RegistrationType.id'] = $registrationItem['RegistrationType']['id'];
        
        $options['conditions']['RegistrationType.off_site'] = 0;
        
        $options['fields'] = array('RegistrationMainVariation.*');
        
        $item = $this->RegistrationMainVariation->find('first', $options);
        
        if($item){
            $data['RegistrationItem']['id'] = $registrationItem['RegistrationItem']['id'];
            
            $data['RegistrationItem']['registration_main_variation_id'] = $item['RegistrationMainVariation']['id'];
            
            $this->Registration->RegistrationItem->save($data);
            
            $registrationItem['RegistrationMainVariation'] = $item['RegistrationMainVariation'];
            $registrationItem['RegistrationItem']['registration_main_variation_id'] = $item['RegistrationMainVariation']['id'];
        }else{
            unset($options['conditions']['RegistrationPeriod.begin_date <']);
            unset($options['conditions']['RegistrationPeriod.end_date >=']);
            
            $options['conditions']['RegistrationPeriod.begin_date >'] = $now;
            $options['order'] = array('RegistrationPeriod.begin_date ASC');
            
            $item = $this->RegistrationMainVariation->find('first', $options);
            
            if($item){
                $this->set('nextRegistrationPeriod', $item);
            }else{
                $this->set('nextRegistrationPeriod', false);
            }
            
            $this->temp['canPay'] = false;
        }
        
        
    }
    
    private function _updatePaymentEntityPagSeguro($registrationId){
        $data['Registration']['id'] = $registrationId;
        $data['Registration']['payment_entity_id'] = 2;
        $data['Registration']['payment_entity_payment_methods'] = null;
        $data['Registration']['payment_status_id'] = 9;
        
        $this->Registration->save($data);
    }

    function payment_pagseguro_checkout($registrationId = null) {

        $registrationInfo = $this->_getRegistrationInfo($registrationId);
        $this->_updatePaymentEntityPagSeguro($registrationId);

        $paymentRequest = new PaymentRequest();

        $paymentRequest->setCurrency('BRL');

        $paymentRequest->setReference($registrationInfo['Registration']['id']);

        /**
         * Informo os dados do Cliente
         * Nome:
         * Email:
         * DDD:
         * Telefone : (valor numerico , exemplo: 6998522)
         */
        $paymentRequest->setSender(
			//utf8_decode($registrationInfo['Profile']['name']),
			Inflector::slug($registrationInfo['Profile']['name'], ' '),
			$registrationInfo['User']['email'],
			$registrationInfo['Profile']['mobile_ddd'],
			$registrationInfo['Profile']['mobile']
		);

        /**
         * Informo as informações do endereço do cliente
         * CEP
         * Rua
         * Numero
         * Complemento
         * Bairro
         * Cidade
         * Estado
         * Pais
         */
        $paymentRequest->setShippingAddress(
                $registrationInfo['Profile']['zipcode'], 
                utf8_decode($registrationInfo['Profile']['address']), 
                $registrationInfo['Profile']['address_number'], 
                utf8_decode($registrationInfo['Profile']['address_complement']), 
                $registrationInfo['Profile']['neighborhood'], 
                $registrationInfo['Profile']['city'], 
                $registrationInfo['Profile']['state'], 
                'BRA'
        );

        /**
         * Obrigatório especificar o tipo do frete
         */
        // Criando o tipo de frete
        $ShippingType = new ShippingType();

        // Definindo tipo de frete 'NOT_SPECIFIED'
        $ShippingType->setByType('NOT_SPECIFIED');

        $shipping = new Shipping();
        $shipping->setType($ShippingType);

        /* $paymentRequest deve ser um objeto do tipo PaymentRequest */
        $paymentRequest->setShipping(1);

        foreach ($registrationInfo['RegistrationItems'] as $item) {
            /**
             * Agora vamos adicionar os produtos.
             * Algo importante é relacionado ao peso do produto.
             * Esse valor terá que ser inteiro. Então 0,300 será 300.
             * As informações a serem adiciona no metodo addItem
             * ID
             * Produto
             * Quantidade
             * Valor
             * Peso
             * Valor do frete
             */
            // Existe um limite de caracteres. Por isto, reduzi
            $product = utf8_decode('Inscrição no Evento ' . Configure::read('EventTitle'));
            $paymentRequest->addItem(
                    $item['RegistrationItem']['id'], $product, 1, $item['RegistrationMainVariation']['price'], 0, 0
            );
        }

		/**
		 * Definindo uma configuração
		 *
		 */

		// Setando credenciais e charset
		$PagSeguroConfig['credentials'] = Array();  
		$PagSeguroConfig['credentials']['email'] = Configure::read('pagseguro_email');  
		$PagSeguroConfig['credentials']['token'] = Configure::read('pagseguro_token');
		
		// $PagSeguroConfig['application'] = Array();
		// $PagSeguroConfig['application']['charset'] = 'ISO-8859-1';   
		// 
		// $this->setCharset(PagSeguroConfig::getApplicationCharset());

        /**
         * Iremos agora utilizar a classe AccountCredentials para adicionar as nossas credencias
         * Quer seria o email cadastrado no pagseguro, e TOKEN gerado no pagseguro
         */
        $credenciais = new AccountCredentials(
            Configure::read('pagseguro_email'),
            Configure::read('pagseguro_token')
        );

        /**
         * Agora vamos adicionar as credenciais informada na classe AccountCredentials
         * Com isso será gerado uma URL para o pagseguro
         *
         */
        $url = $paymentRequest->register($credenciais);

        //Agora vamos redirecionar para o PagSeguro
        $this->redirect($url);
    }

    function payment_pagseguro_notification() {
        /* Informando as credenciais  */
        $credentials = new AccountCredentials(
                        Configure::read('pagseguro_email'),
                        Configure::read('pagseguro_token')
        );

        /* Tipo de notificação recebida */
        $type = $_POST['notificationType'];

        /* Código da notificação recebida */
        $code = $_POST['notificationCode'];

        /* Verificando tipo de notificação recebida */
        if ($type === 'transaction') {

            /* Obtendo o objeto Transaction a partir do código de notificação */
            $transaction = NotificationService::checkTransaction(
                            $credentials, $code // código de notificação  
            );

            $reference = $transaction->getReference(); //IdRegistration
            $date = $transaction->getDate();
            $lastEventDate = $transaction->getLastEventDate();
            
            $status = $transaction->getStatus();
            $status = $this->_getPaymentStatus($status->getValue(), 2); //2 - PagSeguro            
            
            
            $paymentMethod = $transaction->getPaymentMethod();
            $type = $paymentMethod->getType();
            $method = $this->_getPaymentMethod($type->getValue(), 2); //2 - PagSeguro

            
            $data['Registration']['id'] = $reference;
            $data['Registration']['payment_status_id'] = $status;
            $data['Registration']['payment_entity_payment_method_id'] = $method;
            
            $data['PaymentLog'][0]['payment_entity_id'] = 2;
            $data['PaymentLog'][0]['payment_status_id'] = $status;
            $data['PaymentLog'][0]['payment_entity_payment_method_id'] = $method;
            $data['PaymentLog'][0]['transaction_date'] = $date;
            $data['PaymentLog'][0]['transaction_last_event_date'] = $lastEventDate;
            $data['PaymentLog'][0]['description'] = serialize($transaction);
            
            $this->Registration->saveAll($data);
        }
    }
    
    private function _getPaymentStatus($status, $paymentEntity){
        $options['conditions']['PaymentStatus.code'] = $status;
        $options['conditions']['PaymentStatus.payment_entity_id'] = $paymentEntity;
        
        $res = $this->Registration->PaymentStatus->find('first', $options);
        
        return $res['PaymentStatus']['id'];
    }
    
    private function _getPaymentMethod($method, $paymentEntity){
        $options['conditions']['PaymentEntityPaymentMethod.code'] = $method;
        $options['conditions']['PaymentEntityPaymentMethod.payment_entity_id'] = $paymentEntity;
        
        $res = $this->Registration->PaymentEntityPaymentMethod->find('first', $options);
        
        return $res['PaymentEntityPaymentMethod']['id'];
    }


    /**
     * Get product data to set it as client
     * In this case, REGISTRATION
     *
     * @param string $user_id 
     * @return void
     * @author Thiago Colares
     */
    private function _getRegistrationData() {
        $options['recursive'] = -1;
        $options['joins'] = array(
            // PAYMENT --------------------
            // Payment Status
            array('table' => 'payment_statuses',
                'alias' => 'PaymentStatus',
                'type' => 'LEFT',
                'conditions' => array(
                    'PaymentStatus.id = Registration.payment_status_id',
                )
            ),
            // Payment Entity
            array('table' => 'payment_entities',
                'alias' => 'PaymentEntity',
                'type' => 'LEFT',
                'conditions' => array(
                    'PaymentEntity.id = Registration.payment_entity_id',
                )
            ),
            // Payment Entity Methods
            array('table' => 'payment_entity_payment_methods',
                'alias' => 'PaymentEntityPaymentMethod',
                'type' => 'LEFT',
                'conditions' => array(
                    'PaymentEntityPaymentMethod.id = Registration.payment_entity_payment_method_id',
                )
            ),
            // Payment Method
            array('table' => 'payment_methods',
                'alias' => 'PaymentMethod',
                'type' => 'LEFT',
                'conditions' => array(
                    'PaymentMethod.id = PaymentEntityPaymentMethod.payment_method_id',
                )
            ),
        );
        $options['conditions']['Registration.user_id'] = $this->Session->read('Auth.User.id');
        $options['conditions']['Registration.status'] = 1; // Active
        $options['fields'] = array(
            'Registration.id',
			'Registration.payment_entity_payment_method_id',
//            'RegistrationMainVariation.price',
//            'RegistrationType.name',
//            'RegistrationPeriod.end_date',
            // Payment Status
            'PaymentStatus.id',
            'PaymentStatus.name',
            'PaymentStatus.code',
            'PaymentStatus.payable',

            // Payment Entity
            'PaymentEntity.id',
            'PaymentEntity.name',
            'PaymentEntityPaymentMethod.id',
            // Payment Method
            'PaymentMethod.id',
            'PaymentMethod.name'
        );
        
        $this->request->data = $this->Registration->find('first', $options);
        
        $options = array();
        $options['recursive'] = -1;
        $options['joins'] = array(
            // REGISTRATION --------------------
            // Registration Main Variation
            array('table' => 'registration_main_variations',
                'alias' => 'RegistrationMainVariation',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationMainVariation.id = RegistrationItem.registration_main_variation_id',
                )
            ),
            // Registration Period
            array('table' => 'registration_periods',
                'alias' => 'RegistrationPeriod',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationPeriod.id = RegistrationMainVariation.registration_period_id',
                )
            ),
            // Registration Type
            array('table' => 'registration_types',
                'alias' => 'RegistrationType',
                'type' => 'INNER',
                'conditions' => array(
                    'RegistrationType.id = RegistrationMainVariation.registration_type_id',
                )
            ),
        );
        $options['conditions']['RegistrationItem.registration_id'] = $this->request->data['Registration']['id'];
        
        $options['fields'] = array(
            'RegistrationItem.*', 
            'RegistrationMainVariation.*', 
            'RegistrationType.*', 
            'RegistrationPeriod.end_date');
        
        $registrationItem = $this->Registration->RegistrationItem->find('first', $options);
        
        $now = strtotime(date('Y-m-d H:i:s'));
        $end_date = strtotime($registrationItem['RegistrationPeriod']['end_date']);
        
        if(($this->request->data['PaymentStatus']['id'] == 9 || $this->request->data['PaymentStatus']['id'] == 7) && $now > $end_date)
            $this->_updateMainRegistration($this->request->data['Registration']['id'], $registrationItem);
        
        $this->request->data['RegistrationMainVariation'] = $registrationItem['RegistrationMainVariation'];
        $this->request->data['RegistrationType'] = $registrationItem['RegistrationType'];
        $this->request->data['RegistrationPeriod'] = $registrationItem['RegistrationPeriod'];
    }


    /**
     * Show payment status
     *
     * @return void
     * @author Thiago Colares
     */
    public function profile_index() {
        $product = $this->_getRegistrationData();
    }


	/**
	 * Return all registrations under PagSeguro with id as key
	 *
	 * @return array Array of all actived registrations
	 * @author Thiago Colares
	 */
	private function _getRegistrations(){
		// Get all current pagseguro registrations
        $options['conditions']['Registration.status'] = 1; // Actived
        $options['recursive'] = -1;
		$options['joins'] = array(
			// Registration Log
		            array('table' => 'payment_logs',
		                'alias' => 'PaymentLog',
		                'type' => 'LEFT',
		                'conditions' => array(
		                    'PaymentLog.registration_id = Registration.id',
		                ),
				// 'limit' => 1,
		
		
		            ),
		);
		$options['order'] = 'Registration.modified DESC'; // regTmp will set with the earliest
        $options['fields'] = array(
			'Registration.id',
			'Registration.payment_entity_id',
			'Registration.payment_entity_payment_method_id',
			'Registration.payment_status_id',
			'PaymentLog.transaction_last_event_date'
			//'PaymentLog.transaction_date'

		);
		$registrations = $this->Registration->find('all', $options);

		$regTmp = array();
		foreach ($registrations as $registration) {
			$regTmp[$registration['Registration']['id']] = $registration;
		}
		return $regTmp;
	}
	

	/**
	 * Return all current transactions from pagseguro as a SimpleXMLElement object
	 *
	 * @return mixed
	 * @author Thiago Colares
	 */
    private function _getPagSeguroTransactions() {
		$hostName = 		'https://ws.pagseguro.uol.com.br/v2/transactions';
		//$initialDate = 		'2011-12-02T00:00';
		$initialDate = 		date('Y-m-d\TH:i',strtotime("-30 day"));
		// $finalDate = 	 date('Y-m-d\TH:i',strtotime("-5 min"));
		$finalDate = 	 date('Y-m-d\TH:i',strtotime("-6 hour -30 min"));
//		$finalDate = 		date('Y-m-d\TH:i',strtotime("now"));
		$page = 			1;
		$maxPageResults = 	1000;
		$email = 			Configure::read('pagseguro_email');
		$token = 			Configure::read('pagseguro_token');

		$completeUrl = "$hostName?initialDate=$initialDate&finalDate=$finalDate&page=$page&maxPageResults=$maxPageResults&email=$email&token=$token"; 

		$xml = simpleXML_load_file($completeUrl,"SimpleXMLElement",LIBXML_NOCDATA);
		return $xml->transactions;
	}


	/**
	 * This is a FIX action. Just use it if you are VERY SURE on what you are doing
	 * Update Registrations based on PagSeguro API XML query
	 * Update old registration data with the most earlier transaction state
	 *
	 * @author Thiago Colares
	 */
	public function fixRegistrationsFromPagSeguro($token = null){
		if($token === 'H4d8v5t1u7O8fDgZQIY43nK24dc3gQ18vvmp6p2D2eM7qi516Ki7o7o07hPEU6ewu8YJ266b2eBO8Dc214K9rt3J5s'){
			$transactions = $this->_getPagSeguroTransactions();
			$registrations = $this->_getRegistrations();

			$earlierDate = array();
			
			$count = 0;
	        foreach ($transactions->children() as $transaction) {

				$count++;
				$transactionArr = $this->_simplexml2array($transaction);
				
				if(!isset($earlierDate[$transactionArr['reference']])){
					$earlierDate[$transactionArr['reference']] = '';
				}
				
				if(!isset($transactionReg[$transactionArr['reference']])){
					$transactionReg[$transactionArr['reference']] = array();
				}
				if($earlierDate[$transactionArr['reference']] < date("Y-m-d H:i:s", strtotime($transactionArr['lastEventDate']))){
					$earlierDate[$transactionArr['reference']] = date("Y-m-d H:i:s", strtotime($transactionArr['lastEventDate']));
					$transactionReg[$transactionArr['reference']] = $transactionArr;
				}
			}
			
			$this->request->data = array();
			
			foreach($transactionReg as $IdRegistration => $trans){
				$this->request->data['Registration'][] = array(
					'id' => $IdRegistration,
					'price' => 2, // PagSeguro
					'payment_entity_id' => 2,
					'payment_entity_payment_method_id' => $this->_getPaymentMethod($trans['paymentMethod']['type'], 2),
					'payment_status_id' => $this->_getPaymentStatus($trans['status'], 2), //2 - PagSeguro,
				);
			}
					
			// debug($this->request->data['Registration']);
			
			if($this->_save($this->Registration, true)){
				$this->Session->setFlash(__('%s Registration has been updated.', $count), 'default', array('class' => 'alert-message success'));
			}else{
				$this->Session->setFlash(__('Error while saving Payment Logs.'), 'default', array('class' => 'alert-message error'));				
			}
			
		} else {
			$this->Session->setFlash(__('You are not allowed to perform this action'), 'default', array('class' => 'alert-message error'));
		}

	}

	/**
	 * This is a FIX action. Just use it if you are VERY SURE on what you are doing
	 * Create payment logs based on all current PagSeguro transactions. Do not update, just create
	 *
	 * @param string $token 
	 * @return void
	 * @author Thiago Colares
	 */
	public function fixPaymentLogFromPagSeguro($token = null){
		if($token === 'H4d8v5t1u7O8fDgZQIY43nK24dc3gQ18vvmp6p2D2eM7qi516Ki7o7o07hPEU6ewu8YJ266b2eBO8Dc214K9rt3J5s'){
			$transactions = $this->_getPagSeguroTransactions();
			$registrations = $this->_getRegistrations();

			$count = 0;

	        foreach ($transactions->children() as $transaction) {
				$count++;
				$transactionArr = $this->_simplexml2array($transaction);
				$this->request->data['PaymentLog'][] = array(
					'registration_id' => $transactionArr['reference'],
					'payment_entity_id' => 2, // PagSeguro
					'payment_status_id' => $this->_getPaymentStatus($transactionArr['status'], 2), //2 - PagSeguro,
					'payment_entity_payment_method_id' => $this->_getPaymentMethod($transactionArr['paymentMethod']['type'], 2),
					'transaction_date' => date("Y-m-d H:i:s", strtotime($transactionArr['date'])),
					'transaction_last_event_date' => date("Y-m-d H:i:s", strtotime($transactionArr['lastEventDate'])),
					'description' => serialize($transactionArr),
				);
				
			}
			
			if($this->_save($this->PaymentLog, true)){
				$this->Session->setFlash(__('%s Payment Logs has been saved.', $count), 'default', array('class' => 'alert-message success'));
			}else{
				$this->Session->setFlash(__('Error while saving Payment Logs.'), 'default', array('class' => 'alert-message error'));				
			}

		} else {
			$this->Session->setFlash(__('You are not allowed to perform this action'), 'default', array('class' => 'alert-message error'));
		}
	}
	
	
	/**
	 * Update all system registrations using all current transaction as input
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function updateFromPagSeguro($token = null){
		if($token === 'H4d8v5t1u7O8fDgZQIY43nK24dc3gQ18vvmp6p2D2eM7qi516Ki7o7o07hPEU6ewu8YJ266b2eBO8Dc214K9rt3J5s'){
			$transactions = $this->_getPagSeguroTransactions();

			$registrations = $this->_getRegistrations();

			$earlierDate = array();
			
			$empty = 0;
			$error = 0;
			$success = 0;			
			
	        foreach ($transactions->children() as $transaction) {

				$transactionArr = $this->_simplexml2array($transaction);
				
				if(!isset($earlierDate[$transactionArr['reference']])){
					$earlierDate[$transactionArr['reference']] = '';
					$earlierLastEventDate[$transactionArr['reference']] = '';
				}
				
				if(!isset($transactionReg[$transactionArr['reference']])){
					$transactionReg[$transactionArr['reference']] = array();
				}
				
				//debug($transactionArr['reference'] . ': ' . $earlierDate[$transactionArr['reference']] . ' < ' . date("Y-m-d H:i:s", strtotime($transactionArr['date'])));
				if($earlierDate[$transactionArr['reference']] < date("Y-m-d H:i:s", strtotime($transactionArr['date']))){
					if($earlierLastEventDate[$transactionArr['reference']] < date("Y-m-d H:i:s", strtotime($transactionArr['lastEventDate']))){
						//debug($transactionArr['reference'] . ' aew!');
						$earlierDate[$transactionArr['reference']] = date("Y-m-d H:i:s", strtotime($transactionArr['date']));
						$earlierLastEventDate[$transactionArr['reference']] = date("Y-m-d H:i:s", strtotime($transactionArr['lastEventDate']));
						$transactionReg[$transactionArr['reference']] = $transactionArr;
					}					
				}

				
				
			}
			
			//debug($transactionReg); die();
			
			foreach($transactionReg as $IdRegistration => $trans){
				$this->request->data = array();
				
			//	debug($trans['reference'] . ': ' . $registrations[$transactionArr['reference']]['Registration']['payment_status_id'] . ' != ' . $this->_getPaymentStatus($transactionArr['status'], 2));
				
				// Check if is current
				if(
					// Avoid error on empty referenced registration (due to some test)
					isset($registrations[$trans['reference']])
					&&
					(
						// $registrations[$trans['reference']]['PaymentLog']['transaction_last_event_date'] == date("Y-m-d H:i:s", strtotime($trans['lastEventDate']))
						// ||
						// (
							$registrations[$trans['reference']]['Registration']['payment_status_id'] != $this->_getPaymentStatus($trans['status'], 2)
							&&
							$registrations[$trans['reference']]['Registration']['payment_entity_id'] == 2 // pagseguro
						)
						
					// )
					
				){
					$this->request->data['Registration'] = array(
						'id' => $IdRegistration,
						'price' => 2, // PagSeguro
						'payment_entity_id' => 2,
						'payment_entity_payment_method_id' => $this->_getPaymentMethod($trans['paymentMethod']['type'], 2),
						'payment_status_id' => $this->_getPaymentStatus($trans['status'], 2), //2 - PagSeguro,
					);
					$this->request->data['PaymentLog'][0] = array(
						'registration_id' => $IdRegistration,
						'payment_entity_id' => 2, // PagSeguro
						'payment_status_id' => $this->_getPaymentStatus($trans['status'], 2), //2 - PagSeguro,
						'payment_entity_payment_method_id' => $this->_getPaymentMethod($trans['paymentMethod']['type'], 2),
						'transaction_date' => date("Y-m-d H:i:s", strtotime($trans['date'])),
						'transaction_last_event_date' => date("Y-m-d H:i:s", strtotime($trans['lastEventDate'])),
						'description' => serialize($trans),
					);	

				}

				if(!empty($this->request->data)){
					if($this->_save($this->Registration)){
						$success++;
					} else {
						$error++;
					}
				} else {
					$empty++;
				}
			}
						
			$this->Session->setFlash(__('<strong>Updated transaction(s): %d</strong><br>Transaction(s) could be updated due to an error: %d<br> Already updated transaction(s): %d<br> Total of transactions: %d', $success, $error, $empty, $success + $error + $empty), 'default', array('class' => 'alert-message success'));
			
		} else {
			$this->Session->setFlash(__('You are not allowed to perform this action'), 'default', array('class' => 'alert-message error'));
		}
	
	}
	

	/**
	 * Convert SimpleXMLElement object to array
	 * Added a is_object check
	 * Copyleft GPL license
	 *
	 * @param string $xml 
	 * @return void
	 * @author Copyright Daniel FAIVRE 2005 - www.geomaticien.com
	 */
	private function _simplexml2array($xml) {		
	   if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
	       $attributes = $xml->attributes();
	       foreach($attributes as $k=>$v) {
	           if ($v) $a[$k] = (string) $v;
	       }
	       $x = $xml;
	       $xml = get_object_vars($xml);
	   }
	   if (is_array($xml)) {
	       if (count($xml) == 0) return (string) $x; // for CDATA
	       foreach($xml as $key=>$value) {
	           $r[$key] = $this->_simplexml2array($value);
	       }
	       if (isset($a)) $r['@'] = $a;    // Attributes
	       return $r;
	   }
	   return (string) $xml;
	}
	
	
	/**
	 * Get last payment from a given registration ID
	 *
	 * @param string $regIDs Array of registration ID
	 * @return array Array with all transaction, with registration_id as key
	 * @author Thiago Colares
	 */	
	private function _getLastPaymentLog($regIDs = null){
		$regIDs = implode(',', $regIDs);
		
		$paymentLogQuery = 
			"SELECT
				PaymentLog.id, PaymentLog.registration_id, PaymentLog.description, PaymentLog.modified
			FROM (
				SELECT
					PaymentLogTemp.id,
					PaymentLogTemp.registration_id,
					PaymentLogTemp.description,
					PaymentLogTemp.modified from payment_logs AS PaymentLogTemp
				WHERE
					PaymentLogTemp.registration_id IN ($regIDs)
				ORDER BY
					PaymentLogTemp.modified DESC
			) as PaymentLog
			GROUP BY PaymentLog.registration_id";
		
		$items = $this->PaymentLog->query($paymentLogQuery);
		
		// registration_id as key
		$tmp = array();
		foreach ($items as $item) {
			$tmp[$item['PaymentLog']['registration_id']] = $item;
		}
		return $tmp;
	}
	
	
	/**
	 * Return all users by a given registrtion payment status
	 *
	 * @param mixed $status Payment Status, int or array
	 * @return void
	 * @author Thiago Colares
	 */
	private function _getReceiptableRegistrations(){
		$options['recursive'] = -1;
		$options['joins'] = array(
			// USER -----------------------
			// User
		    array('table' => 'users',
		        'alias' => 'User',
		        'type' => 'LEFT',
		        'conditions' => array(
					'Registration.user_id = User.id',
		        )
		    ),
		
			// PROFILE --------------------
			// Profile
			array('table' => 'profiles',
			    'alias' => 'Profile',
			    'type' => 'LEFT',
			    'conditions' => array(
					'User.id = Profile.id',
			    )
			),
		
		    // PAYMENT --------------------
		    // Payment Status
		    array('table' => 'payment_statuses',
		        'alias' => 'PaymentStatus',
		        'type' => 'LEFT',
		        'conditions' => array(
					'PaymentStatus.id = Registration.payment_status_id',
		        )
		    ),
		    // Payment Entity
		    array('table' => 'payment_entities',
		        'alias' => 'PaymentEntity',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'PaymentEntity.id = Registration.payment_entity_id',
		        )
		    ),
		    // Payment Entity Methods
		    array('table' => 'payment_entity_payment_methods',
		        'alias' => 'PaymentEntityPaymentMethod',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'PaymentEntityPaymentMethod.id = Registration.payment_entity_payment_method_id',
		        )
		    ),
		    // Payment Method
		    array('table' => 'payment_methods',
		        'alias' => 'PaymentMethod',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'PaymentMethod.id = PaymentEntityPaymentMethod.payment_method_id',
		        )
		    ),
		    // Payment Item
		    array('table' => 'registration_items',
		        'alias' => 'RegistrationItem',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'Registration.id = RegistrationItem.registration_id',
		        )
		    ),
		    // Payment Main Variation
		    array('table' => 'registration_main_variations',
		        'alias' => 'RegistrationMainVariation',
		        'type' => 'LEFT',
		        'conditions' => array(
		            'RegistrationMainVariation.id = RegistrationItem.registration_main_variation_id',
		        )
		    ),
		);

		$options['conditions']['Registration.user_id'] = 317; // Active

		$options['conditions']['Registration.status'] = 1; // Active
		$options['conditions']['PaymentStatus.receiptable'] = 1; // Able to send receitp
		
		$options['fields'] = array(
			// User
		    'User.email',
		
			// Perfil
		    'Profile.name',
		
			'Registration.id',
			'Registration.ref_code',
			'Registration.payment_entity_payment_method_id',
			'Registration.modified',
			// 'RegistrationMainVariation.price',
			// 'RegistrationType.name',
			// 'RegistrationPeriod.end_date',
		
		    // Payment Status
		    'PaymentStatus.id',
		    'PaymentStatus.name',
		    'PaymentStatus.code',
		    'PaymentStatus.payable',

		    // Payment Entity
		    'PaymentEntity.id',
		    'PaymentEntity.name',
		    'PaymentEntityPaymentMethod.id',
		
		    // Payment Method
		    'PaymentMethod.id',
		    'PaymentMethod.name',
		
			'RegistrationMainVariation.price'
		
			
		);
		
		// // Payment Log
		// 	    array('table' => 'payment_logs',
		// 	        'alias' => 'PaymentLog',
		// 	        'type' => 'LEFT',
		// 	        'conditions' => array(
		// 	            'Registration.id = PaymentLog.registration_id',
		// 	        )
		// 	    ),
		// 	    // Payment Log
		// 	    array('table' => 'payment_logs',
		// 	        'alias' => 'PaymentLog2',
		// 	        'type' => 'LEFT',
		// 	        'conditions' => array(
		// 		'AND' => array(
		// 			'PaymentLog.id = PaymentLog2.id',
		// 			'PaymentLog.modified > PaymentLog2.modified',
		// 		)
		// 	        )
		// 	    ),
		
		$items = $this->Registration->find('all', $options);

        foreach($items as &$item){
			$tmp[$item['Registration']['id']] = $item;
			$regIDs[] = $item['Registration']['id'];		
		}
		
		$paymentLogs = $this->_getLastPaymentLog($regIDs);
		
		foreach($tmp as &$registration){
			$registration['PaymentLog'] = $paymentLogs[$registration['Registration']['id']]['PaymentLog'];
		}

		return $tmp;
		
	}
	
	/**
	 * Send Payment Receipt to all users that alreay
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function sendBackdatedPaymentReceipt(){

		$registrations = $this->_getReceiptableRegistrations();
		foreach($registrations as $reg){
			$this->request->data = $reg;
			debug($this->request->data);
			$this->sendReceiptEmail();
			//send e-mail
		}
		
	}
	
	

	/**
	 * Send Receipt E-mail
	 *
	 */
    protected function sendReceiptEmail(){
		$email = new CakeEmail();

		$viewVars = array(
			'name' => $this->request->data['Profile']['name'],
			'payment_status' => $this->request->data['PaymentStatus']['name'],
			'payment_entity' => $this->request->data['PaymentEntity']['name'],
			'payment_method' => $this->request->data['PaymentMethod']['name'],
			'ref_code' => $this->request->data['Registration']['ref_code'],
			'modified' => date('d/m/Y \à\s H:i', strtotime($this->request->data['Registration']['modified'])),
			'receipt_date' => date('d/m/Y \à\s H:i'),
			'price' => 'R$ ' . number_format($this->request->data['RegistrationMainVariation']['price'], 2, ',', '.')
		);
		
		// Get PagSeguro data
		if($this->request->data['PaymentEntity']['id'] == 2){ // If PagSeguro
			$pagSeguroData = unserialize($this->request->data['PaymentLog']['description']);
			$viewVars['transaction_code'] = $pagSeguroData['code'];
			$viewVars['last_event_date'] = date('d/m/Y \à\s H:i', strtotime($pagSeguroData['lastEventDate']));
		}

		$email->viewVars($viewVars);

		$res = $email->template('Registration.payment/receipt','default')
		    ->emailFormat('html')
			->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
			->to($this->request->data['User']['email'])
			->subject('[' . Configure::read('EventTitle') . '] ' . __('Registration and Payment confirmed') ) 
			->send();

		return array('success' => true);
    }

}
