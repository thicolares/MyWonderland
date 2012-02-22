<?php
/**
 * Print automatic an list ordenable
 *
 * How to use:
 * 	Set some variables at beforeFilter():
 * 	(1) set title (singular) and titleP (plural) 
 *	(2) findFields
 *	Example
 *	
 *	 public function beforeFilter() {
 *	     parent::beforeFilter();
 *	
 *	     $this->Auth->allow('add','login');
 *	
 *		 $this->title  = __('User');
 *	     $this->titleP = __('Users');
 *	
 *		 $this->findFields = array(
 *		 	array('field' => 'User.id', 'title' => __('ID',true)),
 *			array('field' => 'Role.name', 'title' => __('Role',true)),
 *			array('field' => 'User.status', 'title' => __('Status',true)), // render
 *		);
 *	 }
 *
 *	FIELDS FOR OTHER RELATED MODEL
 *	If you want to list an field from another model, for now, implement getPaginateJoins().
 *	For example, want to show Role.name
 *	 protected function getPaginateJoins() {
 *	     $joins = parent::getPaginateJoins();
 *	    
 *	     $specificJoins = array(
 *	         array(
 *					'table' => 'roles',
 *	             'alias' => 'Role',
 *	             'type' => 'INNER',
 *	             'conditions' => array(
 *	                 'User.role_id = Role.id',
 *	             )
 *	         ),
 *	     );
 *	     return array_merge($joins, $specificJoins);
 *	 } 
 *
 *	CUSTOM ACTION AT THE TOP OF THE TABLE
 * 		already does, must doc
 *
 *	CUSTOM ACTION FOR EACH ROW
 *		alreary does, must doc
 *
 * @author Thiago Colares
 */

App::uses('AppHelper', 'View/Helper');

class AdminIndexListHelper extends AppHelper {
	
	var $actions;
	var $findFields;
	var $title;
	var $rowActions = null;
	var $resHtml = '';
	var $data = array();
	
	/**
	 * Each element for this array
	 *
	 * @string	name 	required	Name of template file in the/app/View/Elements/ folder
	 * @array 	data 	optional	Array of data to be made available to the rendered view (i.e. the Element)
	 * @array 	options optional	Array of options
	 */
	var $elements = array();
	
    /**
     * Other helpers used by this helper
     *
     * @var array
     * @access public
     */
    var $helpers = array(
        'Html',
        'Layout',
        'Paginator'
    );
    
    protected function getView() {
        // return ClassRegistry::getObject('View'); // cake 1.3
		return $this->_View; // cake 2.0
    }

	/*
		TODO Replace __set()
	*/
	public function setTitle($title){
		$this->title = $title;
	}
	public function setActions($actions = array()){
		$this->actions = $actions;
	}
	public function setRowActions($rowActions = array()){
		$this->rowActions = $rowActions;
	}
	public function setElements($elements = array()){
		$this->elements = $elements;
	}
	

    
    /**
     * Before render callback. Called before the view file is rendered.
     *
     * @return void
     */
    public function beforeRender() {
	
		$this->resHtml = '';

        // Fetching vars from view
        $View = $this->getView();

        // if($View->action == 'admin_index' || $View->action == 'profile_index' || $View->action == 'profile_evaluations' || $View->action == 'admin_exemption'){
            $this->titleForLayout = isset($View->viewVars['titleP']) ? $View->viewVars['titleP'] : '';
            $this->titleSingular = isset($View->viewVars['title']) ? $View->viewVars['title'] : '';
            $this->data =  isset($View->viewVars['res']) ? $View->viewVars['res'] : '';
			$this->rowActions = isset($View->viewVars['rowActions']) ? $View->viewVars['rowActions'] : null;
			$this->findFields = isset($View->viewVars['findFields']) ? $View->viewVars['findFields'] : '';
            $this->controller = isset($this->params['controller']) ? $this->params['controller'] : '';
            $this->name = isset($View->viewVars['entity']) ? $View->viewVars['entity'] : '';	
        // }
		$options['url']= array(
			'plugin'=>null,
			'action'=>'index',
            $this->request->prefix => true
		);

		if(isset($View->params['prefix'])){
			$options['url'] = array(
				'controller' => 'beach'
			);
			
		}
        
        //debug($options);


		

		// $paginator->options(array('url'=> array(
		// 'controller' => 'categories', 
		//   'action' => 'index',
		// 'slug'=>$this->params['slug']),
		// 'id'=>$this->params['id'])
		//   ));
		$this->Paginator->options($options);

    }

