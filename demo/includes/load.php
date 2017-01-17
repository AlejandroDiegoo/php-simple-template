<?php
	
	require_once('../simple.template.php');

	$template = new SimpleTemplate('templates/', array(
		'PAGE_TITLE' => 'php-simple-template',
		'PAGE_DESCRIPTION' => 'page description',
		'PAGE_LANGUAGE' => 'en'
	));

?>