<div class="registration index">
	<?php 
        $this->AdminIndexList->setActions('');
        $this->AdminIndexList->setRowActions(
            array(
                array(
                    'title' => __('Allow'),
                    'url' => array('plugin' => false, 'admin' => true, 'controller' => 'exemptions', 'action' => 'allow'),
                    'id' => array('Registration.id') // As many as you want
                ),
                array(
                    'title' => __('Deny'),
                    'url' => array('plugin' => false, 'admin' => true, 'controller' => 'exemptions', 'action' => 'deny'),
                    'id' => array('Registration.id') // As many as you want
                )
            )
        );
    
        print $this->AdminIndexList->buildHtml(); 
    ?>
</div>