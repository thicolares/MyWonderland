<div>
    <h2><?php print __('My Personal Data'); ?></h2>

	<?php
		echo $this->Form->create('Profile', array('inputDefaults' => array('label' => false, 'div' => false)));
		echo $this->Form->hidden('Profile.id', array('value' => $this->request->pass[0]));
		echo $this->element('profile/form_part_personal');
		echo $this->element('profile/form_part_address_phone');
		
		echo $this->Html->div('actions',
			$this->Form->submit(__('Save Changes'), array('class' => 'btn primary')) . ' ' . 
			$this->Html->link(__('Cancel', true), array(
				'plugin' => false,
				'action' => 'dashboard',
				'profile' => true,
				'controller' => 'systems'
	        ), array(
	            'class' => 'btn',
	        ))
		);
	?>

</div>