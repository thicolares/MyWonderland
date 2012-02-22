<?php //	echo $this->Html->script('/quiz/js/quiz'); ?>
<br><div class="row">
	<div class="span4">
		<?php echo $this->Html->image(
			(!is_array($userInfo['user']['image'][3]))
				?
				$userInfo['user']['image'][3]
				:
				'http://cdn.last.fm/flatness/catalogue/noimage/2/default_user_large.png'
			,
			array('escape' => false, 'class' => 'avatar', 'alt' => $userInfo['user']['name'] . ', ' . ($pop?'pop':'hipster')
		)); ?>
	</div>
	<div class="span8">
		<div class="row">
			<h1><?php print __("%s, you are %s!", $userInfo['user']['name'], ($pop?'pop':'hipster')); ?></h1>
			<h2><small><?php print __("Your favorite artists represents %d&#37; of the most loved by users in general", $oPer); ?></small></h2>
			<?php
				if($pop){
					print "<p><strong>Pop:</strong> Somoeone who listens popular things.";
				} else {
					print "<p><strong>Hipster:</strong> Someone who listens to bands you've never heard of, wears ironic tee-shirts, and believes they are better than you.</p>";
				}
			?>
			<br>
			<h2>What do You think about you?</h2>
			<?php
				echo $this->Form->create('Quiz', array(
				    'inputDefaults' => array(
				        'label' => false,
				        'div' => false
				    ),
					'type' => 'post',
					'class' => 'pull-left',
					'url' => '/quiz/quizzes/vote'

				));

				echo $this->Form->hidden('username', array('value' => $username));
				
				if(!$hasVoted || !isset($hasVoted) || empty($hasVoted)){
					echo $this->Form->button('<i class="icon-star icon-white"></i> '. __('Pop') .'</a>', array(
						'class' => 'voteBtn btn btn-warning btn-large', 'name' => 'action', 'value' => 'pop'
					));

					echo ' ' . $this->Form->button('<i class="icon-camera icon-white"></i> '. __('Hipster') .'</a>', array(
						'class' => 'voteBtn btn btn-danger btn-large', 'name' => 'action', 'value' => 'hipster'
					));					
				} else {
					echo $this->Form->button("$popPor% " . __('Pop') .'</a>', array(
						'class' => 'voteBtn btn btn-warning btn-large', 'name' => 'action', 'value' => 'pop', 'disable' => 'disable'
					));

					echo ' ' . $this->Form->button("$hipPor% " . __('Hipster') .'</a>', array(
						'class' => 'voteBtn btn btn-danger btn-large', 'name' => 'action', 'value' => 'hipster', 'disable' => 'disable'
					));
				}
				

			
				echo $this->Form->end();
			?>
		<!-- <a class="voteBtn btn btn-warning btn-large" href="#"><i class="icon-star icon-white"></i> Pop</a>
		<a class="voteBtn btn btn-danger btn-large" href="#"><i class="icon-fire icon-white"></i> Hype</a>
		<a class="btn btn-warning btn-large" href="#" disabled><strong>30%</strong> Pop</a>
		<a class="btn btn-danger btn-large" href="#" disabled><strong>70%</strong> Hype</a> -->
		</div>
		<?php 				//	debug($topArtists); ?>
		<?php //debug($userTopArtists); ?>
		<div class="row">
			<div class="span4">
				<h2><?php print __("Your Rank List") ?></h2>
				<table class="table table-bordered">
				  <table class="table">
			        <thead>
			          <tr>
			            <th><?php print __('Rank'); ?></th>
			            <th><?php print __('Artist Name'); ?></th>
			          </tr>
			        </thead>
			        <tbody>
					<?php
						$tableRows = '';
						$i = 0;
						for($i = 0; $i<10; $i++){
							if(!isset($userTopArtists['topartists']['artist'][$i]))
								break;
							$tableRows .= '<tr>';
							$tableRows .= '<td>' . $userTopArtists['topartists']['artist'][$i]['@attributes']['rank'] . '</td>';
							$tableRows .= '<td>' . 
								$this->Html->link(
									$userTopArtists['topartists']['artist'][$i]['name'],
									array('url' => array($userTopArtists['topartists']['artist'][$i]['url']))
								) .	'</td>';
							$tableRows .= '</td></tr>';
						}
						print $tableRows;
					?>
			        </tbody>
			      </table>
			    </div><!-- span4 -->
				<div class="span4">
				</div>
				<!-- <div class="fb-send" data-href="http://example.com"></div> -->
				
				<div class="span4">
					<h2><?php print __("Rest of The World") ?></h2>
					<table class="table table-bordered">
					  <table class="table">
				        <thead>
				          <tr>
				            <th><?php print __('Rank'); ?></th>
				            <th><?php print __('Artist Name'); ?></th>
				          </tr>
				        </thead>
				        <tbody>
							<?php
								$tableRows = '';
								for($i = 0; $i<10; $i++){
									$tableRows .= '<tr>';
									$tableRows .= '<td>' . ($i+1) . '</td>';
									$tableRows .= '<td>' . 
										$this->Html->link(
											$topArtists['artists']['artist'][$i]['name'],
											array('url' => array($topArtists['artists']['artist'][$i]['url']))
										) .	'</td>';
									$tableRows .= '</td></tr>';
								}
								print $tableRows;
							?>
				        </tbody>
				      </table>
				    </div><!-- span4 -->
			
			
			</div>
		</table>
	</div>
  </div>
</div>