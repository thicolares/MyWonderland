<?php
/**
 * Card, Cash, Billet, Check etc.
 *
 * @package default
 * @author Thiago Colares
 */
class PaymentMethod extends RegistrationAppModel{
	public $name = 'PaymentMethod';
	
	public $hasMany = array('Registration.PaymentEntityPaymentMethod');
}
