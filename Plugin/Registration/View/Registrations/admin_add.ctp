<div class="registration form">

    <h2><?php print $title; ?></h2>
     	
    <?php
    echo $this->Form->create('Registration (Consolidation)', array('inputDefaults' => array('label' => false, 'div' => false)));
    ?>
    
	<?php echo $this->element('user/form_part_access', array('automaticPassword' => true)); ?>
	<?php echo $this->element('profile/form_part_personal'); ?>
	<?php echo $this->element('profile/form_part_address_phone'); ?>
	<?php echo $this->element('registration/form_part_registration_admin', array(), array('plugin' => 'registration')); ?>
    

    <?php    
	echo $this->Html->div('actions',
		$this->Form->submit(__('Perform Register'), array('class' => 'btn large primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();
	?>
</div>