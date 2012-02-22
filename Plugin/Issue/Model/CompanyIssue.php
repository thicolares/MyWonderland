<?php
/**
 * Company Issue
 *
 * @package issue
 * @author Thiago Colares
 */
class CompanyIssue extends IssueAppModel{
	public $name = 'CompanyIssue';

	public $belongsTo = array('Issue.Company','Issue.Issue');

	public $useTable = 'companies_issues';
}
