<?php
	//echo $this->Html->script('/event/js/event_owner_combo');
?>
<div class="users form">
    <h2><?php print __('Add User'); ?></h2>
	<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false, 'div' => false))); ?>

	<?php

		echo $this->Form->input(
			'role_id', 
			array('type' => 'select', 'label' => __('Role'), 'div' => 'clearfix', 'empty' => false, 'options' => $roles)
		);
        
		echo $this->Form->input('Profile.name', array('label' => __('Full Name'),'div' => 'clearfix'));
	
		echo $this->Form->input('User.email', array('label' => __('E-mail'), 'div' => 'clearfix'));
		echo $this->Form->input('User.password', array('label' => __('System Password'), 'div' => 'clearfix'));
		echo $this->Html->div('actions',
			$this->Form->submit(__('Save'), array('class' => 'btn primary')) . ' ' . 
			$this->Html->link(__('Cancel', true), array(
	            'action' => 'index',
	        ), array(
	            'class' => 'btn',
	        ))
		);
	?>
</div>