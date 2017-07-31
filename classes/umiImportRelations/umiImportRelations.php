<?php
	class umiImportRelations extends singleton implements iUmiImportRelations {
		protected function __construct() {
		}
		
		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}


		public function getSourceId($source_name) {
			$source_name = mysql_escape_string($source_name);

			$sql = "SELECT id FROM cms3_import_sources WHERE source_name = '{$source_name}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($source_id) = mysql_fetch_row($result)) {
				return $source_id;
			} else {
				return false;
			}
		}


		public function addNewSource($source_name) {
			if($source_id = $this->getSourceId($source_name)) {
				return $source_id;
			} else {
				$source_name = mysql_escape_string($source_name);

				$sql = "INSERT INTO cms3_import_sources (source_name) VALUES('{$source_name}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					return mysql_insert_id();
				}
			}
		}


		public function setIdRelation($source_id, $old_id, $new_id) {
			$source_id = (int) $source_id;
			$old_id = (int) $old_id;
			$new_id = (int) $new_id;

			$sql = "DELETE FROM cms3_import_relations WHERE source_id = '{$source_id}' AND (new_id = '{$new_id}' OR old_id = '{$old_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$sql = "INSERT INTO cms3_import_relations (source_id, old_id, new_id) VALUES('{$source_id}', '{$old_id}', '{$new_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}


		public function getNewIdRelation($source_id, $old_id) {
			$source_id = (int) $source_id;
			$old_id = (int) $old_id;

			$sql = "SELECT new_id FROM cms3_import_relations WHERE old_id = '{$old_id}' AND source_id = '{$source_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($new_id) = mysql_fetch_row($result)) {
				return (int) $new_id;
			} else {
				return false;
			}
		}


		public function getOldIdRelation($source_id, $new_id) {
			$source_id = (int) $source_id;
			$new_id = (int) $new_id;

			$sql = "SELECT old_id FROM cms3_import_relations WHERE new_id = '{$new_id}' AND source_id = '{$source_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($old_id) = mysql_fetch_row($result)) {
				return (int) $old_id;
			} else {
				return false;
			}
		}



		public function setTypeIdRelation($source_id, $old_id, $new_id) {
			$source_id = (int) $source_id;
			$old_id = (int) $old_id;
			$new_id = (int) $new_id;

			$sql = "DELETE FROM cms3_import_types WHERE source_id = '{$source_id}' AND (new_id = '{$new_id}' OR old_id = '{$old_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$sql = "INSERT INTO cms3_import_types (source_id, old_id, new_id) VALUES('{$source_id}', '{$old_id}', '{$new_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}


		public function getNewTypeIdRelation($source_id, $old_id) {
			$source_id = (int) $source_id;
			$old_id = (int) $old_id;

			$sql = "SELECT new_id FROM cms3_import_types WHERE old_id = '{$old_id}' AND source_id = '{$source_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($new_id) = mysql_fetch_row($result)) {
				return (int) $new_id;
			} else {
				return false;
			}
		}


		public function getOldTypeIdRelation($source_id, $new_id) {
			$source_id = (int) $source_id;
			$new_id = (int) $new_id;

			$sql = "SELECT old_id FROM cms3_import_types WHERE new_id = '{$new_id}' AND source_id = '{$source_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($old_id) = mysql_fetch_row($result)) {
				return (int) $old_id;
			} else {
				return false;
			}
		}


		public function setFieldIdRelation($source_id, $type_id, $old_field_name, $new_field_id) {
			$source_id = (int) $source_id;
			$type_id = (int) $type_id;
			$old_field_name = mysql_escape_string($old_field_name);
			$new_field_id = (int) $new_field_id;


			$sql = "DELETE FROM cms3_import_fields WHERE source_id = '{$source_id}' AND type_id = '{$type_id}' AND (field_name = '{$old_field_name}' OR new_id = '{$new_field_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}


			$sql = "INSERT INTO cms3_import_fields (source_id, type_id, field_name, new_id) VALUES('{$source_id}', '{$type_id}', '{$old_field_name}', '{$new_field_id}')";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return (int) $new_field_id;
			}
		}


		public function getNewFieldId($source_id, $type_id, $old_field_name) {
			$source_id = (int) $source_id;
			$type_id = (int) $type_id;
			$old_field_name = mysql_escape_string($old_field_name);

			$sql = "SELECT new_id FROM cms3_import_fields WHERE source_id = '{$source_id}' AND type_id = '{$type_id}' AND field_name = '{$old_field_name}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($new_field_id) = mysql_fetch_row($result)) {
				return (int) $new_field_id;
			} else {
				return false;
			}
		}


		public function getOldFieldName($source_id, $type_id, $new_field_id) {
			$source_id = (int) $soruce_id;
			$type_id = (int) $type_id;
			$new_field_id = (int) $new_field_id;

			$sql = "SELECT field_name FROM cms3_import_fields WHERE source_id = '{$source_id}' AND type_id = '{$type_id}' AND new_id = '{$new_field_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($old_field_name) = mysql_fetch_row($result)) {
				return (string) $old_field_name;
			} else {
				return false;
			}
		}
	};
?>