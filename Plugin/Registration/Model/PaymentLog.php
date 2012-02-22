<?php
/**
 * Logs every payment transaction
 *
 * @package default
 * @author Thiago Colares
 */
class PaymentLog extends RegistrationAppModel {
    public $name = 'PaymentLog';
    
	public $order = "PaymentLog.transaction_date DESC";

    public $belongsTo = array(
	    'PaymentEntity',
        'PaymentMethod',
        'PaymentEntityPaymentMethod',
        'Registration'
    );
}