Olá <strong><?php print $name ?></strong>,<br>

<p>O trabalho <em><strong>"<?php print $paperTitle ?>"</strong><em> recebeu o parecer final de um dos nossos avaliadores. Acesse o link a seguir todos os detalhes:</p>
<?php 
$fullUrl = $this->Html->url("/profile/papers/result/$paperId", true);
print $this->Html->link($fullUrl,$fullUrl); ?>
<br>
<p>Atenciosamente,<br>
Equipe <?php print Configure::read('EventTitle'); ?>.</p>