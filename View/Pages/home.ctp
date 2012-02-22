<div class="hero-unit">
  <h1>Find My Wonderland<small>Find yours</small></h1>
  <h2>Tell us your favorite artists and get known a place for you to live and see them more, alive!</h2>


	<p></p>
	<Br>
		
	<div class="span4" style="margin:0px auto; text-align: center">
		
  	<?php
	    echo $this->Form->create('Place', array(
		    'inputDefaults' => array(
		        'label' => false,
		        'div' => false,
				'class' => 'form-inline'
		    ),
			'type' => 'post',
			'class' => 'pull-left',
			'url' => '/p'
			// 'url' => array(
			// 	                	'controller' => 'translations',
			// 	'prefix' => null,
			// 	'action' => 'parser',
			// 	'plugin' => 'translation'
			// 	                )
		));
		// debug($vars[songURL]);
		echo $this->Form->input('Quiz.lastfm_username', array(
	    	'class' => " span3",
	    	'type' => 'text',
			'error' => false,
			'value' => (isset($vars['songURL']) ? $vars['songURL'] : null),
			'placeholder' => __('Type Your Last.FM username')
		));
		//echo $this->Form->submit('to uke');

		echo ' ' . $this->Form->button(__('Find My Wonderland'), array(
			'type' => 'submit',
			'class' => 'btn btn-large btn-danger'
		));
		echo $this->Form->end();
	?>
	</div>
</div>