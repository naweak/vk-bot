<?php
	require 'core/controllers/BotController.php';
	require 'core/models/BotNamesModel.php';
	require 'core/models/vkApiModel.php';
	require 'core/views/CallbackView.php';
	require 'core/library/thirdparty/rb.php';

	$config = [
		'confirm' => '', // Код подтверждения сервера из управления паблика
		'blacklist' => [
			
		], // Черный список пользователей
		'access_token' => "", // Токен паблика
		'display_errors' => 0, // Отображение исключений
		'mysql' => [ // Параметры БД
			'host' => '127.0.0.1', // Хост БД
			'user' => '', // Юзер БД
			'password' => '', // Пароль БД
			'database' => '' // Название БД
		], 
		'user_token' => '', // Токен юзера для действий, которые нельзя выполнять с токеном паблика
		'on' => true // Включение и выключение бота
	];

	R::setup (
		"mysql:host={$config['mysql']['host']};dbname={$config['mysql']['database']}",
		$config['mysql']['user'],
		$config['mysql']['password']
	);

	if(!R::testConnection()) {
		exit('No DB');
	}
?>