<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Override some Form Helper stuffs to generate Bootstrap (fom Twitter) items
 *
 * @package default
 * @author Thiago Colares
 */
class BFormHelper extends AppHelper {
	
	var $helpers = array(
        'Html',
		'Form',
        'Layout',
        'Paginator'
    );
	
	/**
	 * generate controls item: input text
	 *
	 * @param string $slug Slug name far var
	 * @param string $label Label from input
	 * @param string $tip Tip for input
	 * @return void
	 * @author Thiago Colares
	 */
		// <div class="control-group">
		// 	      <label class="control-label" for="input01">Text input</label>
		// 	      <div class="controls">
		// 	        <input type="text" class="input-xlarge" id="input01">
		// 	        <p class="help-block">In addition to freeform text, any HTML5 text-based input appears like so.</p>
		// 	      </div>
		// 	    </div>
	public function controls($slug, $label, $tip = null, $options = array(), $formItem = null){
		if($formItem == null){
			$ctrls = $this->Html->div('controls',
				$this->Form->input($slug, (empty($options) ? array() : $options)) .
				(($tip == null) ? '' : '<p class="help-inline">' . $tip . '</p>')
			);
		} else {
			$ctrls = $this->Html->div('controls',
				$formItem .
				(($tip == null) ? '' : '<p class="help-inline">' . $tip . '</p>')
			);
		}

		return $this->Html->div('control-group',
			$this->Form->label($slug, $label, array('class' => 'control-label')) . 
			$ctrls
		);
	}
	

}

?>