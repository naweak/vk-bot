<?php
	namespace Models;
	class vkAPI {
		protected $apiVersion = 5.87;
		protected $vkAPIEndpoint = 'https://api.vk.com/method/';
		protected $token;
		protected $userToken;
		public function _vkApi_call($method, $params = []) {
			if(!isset($params['access_token'])) {
				$params['access_token'] = $this->token;
			}

			$params['v'] = $this->apiVersion;

			$query = http_build_query($params);
			$url = $this->vkAPIEndpoint .  $method . '?' . $query;

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($curl);
			$error = curl_error($curl);
			if ($error) {
				log_error($error);
				throw new Exception("Failed {$method} request");
			}

			curl_close($curl);

			$response = json_decode($json, true);
			if (!$response || !isset($response['response'])) {
				exit('ok');
			}

			return $response['response'];
		}

		public function messagesSend($peer_id, $message, $attachments = []) {
			return $this->_vkApi_call(
				'messages.send',
				[
					'peer_id' => $peer_id,
					'message' => $message,
					'attachment' => implode(',', $attachments)
				]
			);
		}

		public function usersGet ($user_ids, $fields) {
			return $this->_vkApi_call (
				'users.get',
				[
					'user_ids' => $user_ids,
					'fields' => $fields
				]
			);
		}

		public function videoSearch ($q, $adult, $count) {
			return $this->_vkApi_call(
				'video.search',
				[
					'q' => $q,
					'adult' => $adult,
					'count' => $count,
					'access_token' => $this->userToken
				]
			);
		}

		public function photosSearch($q, $count) {
			return $this->_vkApi_call(
				'photos.search',
				[
					'q' => $q,
					'count' => $count,
					'access_token' => $this->userToken,
					'radius' => 50000
				]
			);
		}

		public function __construct($access_token, $user_token)
		{
			$this->token = $access_token;
			$this->userToken = $user_token;
		}
	}
?>