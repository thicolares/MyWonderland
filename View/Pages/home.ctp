<div class="hero-unit">
  <h1>My Wonderland<small><span class="label label-warning">b e t a</span></small></h1>
  <h2>Tell us your favorite artists. Find your happy place. <small>(&hearts; more live concerts)</small></h2>


	<p></p>
	<Br>
		
	<div class="span6" style="margin:0px auto; text-align: center">
		
  	<?php

		echo $this->Form->create(
			'Place',
			array(
				'class' => 'well form-inline',
				'inputDefaults' => array(
					'label' => false,
					'div' => false
				),
				'type' => 'post',
				'url' => '/p'
			)
		);

		
		echo $this->Form->input(
			'Quiz.lastfm_username',
			array(
				'label' => '', // has a label element
				'class' => 'span3 large',
				'type' => 'text',
				'error' => false,
				'placeholder' => __('Type Your Last.FM username')				
			)
		); 


		echo ' ' . $this->Form->button(__('Find My Wonderland'), array(
			'type' => 'submit',
			'class' => 'btn btn-danger'
		));
		?> <p class="help-block"><?php print __('This is a beta version, may take few minutes. :)') ?></p><?php
		echo $this->Form->end();
		
	?>
	
	</div>
</div>