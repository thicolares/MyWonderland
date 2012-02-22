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

/**
 * PAPER
 */
Router::connect(
	'/admin/papers',
	array('plugin' => 'paper', 'controller' => 'papers', 'action' => 'index', 'prefix' => 'admin')
);
Router::connect(
	'/admin/papers/:action/*',
	array('plugin' => 'paper', 'controller' => 'papers', 'prefix' => 'admin')
);

Router::connect(
	'/profile/papers',
	array('plugin' => 'paper', 'controller' => 'papers', 'action' => 'index', 'prefix' => 'profile')
);
Router::connect(
	'/profile/papers/:action/*',
	array('plugin' => 'paper', 'controller' => 'papers', 'prefix' => 'profile')
);

/**
 * EVLATUATION
 */
Router::connect(
	'/profile/evaluations',
	array('plugin' => 'paper', 'controller' => 'evaluations', 'action' => 'index', 'prefix' => 'profile')
);

Router::connect(
	'/profile/evaluations/:action/*',
	array('plugin' => 'paper', 'controller' => 'evaluations', 'prefix' => 'profile')
);