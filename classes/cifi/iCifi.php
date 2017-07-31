<?php
	interface iCifi {
		public function __construct($cifiName, $sourceDir, $imagesOnly = true);
		public function read_files();
		public function make_element($defaultValue = "");
		public function make_div();
		public function make_upload();
		public function getUpdatedValue($useHTTP_POST_FILES = false);
	};
?>