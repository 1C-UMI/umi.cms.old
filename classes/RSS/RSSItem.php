<?php
	class RSSItem implements iRSSItem {
		private $url, $title, $anons, $content;

		public function setTitle($title) {
			$this->title = $title;
		}

		public function getTitle() {
			return $this->title;
		}


		public function setContent($content) {
			$this->content = $content;
		}

		public function getContent() {
			return $this->content;
		}


		public function setDate($date) {
			$this->date = $date;
		}

		public function getDate() {
			return $this->date;
		}

		public function setUrl($url) {
			$this->url = $url;
		}

		public function getUrl() {
			return $this->url;
		}
	}
?>