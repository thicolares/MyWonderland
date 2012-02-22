<div class="paper index">
	<?php 
    $this->AdminIndexList->setRowActions(
		array(
			array(
				'title' => __('Evaluation Result', true),
				'url' => array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'evaluationResult'),
				'id' => array('Paper.id'), // As many as you want
				'conditions' => array('Paper.paper_status_id' => array(3))
			),
            array(
				'title' => __('Download Paper'),
				'url' => array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'downloadPaper'),
				'id' => array('Paper.id') // As many as you want
			),
			array(
				'title' => __('Edit'),
				'url' => array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'edit'),
				'id' => array('Paper.id'), // As many as you want
				'conditions' => array('Paper.paper_status_id' => array(1))
			),
			array(
				'title' => __('Delete', true),
				'url' => array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'delete'),
				'id' => array('Paper.id'), // As many as you want
				'confirm' => __('This action can not be undone! Are you sure?'),
				'conditions' => array('Paper.paper_status_id' => array(1))
			),
		)
	);
    
    print $this->AdminIndexList->buildHtml(); 
    
    ?>
</div>