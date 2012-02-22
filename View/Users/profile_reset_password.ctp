<div class="users form">
    <h2><?php print __('Reset password'); ?></h2>
	<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false, 'div' => false))); ?>


    <?php
        echo $this->Form->input('User.current_password', array(
			'label' => __('Current Password'),
			'type' => 'password',
			'value' => '',
			'div' => 'clearfix required'
		));
        echo $this->Form->input('User.password', array(
			'label' => __('New Password'), 
			'type' => 'password',
			'value' => '',
			'div' => 'clearfix required',
			'after' => '<span class="help-block">' . __('At least 6 characters! Do not use blank spaces.') . '</span>'
		));
		echo $this->Form->input('User.confirm_password', array(
			'label' => __('Confirm', true), 
			'type' => 'password',
			'value' => '',
			'div' => 'clearfix required',
			'after' => '<span class="help-block">' . __('Please, repeat your new password to make sure I\'ve typed it right.') . '</span>'
		));
		
		echo $this->Html->div('actions',
			$this->Form->submit(__('Save Password'), array('class' => 'btn primary')) . ' ' . 
			$this->Html->link(__('Cancel', true), array(
				'plugin' => false,
				'action' => 'dashboard',
				'controller' => 'systems'
	        ), array(
	            'class' => 'btn',
	        ))
		);
    ?>


    
</div>