<?php
	interface iMemcachedController {
		public function save(umiEntinty $object, $objectType = "unknown", $expire = 86400);
		public function load($objectId, $objectType = "unknwon");
		
		public function saveSql($sqlString, $sqlResourse);
		public function loadSql($sqlString);
	};
?>