	/**
	 * Append set elements
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _appendElements(){
		$elHtml = '';
		if(!empty($this->elements)){
			foreach($this->elements as $element){
				if(!isset($element['data'])) $element['data'] = array();
				if(!isset($element['options'])) $element['options'] = array();
				$elHtml .= $this->_View->element($element['name'],$element['data'], $element['options']);
			}
		}
		$this->resHtml .= $elHtml;
	}


	/**
	 * Build the header html set
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _buildHeader(){
		// Page title. Title for Layout
        $this->resHtml .= "<h2>" . (isset($this->title) ? $this->title : $this->titleForLayout) . "</h2>";
	}
	
	/**
	 * Build action set above de head
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _buildActions(){
		// Actions below the title. Default is "Add ..."
		if(!isset($this->actions)){
			$actionsDivContent = $this->Html->link(__('Add') . ' ' . $this->titleSingular, array('plugin' => false, $this->request->prefix => true, 'controller' => $this->controller, 'action' => 'add'), array('class' => 'btn'));

			$this->resHtml .= $this->Html->div('well', $actionsDivContent);
		} else {
			if(!empty($this->actions)){
				$actionsDivContent = implode(" ", $this->actions);
				$this->resHtml .= $this->Html->div('well', $actionsDivContent);
			}
		}
	}

	

	/**
	 * Build pagintator counter and a resume text, like "Page %page% of %pages%, showing..." on the footer
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _buildTable(){
		// If exists rows to be showned. just do it! 

        if(!empty($this->data)){
   
            // TABLE HEADERS
            $tableHeadersArr = '';
            foreach($this->findFields as $key => $item){

                /**
                 * @todo check if want to sort
                 */
				if(!isset($item['hide']) || !$item['hide']){
					if(!isset($item['sort']) || $item['sort']){
						$tableHeadersArr[] = $this->Paginator->sort($item['field'], $item['title']);//, array('model' => $fieldPieces[0]));
					} else {
						$tableHeadersArr[] = $item['title'];
					}
				}
			}
			if($this->rowActions === null || !empty($this->rowActions)){
				$tableHeadersArr[] = __('Actions', true);
			}
            $tableHeaders =  $this->Html->tableHeaders($tableHeadersArr);
	        
	        // TABLE ROWS
	        $tableRows = array();
	        foreach ($this->data as $dataItem) {
		
				// Rows
				$tableRowsArr = '';
                foreach($this->findFields as $key => $item){
					if(!isset($item['hide']) || !$item['hide']){
						/**
	                     * @todo check handlers!!!!!! the if elseif below may be wrong!
	                     */
						$fieldPieces = explode(".", $item['field']); // [0] Model  [1] Field name

						if(isset($item['renderer'])){
							$tableRowsArr[] = $this->{$item['renderer']}($dataItem[$fieldPieces[0]][$fieldPieces[1]]);
						}elseif (isset($item['customRenderer'])){
							$tableRowsArr[] = $this->_View->{$item['customRenderer']}($dataItem[$fieldPieces[0]][$fieldPieces[1]]);
						} elseif(isset($item['taxonomy']) && $item['taxonomy']) {
							if(isset($dataItem[$fieldPieces[0]][$fieldPieces[1]])) //REMOVE IT! todos tem que ter isto
								$tableRowsArr[] = $dataItem[$fieldPieces[0]][$fieldPieces[1]][0]['name'];
							else
								$tableRowsArr[] = __('-');
						} else {
							$tableRowsArr[] = $dataItem[$fieldPieces[0]][$fieldPieces[1]];
						}
					}

                }
	            
		
				// Action cells
				$rowActions = '';
				// If is
				if($this->rowActions === null){

		            $rowActions .= $this->Html->link(
						__('Edit', true), 
						array('plugin' => null, 'controller' => $this->controller, 
						'action' => 'edit' . DS . $dataItem[$this->name]['id'], $this->request->prefix => true)
						//array('plugin' => $this->'registration', 'controller' => $this->controller, 'action' => 'admin_edit', $dataItem[$this->name]['id'])
					);

					$rowActions .= ' ' . $this->Html->link(
						__('Delete'), 
                        array('plugin' => false, $this->request->prefix => true, 'controller' => $this->controller, 'action' => 'delete'.DS.$dataItem[$this->name]['id']),
						//DS . $this->request->prefix . DS . $this->controller . DS . 'delete' . DS . $dataItem[$this->name]['id'],
						//array('plugin' => 'registration', 'controller' => $this->controller, 'action' => 'admin_delete', $dataItem[$this->name]['id']),
						null,
						__('This action can not be undone! Are you sure?')
					);
					$tableRowsArr[] = $rowActions;
					
				} elseif(!empty($this->rowActions)) {
					
					$printActions = array();
					foreach($this->rowActions as $rowAction){
						
						$conditionsRes = true;
						if(!empty($rowAction['conditions'])){
							foreach ($rowAction['conditions'] as $condition => $value) {
								$conditionPieces = explode(".", $condition); // [0] Model  [1] Field name
								if(!in_array($dataItem[$conditionPieces[0]][$conditionPieces[1]], $value)){
									$conditionsRes = false;
									break;
								}
							}
						}
						
						if(!$conditionsRes){
							$printActions[] = $rowAction['title']; // no link
						} else {
							if(empty($rowAction['id'])){
								$rowAction['url']['action'] = $rowAction['url']['action'] . DS . $dataItem[$this->name]['id'];
							} else {
								$ids = null;
								foreach($rowAction['id'] as $anId){
									$idPieces = explode(".", $anId); // [0] Model  [1] Field name
									$ids[] = $dataItem[$idPieces[0]][$idPieces[1]];
								}

								$rowAction['url']['action'] = $rowAction['url']['action'] . DS . implode(DS, $ids);
							}

							$printActions[] = $this->Html->link(
								$rowAction['title'],	// title
								$rowAction['url'], //$rowAction['url'],	// url
								isset($rowAction['options']) ? $rowAction['options'] : null	,					// options
								isset($rowAction['confirm']) ? $rowAction['confirm'] : null						// confirm
							);
						}
						
					}
					$rowActions .= implode(' ', $printActions);
					$tableRowsArr[] = $rowActions;
				}
				$tableRows[] = $tableRowsArr;


	            
	        }
	        $tableRows = $this->Html->tableCells($tableRows);
            
            // Full table
            $this->resHtml .= '<table cellpadding="0" cellspacing="0" class="ui-corner-all"><tbody>' . $tableHeaders . $tableRows . $tableHeaders . '</tbody></table>';
        
        } else { // no data to show
            $this->resHtml .= '<p class="empty">' . __('Up to now, nothing has been registered here...') . '</p>';
        }
	}
	
    
	/**
	 * Build pagintator counter and a resume text, like "Page %page% of %pages%, showing..." on the footer
	 *
	 * @return void
	 * @author Thiago Colares
	 */
	private function _buildFooterCounter(){
		$pagNumbers = $this->Paginator->numbers(); 
		$pagCounter = $this->Paginator->counter(
			array(
				'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
			)
		);

        $this->resHtml .= $this->Html->div('paging', $pagNumbers);
		$this->resHtml .= $this->Html->div('counter', $pagCounter);
	}
	

	/**
	 * Build the html code that shows a orderable list
	 *
	 * @return string
	 * @author Thiago Colares
	 */
    public function buildHtml(){

		$this->_buildHeader();
		$this->_buildActions();
		$this->_buildTable();
		$this->_buildFooterCounter();
		$this->_appendElements();
		
        // Return full res html
        return $this->Html->div('example index', $this->resHtml);
    }
    
    /**
	 * YesNo Renderer
	 *
	 * @param int $data Date value
	 * @return string Yes or No
	 * @author Rafael Ávila
	 */
    private function yesNoRenderer($data){
		if($data) return __('Yes');
        else return __('No');
    }

	/**
	 * Translate date for view
	 *
	 * @todo Build rendere options
	 *
	 * @param string $date Date value
	 * @param string $format Format. Default 'd/m/Y'. See more formats at http://php.net/manual/en/function.date.php
	 * @return void
	 * @author Thiago Colares
	 */
    private function date4View($date){
		return $this->datetime4View($date, 'd/m/Y');
    }

	/**
	 * Translate time for view
	 *
	 * @todo Build rendere options
	 *
	 * @param string $datetime 
	 * @return void
	 * @author Thiago Colares
	 */
    protected function time4View($datetime) {
		return $this->datetime4View($datetime, 'H:i');
    }

	/**
	 * Translate time for view
	 *
	 * @todo Build rendere options
	 *
	 * @param string $datetime 
	 * @return void
	 * @author Thiago Colares
	 */
    protected function datetime4View($datetime, $format = 'd/m/Y H:i') { // $options = array('format' => 'd/m/Y')) {
        if (!empty($datetime))
            return date($format, strtotime($datetime));
        else
            return null;
    }


