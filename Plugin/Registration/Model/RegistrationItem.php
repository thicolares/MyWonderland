<?php

class RegistrationItem extends RegistrationAppModel {
    public $name = 'RegistrationItem';
    
    public $belongsTo = array(
        'Registration',
        'RegistrationMainVariation'
    );
    
    public $validate = array(
		'registration_main_variation_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not selected your registration type.',
                'on'   => 'create',
				'required' => true
			)
		)
	);
}