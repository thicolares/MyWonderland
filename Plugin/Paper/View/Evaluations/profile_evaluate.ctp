<div class="paper form">

    <h2><?php print __('Submit') . ' ' . $title; ?></h2>

    <?php
    echo $this->Form->create('Evaluation', array('type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false)));

	echo $this->Form->hidden('Evaluation.id');
	?>
	<fieldset>
		<legend><?php print __('Paper Information'); ?></legend>
		<?php
		// PAPER TYPE
		echo $this->Html->div('clearfix', 
			__('Paper Type').': '.$paper['PaperType']['name']
		);
        
        echo $this->Html->div('clearfix', 
			__('Title').': '.$paper['Paper']['title']
		);
        
        echo $this->Html->div('clearfix', 
			__('Title').': '.$paper['Paper']['abstract']
		);
        
        $res = array();
        foreach($paper['ResearchLines'] as $researchLine){
            $res[] = $researchLine['name'];
        }
        
        echo $this->Html->div('clearfix', 
			__('Research Line').': '.  implode(',', $res)
		);
        
        echo $this->Html->div('clearfix', 
			$this->Html->link(
                __('Download Paper'),
                array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'downloadPaper/'.$paper['Paper']['id'])
            )
		);
		?>
	</fieldset>
	
    <fieldset>
        <legend><?php print __('Authors and Co-Authors'); ?></legend>
        <?php
        echo $this->Html->div('clearfix', 
			__('Author').': '.  $paper['Profile']['name']
		);
        
        for($i=1; $i<=4; $i++){
            if($paper['Paper']['co_author_name_'.$i]){
                echo $this->Html->div('clearfix', 
                    __('Co-Author') . " $i".': '.  $paper['Paper']['co_author_name_'.$i]
                );
            }
        }
        
        ?>
    </fieldset>
        
    <fieldset>
        <legend><?php print __('Evaluation'); ?></legend>
        <?php
        echo $this->Html->div('clearfix required', 
			$this->Form->label('Evaluation.evaluation_status_id', __('Evaluation')) .
			$this->Form->select('Evaluation.evaluation_status_id', $evaluationStatuses, array('empty' => false))
		);
        
        // PAPER ABSTRACT
        $divClass = 'clearfix required';
        $after = '';
        if ($this->Form->isFieldError('Evaluation.comments')){
            $divClass .= ' error';
            $after = $this->Form->error('Evaluation.comments');
        }
        
		echo $this->Html->div($divClass, 
			$this->Form->label('Evaluation.comments', __('Comments')) .
			$this->Form->textarea('Evaluation.comments', array('rows' => '10', 'cols' => '5', 'class' => 'span12')).$after
		);
        
        ?>
    </fieldset>
    
	<?php
	echo $this->Html->div('actions',
		$this->Form->submit(__('Submit Evaluation'), array('class' => 'btn primary')) . ' ' . 
		$this->Html->link(__('Cancel'), array(
            'action' => 'index',
			'profile' => true
        ), array(
            'class' => 'btn',
        ))
	);
	print $this->Form->end();
	?>
</div>