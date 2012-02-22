

<div class="broadcast form">
<h2><?php echo __('Preview Broadcast Message');?></h2>

<div class="alert-message block-message alert">
	<p><strong>ATENÇÃO!</strong><Br>Antes de enviar, confira as informações cuidadosamente. A partir daqui, não será possível desfazer esta operação.</p>
</div>

<div class="container">
	<div class="span12">
		<div class="row">
			<h3><?php echo __('Information About Recipients'); ?></h3>
			<p><?php echo __('Message will be sent to <em><strong>"%s"</strong></em>.', $this->request->data['Broadcast']['filter_name']); ?></p>
			<div class="hero-unit" style="border: 1px solid #e9e9e9">
				<h3><?php echo __('Subject') ?></h3>
				<p><?php echo '<span style="color:#999">[' . Configure::read('EventTitle') . ']</span> ' . $this->request->data['Broadcast']['subject']; ?></p>
				<br>
				<h3><?php echo __('Message') ?></h3>
				<p><?php echo $this->request->data['Broadcast']['message']; ?></p>
				<br>
				Atenciosamente,<br>
					Equipe <?php print Configure::read('EventTitle'); ?>
				<br>
			</div>
		</div>
	</div>
</div>


<div class="actions">
<?php
	echo $this->Form->create('Broadcast', array('style' => 'float:left; margin-right:10px'));
	echo $this->Form->hidden('Broadcast.send', array('value' => true));
	echo $this->Form->hidden('Broadcast.edit', array('value' => false));
	echo $this->Form->hidden('Broadcast.filter', array('value' => $this->request->data['Broadcast']['filter']));
	echo $this->Form->hidden('Broadcast.subject', array('value' => $this->request->data['Broadcast']['subject']));
	echo $this->Form->hidden('Broadcast.message', array('value' => $this->request->data['Broadcast']['message']));
	echo $this->Form->submit(__('Send Message'), array('class' => 'btn primary'));
    echo $this->Form->end();

	echo $this->Form->create('Broadcast', array('style' => 'float:left; margin-right:10px'));
	echo $this->Form->hidden('Broadcast.send', array('value' => false));
	echo $this->Form->hidden('Broadcast.edit', array('value' => true));
	echo $this->Form->hidden('Broadcast.filter', array('value' => $this->request->data['Broadcast']['filter']));
	echo $this->Form->hidden('Broadcast.subject', array('value' => $this->request->data['Broadcast']['subject']));
	echo $this->Form->hidden('Broadcast.message', array('value' => $this->request->data['Broadcast']['message']));
	echo $this->Form->submit(__('Edit Message'), array('class' => 'btn'));
    echo $this->Form->end();	

    echo $this->Html->link(__('Cancel (will discard message)', true), array(
		'admin' => true,
		'plugin' => false,
		'controller' => 'broadcasts',
        'action' => 'index',
    ), array(
        'class' => 'btn danger',
    ));
?>
</div>
</div>