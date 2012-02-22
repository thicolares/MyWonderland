<div class="issue index">
	

    <h2><?php print __('Add') . ' ' . $title; ?></h2>

    <?php
    echo $this->Form->create('Protocol', array('inputDefaults' => array('label' => false, 'div' => false)));

	print $this->Form->hidden('Protocol.issue_id', array('value' => $this->Session->read('Issue.id')));

	// Protocol notes
    print $this->Form->input(
        'protocol_number', 
        array(
            'label' => __('Protocol Number'),
            'div' => 'clearfix required',
        )
    );

	// Attendent Name
    print $this->Form->input(
        'attending', 
        array(
            'label' => __('Attendent Name'),
            'div' => 'clearfix required',
        )
    );

	// Protocol notes
    print $this->Form->input(
        'notes', 
        array(
            'label' => __('Notes'),
            'div' => 'clearfix required',
			'class' => 'span8',
			'rows' => 4
        )
    );

	// Period range date
	$protBeginDate = $this->Form->input('Protocol.begin_date.date', array(
		'label' => __('Attending Duration'),
		'class' => "small",
		'type' => 'text'
	));
	
	$protBeginTime = $this->Form->input('Protocol.begin_date.time', array(
		'class' => "mini",
		'type' => 'text'
	));
	
	$protEndTime = $this->Form->input('Protocol.end_date.time', array(
		'class' => "mini",
		'type' => 'text',

	));
	
	/*
		TODO Set some default date and time values!
	*/
	
	echo $this->Html->div('clearfix required',
		$this->Html->div('inline-inputs',
			"$protBeginDate $protBeginTime " . __('to') . " $protEndTime" 
		)
	);

	echo $this->Html->div('actions',
		$this->Form->submit(__('Save'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
            'action' => 'index',
			'controller' => 'issues',
			'plugin' => false,
			'profile' => true
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();
	?>

	
	
	<!-- LIST -->
	<?php 
				//         $this->AdminIndexList->setRowActions(
				//             array(
				// 	            array(
				//                     'title' => __('Protocols'),
				//                     'url' => array('plugin' => false, 'profile' => true, 'controller' => 'protocols', 'action' => 'add'),
				//                     'id' => array('Issue.id')
				//                 ),
				//                 array(
				//                     'title' => __('Edit'),
				//                     'url' => array('plugin' => false, 'profile' => true, 'controller' => 'issues', 'action' => 'edit'),
				//                     'id' => array('Issue.id')
				//                 ),
				// array(
				// 	'title' => __('Delete', true),
				// 	'url' => array('plugin' => false, 'profile' => true, 'controller' => 'issues', 'action' => 'delete'),
				//                     'id' => array('Issue.id'),
				// 	'confirm' => __('This action can not be undone! Are you sure?')
				// ),
				//             )
				//         );

		$this->AdminIndexList->setActions();
        print $this->AdminIndexList->buildHtml(); 
    ?>
</div>