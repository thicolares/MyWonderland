<?php


Router::connect(
	'/q/*',
	array('plugin' => 'quiz', 'controller' => 'quizzes', 'action' => 'index')
);

Router::connect(
	'/p/*',
	array('plugin' => 'quiz', 'controller' => 'places', 'action' => 'index')
);

?>