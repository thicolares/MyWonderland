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
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes.  See the CakePlugin documentation on 
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Users routes
 *
 * @author Thiago Colares
 */
	Router::connect(
		'/login',
		array('controller' => 'users', 'action' => 'login')
	);
	Router::connect(
		'/logout',
		array('controller' => 'users', 'action' => 'logout')
	);
	
/**
 * Admin and Profile routes / Preferences
 *
 * @author Thiago Colares
 */
	Router::connect(
		'/admin',
		array('controller' => 'systems', 'action' => 'dashboard', 'prefix' => 'admin', 'admin' => true)
	);
	Router::connect(
		'/profile',
		array('controller' => 'systems', 'action' => 'dashboard', 'prefix' => 'profile', 'profile' => true)
	);
	Router::connect(
		'/profile/edit',
		array('controller' => 'profiles', 'prefix' => 'profile', 'action' => 'edit')
	);

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';

