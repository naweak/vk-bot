<?php 
	namespace Views;
	class Callback {
		protected $event;
		protected $confirm;
		protected $blacklist;
		protected $token;
		protected $userToken;
		protected $nsfwRequests;
		public function newMessageHandle($eventObject) {
			$peerId = $eventObject['peer_id'];
			$command = $eventObject['text'];
			if(!isset($eventObject['text'])) {
				$command = $eventObject['body'];
			}
			$fromId = $eventObject['from_id'];
			$blacklist = $this->blacklist;
			$bot = new \Controllers\Bot(
				$peerId,
				$command,
				$fromId,
				$eventObject,
				$blacklist,
				$this->token,
				$this->userToken,
				$this->nsfwRequests
			);
		}

		public function __construct ($event, $confirm, $blacklist, $access_token, $user_token, $nsfwRequests) {
			$this->event = $event;
			$this->confirm = $confirm;
			$this->blacklist = $blacklist;
			$this->token = $access_token;
			$this->userToken = $user_token;
			$this->nsfwRequests = $nsfwRequests;
			try {
				switch ($event['type']) {
					case 'confirmation':
						echo $this->confirm;
						break;
					case 'message_new':
						$this->newMessageHandle($event['object']);
						echo 'ok';
						break;
					case 'message_edit':
						$this->newMessageHandle($event['object']);
						echo 'ok';
						break;
					default:
						echo ('Unsupported event');
						break;
				}
			} catch (Exception $e) {
				echo $e;
			}
		}
	}
?>