<div class="users form">
    <h2><?php __('Login'); ?></h2>
	<?php
	echo $this->Form->create('User', array('url' => '/login', 'inputDefaults' => array('label' => false, 'div' => false), 'class' => 'form-stacked')); 

        echo $this->Form->input('User.email', array('label' => __('Login'), 'div' => 'clearfix'));
        echo $this->Form->input('User.password', array('label' => __('Password'), 'div' => 'clearfix'));
		echo $this->Html->div('actions',
			$this->Form->submit(__('Login') , array('class' => 'btn primary login-btn')) . ' ' . 
			$this->Html->link(__('Forgot Password?'), array(
				'controller' => 'users',
				'action' => 'requestResetPassword',
			), array(
				'style' => 'margin-left:15px',
				'class' => 'btn',
			))
		);
		echo $this->Form->end();
	?>
	
</div>