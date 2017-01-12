<?php
	
	require_once('template.php');

	$template = new Template('templates/', array(

		'PAGE_TITLE' => 'php-simple-template',
		'PAGE_LANGUAGE' => 'en',
		'COMMON_VAR_HEADER' => 'this is a global variable',
		'COMMON_VAR_FOOTER' => 'this is another global variable',
		
	));

?>