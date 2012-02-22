<?php
/*
 * Add the Paper plugin 'locale' folder to your application locales' folders
 */
App::build(array('locales' => App::pluginPath('Paper') . DS . 'locale'));

//Configure::write('Config.language', '/por');
?>