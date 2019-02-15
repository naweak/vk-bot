<?php
	require 'core/controllers/BotController.php';
	require 'core/models/BotNamesModel.php';
	require 'core/models/vkApiModel.php';
	require 'core/models/SmartRepliesModel.php';
	require 'core/views/CallbackView.php';
	require 'core/library/thirdparty/rb.php';

	$config = [
		'confirm' => '', // Код подтверждения сервера из управления паблика
		'blacklist' => [], // Черный список пользователей
		'access_token' => "", // Токен паблика
		'display_errors' => 0, // Отображение исключений
		'mysql' => [ // Параметры БД
			'host' => '', // Хост БД
			'user' => '', // Юзер БД
			'password' => '', // Пароль БД
			'database' => '' // Название БД
		], 
		'user_token' => '', // Токен юзера для действий, которые нельзя выполнять с токеном паблика
		'on' => true, // Включение и выключение бота
		'nsfw_requests' => "/(snuf|снаф|(.*?)спарив(.*?)жив(.*?)|ювелир|гной|гнил|(a|а)(m|м)(p|п)(u|y|у)(t|т)|yiff|гомо|cup|jar|нарк|ezzyrc|телег|teleg|w(h){0,}ats(u|a)pp|ва(тс|ц)|подработка|кл(a|а)дм|з(a|а)кл(a|а)д|к(y|у)(p|р)ь(e|е)(p|р)|(p|р)(o|о)жд(e|е)ни(e|е) (ю(p|р)(ы|(o|о)ч(k|к)и)){0,}\s{0,}г(p|р)иг(о|o)(p|р)ян(a|а)|взрыв пиписьки|цп|(.*?)(отрезал|отрубил)(.*?)((.*?)член(.*?)|(.*?)писюн(.*?)|(.*?)хуй(.*?))(.*?)|кастра|(.*?)(член|хуй)(.*?)в(.*?)розетке(.*?)|cp|child\s{0,}porn|детское порно|copr|копр|зоопрон|членодевк|зоофил|зоопорн|zooporn|zoophil|бабко|аутоф(и|е)л|asian|k-{0,}pop|к-{0,}поп|яой|yaoi|trap|трап|транс|trans|gay|гей|повесился на яйцах|гуро|guro)/iu" // Защита от уебанов с шок-калтентом, выключается пустой строкой
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
