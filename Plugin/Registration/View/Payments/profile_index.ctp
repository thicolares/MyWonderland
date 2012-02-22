<?php echo $this->Html->css('/registration/css/pagseguro'); ?>
<div class="registration-type form">

    <h2><?php print __('Payment'); ?></h2>
    <?php echo $this->element('ExemptNotification', array(), array('plugin' => 'paper')); ?>
    <div class="alert-message block-message warning">
		<p>
			<?php 
				$loginAt = $this->Html->link(
					__('login at your account at PagSeguro.') . ' ' . __('(open new window)') . '.',
					'https://pagseguro.uol.com.br/transaction/search.jhtml',
					array('target' => '_blank')
				);
				print __('<strong>PLEASE NOTE!</strong> The status of payments made through PagSeguro <strong>may take 6 hours to 1 day to be updated</strong> on this page. <br>
For real-time information, please %s', $loginAt);
 			?>
		</p>

	</div>
<?php
    if(!isset($nextRegistrationPeriod)){

        echo $this->Form->create('Payment', 
            array(
                'inputDefaults' => array(
                    'label' => false, 'div' => false
                ),
                // 'url' => array(
                // 	'controller' => 'payments', 'prefix' => null, 'action' => 'payment_pagseguro_checkout', 'plugin' => 'registration'
                // ),
                'type' => 'get'
            )
        );

        echo $this->Form->hidden('Registration.id');

        /*
            TODO Activities
        */
        echo $this->Html->div('clearfix', 
            $this->Form->label('Paper.paper_type_id', __('Items Purchased')) .
            __('Registration at the <strong>%s</strong>', Configure::read('EventTitle'))
        );

        // Registration Type
        echo $this->Html->div('clearfix', 
            $this->Form->label('registration_type_id', __('Registration Type')) .
            $this->request->data['RegistrationType']['name']
        );

        // Total Ammount
        echo $this->Html->div('clearfix', 
            $this->Form->label('price', __('Total Amount')) .
            $this->Number->currency($this->request->data['RegistrationMainVariation']['price'], 'EUR', array('before' => 'R$ ')) .
            (($this->request->data['RegistrationMainVariation']['price'] == 0) ? 
                '<span class="help-block">' . __('Your Registration Type is exempt. But you must show your receipt at the event site.') . '</span>'
            : // else
                '<span class="help-block">' . __('Price valid up to') . ' ' .  
                    $this->Time->format('d/m/Y', $this->request->data['RegistrationPeriod']['end_date']) . 
                    ' ' . __('at') . ' ' . 
                    $this->Time->format('H:i', $this->request->data['RegistrationPeriod']['end_date']) . 

                '</span>'
            )
        );

        // Payment Status
        echo $this->Html->div('clearfix', 
            $this->Form->label('payment_status', __('Payment Status')) .
            $this->request->data['PaymentStatus']['name']
        );

        // Payment Entity
        echo $this->Html->div('clearfix', 
            $this->Form->label('payment_entity', __('Payment Entity')) .
            $this->request->data['PaymentEntity']['name'] 
            // '<span class="help-block">' . $this->request->data['PaymentEntities']['description'] .'</span>'		
        );

		$duplicateLink = $this->Html->link(
             __('Generate Duplicate at PagSeguro (open a new window)'),
			'https://pagseguro.uol.com.br/transaction/search.jhtml',
			array('target' => '_blank')
         );

        // Payment Method
        echo $this->Html->div('clearfix', 
            $this->Form->label('payment_method', __('Payment Method')) .
            (!empty($this->request->data['PaymentMethod']['name']) ? 
                $this->request->data['PaymentMethod']['name'] . 
				'<span class="help-block">' . $duplicateLink . '</span>'
//$this->request->data['Registration']['payment_entity_payment_method_id'] == 2
                :
                __('Undefined') . 
                '<span class="help-block">' . __('Probably the payment has not yet been started.') . '</span>'
            )
        );

		/*
			TODO Refactory Factory
		*/

        // Build Actions Buttons Html
        $actionsHtml = '';

        // If registration type is charge free
        if(
            $this->request->data['RegistrationMainVariation']['price'] != 0 // not free
            &&
            $this->request->data['PaymentStatus']['payable'] == 1 // payable
        ){
            $actionsHtml = $this->Html->link(
                __('Pay with PagSeguro'),
                '/registration/payments/payment_pagseguro_checkout/' .  $this->request->data['Registration']['id'],
                array('class' => 'btn success')
            );
        }

		// Billet
        if(
			isset($this->request->data['Registration']['payment_entity_payment_method_id'])
			&&
            $this->request->data['Registration']['payment_entity_payment_method_id'] == 2 // PagSeguro + Billet
			&&
			(
				$this->request->data['PaymentStatus']['id'] == 1 // 'Aguardando pagamento
				||
				$this->request->data['PaymentStatus']['id'] == 9 // 'Não iniciado
			)
        ){

	        // Restart payment
			$resPaymentLink = $this->Html->link(
				__('Restart Payment with PagSeguro'),
				'/registration/payments/payment_pagseguro_checkout/' .  $this->request->data['Registration']['id'],
			    array('class' => 'btn success')
			);
            $actionsHtml = $this->Form->label('payment_restart', __('Has the billet prescribed?')) . $resPaymentLink;
			$actionsOptions = array('style' => 'padding: 17px 20px 18px 0px');
			$pagFlags = 'style="padding-left:150px"';
		}

        /*
            TODO Build a helper
        */
        $pagSeguro = '<br  class="clearfix"><p><br>Formas de pagamento aceitas pelo PagSeguro:</p><div id="flags" ' . (isset($pagFlags) ? $pagFlags : '') . '>
            <span id="flag_pagseguro" title="Saldo PagSeguro">Saldo PagSeguro</span>
            <span id="flag_visa" title="Visa">Visa</span>
            <span id="flag_mastercard" title="MasterCard">MasterCard</span>
            <span id="flag_diners" title="Diners">Diners</span>
            <span id="flag_americanexpress" title="American Express">American Express</span>
            <span id="flag_hipercard" title="Hipercard">Hipercard</span>
            <span id="flag_aura" title="Aura">Aura</span>
            <span id="flag_elo" title="Elo">Elo</span>
            <span id="flag_plenocard" title="PLENOCard">PLENOCard</span>
            <span id="flag_oipaggo" title="Oi Paggo">Oi Paggo</span>
            <span id="flag_bradesco" title="Banco Bradesco">Banco Bradesco</span>
            <span id="flag_itau" title="Banco Itaú">Banco Itaú</span>
            <span id="flag_bb" title="Banco do Brasil">Banco do Brasil</span>
            <span id="flag_banrisul" title="Banco Banrisul">Banco Banrisul</span>
            <span id="flag_hsbc" title="Banco HSBC">Banco HSBC</span>
            <span id="flag_boleto" title="Boleto">Boleto</span>
        </div>';

        echo $this->Html->div('actions',$actionsHtml . $pagSeguro, (isset($actionsOptions) ? $actionsOptions : array()));


        print $this->Form->end();
    }elseif($nextRegistrationPeriod){
        $beginDate = $this->Time->format('d/m/Y', $nextRegistrationPeriod['RegistrationMainVariation']['begin_date']) . 
            ' ' . __('at') . ' ' . 
            $this->Time->format('H:i', $nextRegistrationPeriod['RegistrationMainVariation']['begin_date']);
        
        $endDate = $this->Time->format('d/m/Y', $nextRegistrationPeriod['RegistrationMainVariation']['end_date']) . 
            ' ' . __('at') . ' ' . 
            $this->Time->format('H:i', $nextRegistrationPeriod['RegistrationMainVariation']['end_date']);
        
        $price = $this->Number->currency($nextRegistrationPeriod['RegistrationMainVariation']['price'], 'EUR', array('before' => 'R$ '));
        
        echo '<div class="alert-message notice">' . __('You cannot pay for your registration now. The deadline for payment, with current price, expired. You can pay between %s and %s. The new price of your registration will be %s', $beginDate, $endDate, $price). 
        '</div>';
    }else{
        echo '<div class="alert-message error">' . __('You can no longer pay by the site. The deadline for payments expired. Please, contact the event organizers.'). 
        '</div>';
    }
	?>
</div>