<fieldset>
    <legend><?php print __('Address and Phone Numbers'); ?></legend>
    <?php
    // --------------------- CEP ------------
    echo $this->Form->input('Profile.zipcode', array(
		'label' => __('Zipcode'),
		'class' => 'small',
		'type' => 'text',
		'maxlength' => 10,
        'div' => 'clearfix required',
		'after' => '<span class="help-block">' . __('Only numbers') . '</span>'
	));

    // --------------------- CEP ------------
    
    print $this->Form->input(
        'Profile.address', 
        array(
            'label' => __('Address'),
            'div' => 'clearfix required',
            'after' => '<span class="help-block">'.__('Street, Avenue, Square, etc').'</span>'
        )
    );
    
    print $this->Form->input(
        'Profile.address_complement', 
        array(
            'label' => __('Complement'),
            'div' => 'clearfix',
        )
    );
    
    print $this->Form->input(
        'Profile.address_number', 
        array(
            'label' => __('Number'),
            'div' => 'clearfix required',
        )
    );
    
    $states = array("AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá","BA"=>"Bahia","CE"=>"Ceará","DF"=>"Distrito Federal","ES"=>"Espírito Santo","GO"=>"Goiás","MA"=>"Maranhão","MT"=>"Mato Grosso","MS"=>"Mato Grosso do Sul","MG"=>"Minas Gerais","PA"=>"Pará","PB"=>"Paraíba","PR"=>"Paraná","PE"=>"Pernambuco","PI"=>"Piauí","RJ"=>"Rio de Janeiro","RN"=>"Rio Grande do Norte","RO"=>"Rondônia","RS"=>"Rio Grande do Sul","RR"=>"Roraima","SC"=>"Santa Catarina","SE"=>"Sergipe","SP"=>"São Paulo","TO"=>"Tocantins");
    
    print $this->Form->input(
            'Profile.state',
            array ('type' => 'select', 'label' => __('State'), 'div' => 'clearfix required', 'empty' => __('(choose a state)', true), 'options' => $states)
        );
    
    print $this->Form->input(
        'Profile.city', 
        array(
            'label' => __('City'),
            'div' => 'clearfix required',
        )
    );
    
    print $this->Form->input(
        'Profile.neighborhood', 
        array(
            'label' => __('Neighborhood'),
            'div' => 'clearfix required',
        )
    );
    
    $phone_types = array(
        array(
            'label' => __('Mobile'),
            'field' => 'Profile.mobile',
            'required' => true
        ),
        array(
            'label' => __('Residential Phone'),
            'field' => 'Profile.residential_phone'
        ),
        array(
            'label' => __('Business Phone'),
            'field' => 'Profile.business_phone'
        ),
        array(
            'label' => __('Fax'),
            'field' => 'Profile.fax'
        ),
    );
    
    foreach($phone_types as $item){
        $ddd = $this->Form->input($item['field'].'_ddd', array(
            'label' => $item['label'],
            'class' => "mini",
            'type' => 'text',
            'maxlength' => 2,
            'error' => false
        ));

        $phone = $this->Form->input($item['field'], array(
            'class' => "medium",
            'type' => 'text',
            'error' => false,
            'maxlength' => 8
        ));

        $divClass = 'clearfix';
        if(isset($item['required'])) $divClass .= ' required';
        
        $after = '<span class="help-block">'.__('DDD and Phone Number').'</span>';
        if ($this->Form->isFieldError($item['field'].'_ddd') || $this->Form->isFieldError($item['field'])){
            $divClass .= ' error';
            
            if($this->Form->isFieldError($item['field'].'_ddd'))
                $after .= $this->Form->error($item['field'].'_ddd');
            
            if($this->Form->isFieldError($item['field']))
                $after .= $this->Form->error($item['field']);
        }
        
        print $this->Html->div($divClass,
            $ddd.' - '.$phone.$after
        );
    }
    
    ?>
</fieldset>