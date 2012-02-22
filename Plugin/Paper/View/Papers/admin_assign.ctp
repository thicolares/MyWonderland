<div class="paper form">

    <h2><?php print __('Assign Papers'); ?></h2>
<br>

    <?php
    	
		echo $this->Form->create('Paper', array('type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false)));

		echo $this->Form->hidden('Paper.user_id', array('value' => $this->Session->read('Auth.User.id')));

		if(!isset($unassignedPapers) || empty($unassignedPapers)){
			$unassignedPapers = 0;
		}

		// PAPER STATUS
		switch ($unassignedPapers) {
			case '0':
				$labelHtml = $this->Html->div('clearfix', 
					$this->Form->label('', '&nbsp;') .
					__('Each paper is already assigned to an appraiser') 
				);
				break;
			
			case '1':
				$labelHtml = $this->Html->div('clearfix', 
					$this->Form->label('', __('There is')) .
					__('1 paper not assigned to an appraiser') 
				);
				break;
			
			default:
				$labelHtml = $this->Html->div('clearfix', 
					$this->Form->label('', __('There are')) .
					__('%d papers not assigned to an appraiser', $unassignedPapers) 
				);
				break;
		}

		echo $labelHtml;

		echo $this->Html->div('actions',
			$this->Form->submit(
				__('Assign These Papers to Appraisers'),
				array(
					'class' => 'btn primary', 
					'disabled' => ($unassignedPapers == 0) ? true : false
				)
			) . ' ' . 
			$this->Html->link(__('Cancel'), array(
				'plugin' => false,
				'controller' => 'systems',
	            'action' => 'dashboard',
				'admin' => true
	        ), array(
	            'class' => 'btn',
	        ))
		);
		
		echo $this->Form->end();
	?>

</div>