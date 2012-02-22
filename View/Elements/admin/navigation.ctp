<div id="nav">
    <ul class="sf-menu">
	
		<!--- SYSTEM -->
        <!-- <li>
			<a href="#"><?php print __('System Preferences'); ?></a>
			<ul>
			    <li><?php echo $this->Html->link('asdasd', array('plugin' => false, 'admin' => true, 'controller' => 'settings', 'action' => 'basic')); ?></li>
			    <li><?php echo $this->Html->link(__('Preferences'), array('plugin' => false, 'admin' => true, 'controller' => 'settings', 'action' => 'preferences')); ?></li>

				<li>
					<?php echo $this->Html->link(__('Taxonomy'), '#'); // List ?>
					<ul>
						<li><?php echo $this->Html->link(__('List Vocabularies'), array('plugin' => false, 'admin' => true, 'controller' => 'vocabularies', 'action' => 'index')); ?>
						<li><?php echo $this->Html->link(__('Add Vocabulary'), array('plugin' => false, 'admin' => true, 'controller' => 'vocabularies', 'action' => 'add')); ?></li>
					</ul>
				</li>
			</ul>
        </li> -->
		<!-- / System -->



		<!-- REGISTRATIONS -->
        <li>
			<?php echo $this->Html->link(__('Registrations'), array('plugin' => false, 'admin' => true, 'controller' => 'registrations', 'action' => 'index'));  ?>
			<ul>
			    <li><?php 
					echo $this->Html->link(__('List'), array('plugin' => false, 'admin' => true, 'controller' => 'registrations', 'action' => 'index')); 
				?></li>
			    <li><?php echo $this->Html->link(
					__('Add Registration (Consolidation)'), 
					array('plugin' => false, 'admin' => true, 'controller' => 'registrations', 'action' => 'add')
					); 
				?></li>
                <li><?php echo $this->Html->link(
					__('Exemption Requests'), 
					array('plugin' => false, 'admin' => true, 'controller' => 'exemptions', 'action' => 'index')
					); 
				?></li>
				<li><?php echo $this->Html->link(__('Registration Periods'), array('plugin' => false, 'admin' => true, 'controller' => 'registration_periods')); ?>
					<ul>
						<li><?php echo $this->Html->link(__('List'), array('plugin' => false, 'admin' => true, 'controller' => 'registration_periods')); ?>
						<li><?php echo $this->Html->link(__('Add Registration Period'), array('plugin' => false, 'admin' => true, 'controller' => 'registration_periods', 'action' => 'add')); ?></li>
					</ul>
				</li>
				<li><?php echo $this->Html->link(__('Registration Types'), array('plugin' => false, 'admin' => true, 'controller' => 'registration_types')); ?>
					<ul>
						<li><?php echo $this->Html->link(__('List'), array('plugin' => false, 'admin' => true, 'controller' => 'registration_types')); ?>
						<li><?php echo $this->Html->link(__('Add Registration Type'), array('plugin' => false, 'admin' => true, 'controller' => 'registration_types', 'action' => 'add')); ?></li>
					</ul>
				</li>
			</ul>
        </li>


		<!--- PAPERS -->
        <li>
			<?php echo $this->Html->link(__('Papers'), array('plugin' => false, 'admin' => true, 'controller' => 'papers')); ?>
			<ul>
			    <li><?php echo $this->Html->link(__('List'), array('plugin' => false, 'admin' => true, 'controller' => 'papers')); ?></li>
			    <li><?php echo $this->Html->link(__('Assign Papers'), array('plugin' => false, 'admin' => true, 'controller' => 'papers', 'action' => 'assign')); ?></li>
			</ul>
        </li>
		<!-- / PAPERS -->

        <li>
			<?php echo $this->Html->link(__('Broadcast'), array('plugin' => false, 'admin' => true, 'controller' => 'broadcasts')); ?>
        </li>

		<!--- USERS -->
        <li>
			<?php echo $this->Html->link(__('Users'), array('plugin' => false, 'admin' => true, 'controller' => 'users', 'action' => 'index')); ?>
			<!-- <ul>
				<li><?php //echo $this->Html->link(__('List'), array('plugin' => false, 'admin' => true, 'controller' => 'users', 'action' => 'index')); ?></li>
				<li><?php //echo $this->Html->link(__('Add User'), array('plugin' => false, 'admin' => true, 'controller' => 'users', 'action' => 'add')); ?></li>
			</ul> -->
		</li>
		
		
		<!--- ACL -->
        <!-- <li>
			<?php //echo $this->Html->link(__('ACL'), array('plugin' => false, 'admin' => true, 'controller' => 'acl', 'action' => 'acos')); ?>
        </li> -->
		<!-- / ACL -->

	</ul>

</div>