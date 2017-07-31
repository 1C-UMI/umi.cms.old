<?php
	abstract class singleton {
		private static $instances = Array();

		abstract protected function __construct();

		public static function getInstance($c) {
			if (!isset(singleton::$instances[$c])) {
				singleton::$instances[$c] = new $c;

//Debug code follows here
//				echo "Creating new \"{$c}\" instance...\n";
//				flush();
//				if(sizeof(singleton::$instances) > 25) exit("Ooops...");
			}
			return singleton::$instances[$c];
		}

		public function __clone() {
			trigger_error('Singletone clonning is not permitted. Just becase it\'s non-sense.', E_USER_ERROR);
		}
	};
?>