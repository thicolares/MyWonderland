<?php
App::uses('RegistrationAppController', 'Registration.Controller');
/**
 * Exemption Controller
 *
 * PHP version 5
 */
class ExemptionsController extends RegistrationAppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Exemptions';

	public $entity = "Registration";
    
    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = array('Registration.Registration','Registration.RegistrationMainVariation', 'Role', 'Registration.PaymentMethod');
    
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
        
        $this->title  = __('Exemption Request');
        $this->titleP = __('Exemption Requests');
        
        $this->findFields = array(
			array('field' => 'Registration.id', 'title' => __('ID'), 'hide' => true),
            array('field' => 'User.id', 'title' => __('ID'), 'hide' => true),
			array('field' => 'Profile.name', 'title' => __('Name')),
            array('field' => 'Profile.main_doc', 'title' => __('Document')),
            array('field' => 'PaymentEntity.name', 'title' => __('Payment Entity')),
            array('field' => 'PaymentMethod.name', 'title' => __('Payment Method')),
            array('field' => 'PaymentStatus.name', 'title' => __('Payment Status'))
        );
        
        $this->Registration->recursive = -1;
    }
    
    protected function getPaginateEntity(){
		return 'Registration';
	}
    
    protected function getPaginateJoins() {
        $defaultJoins = parent::getPaginateJoins();
        
        $joins = array(
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.user_id = Profile.id'
                )
            ),
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.user_id = User.id'
                )
            ),
            array(
                'table' => 'payment_entities',
                'alias' => 'PaymentEntity',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.payment_entity_id = PaymentEntity.id'
                )
            ),
            array(
                'table' => 'payment_statuses',
                'alias' => 'PaymentStatus',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.payment_status_id = PaymentStatus.id'
                )
            ),
            array(
                'table' => 'payment_entity_payment_methods',
                'alias' => 'PaymentEntityPaymentMethod',
                'type' => 'LEFT',
                'conditions' => array(
                    'Registration.payment_entity_payment_method_id = PaymentEntityPaymentMethod.id'
                )
            ),
            array(
                'table' => 'payment_methods',
                'alias' => 'PaymentMethod',
                'type' => 'LEFT',
                'conditions' => array(
                    'PaymentEntityPaymentMethod.payment_method_id = PaymentMethod.id'
                )
            ),
            array(
                'table' => 'papers',
                'alias' => 'Paper',
                'type' => 'INNER',
                'conditions' => array(
                    'Paper.exempt_main_doc = Profile.main_doc'
                )
            )
        );
        
        return array_merge($defaultJoins, $joins);
    }
    
    function admin_allow($id){
        $data['Registration']['id'] = $id;
        $data['Registration']['payment_entity_id'] = 1;
        $data['Registration']['payment_entity_payment_method_id'] = 9;
        $data['Registration']['payment_status_id'] = 11;
        
        try{
            $this->Registration->save($data);
            $this->Session->setFlash(__('Exemption granted'), 'default', array('class' => 'alert-message success'));
        }catch(Exception $e){
            $this->Session->setFlash(__('Error while saving') . '. ' . $this->res['message'], 'default', array('class' => 'alert-message error'));
        }
        
        $this->redirect(array('action' => 'index'));
    }
    
    function admin_deny($id){
        $data['Registration']['id'] = $id;
        $data['Registration']['payment_entity_id'] = 1;
        $data['Registration']['payment_entity_payment_method_id'] = 9;
        $data['Registration']['payment_status_id'] = 12;
        
        try{
            $this->Registration->save($data);
            $this->Session->setFlash(__('Exemption denied'), 'default', array('class' => 'alert-message success'));
        }catch(Exception $e){
            $this->Session->setFlash(__('Error while saving') . '. ' . $this->res['message'], 'default', array('class' => 'alert-message error'));
        }
        
        $this->redirect(array('action' => 'index'));
    }
}