/**
 * After render callback. Called after the view file is rendered
 * but before the layout has been rendered.
 *
 * @return void
 */
    public function afterRender() {
    }
/**
 * Before layout callback. Called before the layout is rendered.
 *
 * @return void
 */
    public function beforeLayout() {
    }
/**
 * After layout callback. Called after the layout has rendered.
 *
 * @return void
 */
    public function afterLayout() {
    }
/**
 * Called after LayoutHelper::setNode()
 *
 * @return void
 */
    public function afterSetNode() {
        // field values can be changed from hooks
        $this->Layout->setNodeField('title', $this->Layout->node('title') . ' [Modified by RegistrationHelper]');
    }
/**
 * Called before LayoutHelper::nodeInfo()
 *
 * @return string
 */
    public function beforeNodeInfo() {
        return '<p>beforeNodeInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeInfo()
 *
 * @return string
 */
    public function afterNodeInfo() {
        return '<p>afterNodeInfo</p>';
    }
/**
 * Called before LayoutHelper::nodeBody()
 *
 * @return string
 */
    public function beforeNodeBody() {
        return '<p>beforeNodeBody</p>';
    }
/**
 * Called after LayoutHelper::nodeBody()
 *
 * @return string
 */
    public function afterNodeBody() {
        return '<p>afterNodeBody</p>';
    }
/**
 * Called before LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    public function beforeNodeMoreInfo() {
        return '<p>beforeNodeMoreInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    public function afterNodeMoreInfo() {
        return '<p>afterNodeMoreInfo</p>';
    }
}
?>