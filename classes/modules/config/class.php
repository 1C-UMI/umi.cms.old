<?php

class config extends def_module {
	public function __construct() {
		parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			if(defined("CURRENT_VERSION_LINE")) {
				if(CURRENT_VERSION_LINE == "free" || CURRENT_VERSION_LINE == "lite" || CURRENT_VERSION_LINE == "freelance") {
					$is_soho = true;
				}
			} else {
				$is_soho = false;
			}

			$this->__loadLib("__admin.php");
			$this->__implement("__config");

			$this->__loadLib("__memcached.php");
			$this->__implement("__memcached_config");

			$this->__loadLib("__mails.php");
			$this->__implement("__mails_config");

			$this->__loadLib("__langs.php");
			$this->__implement("__langs_config");
			
			if(!$is_soho) {
				$this->__loadLib("__domains.php");
				$this->__implement("__domains_config");
			}


			$this->sheets_add("Глобальные", "main");
			$this->sheets_add("Модули", "modules");
			$this->sheets_add("Языки", "langs");
			
			if(!$is_soho) {
				$this->sheets_add("Домены", "domains");
			}
			
			$this->sheets_add("Memcached", "memcached");
			$this->sheets_add("Почта", "mails");
		}
	}
};


?>