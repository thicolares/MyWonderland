<?php

class Paper extends PaperAppModel{
	public $name = 'Paper';
	
	public $hasMany = array(
		'PaperResearchLine' => array(
			'className' => 'Paper.PaperResearchLine',
			'dependent' => true
		),
        'Evaluation' => array(
			'className' => 'Paper.Evaluation',
			'dependent' => true
		)
	);
	public $belongsTo = array('Paper.PaperType', 'Paper.PaperStatus', 'User');

    public function beforeSave($options = array()) {
//        $this->data['Paper']['submittedfile'] = array(
//            'name' => 'conference_schedule.pdf',
//            'type' => 'application/pdf',
//            'tmp_name' => 'C:/WINDOWS/TEMP/php1EE.tmp',
//            'error' => 0,
//            'size' => 41737,
//        );
        if(!empty($this->data['Paper']['submittedfile'])){
			
			$file = $this->data['Paper']['submittedfile'];

	        if(!empty($file['name'])){
	            $fp = fopen($file['tmp_name'], "rb");
	            $content = fread($fp, $file['size']);
	            $content = addslashes($content);
	            fclose($fp);

	            $this->data['Paper']['file'] = $content;
	            $this->data['Paper']['mime_type'] = $file['type'];
	        }
		}


        return true;
    }
    
    public $validate = array(
        'title' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				//'message' => 'You have not entered a title.'
				'message' => 'Você não digitou o título.'
			)
		),
        'abstract' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				// 'message' => 'You have not entered an abstract.'
				'message' => 'Você não digitou o resumo.'
			)
		),
        'submittedfile' => array(
			'validateMime' => array(
				'rule' => 'validateMime',
				'allowEmpty' => true,
				// 'message' => 'This file format is not allowed.',
				'message' => 'Este tipo de arquivo não é permitido.',
			),
			'notEmptyFile' => array(
				'on' => 'create',
				'rule' => 'notEmptyFile',
				// 'message' => 'You must upload a file.',
				'message' => 'Você precisa enviar um arquivo.',
				'allowEmpty' => true,
			),
			// 'notEmpty' => array(
			// 	'on' => 'create',
			// 	'rule' => 'notEmpty',
			// 	'message' => 'You must upload a file.',
			// 	'allowEmpty' => true,
			// ),
			
		),
				
			
		'co_author_name_1' => array(
			'rule' => array('checkCompleteData', 1),
			// 'message' => 'Incomplete information. As you entered the Name, you must also inform the CPF and vice versa.',
			'message' => "Uma vez preenchido o CPF, deve-se informar o Nome.\n Estes dados são obrigatórios APENAS para Pedir Isenção.",
		),
		'co_author_name_2' => array(
			'rule' => array('checkCompleteData', 2),
			// 'message' => 'Incomplete information. As you entered the Name, you must also inform the CPF and vice versa.',
			'message' => "Uma vez preenchido o CPF, deve-se informar o Nome.\n Estes dados são obrigatórios APENAS para Pedir Isenção.",
		),
		'co_author_name_3' => array(
			'rule' => array('checkCompleteData', 3),
			// 'message' => 'Incomplete information. As you entered the Name, you must also inform the CPF and vice versa.',
			'message' => "Uma vez preenchido o CPF, deve-se informar o Nome.\n Estes dados são obrigatórios APENAS para Pedir Isenção.",
		),
		'co_author_name_4' => array(
			'rule' => array('checkCompleteData', 4),
			// 'message' => 'Incomplete information. As you entered the Name, you must also inform the CPF and vice versa.',
			'message' => "Uma vez preenchido o CPF, deve-se informar o Nome.\n Estes dados são obrigatórios APENAS para Pedir Isenção.",
		),
		
		
		/**
		 * CPF validator
		 */
		'co_author_main_doc_1' => array(
			'numeric' => array(
			    'rule'    => 'numeric',
			    'message' => 'Please supply your CPF only with numbers.',
				'allowEmpty' => true
			),
			'minLength' => array(
			    'rule' => array('minLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			),
			'maxLength' => array(
			    'rule' => array('maxLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			)
		),
		'co_author_main_doc_2' => array(
			'numeric' => array(
			    'rule'    => 'numeric',
			    'message' => 'Please supply your CPF only with numbers.',
				'allowEmpty' => true
			),
			'minLength' => array(
			    'rule' => array('minLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			),
			'maxLength' => array(
			    'rule' => array('maxLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			)
		),
		'co_author_main_doc_3' => array(
			'numeric' => array(
			    'rule'    => 'numeric',
			    'message' => 'Please supply your CPF only with numbers.',
				'allowEmpty' => true
			),
			'minLength' => array(
			    'rule' => array('minLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			),
			'maxLength' => array(
			    'rule' => array('maxLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			)
		),
		'co_author_main_doc_4' => array(
			'numeric' => array(
			    'rule'    => 'numeric',
			    'message' => 'Please supply your CPF only with numbers.',
				'allowEmpty' => true
			),
			'minLength' => array(
			    'rule' => array('minLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			),
			'maxLength' => array(
			    'rule' => array('maxLength', 11),
			    'message' => 'Inform the CPF with 11 digits.'
			)
		),
    );

	/**
	 * If co_author_name_N has been filled, its main doc is mandatory and vice-versa.
	 *
	 * @param string $check field name
	 * @param string $index field index, set at validation array
	 * @return void
	 * @author Thiago Colares
	 */
    function checkCompleteData($field, $index){
        if(
			(
				(
					empty($this->data['Paper']['co_author_name_' . $index])
					xor
					empty($this->data['Paper']['co_author_main_doc_' . $index])
				)
				&&
				$this->data['Paper']['ask_for_exemption'] == $index
			)
			||
			(
				empty($this->data['Paper']['co_author_name_' . $index])
				&&
				!empty($this->data['Paper']['co_author_main_doc_' . $index])
			)
		){
			return false;
		} else {
			return true;
		}	
	}

    
    function validateMime(){
		if(!empty($this->data['Paper']['submittedfile']['size'])){
			return true;
			$file = $this->data['Paper']['submittedfile'];
	        if($file['type'] == 'application/msword')
	            return true;
	        else
	            return false;
		} else {
			return true;
		}
    }

	function notEmptyFile(){
		if(empty($this->data['Paper']['submittedfile']['size']))
			return false;
		else
			return true;
	}


}
