<?php
	class umiDate implements iUmiDate {
		public $timestamp;
		public $defaultFormatString = "Y-m-d H:i";

		public function __construct($timestamp = false) {
			if($timestamp === false) {
				$timestamp = self::getCurrentTimeStamp();
			}
			$this->setDateByTimeStamp($timestamp);
		}

		public function getCurrentTimeStamp() {
			return time();
		}

		public function getFormattedDate($formatString = false) {
			if($formatString === false) {
				$formatString = $this->defaultFormatString;
			}
			return date($formatString, $this->timestamp);
		}

		public function setDateByTimeStamp($timestamp) {
			if(!is_numeric($timestamp)) {
				trigger_error("Timestamp must be a numeric", E_USER_ERROR);
				return false;
			}
			$this->timestamp = $timestamp;
			return true;
		}

		public function setDateByString($dateString) {
			$dateString = umiObjectProperty::filterInputString($dateString);
			$timestamp = self::getTimeStamp($dateString);
			return $this->setDateByTimeStamp($timestamp);
		}


		public static function getTimeStamp($dateString) {
			return toTimeStamp($dateString);
		}
	}
?>