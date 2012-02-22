Olá <strong><?php print $name ?></strong>,<br>

<p>Você solicitou uma nova senha. Por medida de segurança, o <?php print Configure::read('EventTitle'); ?> não envia senhas por e-mail. Você deve criar uma nova senha para substituir a antiga. Siga os passos abaixo e assim que a nova senha for confirmada a antiga será anulada.</p>

<p>Clique no link para trocar de senha:
<?php 
$fullUrl = $this->Html->url("/users/resetPassword/$email/$activation_key", true);
print $this->Html->link($fullUrl,$fullUrl) ?>
</p>
<br>
<p>Atenciosamente,<br>
Equipe <?php print Configure::read('EventTitle'); ?>.</p>