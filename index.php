<?php
	require 'core/library/includes.php';

	ini_set('display_errors', $config['display_errors']);
	error_reporting(E_ALL);

	if(!isset($_REQUEST)) {
		exit;
	}

	if(!$config['on']) {
		exit('ok');
	}

	$event = file_get_contents('php://input');

	$event = json_decode($event, true);

	$callback = new Views\Callback (
		$event,
		$config['confirm'],
		$config['blacklist'],
		$config['access_token'],
		$config['user_token']
	);
?>