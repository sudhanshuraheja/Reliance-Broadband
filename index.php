<?php

	$reliance = new Reliance();
	$reliance->login();

	class Reliance {

		private $username = '';
		private $password = '';

		private $base;
		private $login_url;
		private $loggedin_url;
		private $is_logged_in;

		private $start_time;

		private $curl;

		public function __construct() {
			$this->is_logged_in = false;
			//$this->base = 'reliancebroadband.co.in';
			$this->base = '220.224.142.229';
			$this->login_url = 'http://' . $this->base . '/reliance/startportal_isg.do?CPURL=null';

			$this->start_time = time();

			$this->curl = new Curl();
		}

		public function login() {
			echo 'Time ## Message' . "\n";
			if(!$this->is_logged_in) {
				do {
					$time_passed = str_pad(time() - $this->start_time, 4, ' ', STR_PAD_LEFT);
					echo $time_passed . ' ## trying to post [' . $this->username . ', ' . $this->password . '] to the login url [' . $this->login_url . "]\n";
					$data = $this->curl->post($this->login_url, 'userId=' . $this->username . '&password=' . $this->password);
					// If the user logs in, the username shows up on the page after login
					$this->is_logged_in = (strpos($data, $this->username) !== false);
					if( !$this->is_logged_in ) {
						$time_passed = str_pad(time() - $this->start_time, 4, ' ', STR_PAD_LEFT);
						echo $time_passed . ' ## - - - waiting for 10 seconds before trying again - - -' . "\n";
						// Wait for 10 seconds before you try again
						sleep(10);
					} else {
						$time_passed = str_pad(time() - $this->start_time, 4, ' ', STR_PAD_LEFT);
						echo $time_passed . ' __ you have successfully logged in' . "\n";
					}
				} while(!$this->is_logged_in);
			}
		}

	}


	class Curl {

		private $callback = false;
		private $secure = false;
		private $connection = false;
		private $user_agent = '';
		private $user_cookie = '';

		public function __construct() {
			if(!function_exists('curl_version')) {
				var_dump('The extension <strong>php5-curl</strong> has not been installed.');
				return false;
			}
			$this->connection = curl_init();
			$this->setUserAgent('Generatrix 0.47');
			$this->setUserCookie('curl-cookie.txt');
		}

		public function __destruct() {
			if(isset($this->connection)) {
				curl_close($this->connection);
			}
		}
	
		private function setCallback($func_name) {
			$this->callback = $func_name;
		}

		private function doRequest($method, $url, $vars, $header = 1) {
			curl_setopt($this->connection, CURLOPT_URL, $url);
			curl_setopt($this->connection, CURLOPT_HEADER, $header);
			curl_setopt($this->connection, CURLOPT_USERAGENT,$this->user_agent);

			if($this->secure) {
				curl_setopt($this->connection, CURLOPT_SSL_VERIFYHOST,  0);
				curl_setopt($this->connection, CURLOPT_SSL_VERIFYPEER, 0);
			}

			curl_setopt($this->connection, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($this->connection, CURLOPT_COOKIEJAR, $this->user_cookie);
			curl_setopt($this->connection, CURLOPT_COOKIEFILE, $this->user_cookie); 
			curl_setopt($this->connection, CURLOPT_TIMEOUT, 30);

			if ($method == 'POST') {
				curl_setopt($this->connection, CURLOPT_POST, 1);
				curl_setopt($this->connection, CURLOPT_POSTFIELDS, $vars);
			}

			if ($data = curl_exec($this->connection))
				return $data;
			return curl_error($this->connection);
		}

		public function isSecure() {
			$this->secure = true;
		}

		public function get($url, $header = 0) {
			return $this->doRequest('GET', $url, 'NULL', $header);
		}

		public function post($url, $vars, $header = 0) {
			return $this->doRequest('POST', $url, $vars, $header);
		}

		public function setUserAgent($string) {
			$this->user_agent = $string;
		}

		public function setUserCookie($path) {
			$this->user_cookie = $path;
		}
	}

?>
