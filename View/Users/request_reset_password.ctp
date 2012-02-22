<script type="text/javascript">
	var RecaptchaOptions = {
		theme : 'white',
		lang : 'pt',
	};
</script>

<h2><?php print __('Recover Password'); ?></h2>
<?php
	echo $this->Form->create('User', array('type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false)));

	echo $this->Form->input('User.email',
		array("label" => 'E-mail', 'after' => '<span class="help-block">' . __('You will receive an email with instructions to recover your password.') . '</span>')
	);
	
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
		$this->Form->submit(__('Request Reset Password'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
	        'action' => 'login',
			'profile' => false
	    ), array(
	        'class' => 'btn',
	    ))
	);
	print $this->Form->end();	

?>