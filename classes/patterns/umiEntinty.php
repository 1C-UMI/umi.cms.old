<?php
	abstract class umiEntinty {
		protected $id, $is_updated = false;

		public function __construct($id) {
			$this->setId($id);
			$this->loadInfo();
		}

		public function __destruct() {
			if($this->is_updated) {
				$this->save();
				$this->updateCache();
			}
		}

		public function getId() {
			return $this->id;
		}

		protected function setId($id) {
			$this->id = (int) $id;
		}

		protected function setIsUpdated($is_updated = true) {
			$this->is_updated = (bool) $is_updated;
		}

		abstract protected function loadInfo();

		abstract protected function save();

		public function commit() {
			$res = $this->save();
			$this->setIsUpdated(false);
			return $res;
		}

		public function update() {
			$res = $this->loadInfo();
			$this->setIsUpdated(false);
			return $res;
		}

		public static function filterInputString($string) {
			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$string = iconv("UTF-8//IGNORE", "CP1251", $string);
				$string = mysql_real_escape_string($string);
			} else {
				$string = mysql_real_escape_string($string);
			}

			return $string;

		}
		
		protected function updateCache() {
			memcachedController::getInstance()->save($this, $this->store_type);
		}
	};
?>
