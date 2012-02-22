<div class="paper form">

    <h2><?php print __('Paper Information'); ?></h2>

	<div class="row">
		<div class="span10">
			<!-- PAPER TITLE -->
			<h3><?php print __('Title'); ?></h3>
			<p><?php print $paper['Paper']['title'] ?></p>
			
			<!-- PAPER ABSTRACT -->
			<h3><?php print __('Abstract'); ?></h3>
			<p><?php print $paper['Paper']['abstract'] ?></p>
			
			
			<div class="row">
				<div class="span5">
					<!-- AUTHOR -->
					<h3><?php print __('Author'); ?></h3>
					<p><?php print $paper['Profile']['name']; ?></p>
				</div>
				<div class="span5">
					<!-- CO-AUTHOR -->
					<?php
						// CO-AUTHORS NAME
						$coAuthors = array();
				        for($i=1; $i<=4; $i++){
				            if($paper['Paper']['co_author_name_'.$i]){
				                $coAuthors[] = "($i) " . $paper['Paper']['co_author_name_'.$i];
				            }
				        }
						switch (count($coAuthors)) {
							case 0:
								$coTitle = __('Co-Author');
								$coAuthName = __('There is no co-author.');
								break;
							case 1:
								$coTitle = __('Co-Author');
								$coAuthName = implode(',', $coAuthors);
								break;

							default:
								$coTitle = __('Co-Authors');
								$coAuthName = implode(',', $coAuthors);
								break;
						}
					?>
					<h3><?php print $coTitle; ?></h3>
					<p><?php print $coAuthName; ?></p>
				</div>
			</div>
	
		</div>
			
		<div class="span4">
			<!-- PAPER TYPE -->
			<h3><?php print __('Paper Type') ?></h3>
			<p><?php print $paper['PaperType']['name']; ?></p>
			
			<!-- RESEARCH LINES -->
			<?php
			// 
			$resreachLines = array();
			if(!empty($paper['ResearchLines'])){
				foreach($paper['ResearchLines'] as $researchLine){
		            $resreachLines[] = $researchLine['name'];
		        }
			}
			?>
			<h3><?php print __('Research Line'); ?></h3>
			<p><?php print implode(',', $resreachLines); ?></p>
	
			
			<h3><?php print __('Download Paper'); ?></h3>
			<p><?php print $this->Html->link(
                __('Download Paper'),
                array('plugin' => false, 'profile' => true, 'controller' => 'papers', 'action' => 'downloadPaper/'.$paper['Paper']['id'])
            ); ?></p>
		</div><!-- span4 -->
	</div><!-- row -->
	<br>
	<h2><?php print __('Evaluation'); ?></h2>
	
	<?php
	    echo $this->Form->create('Evaluation', array('type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false)));

		echo $this->Form->hidden('Evaluation.id');
		echo $this->Form->hidden('Evaluation.paper_id');
		
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
	
  