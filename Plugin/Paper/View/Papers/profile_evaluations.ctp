<div class="paper index">
	<?php 
    $this->AdminIndexList->setRowActions(
		array(
			array(
				'title' => __('Evaluate'),
				'url' => array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'evaluate'),
				'id' => array('Paper.id'),
				'conditions' => array('EvaluationStatus.id' => array(1))
			),
            array(
				'title' => __('Download Paper'),
				'url' => array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'downloadPaper'),
				'id' => array('Paper.id') // As many as you want
			)
		)
	);
    
    print $this->AdminIndexList->buildHtml(); 
    
    ?>
</div>