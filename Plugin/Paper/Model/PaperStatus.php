<?php

class PaperStatus extends PaperAppModel{
	public $name = 'PaperStatus';
	
	public $hasMany = array('Paper.Paper');
}
