<?php
	interface iRSSItem {
		public function setTitle($title);
		public function getTitle();

		public function setContent($content);
		public function getContent();

		public function setDate($date);
		public function getDate();

		public function setUrl($url);
		public function getUrl();
	}
?>