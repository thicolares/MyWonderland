<?php

class PaperResearchLine extends PaperAppModel{
	public $name = 'PaperResearchLine';

	public $belongsTo = array('Paper.Paper','Paper.ResearchLine');

	public $useTable = 'papers_research_lines';
}
