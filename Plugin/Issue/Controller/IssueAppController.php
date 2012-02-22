<?php

class IssueAppController extends AppController {
	/**
	 * This function is executed before every action in the controller.
	 * It’s a handy place to check for an active session or inspect user permissions.
	 *
	 * @return void
	 */
    public function beforeFilter() {
        parent::beforeFilter();

		// 
		$this->_checkIdIssue();

	}
	
	
	/**
	 * Makes sure that when in profile_add or profile_edit, will be always an Issue.id on Session
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _checkIdIssue(){
		// If it's not on profile_add or profile_edit pages, delete session
		if(
			$this->params->controller != 'protocols'
			||
			(
				$this->params->action != 'profile_add'
				&&
				$this->params->action != 'profile_edit'
				&&
				$this->params->action != 'profile_delete'
			)
		){
			$this->Session->delete('Issue.id');
		}
	}
}

