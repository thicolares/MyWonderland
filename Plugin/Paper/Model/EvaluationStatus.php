<?php

class EvaluationStatus extends PaperAppModel{
	public $name = 'EvaluationStatus';
	
	public $hasMany = array(
		'Evaluation' => array(
			'className' => 'Paper.Evaluation'
		)
	);
}
