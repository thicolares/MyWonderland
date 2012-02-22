<?php
/**
 * Bank, Payment Gateway (PagSeguro, Moip etc.), Event Organization etc.
 *
 * @package default
 * @author Thiago Colares
 */
class PaymentEntity extends RegistrationAppModel{
	public $name = 'PaymentEntity';
	
	public $hasMany = array(
		'PaymentEntityPaymentMethod' => array(
			'className' => 'Registration.PaymentEntityPaymentMethod',
			'dependent' => true
		),
        'PaymentStatus' => array(
            'dependent' => true 
        )
	);
}