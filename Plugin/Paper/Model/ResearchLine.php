<?php

class ResearchLine extends PaperAppModel{
	public $name = 'ResearchLine';
	
	public $hasMany = array('Paper.PaperResearchLine');
}
