<?php

class Registration extends RegistrationAppModel {
    public $name = 'Registration';
    
    public $belongsTo = array(
    	'PaymentEntity',
        'PaymentEntityPaymentMethod',
        'PaymentStatus',
        'User'
    );
    
    public $hasMany = array(
        'RegistrationItem' => array('dependent' => true),
        'PaymentLog' => array('dependent' => true),
    );
    
    public $validate = array(
        'registration_policy' => array(
			'registrationPolicy' => array(
				'rule' => 'checkRegistrationPolicy',
				'message' => 'Você ainda não aceitou a Política de Inscrições.'
			)
		)
	);
    
    function checkRegistrationPolicy(){
        $res = (bool)$this->data['Registration']['registration_policy'];
        return $res;
    }
}