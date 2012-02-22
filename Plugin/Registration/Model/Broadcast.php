<?php
/**
 * Send e-mails to selected group of registrated
 *
 * @package registration
 * @author Thiago Colares
 */
class Broadcast extends RegistrationAppModel {
 
    var $name = 'Broadcast';
    var $useTable = false;

	public $validate = array(
		'filter' => array(
	        'notEmpty' => array(
	            'rule' => 'notEmpty',
	            'message' => 'You have not choosed a filter.'
            ),
        ),
        'subject' => array(
	        'notEmpty' => array(
	            'rule' => 'notEmpty',
	            'message' => 'You have not entered a subject.'
            ),
        ),
        'message' => array(
	        'notEmpty' => array(
	            'rule' => 'notEmpty',
	            'message' => 'You have not entered a message.'
            ),
        ),
	);

}
