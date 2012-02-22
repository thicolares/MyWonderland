<?php

/**
 * ISSUES
 */
Router::connect(
	'/profile/issues',
	array('plugin' => 'issue', 'controller' => 'issues', 'action' => 'index', 'prefix' => 'profile')
);
Router::connect(
	'/profile/issues/:action/*',
	array('plugin' => 'issue', 'controller' => 'issues', 'prefix' => 'profile')
);

/**
 * PROTOCOLS
 *
 */
Router::connect(
	'/profile/protocols',
	array('plugin' => 'issue', 'controller' => 'protocols', 'action' => 'index', 'prefix' => 'profile')
);
Router::connect(
	'/profile/protocols/:action/*',
	array('plugin' => 'issue', 'controller' => 'protocols', 'prefix' => 'profile')
);

?>