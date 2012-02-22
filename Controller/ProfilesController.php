<?php
/**
 * Usually for costumers
 *
 * @author Thiago Colares
 */
class ProfilesController extends AppController {
	
	public $uses = array('User','Profile');
	
	public function beforeFilter() {
        parent::beforeFilter();
		$this->redirectURLHandler = '/profile';
	}
	
	
	/**
	 * Pre Handlers trigget to fill the form
	 *
	 * @var string
	 */
	public $preSaveHandlers = array(
		array('handler' => 'appendTitles')
	);
	
	/**
	 * Join database tables for paginate
	 *
	 * @return Array Set of all tables to query
	 * @author Thiago Colares
	 */
	protected function getReadJoins() {
		$joins = parent::getReadJoins();
		$specificJoins = array();
		$specificJoins = array(
			array(
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => array(
					'Profile.id = User.id',
				)
			),
		);
		return array_merge($joins, $specificJoins);
	}
	
	
	/**
	 * Fields for read( edit)
	 *
	 * @return Array Set of all tables to query
	 * @author Thiago Colares
	 */
	protected function getReadFields() {
		return 'Profile.*,User.*';
	}
	
	
	/**
	 * Append titles array for select 
	 *
	 * @return array
	 * @author Thiago Colares
	 */
	public function appendTitles(){
		$this->set('titleOptions', $this->User->Profile->getProfileTitles());
		return array('success' => true);
	}
	
	
	public function profile_edit(){
		$options['conditions']['Profile.id'] = $this->Session->read('Auth.User.id');
		$options['fields'] = 'Profile.id';
		$options['recursive'] = -1;		
		$profile = $this->Profile->find('first', $options);
		$this->request->pass = array($profile['Profile']['id']);
		parent::admin_edit();
	}
	
}