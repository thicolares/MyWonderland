<?php

// If is logged, show logout button
if(isset($facebook_user) && !empty($facebook_user)){
	print ' ' . $this->Facebook->logout(array(
		'redirect' =>'/users/logout',
		'size' => 'xlarge',
		'id' => 'user-logout'
	));
}

 print $this->Facebook->login(array('size' => 'large'));

?>