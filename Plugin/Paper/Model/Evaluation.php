<?php

class Evaluation extends PaperAppModel {

    public $name = 'Evaluation';
    public $belongsTo = array('Paper.Paper', 'User', 'Paper.EvaluationStatus');
    
    public $validate = array(
        'comments' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have not entered your comments.'
            )
        ),
//        'evaluation_status_id' => array(
//            'evaluationStatus' => array(
//                'rule' => 'evaluationStatus',
//                'message' => 'You must approve or reject this paper.'
//            )
//        )
    );

}
