<?php

	require_once('includes/load.php');

	$template->vars(array(
		'PAGE_VAR' => 'this is a page variable',
		'LIST_ERROR' => 'list error'
	));

	for ($i = 1; $i <= 10; $i++) {
		$template->blocks('LIST', array(
			'ID' => $i,
			'TITLE' => 'title ' . $i
		));
	}

	$template->output('main.tpl', array(
		'list.tpl' => '{TEMPLATE_BODY}'
	));

?>
