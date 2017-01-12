<?php

	require_once('includes/load.php');
	
	$template->set(array(
		'common' => 'main.tpl',
		'body' => 'list.tpl'
	));

	$template->vars(array(
		'PAGE_VAR' => 'this is a page variable',
		'LIST_ERROR' => 'list error'
	));

	for ($i = 1; $i <= 10; $i++) {
	    $template->blocks('LIST', array(
			'ID' => $i,
			'TITLE' => 'title' . $i
		));
	}

	$template->output('common', array(
		'body' => '{TEMPLATE_BODY}'
	));

?>