<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('IssueAppController', 'Issue.Controller');
/**
 * Protocol Controller
 *
 * PHP version 5
 */
class ProtocolsController extends IssueAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Protocols';
    
    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = array('Issue.Protocol', 'Issue.Issue');

	/**
	 * This function is executed before every action in the controller.
	 * It’s a handy place to check for an active session or inspect user permissions.
	 *
	 * @return void
	 */
    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->title  = __('Protocol');
        $this->titleP = __('Protocols');
        
			//         $this->preSaveHandlers = array(
			//             array('handler' => 'setCompaniesHandler'),
			//         );
			//         
			
		$this->saveHandlers = array(
			array('handler' => 'formatRegistrationPeriodDateAndTime'),
			array('handler' => 'finalSaveHandler'),
			array('handler' => 'redirectToProtocol'),
		);

		$this->deleteHandlers = array(
			array('handler' => 'redirectToProtocol'),
		);

		$this->findFields = array(
			array('field' => 'Protocol.id', 'title' => __('ID')),
			array('field' => 'Protocol.notes', 'title' => __('Notes')),
			array('field' => 'Protocol.begin_date', 'title' => __('Day'), 'renderer' => 'date4view'),
			array('field' => 'Protocol.begin_date', 'title' => __('From'), 'renderer' => 'time4view'),
			array('field' => 'Protocol.end_date', 'title' => __('Until'), 'renderer' => 'time4view')
		);
	}
	
	/**
	 * Before save, redirect to protocols_add
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function redirectToProtocol(){
		$this->redirectURLHandler = array(
			'action' => 'add',
			'controller' => 'protocols',
			'plugin' => false,
			'profile' => true
		);

		return array('success' => true);
	}
	
	
	/**
	 * Check if Issue.Id is set on session. If dont, try to set
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _checkIdIssue(){
		if(empty($this->params->pass[0])){
			if(!$this->Session->check('Issue.id')){
				$this->redirect(array('action' => 'index', 'controller' => 'issues'));
			}
		} else {
			$this->Issue->recursive = -1;
			$options['recursive'] = -1;
			$options['conditions']['Issue.id'] = $this->params->pass[0];
			parent::checkOwner($options);
			if($this->Issue->find('first', $options)){
				$this->Session->write('Issue.id', $this->params->pass[0]);
			} else {
				$this->Session->setFlash(__('There is no Issue using %d as reference code.', $this->params->pass[0]), 'default', array('class' => 'alert-message error'));
				$this->redirect(array('action' => 'index', 'controller' => 'issues'));					
			}
		}
	}
	
	/**
	 * Only shows protocols from current Issue
	 *
	 * @return array
	 * @author Thiago Colares
	 */
	protected function getPaginateConditions() {
		return array('Protocol.issue_id' => $this->Session->read('Issue.id'));
	}
	
	/**
	 * Add and display protocols in the same page
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function profile_add(){
		$this->_checkIdIssue();
		parent::profile_add();
		parent::profile_index();
	}
	
	/**
	 * Edit and display protocols in the same page
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function profile_edit(){
		parent::profile_edit();
		parent::profile_index();
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
		$this->request->data['Protocol']['end_date'] = date(
			'Y-m-d H:i',
			$this->_getWellFormatedDate(
				$this->request->data['Protocol']['begin_date']['date'],
				$this->request->data['Protocol']['end_date']['time']
			)
		);
		
		$this->request->data['Protocol']['begin_date'] = date(
			'Y-m-d H:i',
			$this->_getWellFormatedDate(
				$this->request->data['Protocol']['begin_date']['date'],
				$this->request->data['Protocol']['begin_date']['time']
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

}