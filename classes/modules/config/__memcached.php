<?php
	abstract class __memcached_config {
		public function memcached() {
			$this->load_forms();
			$params = Array();

			$regedit = regedit::getInstance();

			$is_enabled = $regedit->getVal("//settings/memcached/is_enabled");
			$host = $regedit->getVal("//settings/memcached/host");
			$port = $regedit->getVal("//settings/memcached/port");

			$params['is_enabled'] = $is_enabled;
			$params['host'] = $host;
			$params['port'] = $port;

			$is_connected = memcachedController::getInstance()->getIsConnected();
			if($is_connected) {
				$status = "<span style='color: green;'>Используется</span>";
			} else {
				if($is_enabled) {
					$status = "<span style='color: red;'>Нет подключения</span>";
				} else {
					$status = "Отключен";
				}
			}
			$params['status'] = $status;

			return $this->parse_form("memcached", $params);
		}

		public function memcached_do() {
			$host = $_REQUEST['host'];
			$port = $_REQUEST['port'];
			$is_enabled = (int) $_REQUEST['is_enabled'];

			$regedit = regedit::getInstance();

			$regedit->setVar("//settings/memcached", "");
			$regedit->setVar("//settings/memcached/is_enabled", $is_enabled);
			$regedit->setVar("//settings/memcached/host", $host);
			$regedit->setVar("//settings/memcached/port", $port);

			$this->redirect($this->pre_lang . "/admin/config/memcached/");
		}
	};
?>