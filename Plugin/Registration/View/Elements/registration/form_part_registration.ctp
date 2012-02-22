<fieldset>
    <legend><?php print __('Registration'); ?></legend>
    <?php
		$errorMessage = $errorClass = '';
	    if ($this->Form->isFieldError('RegistrationItem.registration_main_variation_id')) {
			$errorClass = ' error';
			$errorMessage = '<span class="error-message">' . $this->Form->error('RegistrationItem.registration_main_variation_id') . '</span>';
	    }
		print $this->Html->div('clearfix' . $errorClass,
			'<div class="required"><label id="optionsRadio">' . __('Choose one option') . '</label></div>' . 
				$this->Html->div('input',
				'<ul class="inputs-list"><li><label>' . 
				$this->Form->radio(
					'RegistrationItem.registration_main_variation_id',
					$main_subscriptions_array,
					array(
						'legend' => false,
						'separator' => '</label></li><li><label>',
						'label' => false
					) // attributes
				) . // radio
				'</label></li></ul>'
				
			) .  // div input
			$errorMessage
		); // div clearfix
    ?>

    <div class="clearfix">
		<span class="help-block">Preços válidos até <?php echo $last_day ?>. Veja <?php echo $this->Html->link(__('Prices and Deadlines') . ' (abre em nova janela)',array('plugin' => null, 'controller' => 'pages', 'action' => 'precos'), array('target' => '_blank')); ?></span>
		<br>
		<div class="alert-message block-message warning">
			<p><strong>Idosos</strong> são isentos. Inscrições no Local mediante documento pessoal com foto.</p>
		</div>
		<!-- <div class="alert-message block-message warning">
			<p>Pagamento com <strong>Empenho</strong> apenas na data e local do evento. <span class="label warning" style="font-size:80%; font-weight:normal; background-color: #46A546">R$ 95,00</span></p>
		</div> -->
    </div>
</fieldset>