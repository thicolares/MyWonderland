<div class="registration index">
	
	<?php 
		$this->AdminIndexList->setElements(
			array(
				array(
					'name' => 'registration/payment_status_report',
					'data' => array(),
					'options' => array('plugin' => 'registration')
				)
			)
		);

        $this->AdminIndexList->setRowActions(
            array(
                array(
                    'title' => __('Edit'),
                    'url' => array('plugin' => false, 'admin' => true, 'controller' => 'registrations', 'action' => 'edit'),
                    'id' => array('User.id') // As many as you want
                )
            )
        );
    
        print $this->AdminIndexList->buildHtml(); 
    ?>
</div>