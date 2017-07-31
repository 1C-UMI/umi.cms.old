<?php
	class umiImageFile extends umiFile implements iUmiImageFile {
		public function getWidth() {
			list($width, $height) = getimagesize($this->filepath);
			return $width;
		}

		public function getHeight() {
			list($width, $height) = getimagesize($this->filepath);
			return $height;
		}
	}
?>