<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('IssueAppController', 'Issue.Controller');
/**
 * Issue Controller
 *
 * PHP version 5
 */
class IssuesController extends IssueAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Issues';
    
    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = array('Issue.Issue', 'Issue.Company', 'Issue.CompanyIssue');
    

	/**
	 * This function is executed before every action in the controller.
	 * It’s a handy place to check for an active session or inspect user permissions.
	 *
	 * @return void
	 */
    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->title  = __('Issue');
        $this->titleP = __('Issues');
        
        $this->preSaveHandlers = array(
            array('handler' => 'setCompaniesHandler'),
        );
        
        $this->saveHandlers = array(
			array('handler' => 'removeOldCompanies'),
            array('handler' => 'finalSaveHandler'),
            array('handler' => 'redirectToProtocol'),
        );
        
        $this->findFields = array(
			array('field' => 'Issue.id', 'title' => __('ID')),
            array('field' => 'Issue.description', 'title' => __('Description'))
        );
	}
	
	/**
	 * Set Company combo source
	 *
	 * @author Thiago Colares
	 */
	public function setCompaniesHandler(){
		$this->set('companies', $this->Company->find('list'));
		return array('success' => true);
	}
	
	
	/**
	 * After save, redirect user to protocol
	 *
	 * @author Thiago Colares
	 */
	public function redirectToProtocol(){
		if(empty($this->request->data['Issue']['id'])){
			$url = array('plugin' => false, 'controller' => 'protocols', 'action' => 'add/' . $this->Issue->id, 'prefix' => 'profile', 'profile' => true);
			$this->redirect($url);
		}
		return array('success' => true);
	}
	
	
	/**
	 * Overriding Edit just to reset URL to redirect
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function profile_edit(){
		$this->redirectURLHandler = array('action' => 'index');
		parent::profile_edit();
	}
	
	
	/**
     * Before edit, delete all related Companies
     *
     * @return array
     * @author Thiago Colares
     */
    protected function removeOldCompanies() {
        if (isset($this->request->data['Issue']['id'])) {
            $this->CompanyIssue->deleteAll(array('CompanyIssue.issue_id' => $this->request->data['Issue']['id']));
        }
        return array('success' => true);
    }

}