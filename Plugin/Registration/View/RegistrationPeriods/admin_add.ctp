<?php
	// Date and Time picker stuffs
	echo $this->Html->css(array(
		'/registration/css/timePicker.css',

	));
	echo $this->Html->script(array(
		'/registration/js/jquery.timePicker.min.js',
		'/registration/js/datepicker.js',
		'/registration/js/timepicker.js'
	));	
?>

<div class="users form">

    <h2><?php print __('Add') . ' ' . $title; ?></h2>
     	
    <?php
		echo $this->Form->create('RegistrationPeriod', array('inputDefaults' => array('label' => false, 'div' => false)));
		
		// Period range date
		$regPeriodBeginDate = $this->Form->input('RegistrationPeriod.begin_date.date', array(
			'label' => __('Period Range'),
			'class' => "small",
			'type' => 'text'
		));
		$regPeriodBeginTime = $this->Form->input('RegistrationPeriod.begin_date.time', array(
			'class' => "mini",
			'type' => 'text'
		));
		
		$regPeriodEndDate = $this->Form->input('RegistrationPeriod.end_date.date', array(
			'class' => "small",
			'type' => 'text'
		));
		$regPeriodEndTime = $this->Form->input('RegistrationPeriod.end_date.time', array(
			'class' => "mini",
			'type' => 'text',

		));
		
		/*
			TODO Set some default date and time values!
		*/
		
		echo $this->Html->div('clearfix required',
			$this->Html->div('inline-inputs',
				"$regPeriodBeginDate $regPeriodBeginTime " . __('to') . " $regPeriodEndTime $regPeriodEndDate" . 
				'<span class="help-block">' . __('Registration Period') . '.</span>'
			)
		);
		

		$i = 0;
		$regTypesForm = '';
		foreach($registrationTypes as $registrationType){
			$intpuHtml = $this->Form->input('RegistrationMainVariation.' . $i . '.price', array(
				'label' => $registrationType['RegistrationType']['name'],
				'length' => 3,
				'type' => 'text',
				'class' => 'input-short',
				'div' => 'clearfix required',
				'after' => '<span class="help-text">' . __('Values in Brazilian Real. Example: 150,00')
				)
			);
		               
			$hiddenHtml = $this->Form->hidden('RegistrationMainVariation.' . $i . '.registration_type_id', array(
				'value' => $registrationType['RegistrationType']['id'])
			);
			$i++;
			$regTypesForm .= '<div class="block-short">' . $intpuHtml . $hiddenHtml . '</div>';
		}
	?>

	<fieldset>
		<legend><?php print __('Main Registration. Prices for this period'); ?></legend>
		<?php print $regTypesForm; ?>
	</fieldset>

	<?php
	
	print $this->Form->input(
		'off_site', 
		array(
			'label' => __('To be done in offline mode', true),
			'after' => '<span class="help-text">Se marcado, este tipo de inscrição não poderá ser realizado pelo site. Exemplo: inscrições em grupo.</span>', 'type' => 'checkbox'
	));
	
	echo $this->Html->div('actions',
		$this->Form->submit(__('Save'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel', true), array(
            'action' => 'index',
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();

	
?>


    
</div>