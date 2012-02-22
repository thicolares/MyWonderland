<fieldset>
    <legend><?php print __('Registration'); ?></legend>
    <?php
    print $this->Form->hidden('Registration.0.id');
    print $this->Form->hidden('RegistrationItem.id');
    
    
    if(isset($registration)){
        $class = 'notice';
        if($registration['PaymentStatus']['payable'] != 1) $class = 'error';
        
        echo '<div class="'.$class.' alert-message">Esta inscrição tem como entidade de pagamento o PagSeguro. <br><br>';
        echo 'Status da transação: '.$registration['PaymentStatus']['name'];
        
        if($registration['PaymentStatus']['payable'] != 1){
            echo '<br><br> O processo de pagamento pelo PagSeguro já se iniciou por isso não é recomendado alterar informações de pagamento. Caso haja a necessidade de alterar a melhor maneira é cancelar o pagamento no PagSeguro e depois retornar a esta tela.';
        }
        echo '</div>';
    }
    
    
    print $this->Form->input(
        'RegistrationItem.registration_main_variation_id',
        array ('type' => 'select', 'label' => __('Registration Options'), 'div' => 'clearfix', 'empty' => __('(choose an option)', true), 'options' => $main_subscriptions_array)
    );
    
    print $this->Form->input(
        'Registration.0.payment_entity_payment_method_id',
        array ('type' => 'select', 'label' => __('Payment Method'), 'div' => 'clearfix', 'empty' => __('(choose an option)', true), 'options' => $paymentMethods)
    );
    
    print $this->Form->input(
        'Registration.0.payment_status_id',
        array ('type' => 'select', 'label' => __('Payment Status'), 'div' => 'clearfix', 'empty' => __('(choose an option)', true), 'options' => $paymentStatuses)
    );
    ?>
</fieldset>