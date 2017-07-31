<?php
	interface iRSSFeed {
		public function __construct($url);
		public function loadContent();

		public function loadRSS();
		public function loadAtom();

		public function returnItems();
	}
?>