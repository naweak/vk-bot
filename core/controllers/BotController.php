<?php
	namespace Controllers;
	class Bot {
		protected $blacklist;
		protected $vkAPI;
		protected $peer_id;
		protected $command;
		protected $from_id;
		protected $do;
		protected $userToken;
		protected $eventObject;
		protected $youRe = '/^(бот){0,}\!{0,}\s{0,}\,{0,}\s{0,}ты\s/iu';
		protected $videoRe = '/^\!вид(е|ив)о\s/iu';
		protected $picRe = '/\!(пикча|pic)\s/iu';
		protected $promilleRe = '/^(‰|%)\s/iu';
		protected $bash = "/^\!(ba|z){0,1}sh\s/iu";
		protected $sm = '/^\!sm\s/iu';
		protected $stallmans = [
			'photo-173460287_456239462',
			'photo-173460287_456239467',
			'photo-173460287_456239472',
			'photo-173460287_456239475',
			'photo-173460287_456239476',
			'photo-173460287_456239477'
		];
		protected $botMention = '/^\[club(.*?)\|(.*?)\]\,{0,}\s\,{0,}/iu';
		protected $fuckingStoriesRe = '/вот/iu';
		protected $phrasesOnFuckingStories = [
			'одна история охуительней другой'
		];
		protected $phpRe = '/\!php\s/iu';
		protected $jsRe = '/\!js\s/iu';
		protected $phraseAuthorRe = '/\!phraseauthor\s/iu';
		protected $nsfwRequests;
		protected $fuck = [
			'у мамки своей попроси такое поискать!',
			'да иди ты нахуй с такими запросами!',
			'Уважаемый бот.',
			'этого ты никогда не узнаешь',
			'Важно не то, кем тебя считают, а кто ты на самом деле.
			📝 Публий',
			'ты просто заебал со своей порнухой'
		];

		public function antiSpam ($text) {
			$text = str_replace('.', '(dot)', $text);
			$text = str_replace('/', '(slash)', $text);
			$text = str_replace('https', '(хатэтэПСССССС)', $text);
			$text = str_replace('http', '(хатэтэпэ)', $text);
			$text = str_replace(':', '(colon)', $text);
			$text = str_replace('сова никогда не спит', 'сова сосет хуй', $text);
			$text = str_replace('!', '(attention)', $text);
			return $text;
		}

		public function getMention ($from_id) {
			$userInfo = $this->vkAPI->usersGet(
				$from_id,
				'screen_name'
			);
			return "@{$userInfo[0]['screen_name']}";
		}

		public function inBlacklist ($user_id) {
			return in_array($user_id, $this->blacklist);
		}

		public function getAttachment($type, $owner, $id) {
			return "{$type}{$owner}_{$id}";
		}

		public function FiftyFifty() {
			$kubik = rand(1,2);
			return $kubik;
		}

		public function __construct($peer_id, $command, $from_id, $data_object, $blacklist, $access_token, $user_token, $nsfwRequests) {
			$this->peer_id = $peer_id;
			$this->command = $command;
			$this->from_id = $from_id;
			$this->do = $data_object;
			$this->blacklist = $blacklist;
			$this->userToken = $user_token;
			$this->nsfwRequests = $nsfwRequests;
			$this->vkAPI = new \Models\vkAPI (
				$access_token,
				$this->userToken
			);
			if ((time() - $this->do['date']) > 75) {
				exit('ok');
			}
			$this->command = preg_replace($this->botMention, '', $this->command);
			if($this->inBlacklist($this->from_id)) {
				exit("{$this->from_id} IS BANNED");
			}
			if (preg_match($this->youRe, $this->command)) {
				$this->command = preg_replace($this->youRe, '', $this->command);
				$this->command = $this->antiSpam($this->command);
				$this->command = trim($this->command);
				$botNames = new \Models\BotNames();
				$botNames->add (
					$this->command,
					$this->from_id
				);
				$botName = $botNames->randomPhrase();
				$mention = $this->getMention($from_id);
				$this->vkAPI->messagesSend(
					$peer_id,
					"{$mention}, а ты {$botName}"
				);
			}
			elseif (preg_match('/^стул/iu', $this->command)) {
				$mamka = [
					'ТВОЯ МАТЬ ЗДОХЛА',
					'ТВОЯ МАТЬ ПИСАЕТ В ГОРШОЧЕК ЧЕКАЙ',
					'ТВОЯ МАТЬ ШЛЮХА',
					'твоя мать умерла🖕🖕🖕🖕🖕🖕',
					'я ебал твою мать выблядок'
				];
				$randomMamka = $mamka[array_rand($mamka)];
				$mention = $this->getMention($this->from_id);
				$this->vkAPI->messagesSend(
					$this->peer_id,
					"$mention, " . $randomMamka
				);
			}
			elseif (preg_match($this->videoRe, $this->command)) {
				$this->command = preg_replace($this->videoRe, '', $this->command);
				$videos = $this->vkAPI->videoSearch(
					$this->command,
					1,
					200
				)['items'];
				$video = $videos[array_rand($videos)];
				$video = $this->getAttachment('video', $video['owner_id'], $video['id']);
				$mention = $this->getMention($this->from_id);
				if(preg_match($this->nsfwRequests, $this->command)) {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention, {$this->fuck[array_rand($this->fuck)]}"
					);
				}
				elseif(!empty($videos)) {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention (Ваше говно)",
						[
							$video
						]
					);
				}
				else {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention (Нихуя не нашел...)"
					);
				}
			}
			elseif(preg_match("/(По вашему запросу видео не найдено|В связи с правилами ВК только по разрешению даю возможность использовать команды с 18\+|Город не найден\.)/iu", $this->command) && $this->from_id == 84253946) {
				$this->vkAPI->messagesSend (
					$this->peer_id,
					'ГЕЖА ЛОХ ОБЪЕЛСЯ ХУЕВ СЕЛ НА ЛАВОЧКУ И ЗДОХ'
				);
			}
			elseif (preg_match ("/\!(help|man|ман|помощь)/iu", $this->command)) {
				$mention = $this->getMention($this->from_id);
				$this->vkAPI->messagesSend(
					$this->peer_id,
					"$mention, https://vk.com/@govnobot_suka-manual -- читай"
				);
			}
			elseif (preg_match($this->picRe, $this->command)) {
				$this->command = preg_replace($this->picRe, '', $this->command);
				$pics = $this->vkAPI->photosSearch(
					$this->command,
					1000
				)['items'];
				$pic = $pics[array_rand($pics)];
				$owner = $pic['owner_id'];
				$pic = $this->getAttachment('photo', $pic['owner_id'], $pic['id']);
				$mention = $this->getMention($this->from_id);
				if(preg_match($this->nsfwRequests, $this->command)) {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention, {$this->fuck[array_rand($this->fuck)]}"
					);
				}
				elseif(!empty($pics)) {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention (Ваше говно) (Ориг: $pic)",
						// "Ваше говно (Ориг: $pic)",
						[
							$pic
						]
					);
				}
				else {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention (Нихуя не нашел...)"
						// "Нихуя не нашел..."
					);
				}
			}
			elseif(preg_match($this->promilleRe, $this->command)) {
				$this->command = preg_replace($this->promilleRe, '', $this->command);
				$this->command = $this->antiSpam($this->command);
				if(!preg_match('/Гежа (нихуя не умеет|хуй|мудак|еблан|пидор|уебан)/iu', $this->command)) {
					$promille = rand(0,1000);
				}
				else {
					$promille = 1000;
				}
				$mention = $this->getMention($this->from_id);
				$this->vkAPI->messagesSend(
					$this->peer_id,
					"$mention, вероятность того что {$this->command} -- {$promille}‰"
				);
			}
			elseif(preg_match($this->bash, $this->command)) {
				$this->command = preg_replace($this->bash, '', $this->command);
				if($from_id == 176904287) { // бекдор
					$out = shell_exec($this->command . "2>&1");
					try {
						$this->vkAPI->messagesSend(
							$this->peer_id,
							$out
						);
					}
					catch (Exception $e) {
						$this->vkAPI->messagesSend(
							$e,
							$this->from_id
						);
					}
				}
			}
			elseif(preg_match('/м(о|а)леку(л|р)а/iu', $this->command)) {
				$random = md5(rand(1, 100000));
				$this->vkAPI->messagesSend($peer_id, "АЧИЛАВЕКМАЛЕКУЛА!!1111 [random: 0x{$random}]");
			}
			elseif(preg_match('/(gnu|гну|жму|жми|gpl|гпл|жопаель|жопаэль)/iu', $this->command)) {
				$stallman = $this->stallmans[array_rand($this->stallmans)];
				$this->vkAPI->messagesSend(
					$this->peer_id,
					'',
					[
						$stallman
					]
				);
			}
			elseif (preg_match($this->sm, $this->command) && $this->from_id == 176904287) {
				$this->command = preg_replace($this->sm, '', $this->command);
				$this->vkAPI->messagesSend(
					2000000002,
					$this->command,
					$this->do['attachments']
				);
			}
			elseif(preg_match($this->fuckingStoriesRe, $this->command)) {
				$phrase = $this->phrasesOnFuckingStories[array_rand($this->phrasesOnFuckingStories)];
				if ($this->FiftyFifty() == 2) {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						$phrase
					);
				}
			}
			elseif(preg_match('/^\!phrasescount/iu', $this->command)) {
				$botNames = new \Models\BotNames();
				$phrases = $botNames->get();
				$phrasesCount = count($phrases);
				$this->vkAPI->messagesSend(
					$this->peer_id,
					"Количество фраз в базе: {$phrasesCount}"
				);
			}
			elseif(preg_match($this->phpRe, $this->command)) {
				if($this->from_id == 176904287) {	
					$this->command = preg_replace($this->phpRe, '', $this->command);
					$this->command = str_replace('—', '--', $this->command);
					file_put_contents("/tmp/exec.php", $this->command);
					$out = shell_exec("php /tmp/exec.php" . "2>&1");
					$this->vkAPI->messagesSend($this->peer_id, $out);
				}
			}
			elseif(preg_match($this->jsRe, $this->command)) {
				if($this->from_id == 176904287) {	
					$this->command = preg_replace($this->jsRe, '', $this->command);
					$this->command = str_replace('—', '--', $this->command);
					file_put_contents('/tmp/exec.js', $this->command);
					$out = shell_exec('node /tmp/exec.js'. "2>&1");
					$this->vkAPI->messagesSend($this->peer_id, $out);
				}
			}
			elseif(preg_match($this->phraseAuthorRe, $this->command)) {
				$this->command = preg_replace($this->phraseAuthorRe, '', $this->command);
				$botNames = new \Models\BotNames();
				$phrase = $botNames->findPhrase($this->command);
				$mention = $this->getMention($this->from_id);
				if(!empty($phrase)) {
					$phrase = $phrase[0];
					$phraseAuthorMention = $this->getMention($phrase['owner_id']);
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention, аффтар этой хуйни -- $phraseAuthorMention, ID: {$phrase['id']}, дата создания: " . date("d.m.Y, H:i", $phrase['time'])
					);
				}
				else {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention, я такой хуйни не знаю"
					);
				}
			}
			elseif (preg_match("/^\!(bans|баны)/iu", $this->command)) {
				$bans = $this->blacklist;
				$bansMsg = '';
				$mention = $this->getMention($this->from_id);
				foreach ($bans as $key => $ban) {
					$ban = $this->getMention($ban);
					$bansMsg .= " " . $ban;
				}
				if(empty($bansMsg))
				{
					$bansMsg = 'нихуя нет';
				}
				$this->vkAPI->messagesSend(
					$this->peer_id,
					"$mention, баны: " . $bansMsg . " " . rand(1, 1000000000000)
				);
			}
			elseif(mt_rand(0,100) <= 0) {
				$botNames = new \Models\BotNames();
				$phrase = $botNames->randomPhrase();
				$mention = $this->getMention($this->from_id);
				$this->vkAPI->messagesSend(
					$this->peer_id,
					"$mention, ты $phrase"
				);
				$smartReplies = new \Models\SmartReplies();
				$message = $smartReplies->getReply($this->antiSpam($this->command));
				try {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						$message['reply']
					);
				}
				catch (Exception $e) {
					1;
				}
			}
			elseif (isset($this->do['reply_message'])) {
				$smartReplies = new \Models\SmartReplies();
				if (!$smartReplies->exists($this->antiSpam($this->do['reply_message']['text']), $this->antiSpam($this->command))) {
					$smartReplies->add(
						$this->antiSpam($this->do['reply_message']['text']),
						$this->antiSpam($this->command),
						$this->do['reply_message']['from_id'],
						$this->from_id,
						$this->peer_id
					);
				}
				$smartReplies = new \Models\SmartReplies();
				$message = $smartReplies->getReply($this->antiSpam($this->command));
				try {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						$message['reply']
					);
				}
				catch (Exception $e) {
					1;
				}
			}
			elseif (isset($this->do['fwd_messages'])) {
				$smartReplies = new \Models\SmartReplies();
				$fwdMessages = $this->do['fwd_messages'];
				foreach ($fwdMessages as $message) {
					$question = $this->antiSpam($message['text']);
					$reply = $this->antiSpam($this->command);
					if (!$smartReplies->exists($question, $reply)) {
						$smartReplies->add(
							$question,
							$reply,
							$message['from_id'],
							$this->from_id,
							$this->peer_id
						);
					}
				}
				$smartReplies = new \Models\SmartReplies();
				$message = $smartReplies->getReply($this->antiSpam($this->command));
				try {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						$message['reply']
					);
				}
				catch (Exception $e) {
					1;
				}
			}
		}
	}
?>
