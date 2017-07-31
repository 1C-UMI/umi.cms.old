<?php
	interface iCatalog {
		public function category($template = "default", $elementPath = false);

		public function getCategoryList($template = "default", $categoryId, $limit = false);
		public function getObjectsList($template = "default", $categoryId, $limit = false);

		public function viewObject($elementId, $template = "default");
		public function object($template = "default", $elementPath = false);

		public function search($categoryId, $groupNames, $template = "default");

		public function getEditLink($elementId, $elementType);
	}
?>