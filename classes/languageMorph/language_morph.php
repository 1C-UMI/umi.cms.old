<?php
	class language_morph implements iLanguageMorph {	//TODO Write interface
		private $lang;

		public function __construct() {
			//TODO
		}

		public static function get_word_base($word) {
			return morph_get_root($word);	//TODO
		}

		public static function get_word_morph($word, $type = 'noun', $count = 0) {
			//TODO
		}
	};
?>