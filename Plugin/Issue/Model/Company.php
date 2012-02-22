<?php
/**
 * Company
 *
 * @package issue
 * @author Thiago Colares
 */
class Company extends IssueAppModel{
	public $name = 'Company';
	
	public $hasMany = array(
		'CompanyIssue' => array(
			'className' => 'Issue.CompanyIssue',
			'dependent' => true
		)
	);
}