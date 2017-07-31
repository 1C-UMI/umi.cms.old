<?php
	interface iNews {
		public function lastlist($elementPath = "", $template = "default", $limit = false);
  
		public function rubric($elementPath = "", $template = "default");

		public function view($elementId, $template = "default");

		public function related_links($elementId, $template = "default");
	}
?>