<div class="registration form">

    <h2><?php print $title; ?></h2>
     	
    <?php
    echo $this->Form->create('Registration', array('inputDefaults' => array('label' => false, 'div' => false)));
    ?>
    
	<?php echo $this->element('user/form_part_access'); ?>
	<?php echo $this->element('profile/form_part_personal'); ?>
	<?php echo $this->element('profile/form_part_address_phone'); ?>
	<?php echo $this->element('registration/form_part_registration', array(), array('plugin' => 'registration')); ?>
    



	          <div class="clearfix required <?php if ($this->Form->isFieldError('Registration.0.registration_policy')) echo ' error' ?>">
	            <label id="optionsCheckboxes">Política de Inscrições</label>
                <?php
                if ($this->Form->isFieldError('Registration.0.registration_policy')) {
                    echo $this->Form->error('Registration.0.registration_policy');
                }
                ?>
	            <div class="input">
	              <ul class="inputs-list">
	                <li>
	                  <label>
                        <input type="hidden" name="data[Registration][0][registration_policy]" value="0" />
	                    <input type="checkbox" name="data[Registration][0][registration_policy]" value="1" />
	                    <span>Li e estou de acordo com a <?php echo $this->Html->link('Política de Inscrições (abre em nova janela)',array('plugin' => null, 'controller' => 'pages', 'action' => 'precos'), array('target' => '_blank')); ?></span>
	                  </label>
	                </li>
	                <li>
				</ul>
				</div>
				</div>

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