<?php

	require_once('includes/load.php');

	$template->add(array(
		'LIST_TITLE' => 'list title',
		'LIST_ERROR' => 'list error'
	));

	for ($i = 1; $i <= 10; $i++) {

		$template->add(array(
			'ATTR_1' => 'item ' . $i . ' attr 1',
			'ATTR_2' => 'item ' . $i . ' attr 2'
		), 'LIST_NAME');

	}

	$template->render('main.tpl', array(
		'header.tpl' => '{{HEADER_HERE}}',
		'list.tpl' => '{{LIST_HERE}}'
	));

?>
