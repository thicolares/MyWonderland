<div class="broadcast form">
<h2><?php echo __('Do A Broadcast');?></h2>

<?php
	echo $this->Form->create('Broadcast', array('inputDefaults' => array('label' => false, 'div' => false)));
   	echo $this->Html->div('required-tip', __('* Means the box must be filled in before sending'));
	echo $this->Form->hidden('Broadcast.send', array('value' => false));
	echo $this->Form->hidden('Broadcast.edit', array('value' => false));
	echo $this->Html->div('clearfix', 
		$this->Form->label('Broadcast.filter', __('Send to')) .
		$this->Form->select('Broadcast.filter', $formOptions, array('class' => 'span8', 'empty' => false, 'div' => 'clearfix')) .
		'<span class="help-block">' . __('Payment Status (How many registrations on this status), The Payment Entity') . '</span>'
	);

	// echo $this->Html->div('clearfix', 
	// 	$this->Form->label('Broadcast.copy', __('Copy')) .
	// 	$this->Html->div(
	// 		'input', 
	// 		$this->Form->input('Broadcast.copy', array(
	// 		    'before' => '<ul class="inputs-list"><li>',
	// 		    'after' => '</li></ul>',
	// 		    'separator' => '</li><li>',
	// 			'multiple' => 'checkbox',
	// 			'legend' => false,
	// 		    'options' => array(true => __('Send a copy to %s', $this->Session->read('Auth.User.email')))
	// 		))
	// 	)
	// );
	
	echo $this->Form->input('Broadcast.subject', array('class' => 'span12','maxlength' => 100, 'size' => 40, 'label' => __('Subject'), 'div' => 'clearfix',
	'after' => '<span class="help-block">' . __('The text <em><strong>%s</strong></em> will be automatically placed in the beginning of the subject.', '[' . Configure::read('EventTitle') . ']') . '</span>'
	));

	echo $this->Html->div('clearfix', 
		$this->Form->label('Broadcast.filter', __('Wildcards')) .
		"___NAME___ " . __('Name of registered') . 
		'<span class="help-block">' . __('Copy and Past the wildcards above into "Message" area to write usefull data.') . '</span>'
	);
	
	echo $this->Form->input('Broadcast.message', array('class' => 'mceEditor span12', 'cols' => 200, 'rows' => 10, 'label' => __('Message'), 'div' => 'clearfix'));
	
	echo $this->Html->div('clearfix', 
		$this->Form->label('Bli', '&nbsp;') .
		'Atenciosamente,<br>
			Equipe ' . Configure::read('EventTitle') . 
		'<span class="help-block">' . __('The default signature above will be appended at the end of the message.') . '</span>'
	);
	

	 // debug($this->Session->read('Auth.User.email'));
	
	echo $this->Html->div('actions',
		$this->Form->submit(__('Preview Message'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Discard', true), array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => false,
			'controller' => 'systems',
            'action' => 'dashboard',
        ), array(
            'class' => 'btn',
        ))
	);
	echo $this->Form->end();
?>

</div>