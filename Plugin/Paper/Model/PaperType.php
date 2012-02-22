<?php

class PaperType extends PaperAppModel{
	public $name = 'PaperType';
	
	public $belongsTo = array('Paper.Paper');
    
    /**
     * TODO: Trazer do banco
     * @return int
     */
    public function getWorkshopId(){
        return 1;
    }
    
    /**
     * TODO: trazer do banco
     * @return int
     */
    public function getShortCourseId(){
        return 2;
    }
}
