<div class="dashboard index">
    <h2><?php echo $title_for_layout; ?></h2>

	<div class="alert-message block-message success">
	<p>APPRRAIISERO formulário de <strong>Submissão de Trabalhos</strong> encontra-se disponível!</p>
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

	<div class="alert-message block-message warning">
	<p><strong>Seu lugar no VII Fórum Brasileiro de Educação Ambiental está quase garantido!</strong></p>
	<p>O mecanismo de pagamento está prestes a ser lançado. Quando isto acontecer, avisaremos por e-mail, pelo site e pelas redes sociais.</p>
	</div>


	
</div>