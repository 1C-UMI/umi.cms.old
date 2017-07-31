<?php
	abstract class __index_search {
		public function index_all() {
			$sql = "SELECT id, updatetime FROM cms3_hierarchy WHERE is_deleted = '0'";	//TODO
			$result = mysql_query($sql);
			while(list($element_id, $updatetime) = mysql_fetch_row($result)) {
				if(!$this->elementIsReindexed($element_id, $updatetime)) {
					$this->index_item($element_id);
				}
			}
		}

		public function index_item($element_id) {
			$index_data = __index_search::parseItem($element_id);
		}

		public function elementIsReindexed($element_id, $updatetime) {
			$sql = "SELECT COUNT(*) FROM cms3_search WHERE rel_id = '{$element_id}' AND indextime > '{$updatetime}'";
			$result = mysql_query($sql);
			list($c) = mysql_fetch_row($result);

			return (bool) $c;
		}

		public static function parseItem($element_id) {
			if(!($element = umiHierarchy::getInstance()->getElement($element_id))) {
				return false;
			}

			if($element->getValue("is_unindexed")) return false;


			$index_fields = Array();

			$type_id = $element->getObject()->getTypeId();
			$type = umiObjectTypesCollection::getInstance()->getType($type_id);

			$field_groups = $type->getFieldsGroupsList();
			foreach($field_groups as $field_group_id => $field_group) {
				foreach($field_group->getFields() as $field_id => $field) {
					if($field->getIsInSearch() == false) continue;

					$field_name = $field->getName();

					$val = $element->getValue($field_name);
					if(is_null($val) || !$val) continue;

					$index_fields[$field_name] = $val;
				}
			}

			$index_image = __index_search::buildIndexImage($index_fields);
			__index_search::updateSearchIndex($element_id, $index_image);
		}

		public static function buildIndexImage($index_fields) {
			$img = Array();
			$weight = 1;


			foreach($index_fields as $str) {
				$arr = __index_search::splitString($str);

				foreach($arr as $word)  {
					if(array_key_exists($word, $img)) {
						$img[$word] += $weight;
					} else {
						$img[$word] = $weight;
					}
				}
			}
			return $img;
		}

		public static function splitString($str) {
			if(is_object($str)) {	//TODO: Temp
				return NULL;
			}

			$to_space = Array("&nbsp;", "&quote;", ".", ",", "?", ":", ";", "%", ")", "(", "/", 0x171, 0x187, "<", ">");

			$str = str_replace(">", "> ", $str);
			$str = str_replace("\"", " ", $str);
			$str = strip_tags($str);
			$str = str_replace($to_space, " ", $str);
			$str = preg_replace("/([ ]{1-100})/u", " ", $str);
			$str = wa_strtolower($str);
			$tmp = explode(" ", $str); 

			$res = Array();
			foreach($tmp as $v) {
				$v = trim($v);

				if(strlen($v) <= 2) continue;

				$res[] = $v;
			}

			return $res;
		}

		public static function updateSearchIndex($element_id, $index_image) {
			$element = umiHierarchy::getInstance()->getElement($element_id);

			$domain_id = $element->getDomainId();
			$lang_id = $element->getLangId();
			$type_id = $element->getTypeId();

			$sql = "SELECT COUNT(*) FROM cms3_search WHERE rel_id = '{$element_id}'";
			list($c) = mysql_fetch_row(mysql_query($sql));

			if(!$c) {
				$sql = "INSERT INTO cms3_search (rel_id, domain_id, lang_id, type_id) VALUES('{$element_id}', '{$domain_id}', '{$lang_id}', '{$type_id}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_ERROR);
				}
			}

			$sql = "DELETE FROM cms3_search_index WHERE rel_id = '{$element_id}'";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			foreach($index_image as $word => $weight) {
				if(($word_id = __index_search::getWordId($word)) == false) continue;

				$sql = "INSERT INTO cms3_search_index (rel_id, weight, word_id) VALUES('{$element_id}', '{$weight}', '{$word_id}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					continue;
				}
			}

			$time = time();

			$sql = "UPDATE cms3_search SET indextime = '{$time}' WHERE rel_id = '{$element_id}'";
			mysql_query($sql);
			
			umiHierarchy::getInstance()->unloadElement($element_id);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}

		public static function getWordId($word) {
			$word = mysql_real_escape_string($word);

			$sql = "SELECT id FROM cms3_search_index_words WHERE word = '{$word}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($word_id) = mysql_fetch_row($result)) {
				return $word_id;
			} else {
				$sql = "INSERT INTO cms3_search_index_words (word) VALUES('{$word}')";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				return (int) mysql_insert_id();
			}
		}


		public function getIndexPages() {
			$sql = "SELECT SQL_CACHE SQL_SMALL_RESULT COUNT(*) FROM cms3_search";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				list($c) = mysql_fetch_row($result);
				return (int) $c;
			}
		}


		public function getIndexWords() {
			$sql = "SELECT SQL_CACHE SQL_SMALL_RESULT SUM(weight) FROM cms3_search_index";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				list($c) = mysql_fetch_row($result);
				return (int) $c;
			}
		}


		public function getIndexWordsUniq() {
			$sql = "SELECT SQL_CACHE SQL_SMALL_RESULT COUNT(*) FROM cms3_search_index_words";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				list($c) = mysql_fetch_row($result);
				return (int) $c;
			}
		}


		public function getIndexLast() {
			$sql = "SELECT SQL_CACHE SQL_SMALL_RESULT indextime FROM cms3_search ORDER BY indextime DESC LIMIT 1";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				list($c) = mysql_fetch_row($result);
				return (int) $c;
			}
		}


		public function truncate_index () {
			$sql = "TRUNCATE TABLE cms3_search_index_words";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$sql = "TRUNCATE TABLE cms3_search_index";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}


			$sql = "TRUNCATE TABLE cms3_search";
			mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			return true;
		}
	};
?>