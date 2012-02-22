<div class="topbar">
	<div class="topbar-inner">
		<div class="container-fluid">
			<a class="brand" href="/"><?php print Configure::read('SiteTitle'); ?></a>
			<!-- <ul class="nav">
					        <li class="active"><a href="#">Home</a></li>
					        <li>
								<?php
									echo $this->Html->link(__('Issues'),
										array('profile' => true, 'plugin' => null, 'controller' => 'issues', 'action' => 'index')
									);
								?>
							</li>
					        <li><a href="#contact">Contact</a></li>
						</ul>
						<p class="pull-right">
							<?php
								// Show login buttom if user is not logged
								// Show face and user name if is logged
								//print $this->Facebook->login(array('size' => 'large'));
								
								// If is logged, show logout button
								if(isset($facebook_user) && !empty($facebook_user)){
									print ' ' . $this->Facebook->logout(array(
										'redirect' =>'/users/logout',
										'size' => 'xlarge',
										'id' => 'user-logout'
									));
								}
							?>
						</p> -->
		</div>
	</div>
</div>


<!-- <ul class="pills">
	<li class="<?php echo ($this->request->here == '/' || $this->request->here == '/pages/home') ? 'active' : ''; ?>">
		<?php
			echo $this->Html->link(__('Home'),
				//array('plugin' => null, 'controller' => 'pages', 'action' => 'home')
				'/'
			);
 		?>
	</li>
	<li class="<?php echo ($this->request->here == '/registrations') ? 'active' : ''; ?>">
		<?php
			echo $this->Html->link(__('Register'),
				array('plugin' => null, 'controller' => 'registrations', 'action' => 'index')
			);
 		?>
	</li>
	<li class="<?php echo ($this->request->here == '/pages/precos') ? 'active' : ''; ?>">
		<?php
			echo $this->Html->link(__('Prices and Deadlines'),
				array('plugin' => null, 'controller' => 'pages', 'action' => 'precos')
			);
 		?>
	</li>
	<li class="<?php echo ($this->request->here == '/login') ? 'active' : ''; ?>">
		<?php
			echo $this->Html->link(__('Login'),
				array('plugin' => null, 'controller' => false, 'action' => 'login')
			);
 		?>
	</li>
	<li class="<?php echo ($this->request->here == '/pages/contato') ? 'active' : ''; ?>">
		<?php
			echo $this->Html->link(__('Contact'),
				array('plugin' => null, 'controller' => 'pages', 'action' => 'contato')
			);
 		?>
	</li>
</ul> -->
