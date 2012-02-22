Olá <strong><?php print $name ?></strong>,<br>

<p>Você foi cadastrado(a) no sistema do <?php print Configure::read('EventTitle'); ?> por um de nossos administradores. Por medida de segurança, o <?php print Configure::read('EventTitle'); ?> não envia senhas por e-mail.</p>

<p>Você deve criar sua senha de acesso seguindo os passos contidos no link abaixo:</p>

<p>
    <?php 
    $fullUrl = $this->Html->url("/users/resetPassword/$email/$activation_key", true);
    print $this->Html->link($fullUrl, $fullUrl);
    ?>
</p>

<p><strong>Não esqueça de conferir a situação do pagamento para garantir sua vaga no <?php print Configure::read('EventTitle'); ?>.</strong></p>

<br>
<p>Atenciosamente,<br>
Equipe <?php print Configure::read('EventTitle'); ?>.</p>