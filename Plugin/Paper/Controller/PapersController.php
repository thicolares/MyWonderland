<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('PaperAppController', 'Paper.Controller');

/**
 * Paper  Controller
 *
 * PHP version 5
 */
class PapersController extends PaperAppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Papers';
    public $entity = "Paper";

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */

    public $uses = array('User', 'Paper.Paper', 'Paper.Evaluation', 'Profile');

    public function beforeFilter() {
        parent::beforeFilter();

		$this->_checkActionsLimitDate();

		//$this->Auth->allow('admin_paperTypeReport');

        $this->title = __('Paper');
        $this->titleP = __('Papers');

        $this->preSaveHandlers = array(
            array('handler' => 'checkLimit'),
            array('handler' => 'getProfile'),
            array('handler' => 'getResearchLines'),
            array('handler' => 'getPaperTypes')
        );

        $this->saveHandlers = array(
		    array('handler' => 'removeOldResearchLine'),
		    array('handler' => 'addExemptionRequirement'),
	        array('handler' => 'finalSaveHandler'),
		    array('handler' => 'forwardPaper'),
		    array('handler' => 'sendNewPaperNotice'),
		);
		
		$this->readHandlers = array(
			array('handler' => 'notYetBeenEvaluated'),
		    array('handler' => 'defaultReadHandler'),
		    array('handler' => 'editExemptionRequirement')
		);
		
		$this->deleteHandlers = array(
			array('handler' => 'notYetBeenEvaluated')
		);

        $this->findFields = array(
            array('field' => 'Paper.id', 'title' => __('ID')),
            array('field' => 'Paper.title', 'title' => __('Name')),
            array('field' => 'Paper.paper_status_id', 'title' => '', 'hide' => true),
            array('field' => 'PaperType.name', 'title' => __('Paper Type')),
            array('field' => 'PaperStatus.name', 'title' => __('Paper Status')),
        );
    }

    protected $allowedPaperTypes;


	/**
	 * Change some fields just for admin
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function admin_index(){
		$this->findFields = array(
            array('field' => 'Paper.id', 'title' => __('ID')),
            array('field' => 'Paper.title', 'title' => __('Name')),
            array('field' => 'Paper.paper_status_id', 'title' => '', 'hide' => true),
            array('field' => 'PaperType.name', 'title' => __('Paper Type')),
            array('field' => 'PaperStatus.name', 'title' => __('Paper Status')),
            array('field' => 'Profile.name', 'title' => __('Author')),

            array('field' => 'User.email', 'title' => __('E-mail')),
            array('field' => 'Paper.co_author_name_1', 'title' => __('Co-Author') . ' 1'),
            array('field' => 'Paper.co_author_name_2', 'title' => __('Co-Author') . ' 2'),
            array('field' => 'Paper.co_author_name_3', 'title' => __('Co-Author') . ' 3'),
            array('field' => 'Paper.co_author_name_4', 'title' => __('Co-Author') . ' 4'),
            array('field' => 'Paper.created', 'title' => __('Submited at'), 'renderer' => 'datetime4view'),
            array('field' => 'Paper.modified', 'title' => __('Last modification'), 'renderer' => 'datetime4view'),
            // array('field' => 'User.id', 'title' => __('Paper Status')),
        );
		parent::admin_index();
	}
	
	
	/*
		TODO This method may affect user list index too!! No extra data will be displayed, but extra data will be fetched.
	*/
	protected function getPaginateJoins() {
        $defaultJoins = parent::getPaginateJoins();
        
        $joins = array(
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'Paper.user_id = Profile.id'
                )
            ),
        );
        
        return array_merge($defaultJoins, $joins);
    }


	/**
	 * After PaperSubmissionLimitDate, some actions can not be performed
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _checkActionsLimitDate(){

		// Forbidden actions after PaperSubmissionLimitDate
		$forbiddenActions = array(
			'profile_add', 'profile_edit', 'profile_delete'
		);
		
		if(
			$this->params['plugin'] == 'paper'
			&&
			$this->params['controller'] == 'papers'
			&&
			in_array($this->params['action'], $forbiddenActions)
			&&
			Configure::read('PaperSubmissionLimitDate') <= time()
		){
			$this->Session->setFlash(__("The deadline for submit papers is terminated. Your are no able to submit or edit a paper."), 'default', array('class' => 'alert-message notice'));
			$this->redirect(array('profile' => true, 'action' => 'index'));
		}
	}
	

    /**
     * Check limit per paper already submited
     *
     * @return void
     * @author Thiago Colares
     */
    protected function checkLimit() {
        $workshopId = $this->Paper->PaperType->getWorkshopId();
        $shortCourseId = $this->Paper->PaperType->getShortCourseId();

        if (
                !isset($this->request->pass[0])
        ) {


            $countWorkshop = $this->_countPaper($workshopId);
            $countShortCourse = $this->_countPaper($shortCourseId);

            if ($countWorkshop < 2)
                $this->allowedPaperTypes[] = $workshopId;
            if ($countShortCourse < 2)
                $this->allowedPaperTypes[] = $shortCourseId;

            if ($countWorkshop >= 2 && $countShortCourse >= 2) {
                $this->Session->setFlash(__("You have already exceeded the limit for submited papers. Each registered may submit up to two (2) proposals for workshop and two (2) proposals for short course."), 'default', array('class' => 'alert-message notice'));
                $this->redirect(array('profile' => true, 'action' => 'index'));
            }
        } else {
            $this->allowedPaperTypes[] = $workshopId;
            $this->allowedPaperTypes[] = $shortCourseId;
        }
        return array('success' => true);
    }

    /**
     * Count how many paper has been submited for each type (not final implemented)
     *
     * @param string $type 
     * @return void
     * @author Thiago Colares
     */
    private function _countPaper($type = null) {
        $options['conditions']['Paper.user_id'] = $this->Session->read('Auth.User.id');

        if ($type)
            $options['conditions']['Paper.paper_type_id'] = $type;

        return $this->Paper->find('count', $options);
    }

    /**
     * Get all avalilable research lines
     *
     * @return void
     * @author Thiago Colares
     */
    protected function getResearchLines() {
        $researchLines = $this->Paper->PaperResearchLine->ResearchLine->find('list');
        $this->set(compact('researchLines'));
        return array('success' => true);
    }

    /**
     * Get all avalilavle paper types
     *
     * @return void
     * @author Thiago Colares
     */
    protected function getPaperTypes() {
        $options['conditions']['PaperType.id'] = $this->allowedPaperTypes;

        $paperTypes = $this->Paper->PaperType->find('list', $options);
        $this->set(compact('paperTypes'));
        return array('success' => true);
    }

    /**
     * Get profile data
     *
     * @return array
     * @author Thiago Colares
     */
    protected function getProfile() {
        $profile = $this->Profile->findById($this->Session->read('Auth.User.id'));
        $this->set(compact('profile'));
        return array('success' => true);
    }

    /**
     * Before edit, delete all!
     *
     * @return array
     * @author Thiago Colares
     */
    protected function removeOldResearchLine() {
        if (isset($this->request->data['Paper']['id'])) {
            $this->Paper->PaperResearchLine->deleteAll(array('PaperResearchLine.paper_id' => $this->request->data['Paper']['id']));
        }
        return array('success' => true);
    }


	/**
	 * Set main document choosed for ark for exemption
	 *
	 * @return array
	 * @author Thiago Colares
	 */
    protected function addExemptionRequirement() {
        $mainDocs = array(
            (isset($this->request->data['Paper']['author_main_doc']) ? $this->request->data['Paper']['author_main_doc'] : null),
            $this->request->data['Paper']['co_author_main_doc_1'],
            $this->request->data['Paper']['co_author_main_doc_2'],
            $this->request->data['Paper']['co_author_main_doc_3'],
            $this->request->data['Paper']['co_author_main_doc_4']
        );

        // Remove empty values
        foreach ($mainDocs as $key => $item) {
            if (empty($item))
                unset($mainDocs[$key]);
        }

        if (count($mainDocs) != count(array_unique($mainDocs))) {
            return array('success' => false, 'message' => __('The author\'s and co-author\'s CPF or Passport can not be repeated.'));
        }

		//debug($this->request->data['Paper']['ask_for_exemption']);
        if (
			isset($this->request->data['Paper']['ask_for_exemption'])

		) {
			switch ($this->request->data['Paper']['ask_for_exemption']) {
				case -1:
					
					break;
				
				case 0:
					$this->request->data['Paper']['exempt_main_doc'] = $this->request->data['Paper']['author_main_doc'];
					break;

				default:
					$this->request->data['Paper']['exempt_main_doc'] = $this->request->data['Paper']['co_author_main_doc_' . $this->request->data['Paper']['ask_for_exemption']];    // não precisa disto
					break;
			}
            	
        }

        return array('success' => true);
    }


	/**
	 * Retrieve all paper main docs and retur as an array
	 *
	 * @return array 
	 * @author Thiago Colares
	 */
	private function _getPaperMainDocs(){
		$mainDocs = array(
			$this->request->data['Paper']['author_main_doc'],
			(isset($this->request->data['Paper']['co_author_main_doc_1']) ? $this->request->data['Paper']['co_author_main_doc_1'] : null),
			(isset($this->request->data['Paper']['co_author_main_doc_2']) ? $this->request->data['Paper']['co_author_main_doc_2'] : null),
			(isset($this->request->data['Paper']['co_author_main_doc_3']) ? $this->request->data['Paper']['co_author_main_doc_3'] : null),
			(isset($this->request->data['Paper']['co_author_main_doc_4']) ? $this->request->data['Paper']['co_author_main_doc_4'] : null)
		);
		// Remove any FALSE values. This includes NULL values, EMPTY arrays, etc.
		return $mainDocs;
	}

	
	/**
	 * Get the id from the next appraiser
	 * Conditions:
	 * 		Less number os unevaluated papers
	 *		Not Author or Co-author from the paper
	 * 
	 * @var string find type. first, all, count etc.
	 * @var bool If checked false, do check main docs. And you should do it outside this action
	 * @return array [user_id] => papers not evaluated
	 * @author Thiago Colares
	 */
	private function _getNextAppraiserId($findType = 'first', $checkOnCreate = true){
        $options = array();

		if($checkOnCreate){
			$profileConditions = array(
                'Profile.id = User.id',
				array('Profile.main_doc NOT' => $this->_getPaperMainDocs())
            );
		} else {
			$profileConditions = array(
                'Profile.id = User.id'
            );
		}

        $options['joins'] = array(
            array(
                'table' => 'evaluations',
                'alias' => 'Evaluation',
                'type' => 'LEFT',
                'conditions' => array(
                    'Evaluation.user_id = User.id',
					'Evaluation.evaluation_status_id = 2' // Não avaliado
                )
            ),
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => $profileConditions
            ),
        );

		$options['recursive'] = -1;
		$options['group'] = 'User.id';
		$options['conditions']['User.role_id'] = 6; // Avaliadores
		$options['conditions']['User.status'] = 1;
		$options['fields'] = 'User.id, Profile.main_doc, COUNT(Evaluation.id) as papers'; 
		$options['order'] = 'papers ASC'; // With less papers

		return $this->User->find($findType, $options);
	}
	
	/**
	 * Atfer save, foward paper to a apprasier
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function forwardPaper(){
		$appraiser = $this->_getNextAppraiserId();
		if(isset($appraiser['User']['id']) && !empty($appraiser['User']['id'])){
			$this->request->data['Evaluation']['paper_id'] = $this->Paper->id;
			$this->request->data['Evaluation']['user_id'] = $appraiser['User']['id']; // get the first may be null
			$this->request->data['Evaluation']['evaluation_status_id'] = 1;
			$this->Evaluation->create();
			$this->Evaluation->save($this->request->data); // if could not be saved, don't worry
		}
		return array('success' => true);
	}
	
	
	/**
	 * Sends a New Paper Notice to appraiser
	 *
	 */
    protected function sendNewPaperNotice(){
		$this->User->recursive = 0;
		if(isset($this->request->data['Evaluation']['user_id'])){
			$appraiser = $this->User->findById(
				$this->request->data['Evaluation']['user_id'], 
				array(
					'fields' => 'User.email, Profile.name'
				)
			);
		}
		
		if(isset($appraiser) && !empty($appraiser)){

			$email = new CakeEmail();

			$email->viewVars(array(
				'name' => $appraiser['Profile']['name'],
				'paperId' => $this->Paper->id
			));

			$res = $email->template('Paper.paper/new_paper_notice','default')
			    ->emailFormat('html')
				->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
				->to($appraiser['User']['email'])
				->subject('[' . Configure::read('EventTitle') . '] ' . __('There is a new paper waiting for you evaluation'))
				->send();	
		}

		return array('success' => true);
    }

	/**
	 * Send an unique e-mail for a lot of new papers
	 *
	 * @param string $appraisersId 
	 * @return void
	 * @author Thiago Colares
	 */
    protected function sendNewPapersNotices($appraiser = array()){
		if(!empty($appraiser)){	
			$email = new CakeEmail();

			$email->viewVars(array(
				'name' => $appraiser['Profile']['name'],
			));

			$res = $email->template('Paper.paper/new_papers_notice','default')
			    ->emailFormat('html')
				->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
				->to($appraiser['User']['email'])
				->subject('[' . Configure::read('EventTitle') . '] ' . __('There are new papers waiting for you evaluation'))
				->send();	
		}
		return array('success' => true);
    }


	
	
	protected function editExemptionRequirement(){
		if(!empty($this->request->data['Paper']['exempt_main_doc'])){
			$key = array_search($this->request->data['Paper']['exempt_main_doc'], $this->request->data['Paper']);

			if($key == 'author_main_doc'){
				$index = 0;
			} else {
				$index = substr($key, -1);
			}
		} else {
			$index = -1;
		}
		
		$this->request->data['Paper']['ask_for_exemption'] = $index;

		return array('success' => true);
						
	}




    function profile_downloadPaper($idPaper) {
        $options['conditions']['Paper.id'] = $idPaper;

        $options['fields'] = array('Paper.file', 'Paper.mime_type');

        $paper = $this->Paper->find('first', $options);

        $type = $paper['Paper']['mime_type'];

        if (!empty($paper['Paper']['file'])) {
            header("Content-Disposition: attachment; Content-type: $type; filename=paper_$idPaper.doc");
            print stripslashes($paper['Paper']['file']);
        } else {
            $this->Session->setFlash('Arquivo não encontrado', 'default', array('class' => 'error'));
            $this->redirect('/profile/papers/');
        }
    }
    
    function profile_exemptNotification(){
        $user_id = $this->Session->read('Auth.User.id');
        
        $options['joins'] = array(
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'Profile.main_doc = Paper.exempt_main_doc',
                    'Profile.id = '.$user_id
                )
            ),
            array(
                'table' => 'registrations',
                'alias' => 'Registration',
                'type' => 'INNER',
                'conditions' => array(
                    'Registration.user_id = Profile.id'
                )
            ),
        );
        $options['recursive'] = -1;
        $options['fields'] = array('Registration.*');
        
        
        $paper = $this->Paper->find('first', $options);
        
        $message = '';
        $class = '';
        if($paper){
            if($paper['Registration']['payment_entity_payment_method_id'] == 9 && $paper['Registration']['payment_status_id'] == 11){
                $message = 'Solicitação de isenção confirmada pela organização do evento';
                $class = 'success';
            }elseif($paper['Registration']['payment_entity_payment_method_id'] == 9 && $paper['Registration']['payment_status_id'] == 12){
                $message = 'Solicitação de isenção negada pela organização do evento. Caso deseje participar do evento você deve efetuar o pagamento normalmente.';
                $class = 'error';
            }else{
                $message = 'Foi solicitado através do formulário de submissão de trabalhos uma isenção para sua inscrição. Este isenção, porém, ainda não foi confirmada. Por favor, aguarde um posicionamento da comissão.'; 
                $class = 'warning';
            }
        }
        
        return array('message' => $message, 'class' => $class);
    }

    protected function getPaginateEvaluateJoins() {
        $joins = array(
            array(
                'table' => 'paper_types',
                'alias' => 'PaperType',
                'type' => 'INNER',
                'conditions' => array(
                    'Paper.paper_type_id = PaperType.id'
                )
            ),
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'Paper.user_id = Profile.id'
                )
            )
        );

        return $joins;
    }
    
    
    
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
        $evaluationStatuses = $this->Paper->Evaluation->EvaluationStatus->find('list', array('conditions' => array('EvaluationStatus.id !=' => 1)));
        $this->set(compact('evaluationStatuses'));
        return array('success' => true);
    }



    public function profile_evaluate($id) {
        $this->titleP = __('Evaluations');
        $this->title = __('Evaluation');
        
        $this->preSaveHandlers = array(
            array('handler' => 'getPaper'),
            array('handler' => 'getEvaluationStatus')
        );

        $this->saveHandlers = array(
            array('handler' => 'evaluateSaveHandler'),
            array('handler' => 'sendEvaluationResult')
        );

        $this->readHandlers = array(
            array('handler' => 'evaluateReadHandler'),
			array('handler' => 'checkAlreadyEvaluatedHandler')
        );
        
        $this->redirectURLHandler = array('profile' => true, 'action' => 'evaluations');
        
        $this->profile_edit($id);
    }
    
    protected function evaluateSaveHandler(){
        return $this->_save($this->Paper->Evaluation);
    }


	/**
	 * When Appraiser finishes the evaluation, the system reports it to the user
	 *
	 * @return void
	 * @author Thiago Colares
	 */
    protected function sendEvaluationResult(){
		$options = array();
        $options['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'User.id = Paper.user_id'
                )
            ),
            array(
                'table' => 'profiles',
                'alias' => 'Profile',
                'type' => 'INNER',
                'conditions' => array(
                    'Profile.id = User.id'
                )
            ),
        );

		$options['recursive'] = -1;
        $options['conditions']['Paper.id'] = $this->request->data['Evaluation']['paper_id'];
        $options['fields'] = 'Profile.name, User.id, User.email, Paper.id, Paper.title';

		$user = $this->Paper->find('first', $options);
		
		$email = new CakeEmail();

		$email->viewVars(array(
			'name' => $user['Profile']['name'],
			'email' => $user['User']['email'],
			'paperId' => $user['Paper']['id'],
			'paperTitle' => $user['Paper']['title']
		));

		$res = $email->template('Paper.paper/paper_result','default')
		    ->emailFormat('html')
			->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
			->to($user['User']['email'])
			->subject('[' . Configure::read('EventTitle') . '] ' . __('Your Paper Has Been Evaluated'))
			->send();

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
	
	/**
	 * Before edit, check if appraiser has already done it
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	protected function checkAlreadyEvaluatedHandler(){
		if(
			isset($this->request->data['Evaluation']['evaluation_status_id'])
			&&
			!empty($this->request->data['Evaluation']['evaluation_status_id'])
			&&
			$this->request->data['Evaluation']['evaluation_status_id'] == 1
		){
			return array('success' => true);
		} else {
			return array('success' => false, 'message' => __('This Paper has been already evaluated'));
		}
	}

    protected function getPaginateEvaluationJoins() {
        $joins = array(
            array(
                'table' => 'evaluations',
                'alias' => 'Evaluation',
                'type' => 'INNER',
                'conditions' => array(
                    'Paper.id = Evaluation.paper_id'
                )
            ),
            array(
                'table' => 'evaluation_statuses',
                'alias' => 'EvaluationStatus',
                'type' => 'INNER',
                'conditions' => array(
                    'Evaluation.evaluation_status_id = EvaluationStatus.id'
                )
            )
        );

        return $joins;
    }

    protected function getPaginateEvaluationConditions() {
        $conditions['Evaluation.user_id'] = $this->Auth->user('id');
        return $conditions;
    }

    public function profile_evaluations() {
        $this->set('titleP', $this->titleP);
        $this->set('title', $this->title);

        $this->findFields = array(
            array('field' => 'Paper.id', 'title' => __('ID')),
            array('field' => 'Paper.title', 'title' => __('Name')),
            array('field' => 'PaperType.name', 'title' => __('Paper Type')),
            array('field' => 'EvaluationStatus.id', 'title' => __('Paper Status ID'), 'hide' => true),
            array('field' => 'EvaluationStatus.name', 'title' => __('Paper Status')),
        );

        $this->set('findFields', $this->findFields);

        $this->setVar('entity', $this->getPaginateEntity());
        $this->setVar('conditions', $this->getPaginateEvaluationConditions());
        $this->setVar('joins', $this->getPaginateEvaluationJoins());
        $this->setVar('orders', $this->getPaginateOrder());
        $this->setVar('limit', $this->getPaginateLimit());
        $this->setVar('offset', $this->getPaginateOffset());
        $this->setVar('fields', $this->getPaginateFields());
        $this->setVar('baseAdditionalElements', $this->additionalElements);
        $this->setVar('getFieldsForSearcFunction', 'getPaginateFields');

        $res = $this->basePaginate();

        if ($res['success']) {
            $this->set('entity', $this->getVar('entity'));
            $this->set('rowActions', $this->rowActions);
            $this->set('res', $res['items']);
        } else {
            $this->Session->setFlash(__('Error:') . ' ' . $res['message'], 'default', array('class' => 'alert-message error'));
        }
    }


	/**
	 * Nothing can be done if paper is under evaluation
	 *
	 * @author Thiago Colares
	 */
	function notYetBeenEvaluated(){
		if(
			isset($this->request->pass[0])
		){
			$options = array();
			$options['recursive'] = -1;
			$options['conditions']['Paper.id'] = $this->request->pass[0];
			$options['fields'] = 'Paper.paper_status_id';
			$res = $this->Paper->find('first', $options);
			
	        if($res['Paper']['paper_status_id'] == 1){
				return array('success' => true);
			} else {
				switch ($res['Paper']['paper_status_id']) {
					case 2:
						$msg = __('This Paper is Under Evaluation and can not be modified.');
						break;
					case 3:
						$msg = __('This Paper has already been evaluated and can not be modified.');
						break;
				}
				return array('success' => false, 'message' => $msg);
			}
		}
        return array('success' => false);
	}
	
	
	/**
	 * Return papers that is not assigned
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function _getUnassignedPapers($findType = 'all'){
		$options = array();
		$options['joins'] = array(
            array(
                'table' => 'evaluations',
                'alias' => 'Evaluation',
                'type' => 'LEFT',
                'conditions' => array(
                    'Paper.id = Evaluation.paper_id' // Não avaliado
                )
            )
        );

		$options['recursive'] = -1;
		$options['conditions']['Evaluation.paper_id'] = null; // Avaliadores
		
		// Não passe fields como um vetor para find('count').
		// Você somente precisará especificar campos para um DISTINCT count (caso contrário, o count é sempre o mesmo - ditado pelas condições).
		if($findType != 'count'){
			$options['fields'] = 'Paper.id, Paper.author_main_doc, Paper.co_author_main_doc_1, Paper.co_author_main_doc_2, Paper.co_author_main_doc_3, Paper.co_author_main_doc_4'; 
		}
		

		return $this->Paper->find($findType, $options);
	}
	
	
	/**
	 * Assign papers to appraisers
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function admin_assign(){
		if ($this->request->is('post')){
			// Preparing appraisers arrays
			$appraisersTmp = $this->_getNextAppraiserId('all', false);
			if(empty($appraisersTmp)){
				$this->Session->setFlash(__('There is no appraiser to be assign. You must register appraisers before continue.'), 'default', array('class' => 'alert-message notice'));
                $this->redirect(array('admin' => true, 'controller' => 'papers', 'action' => 'assign'));
			}
			$appraisersSort = array();
			foreach($appraisersTmp as $appraiser){
				$appraisers[$appraiser['User']['id']] = $appraiser;
				$appraisersSort[$appraiser['User']['id']] = $appraiser[0]['papers'];
			}
			
			$papers = $this->_getUnassignedPapers();
			$this->request->data = array();

			foreach($papers as $paper){
				
				reset($appraisersSort);
				$appKey = false;
				
				// Paper Author and Co-Author's Main Docs
				$mainDocs = array(
		            (isset($paper['Paper']['author_main_doc']) ? $paper['Paper']['author_main_doc'] : null),
		            $paper['Paper']['co_author_main_doc_1'],
		            $paper['Paper']['co_author_main_doc_2'],
		            $paper['Paper']['co_author_main_doc_3'],
		            $paper['Paper']['co_author_main_doc_4']
		        );
		
				// Check if appraiser is author or co-author
				foreach($appraisersSort as $aKey => $aItem){
					if(!in_array($appraisers[$aKey]['Profile']['main_doc'], $mainDocs)){
						$appKey = $aKey;
						break;
					}
				}
				if($appKey){
					// Sum papers
					$appraisers[$appKey][0]['papers']++;
					$appraisersSort[$appKey]++;

					$this->request->data[] = array(
						'Evaluation' => array(
							'paper_id' => $paper['Paper']['id'],
							'user_id' => $appKey,
							'evaluation_status_id' => 1
						)	
					);

					// Sort array
					asort($appraisersSort);
				}
				// else.. nothing happens :S Are every appraisers are co-authors for each papers? gzus...
				
			}
			
	        $res = $this->_save($this->Evaluation);
			if($res['success']){
				$this->Session->setFlash('All Papers has been successfully assigned.', 'default', array('class' => 'alert-message success'));
			} else {
				$this->Session->setFlash($res['message'], 'default', array('class' => 'alert-message error'));
			}

		}else{
			$this->set('unassignedPapers', $this->_getUnassignedPapers('count'));
		}
	}
	
	
	/**
	 * Return how many registrations per entity and its status
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	public function admin_paperTypeReport(){
		$options = array();
		$options['recursive'] = -1;
		$options['joins'] = array(
            array(
                'table' => 'paper_types',
                'alias' => 'PaperType',
                'type' => 'INNER',
                'conditions' => array(
                    'Paper.paper_type_id = PaperType.id'
                )
            ),
        );
		$options['group'] = 'Paper.paper_type_id';
		//$options['conditions'] = 'Registration.status';
        $options['fields'] = 'PaperType.id,PaperType.name, COUNT(*) as paperss';

		$papers = $this->Paper->find('all', $options);
		$tmp = array();
		foreach($registrations as $registration){
			$tmp[$registration['PaymentEntity']['id']]['name'] = $registration['PaymentEntity']['name'];
			$tmp[$registration['PaymentEntity']['id']]['status'][] = array(
				'status' => $registration['PaymentStatus']['name'],
				'registrations' => $registration[0]['registrations']
			);
		}
		return $tmp;
	}
	
	
	
	/**
	 * Users must be informed when payment is finished. Its a feature.
	 *
	 * @author Thiago Colares
	 */
	// protected function sendNewPaperNotice(){
	// 	$this->User->recursive = 0;
	// 	if(isset($this->request->data['Evaluation']['user_id'])){
	// 		$appraiser = $this->User->findById(
	// 			$this->request->data['Evaluation']['user_id'], 
	// 			array(
	// 				'fields' => 'User.email, Profile.name'
	// 			)
	// 		);
	// 	}
	// 
	// 	if(isset($appraiser) && !empty($appraiser)){
	// 
	// 		$email = new CakeEmail('gmail');
	// 
	// 		$email->viewVars(array(
	// 			'name' => $appraiser['Profile']['name'],
	// 			'paperId' => $this->Paper->id
	// 		));
	// 
	// 		$res = $email->template('Paper.paper/new_paper_notice','default')
	// 		    ->emailFormat('html')
	// 			->from(array(Configure::read('NoReplyEmail') => Configure::read('EventTitle')))
	// 			->to($appraiser['User']['email'])
	// 			->subject('[' . Configure::read('EventTitle') . '] ' . __('There is a new paper waiting for you evaluation'))
	// 			->send();	
	// 	}
	// 
	// 	return array('success' => true);
	//     }
	
	
	
}
