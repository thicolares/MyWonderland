<div class="registration-type form">

    <h2><?php print __('Add') . ' ' . $title; ?></h2>
     	
    <?php
    echo $this->Form->create('RegistrationType', array('inputDefaults' => array('label' => false, 'div' => false)));
    
    print $this->Form->input(
        'name', 
        array(
            'label' => __('Name'),
            'div' => 'clearfix required',
        )
    );

    print $this->Form->input(
        'off_site', 
        array(
            'label' => __('To be done in offline mode'),
            'after' => '<span class="help-block">'.__('If checked, this type of application can not be conducted through the site. Example: group entries.').'</span>', 
            'type' => 'checkbox',
            'div' => 'clearfix'
        )
    );

    print $this->Form->input(
        'proof_needed',
        array(
            'label' => __('Proof Needed'),
            'after' => '<span class="help-block">'.__('If checked, the user must provide a document proving that belongs to this category.').'</span>', 
            'div' => 'clearfix',
            'type' => 'checkbox'
        )
    );
        
	echo $this->Html->div('actions',
		$this->Form->submit(__('Save'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();
	?>
</div>