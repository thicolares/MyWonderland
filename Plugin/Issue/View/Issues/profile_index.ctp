<div class="issue index">
	<?php 
        $this->AdminIndexList->setRowActions(
            array(
	            array(
                    'title' => __('Protocols'),
                    'url' => array('plugin' => false, 'profile' => true, 'controller' => 'protocols', 'action' => 'add'),
                    'id' => array('Issue.id')
                ),
                array(
                    'title' => __('Edit'),
                    'url' => array('plugin' => false, 'profile' => true, 'controller' => 'issues', 'action' => 'edit'),
                    'id' => array('Issue.id')
                ),
				array(
					'title' => __('Delete', true),
					'url' => array('plugin' => false, 'profile' => true, 'controller' => 'issues', 'action' => 'delete'),
                    'id' => array('Issue.id'),
					'confirm' => __('This action can not be undone! Are you sure?')
				),
            )
        );
    
        print $this->AdminIndexList->buildHtml(); 
    ?>
</div>