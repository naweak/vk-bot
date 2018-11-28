<?php
	namespace Models;
	class BotNames {
		public function exists($text) {
			$exists = \R::getAll("SELECT * FROM `bot_names` WHERE `text` = ?", [
				$text
			]);
			return !empty($exists);
		}
		public function add ($text, $owner_id) {
			if(!$this->exists($text)) {
				\R::exec("INSERT INTO `bot_names`(`text`, `owner_id`, `time`) VALUES (?, ?, ?)", [
					$text,
					$owner_id,
					time()
				]);
			}
		}
		public function get () {
			return \R::getAll('SELECT * FROM `bot_names`');
		}
		public function randomPhrase() {
			$phrases = $this->get();
			$phrase = $phrases[array_rand($phrases)];
			return $phrase['text'];
		}
	}
?>	