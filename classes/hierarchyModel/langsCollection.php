<?php
	class langsCollection extends singleton implements iSingleton, iLangsCollection {
		private $langs = Array(),
			$def_lang;

		protected function __construct() {
			$this->loadLangs();
		}


		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}

		private function loadLangs() {
			$sql = "SELECT SQL_CACHE id FROM cms3_langs";
			$result = mysql_query($sql);

			if($err = mysql_error()) trigger_error($err, E_USER_WARNING);

			while(list($lang_id) = mysql_fetch_row($result)) {
				if($lang = memcachedController::getInstance()->load($lang_id, "lang")) {
				} else {
					$lang = new lang($lang_id);
					memcachedController::getInstance()->save($lang, "lang");
				}
				
				$this->langs[$lang_id] = $lang;
				if($this->langs[$lang_id]->getIsDefault()) {
					$this->def_lang = $this->langs[$lang_id];
				}
			}
		}

		public function getLangId($prefix) {
			foreach($this->langs as $lang) {
				if($lang->getPrefix() == $prefix) {
					return $lang->getId();
				}
			}
			return false;
		}

		public function addLang($prefix, $title, $is_default = false) {
			if($lang_id = $this->getLangId($prefix)) {
				return $lang_id;
			}

			$sql = "INSERT INTO cms3_langs VALUES()";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				$lang_id = mysql_insert_id();

				$lang = new lang($lang_id);

                                $lang->setPrefix($prefix);
				$lang->setTitle($title);
				$lang->setIsDefault($is_default);

				$lang->commit();

				$this->langs[$lang_id] = &$lang;

				return $lang_id;
			}
		}

		public function delLang($lang_id) {
			$lang_id = (int) $lang_id;

			if(!$this->isExists($lang_id)) {
				return false;
			}

			$sql = "DELETE FROM cms3_langs WHERE id = '{$lang_id}'";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				unset($this->langs[$lang_id]);
			}
		}

		public function getLang($lang_id) {
			$lang_id = (int) $lang_id;
			return ($this->isExists($lang_id)) ? $this->langs[$lang_id] : false;
		}

		public function isExists($lang_id) {
			return (bool) array_key_exists($lang_id, $this->langs);
		}

		public function getList() {
			return (Array) $this->langs;
		}

		public function setDefault($lang_id) {
			if(!$this->isExists($lang_id)) {
				return false;
			}

			if($this->def_lang) {
				$this->def_lang->setIsDefault(false);
				$this->def_lang->commit();
			}

			$this->def_lang = $this->getLang($lang_id);
			$this->def_lang->setIsDefault(true);
			$this->def_lang->commit();
		}

		public function getDefaultLang() {
			return ($this->def_lang) ? $this->def_lang : false;
		}


		public function getAssocArray() {
			$res = Array();

			foreach($this->langs as $lang) {
				$res[$lang->getId()] = $lang->getTitle();
			}

			return $res;
		}
	}
?>