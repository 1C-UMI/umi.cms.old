<?php
	class umiDistrFolder extends umiDistrInstallItem {
		protected $filePath, $permissions;

		public function __construct($filePath = false) {
			if($filePath !== false) {
				$this->filePath = $filePath;
				$this->permissions = fileperms($filePath) & 0x1FF;
			}
		}

		public function pack() {
			return base64_encode(serialize($this));
		}

		public static function unpack($data) {
			return base64_decode(unserialize($data));
		}

		public function restore() {
			$pathinfo = pathinfo($this->filePath);
			if(is_dir($pathinfo['dirname'])) {
				mkdir($this->filePath);
			}

			if(is_dir($this->filePath)) {
				chmod($this->filePath, $this->permissions);
				return true;
			} else {
				return false;
			}
		}
	};
?>