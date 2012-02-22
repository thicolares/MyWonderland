<?php echo $content_for_layout; ?>

<p>Este é um e-mail automático disparado pelo sistema. Favor não respondê-lo, pois esta conta não é monitorada. <br><?php print $this->Html->link('Veja nossos Contatos.', Configure::read('ContactUsURL')) ?></p>

<small style="color: #BFBFBF">O <strong><?php print Configure::read('EventTitle') ?></strong> é gerenciado com <?php print $this->Html->link('Apimenti Eventos', 'http://www.apimenti.com.br') ?><small>