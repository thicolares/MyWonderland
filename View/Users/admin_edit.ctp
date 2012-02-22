<div class="users form">
    <h2><?php print __('Edit User'); ?></h2>
	<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false, 'div' => false))); ?>

		<div id="user-main">
		<?php

			echo $this->Form->hidden('Profile.id');
			echo $this->Form->hidden('User.id');

			echo $this->Form->input(
				'role_id', 
				array('type' => 'select', 'label' => __('Role'), 'div' => 'clearfix', 'empty' => false, 'options' => $roles, 'disabled' => true)
			);

			echo $this->Form->input('Profile.name', array('label' => __('Full Name'),'div' => 'clearfix'));
			
			echo $this->Form->input('User.email', array('label' => __('E-mail'), 'div' => 'clearfix', 'disabled' => true));
			
			echo $this->Form->input('User.password', array('label' => __('Password'), 'div' => 'clearfix'));
			
			echo $this->Form->input('User.confirm_password', array('label' => __('Confirm Password'), 'div' => 'clearfix', 'type' => 'password'));

			echo $this->Html->div('actions',
				$this->Form->submit(__('Save Changes'), array('class' => 'btn primary')) . ' ' . 
				$this->Html->link(__('Cancel', true), array(
		            'action' => 'index',
		        ), array(
		            'class' => 'btn',
		        ))
			);	
		?>
		</div>

</div>