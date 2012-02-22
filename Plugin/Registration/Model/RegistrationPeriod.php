<?php

class RegistrationPeriod extends RegistrationAppModel {
    public $name = 'RegistrationPeriod';

    public $hasMany = array(
        'RegistrationMainVariation' => array(
            'className' => 'Registration.RegistrationMainVariation',
            'dependent' => true
        )
    );
    
	//     public $validate = array(
	//         'title' => array(
	//         'notEmpty' => array(
	//             'rule' => 'notEmpty',
	//             'message' => 'You have not entered a title.'
	//             ),
	//             'maxLength' => array(
	//             'rule' => array('maxLength', '100'),
	//             'message' => 'Maximum 100 characters long'
	//             )
	//         ),
	//         'description' => array(
	//         'notEmpty' => array(
	//             'rule' => 'notEmpty',
	//             'message' => 'You have not entered a description.'
	//             ),
	//             'maxLength' => array(
	//             'rule' => array('maxLength', '255'),
	//             'message' => 'Maximum 255 characters long'
	//             )
	//         ),
	//         'address' => array(
	//         'notEmpty' => array(
	//             'rule' => 'notEmpty',
	//             'message' => 'You have not entered a address.'
	//             ),
	//             'maxLength' => array(
	//             'rule' => array('maxLength', '255'),
	//             'message' => 'Maximum 255 characters long'
	//             )
	//         ),
	// 	'zipcode' => array(
	//         'notEmpty' => array(
	//             'rule' => 'notEmpty',
	//             'message' => 'You have not entered a zipcode.'
	//             ),
	//             'maxLength' => array(
	//             'rule' => array('maxLength', '10'),
	//             'message' => 'Maximum 10 characters long'
	//             )
	//         ),
	// 	'city' => array(
	//         'notEmpty' => array(
	//             'rule' => 'notEmpty',
	//             'message' => 'You have not entered a city.'
	//             ),
	//             'maxLength' => array(
	//             'rule' => array('maxLength', '50'),
	//             'message' => 'Maximum 50 characters long'
	//             )
	//         ),
	// 	'state' => array(
	//         'notEmpty' => array(
	//             'rule' => 'notEmpty',
	//             'message' => 'You have not entered a state.'
	//             ),
	//             'maxLength' => array(
	//             'rule' => array('maxLength', '30'),
	//             'message' => 'Maximum 30 characters long'
	//             )
	//         ),
	// 	'country' => array(
	//         'notEmpty' => array(
	//             'rule' => 'notEmpty',
	//             'message' => 'You have not entered a country.'
	//             ),
	//             'maxLength' => array(
	//             'rule' => array('maxLength', '20'),
	//             'message' => 'Maximum 20 characters long'
	//             )
	//         ),
	// 	'begin_date' => array(
	// 	    // 'beginDateGrateToday' => array(
	// 	    //     'rule' => array('beginDateGrateToday'),
	// 	    //     'message' => 'Begin date must be greater than today.'
	// 	    // ),
	// 		'checkDate' => array(
	// 			'rule' => array('endDateGreaterBeginDate', 'end_date'),
	// 			'message' => 'Begin date must be less than end date.'
	//             ),
	// 	    // 'validDate' => array(
	// 	    //     'rule' => array('date','ymd'),
	// 	    //     'message' => 'Enter a valid date in DD/MM/YYYY format.',
	// 	    //     'allowEmpty' => true
	// 	    // ),
	// 	),
	// 	// 'end_date' => array(
	// 	//     // 'beginDateGrateToday' => array(
	// 	//     //     'rule' => array('beginDateGrateToday'),
	// 	//     //     'message' => 'Begin date must be greater than today.'
	// 	//     // ),
	// 	//     'validDate' => array(
	// 	//         'rule' => array('date','ymd'),
	// 	//         'message' => 'Enter a valid date in DD/MM/YYYY format.',
	// 	//         'allowEmpty' => true
	// 	//     ),
	// 	// ),
	// 	'rsvp_begin_date' => array(
	// 	    // 'beginDateGrateToday' => array(
	// 	    //     'rule' => array('beginDateGrateToday'),
	// 	    //     'message' => 'Begin date must be greater than today.'
	// 	    // ),
	// 		'checkDate' => array(
	// 			'rule' => array('endDateGreaterBeginDate', 'rsvp_end_date'),
	// 			'message' => 'Begin date must be less than end date.'
	//             ),
	// 	    // 'validDate' => array(
	// 	    //     'rule' => array('date','ymd'),
	// 	    //     'message' => 'Enter a valid date in DD/MM/YYYY format.',
	// 	    //     'allowEmpty' => true
	// 	    // )
	// 	),
	// );


	/**
	 * Check if begin date is earlier than end date. If doesn't: ERROR! :)
	 *
	 * @param string $field will have value: array('begin_date)' => 'some-value')      
	 * @param string $otherField will have value like 'end_date'
	 * @return void
	 * @author Thiago Colares
	 */
	function endDateGreaterBeginDate($field, $otherField) {
		if(strtotime(current($field)) > strtotime($this->data[$this->name][$otherField])) {
			return false;
		}
		return true;
	}
    
}