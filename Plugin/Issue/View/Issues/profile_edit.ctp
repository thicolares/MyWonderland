<div class="issue form">

    <h2><?php print __('Edit') . ' ' . $title; ?></h2>
     	
    <?php
    echo $this->Form->create('Issue', array('inputDefaults' => array('label' => false, 'div' => false)));

	echo $this->Form->hidden('Issue.id', array('value' => $this->params->pass[0]));

	// Available companies
	echo $this->Html->div('clearfix', 
		$this->Form->label('CompanyIssue.0.company_id', __('Send to')) .
		$this->Form->select('CompanyIssue.0.company_id', $companies, array('empty' => false, 'div' => 'clearfix'))
	);

	// Issue description
    print $this->Form->input(
        'description', 
        array(
            'label' => __('Description'),
            'div' => 'clearfix required',
			'class' => 'span8',
			'rows' => 3
        )
    );
        
	echo $this->Html->div('actions',
		$this->Form->submit(__('Save'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();
	?>
</div>