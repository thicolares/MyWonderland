<?php
/**
 * A user profile is a collection of personal data associated to a specific user.
 *
 * @package default
 * @author Thiago Colares
 */
class Profile extends AppModel {
	public $name = 'Profile';
	
	public function getProfileTitles(){
		return array(
			'Sr.' => 'Sr. (Senhor)', 
			'Sra.' => 'Sra. (Senhora)', 
			'Dr.' => 'Dr. (Doutor)', 
			'Dra' => 'Dra. (Doutora)',
			'V. Ex.ª' => 'V. Ex.ª (Vossa Excelência)',
			'V. Em.ª' => 'V. Em.ª (Vossa Eminência)',
			'V. Mag.ª' => 'V. Mag.ª (Vossa Magnificência)'
		);
	}
	
    public function validateName(){
        $res = explode(' ',$this->data['Profile']['name']);
        
        return count($res) > 1;
    }
    
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your name.'
			),
            'validateName' => array(
                'rule' => 'validateName',
				'message' => 'You have not entered your last name.'
            )
		),
        'main_doc' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your CPF.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
		        'message' => 'This CPF is already in use'
			),
            'numeric' => array(
                'rule'    => 'numeric',
                'message' => 'Please supply your CPF only with numbers.'
            ),
            'minLength' => array(
                'rule' => array('minLength', 11),
                'message' => 'Inform the CPF with 11 digits.'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 11),
                'message' => 'Inform the CPF with 11 digits.'
            )
        ),
        'zipcode' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your zipcode.'
			),
            'numeric' => array(
                'rule'    => 'numeric',
                'message' => 'Only number are allowed.'
            ),
            'minLength' => array(
                'rule' => array('minLength', 8),
                'message' => 'Zipcode must be at least 8 characters.'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 8),
                'message' => 'Zipcode must be no larger than 8 characters..'
            )
        ),
        'address' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your address.'
			),
        ),
        'address_number' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your number.'
			),
            'numeric' => array(
                'rule'    => 'numeric',
                'message' => 'Only number are allowed.'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 5),
                'message' => 'Number must be no larger than 5 characters..'
            )
        ),
        'state' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your state.'
			),
        ),
        'city' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your city.'
			),
        ),
        'neighborhood' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your neighborhood.'
			),
        ),
        'mobile' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your mobile.'
			),
            'numeric' => array(
                'rule'    => 'numeric',
                'message' => 'Only number are allowed on Phone Field.'
            ),
            'minLength' => array(
                'rule' => array('minLength', 8),
                'message' => 'Mobile must be at least 8 characters.'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 8),
                'message' => 'Mobile must be no larger than 8 characters.'
            )
        ),
        'mobile_ddd' => array(
            'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have not entered your ddd.'
			),
            'numeric' => array(
                'rule'    => 'numeric',
                'message' => 'Only number are allowed on DDD field.'
            ),
            'minLength' => array(
                'rule' => array('minLength', 2),
                'message' => 'DDD must be at least 2 characters.'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 2),
                'message' => 'DDD must be no larger than 2 characters.'
            )
        )
	);
}