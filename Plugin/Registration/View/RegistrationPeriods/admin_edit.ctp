<?php

// http://book.cakephp.org/2.0/en/core-libraries/helpers/number.html

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

    <h2><?php print __('Edit') . ' ' . $title; ?></h2>
     	
    <?php		
		echo $this->Form->create('RegistrationPeriod', array('inputDefaults' => array('label' => false, 'div' => false)));
		
		print $this->Form->hidden('RegistrationPeriod.id');
		
		// Period range date
		$regPeriodBeginDate = $this->Form->input('RegistrationPeriod.begin_date.date', array(
			'label' => __('Period Range'),
			'class' => "small",
			'type' => 'text',
			'value' => $this->Time->format('d/m/Y', $this->request->data['RegistrationPeriod']['begin_date'])
		));
		$regPeriodBeginTime = $this->Form->input('RegistrationPeriod.begin_date.time', array(
			'class' => "mini",
			'type' => 'text',
			'value' => $this->Time->format('H:i', $this->request->data['RegistrationPeriod']['begin_date'])
		));
		
		$regPeriodEndDate = $this->Form->input('RegistrationPeriod.end_date.date', array(
			'class' => "small",
			'type' => 'text',
			'value' => $this->Time->format('d/m/Y', $this->request->data['RegistrationPeriod']['end_date'])
		));
		$regPeriodEndTime = $this->Form->input('RegistrationPeriod.end_date.time', array(
			'class' => "mini",
			'type' => 'text',
			'value' => $this->Time->format('H:i', $this->request->data['RegistrationPeriod']['end_date'])
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
		
		?>
		
		<?php
			$i = 0;
			$regTypesForm = '';
			foreach($registrationTypes as $registrationType){
				$intpuHtml = $this->Form->input('RegistrationMainVariation.' . $i . '.price', array(
					'label' => $registrationType['RegistrationType']['name'],
					'length' => 3,
					'type' => 'text',
					'class' => 'input-short',
					'div' => 'clearfix',
//					'value' => $number->currency($registrationType['RegistrationMainVariation']['price']);
					'after' => '<span class="help-block">' . __('Values in Brazilian Real. Example: 150,00')
					)
				);

				$hiddenHtmlRegMain = $this->Form->hidden('RegistrationMainVariation.' . $i . '.id');

				$hiddenHtml = $this->Form->hidden('RegistrationMainVariation.' . $i . '.registration_type_id', array(
					'value' => $registrationType['RegistrationType']['id'])
				);
				$i++;
				$regTypesForm .= '<div class="help-block">' . $intpuHtml . $hiddenHtml . $hiddenHtmlRegMain . '</div>';
			}
		?>

		<fieldset>
			<legend><?php print __('Main Registration'); ?></legend>
			<?php print $regTypesForm; ?>
		</fieldset>
		
		
		<?php

		echo $this->Form->input('off_site', array('label' => __('To be done in offline mode', true), 'after' => '<span class="help-text">Se marcado, este tipo de inscrição não poderá ser realizado pelo site. Exemplo: inscrições em grupo.</span>', 'type' => 'checkbox'));

		echo $this->Html->div('actions',
			// ($canNotBeChanged ? '' : $this->Form->submit(__('Save'), array('class' => 'btn primary'))) . ' ' . 
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