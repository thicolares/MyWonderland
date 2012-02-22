<fieldset>
    <legend><?php print __('Personal Information'); ?></legend>

<?php
    print $this->Form->hidden('Profile.id');

    print $this->Form->input('Profile.name', array('label' => __('Full Name'),'div' => 'clearfix'));

    print $this->Form->input(
        'Profile.main_doc', 
        array(
            'label' => __('CPF'),
            'div' => 'clearfix required',
        )
    );

    // --------------- Gender --------------------
    $genderOptions = array(
        'M' => __('Male'),
        'F' => __('Female')
    );

    echo $this->Form->input(
            'Profile.gender',
            array ('type' => 'select', 'label' => __('Gender'), 'div' => 'clearfix', 'empty' => __('(choose a gender)', true), 'options' => $genderOptions)
        );
    // --------------- Gender --------------------

    print $this->Form->input(
        'Profile.company', 
        array(
            'label' => __('Institution/Company'),
            'div' => 'clearfix',
        )
    );

    print $this->Form->input(
        'Profile.exibition_name', 
        array(
            'label' => __('Exibition Name'),
            'div' => 'clearfix',
        )
    );
    ?>
</fieldset> <!-- Personal Information -->