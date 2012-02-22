<?php
/**
 * System Stuffs
 *
 * @package default
 */
class SystemsController extends AppController {
	public $name = 'Systems';
	/**
	 * Models used by the Controller
	 *
	 * @var array
	 * @access public
	 */
    public $uses = null;

    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('profile_dashboard');
    }
	
	
	
	public function admin_dashboard() {
        $this->set('title_for_layout', __('Dashboard'));
    }

    public function profile_dashboard() {
        $this->set('title_for_layout', __('Dashboard'));
    }

    public function profile_appraiserDashboard() {
        $this->set('title_for_layout', __('Dashboard'));
    }

}