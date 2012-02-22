<!-- olá
$viewVars = array(

	'payment_status' => $this->request->data['PaymentStatus']['name'],
	'payment_entity' => $this->request->data['PaymentEntity']['name'],
	'payment_method' => $this->request->data['PaymentMethod']['name'],
	'modified' => $this->request->data['PaymentMethod']['name'],
);

// Get PagSeguro data
if($this->request->data['PaymentEntity']['id'] == 2){ // If PagSeguro
	$pagSeguroData = unserialize($this->request->data['PaymentLog']['description']);
	$viewVars['transaction_code'] = $pagSeguroData['code'];
	$viewVars['last_event_date'] = $pagSeguroData['lastEventDate'];
} -->

Olá <strong><?php print $name ?></strong>,<br>

<h2>Inscrição e Pagamento confirmados.</h2>
<p>Sua participação no <?php print Configure::read('EventTitle'); ?> está garantida, nos vemos em breve!</p>

<h2>Detalhes da Transação</h2>
<div style="background-color:#e0e0e0; font-size:110%; padding:20px; text-align:left">
	<table>
		<tr>
			<th>Código de Referência: <th>
			<td> <?php print $ref_code; ?></td>
		</tr>
		<tr>
			<th>Comprovante gerado em:<th>
			<td><?php print $receipt_date; ?></td>
		</tr>
		<tr>
			<th>Forma de Pagamento:<th>
			<td><?php print $payment_method; ?></td>
		</tr>
		<tr>
			<th>Situação do Pagamento:<th>
			<td>Paga</td>
		</tr>
		<tr>
			<th>Valor:<th>
			<td><?php print $price; ?></td>
		</tr>
		<tr>
			<th>Entidade de Pagamento:<th>
			<td><?php print $payment_entity; ?>
			</td>
		</tr>
	</table>
</div>
<?php if(!empty($transaction_code)){ // PagSeguro ?>
<h2>Detalhes do PagSeguro</h2>
<div style="background-color:#e0e0e0; font-size:110%; padding:20px; text-align:left">
	<table>
		<tr style="background-color: #d0d0d0">
			<th style="background-color: #d0d0d0" colspan=2><th>
		</tr>
		<tr>
			<th>Referência da Transação (PagSeguro):<th>
			<td><?php print $transaction_code; ?></td>
		</tr>
		<tr>
			<th>Última Atualização (PagSeguro):<th>
			<td><?php print $last_event_date; ?></td>
		</tr>
	</table>
</div>
<?php }?>

<br>
<p>Atenciosamente,<br>
Equipe <?php print Configure::read('EventTitle'); ?>.</p>