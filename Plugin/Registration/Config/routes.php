<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Connection base '/admin/registration/' to
 * array('plugin' => 'registration', 'controller' => 'registration', 'prefix' => 'admin')
 * ie. If ai type http://mysite.com/admin/registration/ will be same as http://mysite.com/admin/registration/registration/index
 * @author Thiago Colares
 */
Router::connect(
	'/admin/registrations',
	array('plugin' => 'registration', 'controller' => 'registrations', 'action' => 'index', 'prefix' => 'admin')
);

/**
 * REGISTRATION PERIODS
 */
Router::connect(
	'/admin/registration_periods',
	array('plugin' => 'registration', 'controller' => 'registration_periods', 'action' => 'index', 'prefix' => 'admin')
);
Router::connect(
	'/admin/registration_periods/:action/*',
	array('plugin' => 'registration', 'controller' => 'registration_periods', 'prefix' => 'admin')
);

/**
 * REGISTRATION TYPES
 */
Router::connect(
	'/admin/registration_types',
	array('plugin' => 'registration', 'controller' => 'registration_types', 'action' => 'index', 'prefix' => 'admin')
);
Router::connect(
	'/admin/registration_types/:action/*',
	array('plugin' => 'registration', 'controller' => 'registration_types', 'prefix' => 'admin')
);

/**
 * REGISTRATION
 */
Router::connect(
	'/registrations',
	array('plugin' => 'registration', 'controller' => 'registrations', 'action' => 'index')
);

Router::connect(
	'/admin/registrations',
	array('plugin' => 'registration', 'controller' => 'registrations', 'action' => 'index', 'prefix' => 'admin')
);

Router::connect(
	'/admin/registrations/:action/*',
	array('plugin' => 'registration', 'controller' => 'registrations', 'prefix' => 'admin')
);


/**
 * EXEMPTION
 */

Router::connect(
	'/admin/exemptions',
	array('plugin' => 'registration', 'controller' => 'exemptions', 'action' => 'index', 'prefix' => 'admin')
);

Router::connect(
	'/admin/exemptions/:action/*',
	array('plugin' => 'registration', 'controller' => 'exemptions', 'prefix' => 'admin')
);

/**
 * BROADCAST
 */

Router::connect(
	'/admin/broadcasts',
	array('plugin' => 'registration', 'controller' => 'broadcasts', 'action' => 'index', 'prefix' => 'admin')
);

Router::connect(
	'/admin/broadcasts/:action/*',
	array('plugin' => 'registration', 'controller' => 'broadcasts', 'prefix' => 'admin')
);



/**
 * PAYMENTS
 */
Router::connect(
	'/admin/payments',
	array('plugin' => 'registration', 'controller' => 'payments', 'action' => 'index', 'prefix' => 'admin')
);
Router::connect(
	'/admin/payments/:action/*',
	array('plugin' => 'registration', 'controller' => 'payments', 'prefix' => 'admin')
);
Router::connect(
	'/profile/payments',
	array('plugin' => 'registration', 'controller' => 'payments', 'action' => 'index', 'prefix' => 'profile')
);
Router::connect(
	'/profile/payments/:action/*',
	array('plugin' => 'registration', 'controller' => 'payments', 'prefix' => 'profile')
);