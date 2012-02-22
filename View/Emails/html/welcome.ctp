<!--- CAIRÁ EM DESUSO. UTILIZAR O QUE ESTÁ EM REGISRTION/ -->
Olá <strong><?php print $name ?></strong>,<br>

<p>Bem vindo ou bem vinda ao <?php print Configure::read('EventTitle'); ?>. Seu cadastro no nosso <em>website</em> foi realizado com sucesso. Siga as instruções de pagamento contidas no link a seguir para garantir sua participação.</p>

<p></strong>Link para Realizar Pagamento:</strong> <?php echo $this->Html->link(__('Prices and Deadlines'), 'http://bit.ly/viifeab-pagamento'); ?></p>

<p>Idosos são isentos. Inscrições no Local mediante documento pessoal com foto.</p>
<p>Pagamento com Empenho apenas na data e local do evento.</p>
<p>Veja <?php echo $this->Html->link(__('Prices and Deadlines'),array('plugin' => null, 'controller' => 'pages', 'action' => 'precos'), array('target' => '_blank')); ?>

<br>
<p>Atenciosamente,<br>
Equipe <?php print Configure::read('EventTitle'); ?>.</p>