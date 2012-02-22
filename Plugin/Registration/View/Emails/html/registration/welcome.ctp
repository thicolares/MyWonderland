Olá <strong><?php print $name ?></strong>,<br>

<p>Bem vindo ou bem vinda ao <?php print Configure::read('EventTitle'); ?>! Seu cadastro no nosso <em>website</em> foi realizado com sucesso.</p>

<div style="background-color:#e0e0e0; font-size:150%; padding:30px; text-align:center;">
<p>Siga as instruções contidas no link a seguir para<br>realizar o pagamento e garantir sua participação</p>

<?php 
$fullUrl = $this->Html->url('/profile/payments/', true);
print $this->Html->link($fullUrl, $fullUrl); ?>
</div>
<br>
<p>Idosos são isentos. Inscrições no Local mediante documento pessoal com foto.</p>

<p>Veja <?php echo $this->Html->link(__('Prices and Deadlines'),array('plugin' => null, 'controller' => 'pages', 'action' => 'precos'), array('target' => '_blank')); ?>

<br>
<p>Atenciosamente,<br>
Equipe <?php print Configure::read('EventTitle'); ?>.</p>