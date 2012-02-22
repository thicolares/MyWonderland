<script type="text/javascript">
	var RecaptchaOptions = {
		theme : 'white',
		lang : 'pt',
	};
</script>

<?php // App::import('Vendor', 'recaptchalib'); ?>

<h2><?php print __('Reset Password'); ?></h2>
<?php
	echo $this->Form->create('User', array('type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false)));

	echo $this->Form->input('User.password', array('type' => 'password', "label" => __('New Password'), 'value' => '', 'div' => 'clearfix'));
	echo $this->Form->input('User.confirm_password', array('type' => 'password', "label" => __('Confirm'), 'value' => '', 'div' => 'clearfix'));

	// RECAPTCHA
	$divClass = 'clearfix';
    $after = '';
    if(isset($captchaError) && $captchaError == true){
        $divClass .= ' error';
        $after = '<div class="error-message">' . __('You have mistyped the two words above.') . '</div>';
    }
	
	echo $this->Html->div($divClass, 
		'<div class="required">' . $this->Form->label('captcha', __('Visual Confirmation')) . '</div>' .
		$this->Form->label('captcha', $this->Recaptcha->display_form()) .
		$after
	);

	echo $this->Html->div('actions',
		$this->Form->submit(__('Reset Password'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
	        'action' => 'login',
			'profile' => false
	    ), array(
	        'class' => 'btn',
	    ))
	);
	print $this->Form->end();	

?>