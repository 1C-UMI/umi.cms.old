<?php
	interface iContent {
		public function content($elementId = false);
		public function insert($elementId);
		public function menu($templateName = "default", $maxDepth = 3, $elementId = false);
		public function sitemap($maxDepth = 3);

		public function gen404();
	}
?>
