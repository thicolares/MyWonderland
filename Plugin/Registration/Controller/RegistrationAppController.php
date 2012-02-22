<?php

class RegistrationAppController extends AppController {

	/**
	 * Send receipt to user when payment status is receiptable
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function sendReceipt(){
		
	}
	
	/**
	 * Get last payment from a given registration ID
	 *
	 * @param string $regIDs Array of registration ID
	 * @return array Array with all transaction, with registration_id as key
	 * @author Thiago Colares
	 */	
	protected function getLastPaymentLog($regIDs = null){
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
	protected function getReceiptableRegistrations(){
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
}

