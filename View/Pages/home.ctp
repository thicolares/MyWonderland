<div class="hero-unit">
	<h1>My Wonderland<small><span class="label label-warning">b e t a</span></small></h1>
	<h2>Tell us your favorite artists. Find your happy place. <small>(&hearts; more live concerts)</small></h2>
	<hr>
	<!-- Example row of columns -->
    <div class="row">
      <div class="span6">
		<?php

			echo $this->Form->create(
				'Place',
				array(
					'class' => 'well form-inline',
					'inputDefaults' => array(
						'label' => false,
						'div' => false
					),
					'type' => 'post',
					'url' => '/p'
				)
			);


			echo $this->Form->input(
				'Quiz.lastfm_username',
				array(
					'label' => '', // has a label element
					'class' => 'span3 large',
					'type' => 'text',
					'error' => false,
					'placeholder' => __('Type Your Last.FM username')				
				)
			); 


			echo ' ' . $this->Form->button(__('Find My Wonderland'), array(
				'type' => 'submit',
				'class' => 'btn btn-danger'
			));
			?> <p class="help-block"><?php print __('This is a beta version, may take few minutes. :)') ?></p><?php
			echo $this->Form->end();

		?>
      </div>
    </div>
	</div>


	<div class="row">
		<div class="span3">
			<?php echo $this->Html->image('music-hack-day-logo.png', array('alt' => 'CakePHP', 'url' => 'http://musichackday.org/')); ?>
		</div>
		
		<div class="span4">
			<i class="icon-star"></i> <?php echo __('<strong>First prize winner – in the first Music Hack Day Brazil</strong>. <small>The goal of Music Hack Day is to explore and build the next generation of music applications. At São Paulo, each participant had 24 hours to conceptualize, create and present their projects</small>'); ?>
		</div>
		
		<div class="span5">
			
			<div class="row">
				<div class="span5">
					<?php
						echo __('<i class="icon-fire"></i> <strong>Press:</strong> ');
						echo $this->Html->link('IG Tecnologia', 'http://tecnologia.ig.com.br/campus-party-aplicativos-integrados-com-lastfm-vencem-torneio/n1597619041331.html');
						echo ', ';
						echo $this->Html->link('A Tarde', 'http://www.fotonblog.com/2012/02/tecnonerd-baiano-leva-premio-em.html');
						echo ', ';
						echo $this->Html->link('Blog Campus Party', 'http://blog.campus-party.com.br/index.php/2012/02/09/maratona-de-desenvolvimento-music-hack-day-conhece-seu-vencedor/');
						echo ', ';
						echo $this->Html->link('Caia no Mundo', 'http://caianomundo.ci.com.br/blog/2012/02/08/habemus-ganhador-2/');
						echo ', ';
						echo $this->Html->link('REMIX', 'http://remix.ag/2012/02/mhd-e-da-bahia/');
						echo ' ' . __('and more...');
					 ?>
				</div>			
			</div>
			
			<div class="row">
				<div class="span5">
					<i class="icon-user"></i> Developed by <?php echo $this->Html->link('Thiago Colares', 'http://twitter.com/thicolares'); ?>. – <em>"Thanks for all my partners at <?php echo $this->Html->link('Apimenti', 'http://apimenti.com.br'); ?>, especially Jailson Brito."</em>
				</div>
			</div>
			
			<div class="row">
				<div class="span5">
					<i class="icon-time"></i> <em>Version 0.1.0b</em> <?php print $this->Html->link('MyWonderland at GitHub', 'https://github.com/colares/MyWonderland'); ?>
				</div>
			</div>
			
		</div>
		
	</div>
		
		


</div>