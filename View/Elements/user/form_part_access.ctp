<?php
       print $this->Form->hidden('User.id');
	print $this->Form->input(
           'User.email', 
           array(
               'label' => __('Email'),
               'div' => 'clearfix required',
               'maxlength' => 50,
               'type' => 'email'
           )
       );
       
       if(!isset($automaticPassword)) $automaticPassword = false;
   
       if(!$automaticPassword){
           print $this->Form->input(
               'User.password', 
               array(
                   'label' => __('Password'),
                   'div' => 'clearfix required',
                   'type' => 'password',
                   'after' => '<span class="help-block">' . __('This is not you personal E-mail password.') . '</span>'
               )
           );

           print $this->Form->input(
               'User.confirm_password', 
               array(
                   'label' => __('Confirm Password'),
                   'div' => 'clearfix required',
                   'type' => 'password'
               )
           );
       }
?>