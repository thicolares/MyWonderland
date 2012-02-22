<?php

	echo $this->element("system/profile_dashboard");

	/*
		TODO Show last papers to appraiser
	*/
	// switch ($this->Session->read('Auth.User.role')) {
	// 	case 'registered':
	// 		echo $this->element("system/profile_dashboard");
	// 		break;
	// 		
	// 	case 'appraiser':
	// 		echo $this->element("system/appraiser_dashboard");
	// 		break;
	// 
	// 	default:
	// 		echo $this->element("system/profile_dashboard");
	// 		break;
	// }
?>