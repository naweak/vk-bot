<?php
	require 'core/library/includes.php';

	ini_set('display_errors', $config['display_errors']);
	error_reporting(E_ALL);

	if(!isset($_REQUEST)) exit;

	if(!$config['on']) exit('ok');

	$event = file_get_contents('php://input');

	$event = json_decode($event, true);

	if ($event['secret'] != $config['secret']) exit('ok');

	function logMediaRequest ($query, $type, $from, $peer, $time) {
		file_put_contents(__DIR__ . '/log.txt', json_encode([
			'media_request' => [
				$query,
				$type,
				$from,
				$peer,
				$time
			]
		], JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
	}

	$callback = new Views\Callback (
		$event,
		$config['confirm'],
		$config['blacklist'],
		$config['access_token'],
		$config['user_token'],
		$config['nsfw_requests']
	);
?>
