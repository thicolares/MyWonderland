<?php
/**
 * Issue
 *
 * @package issue
 * @author Thiago Colares
 */
class Issue extends IssueAppModel{
	public $name = 'Issue';
	
	public $hasMany = array('Issue.CompanyIssue', 'Issue.Protocol');
}
