<div class="users form">
<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false, 'div' => false))); ?>


	<?php echo $this->element('user/form_part_access'); ?>
	
	<?php    
	echo $this->Html->div('actions',
		$this->Form->submit(__(' Register'), array('class' => 'btn large primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();
	?>
</div>

