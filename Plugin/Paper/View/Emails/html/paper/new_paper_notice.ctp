Olá <strong><?php print $name ?></strong>,<br>

<p>Um novo trabalho aguarda ansiosamente pela sua avaliação. Acesse o link a seguir para ver os detalhes e emitir o seu parecer.</p>
<?php 
$fullUrl = $this->Html->url("/profile/papers/evaluate/$paperId", true);
print $this->Html->link($fullUrl, $fullUrl); 
?>
<br>
<p>Atenciosamente,<br>
Equipe <?php print Configure::read('EventTitle'); ?>.</p>