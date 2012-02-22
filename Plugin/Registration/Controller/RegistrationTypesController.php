<?php
App::uses('RegistrationAppController', 'Registration.Controller');
/**
 * Registration Type Controller
 *
 * PHP version 5
 */
class RegistrationTypesController extends RegistrationAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'RegistrationTypes';

	public $entity = "RegistrationType";
    
    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = array('Registration.RegistrationType','Registration.RegistrationMainVariation');
    
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
        
        $this->title  = __('Registration Type');
        $this->titleP = __('Registration Types');
        
        $this->saveHandlers = array(
		    array('handler' => 'setStatusHandler'),
	        array('handler' => 'finalSaveHandler')	
		);
        
        $this->findFields = array(
			array('field' => 'RegistrationType.id', 'title' => __('ID')),
			array('field' => 'RegistrationType.name', 'title' => __('Name')),
			array('field' => 'RegistrationType.off_site', 'title' => __('Off site'), 'renderer' => 'yesNoRenderer'),
            array('field' => 'RegistrationType.proof_needed', 'title' => __('Proof need'), 'renderer' => 'yesNoRenderer'),
            //array('field' => 'RegistrationType.status', 'title' => __('Status'))
        );
    }
    
    protected function setStatusHandler(){
        $this->request->data['RegistrationType']['status'] = 1;
        
        return array('success' => true);
    }
}
