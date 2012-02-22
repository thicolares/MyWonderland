<?php
App::uses('RegistrationAppController', 'Registration.Controller');

/**
 * Registration Period Controller
 *
 * PHP version 5
 */
class RegistrationPeriodsController extends RegistrationAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'RegistrationPeriods';
    
    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = array('Registration.Registration', 'Registration.RegistrationPeriod', 'Registration.RegistrationMainVariation', 'Registration.RegistrationType');
    
    /**
     * Paginate configuration
     *
     * @var array
     * @access public
     */
    var $paginate = array(
        /**
        * @todo turn into a enviroment variable!
        */
        'limit' => 25, 
        //'contain'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->layout = 'admin';
        
        $this->title  = __('Registration Period', true);
        $this->titleP = __('Registration Periods', true);
        
		$this->saveHandlers = array(
			array('handler' => 'formatRegistrationPeriodDateAndTime'),
		    array('handler' => 'addRegistrationMainVariation'),
	        array('handler' => 'finalSaveHandler')	
		);

	    $this->preSaveHandlers = array(
			array('handler' => 'getRegistrationTypes')
		);

        $this->findFields = array(
			array('field' => 'RegistrationPeriod.id', 'title' => __('ID',true)),
			array('field' => 'RegistrationPeriod.begin_date', 'title' => __('Begins on'), 'renderer' => 'datetime4view'),
            array('field' => 'RegistrationPeriod.end_date', 'title' => __('Ends on'), 'renderer' => 'datetime4view'),
			array('field' => 'RegistrationPeriod.off_site', 'title' => __('Off site'), 'renderer' => 'yesNoRenderer'),
            //array('field' => 'Person.Logo_200', 'Virtual' => true, 'Renderer' => 'renderLogo200'),
        );
        
    }

	/**
	 * Maketime From a date set from request (POST or GET)
	 * Why?? 			http://stackoverflow.com/questions/6136430/a-non-well-formed-numeric-value-encountered
	 * @param string $date 
	 * @param string $time 
	 * @return int timestamp
	 * @author Thiago Colares
	 */
	private function _getWellFormatedDate($date,$time){
		$date = explode('/', $date);
		$time = explode(':', $time);
		return mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
	}
	
	/**
	 * Get all managers from the system
	 *
	 * @return array
	 * @author Thiago Colares
	 */
	public function formatRegistrationPeriodDateAndTime() {  
		$this->request->data['RegistrationPeriod']['begin_date'] = date(
			'Y-m-d H:i',
			$this->_getWellFormatedDate(
				$this->request->data['RegistrationPeriod']['begin_date']['date'],
				$this->request->data['RegistrationPeriod']['begin_date']['time']
			)
		);
		
		$this->request->data['RegistrationPeriod']['end_date'] = date(
			'Y-m-d H:i',
			$this->_getWellFormatedDate(
				$this->request->data['RegistrationPeriod']['end_date']['date'],
				$this->request->data['RegistrationPeriod']['end_date']['time']
			)
		);
		
		/*
			TODO http://php.net/manual/pt_BR/function.checkdate.php
			Check date using strtotime
		*/
		if(true){
			return array('success' => true);
		} else {
			return array('success' => false, 'message' => __('Invalid date. Please, check the form and correct date values.'));
		}
    }
	

    /**
     * Saving: Append Registrations Periods date to Main Variation
     *
     * @return void
     * @author Thiago Colares
     */
    public function addRegistrationMainVariation(){
		if(
			$this->request->is('post') &&
			!isset($this->request->data['RegistrationPeriod']['id'])
		){
			foreach($this->request->data['RegistrationMainVariation'] as &$regMainVar){
	            $regMainVar['begin_date'] = $this->request->data['RegistrationPeriod']['begin_date'];
	            $regMainVar['end_date'] = $this->request->data['RegistrationPeriod']['end_date'];
	        }
		}
        return array('success' => true);
    }


    /**
     * Updating: Append Registrations Periods date to Main Variation
     *
     * @return void
     * @author Thiago Colares
     */
    public function editRegistrationMainVariation(){
		if(
			$this->request->is('post') &&
			isset($this->request->data['RegistrationPeriod']['id']) &&
			!empty($this->request->data['RegistrationPeriod']['id'])
		){
			$res = $this->RegistrationMainVariation->updateAll(
	            array(
	                'RegistrationMainVariation.begin_date' => "'" . $this->request->data['RegistrationPeriod']['begin_date'] . "'",
	                'RegistrationMainVariation.end_date' => "'" . $this->request->data['RegistrationPeriod']['begin_date'] . "'" 
	            ),
	            array('RegistrationMainVariation.registration_period_id' => $this->request->data['RegistrationPeriod']['id'])
	        );
	        if($res){
	            return array('success' => true);
	        } else {
	            return array('success' => false, 'message' => __('Error while trying to update Registrations Main Variations'));
	        }
		}
        return array('success' => true);
    }

    
    /**
     * Get Registratio Types to append it on forms
     * For example, Registration Period uses it
     */
    public function getRegistrationTypes(){
        $options = array(
            'recursive' => 1,
			'status' => 1
        );
        $registrationTypes = $this->RegistrationType->find('all', $options);
        if($registrationTypes){
            $this->set(compact('registrationTypes'));
            return array('success' => true);
        } else {
            return array('success' => false, 'message' => __('Error while trying to load Registrations Types'));
        }
    }

	/**
     * Before shows edit form, check if there is at least one registered user
     */
		//     public function checkRegistrationsBeforeEdit(){
		// $options['recursive'] = 1;
		// $options['conditions']['RegistrationMainVariation.registration_period_id'] = $this->request->pass[0];
		// $options['conditions']['RegistrationMainVariation.status'] = 1;
		// $options['conditions']['Registration.status'] = 1;
		// $options['joins'] = array(
		//             array(
		// 		'table' => 'registrations',
		//                 'alias' => 'Registration',
		//                 'type' => 'INNER',
		//                 'conditions' => array(
		// 			'Registration.registration_main_variation_id = RegistrationMainVariation.id'
		//                 )
		//             ),
		//         );
		// $options['fields'] = 'Registration.*';
		// $registrations = $this->RegistrationMainVariation->find('all', $options);
		// 
		// $this->set('canNotBeChanged',true);
		// $this->Session->setFlash(
		// 	__('Some people has already Registered within this Registration Period. So, It can NOT be changed! <br> Registration IDs: %s', '123'),
		// 	'default',
		// 	array('class' => 'error')
		// );
		// 
		// 
		// 
		// 
		// 
		// return;
		// 	
		//     }

	public function admin_edit(){
		//$this->checkRegistrationsBeforeEdit();
		parent::admin_edit();
	}

    
}
