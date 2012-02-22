<div id="nav">
    <ul class="sf-menu">
		
		<!-- PAPERS -->
		<li>
			<?php 
				echo $this->Html->link(__('My Papers'), 
					array('plugin' => null, 'controller' => 'papers', 'action' => 'index', 'profile' => true)
				);
			?>
			<ul>
			    <li><?php echo $this->Html->link(__('List'), array('plugin' => null, 'controller' => 'papers', 'action' => 'index', 'profile' => true)); ?></li>
			    <li><?php echo $this->Html->link(__('Submit Paper'), array('plugin' => null, 'controller' => 'papers', 'action' => 'add', 'profile' => true)); ?></li>
				<?php if($this->Session->read('Auth.User.role') == 'appraiser'){ ?>
					<li>
						<?php 
							echo $this->Html->link(__('Evaluate Papers'), 
								array('plugin' => null, 'controller' => 'papers', 'action' => 'evaluations', 'profile' => true)
							);
						?>
					</li>
				<?php } //if ?>			
			</ul>
		</li>
		
		<!-- PAYMENT -->
		<li>
			<?php 
				echo $this->Html->link(__('Payment'),  // My Data? My Account? Profile?
					array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'profile' => true)
				);
			?>
		</li>
		
	    <li>
			<?php 
				echo $this->Html->link(__('My Personal Data'),  // My Data? My Account? Profile?
					array('plugin' => null, 'controller' => null, 'action' => 'edit', 'profile' => true)
				);
			?>
		</li>
		
		<li>
			<?php 
				echo $this->Html->link(__('Reset Password'),  // My Data? My Account? Profile?
					array('plugin' => null, 'controller' => 'users', 'action' => 'resetPassword', 'profile' => true)
				);
			?>
		</li>
		
	</ul>
	
</div>			