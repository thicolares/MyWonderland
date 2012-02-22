<?php
/**
 * HABTM entity
 *
 * @package default
 * @author Thiago Colares
 */
class PaymentEntityPaymentMethod extends RegistrationAppModel{
	public $name = 'PaymentEntityPaymentMethod';
    public $useTable = 'payment_entity_payment_methods';
	
	public $belongsTo = array('Registration.PaymentEntity','Registration.PaymentMethod');
}
