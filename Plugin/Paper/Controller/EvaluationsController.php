<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('PaperAppController', 'Paper.Controller');

/**
 * Paper  Controller
 *
 * PHP version 5
 */
class EvaluationsController extends PaperAppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Evaluations';
    public $entity = "Evaluation";

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */

    public $uses = array('User', 'Paper.Paper', 'Paper.Evaluation', 'Profile');

    public function beforeFilter() {
        parent::beforeFilter();

        $this->titleP = __('Evaluations');
        $this->title = __('Evaluation');
        
        $this->preSaveHandlers = array(
            array('handler' => 'getPaper'),
            array('handler' => 'getEvaluationStatus')
        );

        $this->saveHandlers = array(
            array('handler' => 'evaluateSaveHandler')
        );

        $this->readHandlers = array(
            array('handler' => 'evaluateReadHandler'),
		//	array('handler' => '')
        );

		$this->findFields = array(
            array('field' => 'Paper.id', 'title' => __('ID')),
            array('field' => 'Paper.title', 'title' => __('Name')),
            array('field' => 'PaperType.name', 'title' => __('Paper Type')),
            array('field' => 'EvaluationStatus.name', 'title' => __('Paper Status')),
        );

    }

	// public function profile_evaluate($id) {
	//         $this->titleP = __('Evaluations');
	//         $this->title = __('Evaluation');
	//         
	//         $this->preSaveHandlers = array(
	//             array('handler' => 'getPaper'),
	//             array('handler' => 'getEvaluationStatus')
	//         );
	// 
	//         $this->saveHandlers = array(
	//             array('handler' => 'evaluateSaveHandler')
	//         );
	// 
	//         $this->readHandlers = array(
	//             array('handler' => 'evaluateReadHandler'),
	// 		array('handler' => '')
	//         );
	//         
	//         $this->redirectURLHandler = array('profile' => true, 'action' => 'evaluations');
	//         
	//         $this->profile_edit($id);
	//     }

    // protected function evaluateSaveHandler(){
    //     return $this->_save($this->Paper->Evaluation);
    // }
    
    protected function getPaper(){
        $options['conditions']['Paper.id'] = $this->request->pass[0];
        $options['joins'] = $this->getPaginateEvaluateJoins();
        $options['fields'] = array('Paper.*', 'PaperType.name', 'Profile.name');
        $options['recursive'] = -1;

        $paper = $this->Paper->find('first', $options);

        $options = array();
        $options['joins'] = array(
            array(
                'table' => 'papers_research_lines',
                'alias' => 'PaperResearchLine',
                'type' => 'INNER',
                'conditions' => array(
                    'PaperResearchLine.research_line_id = ResearchLine.id'
                )
            )
        );

        $options['conditions']['PaperResearchLine.paper_id'] = $paper['Paper']['id'];
        $options['fields'] = array('ResearchLine.name');

        $researchLines = $this->Paper->PaperResearchLine->ResearchLine->find('all', $options);

        foreach ($researchLines as $item) {
            $paper['ResearchLines'][] = $item['ResearchLine'];
        }

        $this->set('paper', $paper);
        
        return array('success' => true);
    }
    
    protected function getEvaluationStatus(){
        $evaluationStatuses = $this->Paper->Evaluation->EvaluationStatus->find('list');
        $this->set(compact('evaluationStatuses'));
        return array('success' => true);
    }


    protected function evaluateReadHandler(){
		$options['conditions']['Paper.id'] = $this->request->pass[0];
        $options['conditions']['Evaluation.user_id'] = $this->Session->read('Auth.User.id');

		$options['fields'] = array('Evaluation.*');
		$this->request->data = $this->Paper->Evaluation->find('first', $options);

        if(!$this->request->data){
			return array('success' => false, 'message' => __('Invalid ID'));
		} else {
			return array('success' => true);
		}
	}

    // protected function getPaginateJoins() {
    //     $joins = array(
    //         array(
    //             'table' => 'evaluations',
    //             'alias' => 'Evaluation',
    //             'type' => 'INNER',
    //             'conditions' => array(
    //                 'Paper.id = Evaluation.paper_id'
    //             )
    //         ),
    //         array(
    //             'table' => 'evaluation_statuses',
    //             'alias' => 'EvaluationStatus',
    //             'type' => 'INNER',
    //             'conditions' => array(
    //                 'Evaluation.evaluation_status_id = EvaluationStatus.id'
    //             )
    //         )
    //     );
    // 
    //     return $joins;
    // }

    // protected function getPaginateEvaluationConditions() {
    //     $conditions['Evaluation.user_id'] = $this->Auth->user('id');
    //     return $conditions;
    // }

    // public function profile_evaluations() {
    //     $this->set('titleP', $this->titleP);
    //     $this->set('title', $this->title);
    // 
    //     $this->findFields = array(
    //         array('field' => 'Paper.id', 'title' => __('ID')),
    //         array('field' => 'Paper.title', 'title' => __('Name')),
    //         array('field' => 'PaperType.name', 'title' => __('Paper Type')),
    //         array('field' => 'EvaluationStatus.name', 'title' => __('Paper Status')),
    //     );
    // 
    //     $this->set('findFields', $this->findFields);
    // 
    //     $this->setVar('entity', $this->getPaginateEntity());
    //     $this->setVar('conditions', $this->getPaginateEvaluationConditions());
    //     $this->setVar('joins', $this->getPaginateEvaluationJoins());
    //     $this->setVar('orders', $this->getPaginateOrder());
    //     $this->setVar('limit', $this->getPaginateLimit());
    //     $this->setVar('offset', $this->getPaginateOffset());
    //     $this->setVar('fields', $this->getPaginateFields());
    //     $this->setVar('baseAdditionalElements', $this->additionalElements);
    //     $this->setVar('getFieldsForSearcFunction', 'getPaginateFields');
    // 
    //     $res = $this->basePaginate();
    // 
    //     if ($res['success']) {
    //         $this->set('entity', $this->getVar('entity'));
    //         $this->set('rowActions', $this->rowActions);
    //         $this->set('res', $res['items']);
    //     } else {
    //         $this->Session->setFlash(__('Error:') . ' ' . $res['message'], 'default', array('class' => 'alert-message error'));
    //     }
    // }


	/**
	 * As evaluation finishes, its author
	 *
	 */
    protected function sendEvaluationResult(){
		$email = new CakeEmail();

		$email->viewVars(array(
			'name' => $appraiser['Profile']['name'],
			'paperId' => $this->Paper->id
		));

		$res = $email->template('Paper.paper/new_paper_notice','default')
		    ->emailFormat('html')
			->from('no-reply@viiforumeducacaoambiental.org.br')
			->to($appraiser['User']['email'])
			->subject('[' . Configure::read('EventTitle') . '] ' . __('There is a new paper waiting for you evaluation'))
			->send();

		return array('success' => true);
    }

}