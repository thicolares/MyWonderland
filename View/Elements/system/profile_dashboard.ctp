<div class="dashboard index">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php echo $this->element('ExemptNotification', array(), array('plugin' => 'paper')); ?>

	<div class="alert-message block-message success">
		<p><strong>VIRADA DE PREÇOS ADIADA! Pague mais barato até 25/01/2012</strong></p>
		<div class="alert-actions">
			<?php
			// echo $this->Html->link(__('Submit A Paper Now!'), array(
			echo $this->Html->link(__('Pague agora e garanta sua vaga'), array(
	            'action' => 'index',
				'profile' => true,
				'plugin' => false,
				'controller' => 'payments',
				'action' => 'index'
	        ), array(
	            'class' => 'btn',
	        ))
			?>
  		</div>
	</div>

    <?php if(Configure::read('PaperSubmissionLimitDate') > time()){ ?>
		<div class="alert-message block-message warning">
		<p><strong>ATENÇÃO!</strong> O prazo para submissão de trabalhos encerrar-se-á às <strong><?php print date('H:i', Configure::read('PaperSubmissionLimitDate')); ?></strong> do dia <strong><?php print date('d/m/Y', Configure::read('PaperSubmissionLimitDate')); ?></strong>.</p>
		<p>O formulário de <strong>Submissão de Trabalhos</strong> já encontra-se disponível!</p>
			<div class="alert-actions">
				<?php
				// echo $this->Html->link(__('Submit A Paper Now!'), array(
				echo $this->Html->link(__('Envie Um Trabalho Agora!'), array(
		            'action' => 'index',
					'profile' => true,
					'plugin' => false,
					'controller' => 'papers',
					'action' => 'add'
		        ), array(
		            'class' => 'btn',
		        ))
				?>
	  		</div>
		</div>
	<?php } else { ?>
		<div class="alert-message block-message warning">
		<p>Prazo para submissão de trabalhos <strong>encerrado</strong>!</p>
		</div>
	<?php } ?>

</div>