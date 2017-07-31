<?php
	class umiHierarchy extends singleton implements iSingleton, iUmiHierarchy {
		private $elements = Array(),
			$objects, $langs, $domains, $templates;
			
		private $updatedElements = Array();
		private $autocorrectionDisabled = false;


		protected function __construct() {
			$this->objects		=	&umiObjectsCollection::getInstance();
			$this->langs		=	&langsCollection::getInstance();
			$this->domains		=	&domainsCollection::getInstance();
			$this->templates	=	&templatesCollection::getInstance();
			
			if(regedit::getInstance()->getVal("//settings/disable_url_autocorrection")) {
				$this->autocorrectionDisabled = true;
			}
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}

		public function isExists($element_id) {
			if($this->isLoaded($element_id)) {
				return true;
			} else {
				$element_id = (int) $element_id;

				$sql = "SELECT COUNT(*) FROM cms3_hierarchy WHERE id = '{$element_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					list($count) = mysql_fetch_row($result);
				}
				return (bool) $count;
			}
		}

		public function isLoaded($element_id) {
			return (bool) array_key_exists($element_id, $this->elements);
		}


		public function getElement($element_id, $ignorePermissions = false, $ignoreDeleted = false) {
			if(!$ignorePermissions) {
				if(!$this->isAllowed($element_id)) return false;
			}

			if($this->isLoaded($element_id)) {
				return $this->elements[$element_id];
			} else {
				if($element = memcachedController::getInstance()->load($element_id, "element")) {
				} else {
					$element = new umiHierarchyElement($element_id);
					memcachedController::getInstance()->save($element, "element");
				}
				
				if(is_object($element)) {
					if($element->getIsBroken()) {
						return false;
					}

					if($element->getIsDeleted() && !$ignoreDeleted) return false;

					$this->elements[$element_id] = $element;
					return $this->elements[$element_id];
				} else {
					return false;
				}
			}
		}

		public function delElement($element_id) {
			//Inline checking permissions
			if($users_module = cmsController::getInstance()->getModule("users")) {
				if(!$users_module->isAllowedObject($users_module->user_id, $element_id)) {
					return false;
				}
			}

			if($element = $this->getElement($element_id)) {
				$sql = "SELECT id FROM cms3_hierarchy WHERE rel = '{$element_id}'";
				$result = mysql_query($sql);
					
				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}					
					
				while(list($child_id) = mysql_fetch_row($result)) {
					$child_element = $this->getElement($child_id, true, true);
					$this->delElement($child_id);
				}


				$element->setIsDeleted(true);
				$element->commit();
				unset($this->elements[$element_id]);
				
				$this->addUpdatedElementId($element_id);

				return true;
			} else {
				return false;
			}
		}

		public function copyElement($element_id, $rel_id, $copySubPages = false) {
			if($this->isExists($element_id) && ($this->isExists($rel_id) || $rel_id === 0)) {
				$rel_id = (int) $rel_id;
				$timestamp = self::getTimeStamp();

				if($element = $this->getElement($element_id)) {
					$ord = (int) $element->getOrd();
					unset($element);
				}

				$sql = <<<SQL

INSERT INTO cms3_hierarchy
	(rel, type_id, lang_id, domain_id, tpl_id, obj_id, alt_name, is_active, is_visible, is_deleted, updatetime, ord)
		SELECT '{$rel_id}', type_id, lang_id, domain_id, tpl_id, obj_id, alt_name, is_active, is_visible, is_deleted, '{$timestamp}', '{$ord}'
				FROM cms3_hierarchy WHERE id = '{$element_id}' LIMIT 1
SQL;
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$old_element_id = $element_id;
				$element_id = mysql_insert_id();

				//Copy permissions

				$sql = <<<SQL

INSERT INTO cms3_permissions
	(level, owner_id, rel_id)
		SELECT level, owner_id, '{$element_id}' FROM cms3_permissions WHERE rel_id = '{$old_element_id}'

SQL;
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}



				if($element = $this->getElement($element_id)) {
					//инкрементируем alt_name
					$element->setAltName($element->getAltName());
					$element->commit();

					if($copySubPages) {
						$domain_id = $element->getDomainId();

						$childs = $this->getChilds($old_element_id, true, true, 0, false, $domain_id);
						foreach($childs as $child_id => $nl) {
							$this->copyElement($child_id, $element_id, true);
						}
					}

					return $element_id;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function cloneElement($element_id, $rel_id, $copySubPages = false) {
			if($this->isExists($element_id) && ($this->isExists($rel_id) || $rel_id === 0)) {
				if($element = $this->getElement($element_id)) {
					$ord = (int) $element->getOrd();
				}

				$object_id = $element->getObject()->getId();

				$sql = <<<SQL
INSERT INTO cms3_objects
	(name, is_locked, type_id)
		SELECT name, is_locked, type_id
			FROM cms3_objects
				WHERE id = '{$object_id}'
SQL;
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$new_object_id = mysql_insert_id();

				$sql = <<<SQL
INSERT INTO cms3_object_content
	(field_id, int_val, varchar_val, text_val, rel_val, obj_id)
		SELECT field_id, int_val, varchar_val, text_val, rel_val, '{$new_object_id}'
			FROM cms3_object_content
				WHERE obj_id = '{$object_id}'
SQL;
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$timestamp = self::getTimeStamp();

				$sql = <<<SQL

INSERT INTO cms3_hierarchy
	(rel, type_id, lang_id, domain_id, tpl_id, obj_id, alt_name, is_active, is_visible, is_deleted, updatetime, ord)
		SELECT '{$rel_id}', type_id, lang_id, domain_id, tpl_id, '{$new_object_id}', alt_name, is_active, is_visible, is_deleted, '{$timestamp}', '{$ord}'
				FROM cms3_hierarchy WHERE id = '{$element_id}' LIMIT 1
SQL;
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}


				$old_element_id = $element_id;

				$element_id = mysql_insert_id();


				//Copy permissions

				$sql = <<<SQL

INSERT INTO cms3_permissions
	(level, owner_id, rel_id)
		SELECT level, owner_id, '{$element_id}' FROM cms3_permissions WHERE rel_id = '{$old_element_id}'

SQL;
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				if($element = $this->getElement($element_id)) {
					//инкрементируем alt_name
					$element->setAltName($element->getAltName());
					$element->commit();

					if($copySubPages) {
						$domain_id = $element->getDomainId();

						$childs = $this->getChilds($old_element_id, true, true, 0, false, $domain_id);
						foreach($childs as $child_id => $nl) {
							$this->copyElement($child_id, $element_id, true);
						}
					}

					return $element_id;
				}
				 else {
					return false;
				}
			}
    	}

		public function getDeletedList() {
			$res = Array();

			$sql = <<<SQL
SELECT DISTINCT h.id, h.rel FROM cms3_hierarchy h, cms3_hierarchy th WHERE h.is_deleted = '1' AND ((h.rel = th.id AND th.is_deleted = '0') OR h.rel = 0) ORDER BY h.updatetime DESC;
SQL;

			$result = mysql_unbuffered_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$tmp = Array();
			while(list($id, $rel) = mysql_fetch_row($result)) {
				if(array_key_exists($rel, $tmp)) {
					continue;
				}

				if(array_key_exists($id, $res)) {
					unset($res[$tmp[$id]]);
				}

				$res[$id] = $id;

				$tmp[$id] = $rel;
			}

			return array_values($res);
		}


		public function restoreElement($element_id) {
			if($element = $this->getElement($element_id, false, true)) {
				$element->setIsDeleted(false);
				$element->commit();

				$sql = "SELECT id FROM cms3_hierarchy WHERE rel = '{$element_id}'";
				$result = mysql_query($sql);
					
				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}					
					
				while(list($child_id) = mysql_fetch_row($result)) {
					$child_element = $this->getElement($child_id, true, true);
					$this->restoreElement($child_id);
				}

				return true;
			} else {
				return false;
			}
		}

		public function removeDeletedElement($element_id) {
			if($element = $this->getElement($element_id, true, true)) {
				if($element->getIsDeleted()) {
					$element_id = (int) $element_id;
					
					
					$sql = "SELECT id FROM cms3_hierarchy WHERE rel = '{$element_id}'";
					$result = mysql_query($sql);
					
					if($err = mysql_error()) {
						trigger_error($err, E_USER_WARNING);
						return false;
					}					
					
					while(list($child_id) = mysql_fetch_row($result)) {
						$child_element = $this->getElement($child_id, true, true);
						$child_element->setIsDeleted(true);
						$this->removeDeletedElement($child_id);
					}

					$sql = "DELETE FROM cms3_hierarchy WHERE id = '{$element_id}' LIMIT 1";
					mysql_query($sql);

					if($err = mysql_error()) {
						trigger_error($err, E_USER_WARNING);
						return false;
					}

					unset($this->elements[$element_id]);
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function removeDeletedAll() {
			$sql = "SELECT id FROM cms3_hierarchy WHERE is_deleted = '1'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($element_id) = mysql_fetch_row($result)) {
				$this->removeDeletedElement($element_id);
			}
			return true;
		}

		public function getParent($element_id) {
			$element_id = (int) $element_id;

			$sql = "SELECT SQL_CACHE rel FROM cms3_hierarchy WHERE id = '{$element_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(mysql_num_rows($result)) {
				list($parent_id) = mysql_fetch_row($result);
				return (int) $parent_id;
			} else {
				return false;
			}
		}

		public function getAllParents($element_id, $include_self = false) {
			$res = Array();

			$self_id = $element_id;

			if($include_self) $res[] = $self_id;
			while($element_id > 0) {
				$element_id = $this->getParent($element_id);
				if($element_id === false) return false;
				$res[] = $element_id;
			}
			return array_reverse($res);
		}

		public function getChilds($element_id, $allow_unactive = true, $allow_unvisible = true, $depth = 0, $hierarchy_type_id = false, $domainId = false) {
			$res = Array();
			$element_id = (int) $element_id;
			$allow_unactive = (int) $allow_unactive;
			$allow_unvisible = (int) $allow_unvisible;
			$hierarchy_type_id = (int) $hierarchy_type_id;

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();

			$domain_id = ($domainId) ? $domainId : cmsController::getInstance()->getCurrentDomain()->getId();
			$domain_cond = ($element_id > 0) ? "" : " AND domain_id = '{$domain_id}'";

			$sql = "SELECT SQL_CACHE id FROM cms3_hierarchy WHERE rel = '{$element_id}' {$domain_cond} AND lang_id = '{$lang_id}' AND is_deleted = '0'";

			if(!$allow_unactive)	$sql .= " AND is_active = '1'";
			if(!$allow_unvisible)	$sql .= " AND is_visible = '1'";
			if($hierarchy_type_id) $sql .= " AND type_id = '{$hierarchy_type_id}'";
			$sql .= " ORDER BY ord";

			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($child_id) = mysql_fetch_row($result)) {
				if($depth > 0) {
					$res[$child_id] = $this->getChilds($child_id, $allow_unactive, $allow_unvisible, ($depth - 1));
				} else {
					$res[$child_id] = Array();
				}
			}
			return $res;
		}

		public function getPathById($element_id, $ignoreLang = false) {
			$pre_lang = cmsController::getInstance()->pre_lang;

			if($element = umiHierarchy::getInstance()->getElement($element_id, true)) {
				$current_domain = cmsController::getInstance()->getCurrentDomain();
				$element_domain_id = $element->getDomainId();

				if($current_domain->getId() == $element_domain_id) {
					$domain_str = "";
				} else {
					$domain_str = "http://" . domainsCollection::getInstance()->getDomain($element_domain_id)->getHost();
				}
				
				$element_lang_id = $element->getLangId();
				$element_lang = langsCollection::getInstance()->getLang($element_lang_id);
				
				if($element_lang->getIsDefault() || $ignoreLang == true) {
					$lang_str = "";
				} else {					
					$lang_str = "/" . $element_lang->getPrefix();
				}
			
				if($element->getIsDefault()) {
					return $domain_str . $lang_str . "/";
				}
			} else {
				return "";
			}
			
			if($parents = $this->getAllParents($element_id)) {
				$path = $domain_str . $lang_str;
				$parents[] = $element_id;

				$sz = sizeof($parents);
				for($i = 0; $i < $sz; $i++) {
					$sql = "SELECT SQL_CACHE alt_name FROM cms3_hierarchy WHERE id = '{$parents[$i]}'";
					$result = mysql_query($sql);

					if($err = mysql_error()) {
						trigger_error($err, E_USER_WARNING);
						return false;
					}

					if(mysql_num_rows($result)) {
						list($alt_name) = mysql_fetch_row($result);
						$path .= "/" . $alt_name;
					}
				}
				$path .= "/";
				return $path;
			} else {
				return false;
			}
		}

		public function getIdByPath($element_path, $show_disabled = false) {
			if($element_path == "/") {
				return $this->getDefaultElementId();
			}
			
			$element_path = trim($element_path, "\/ \n");
			$paths = split("/", $element_path);

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

			$sz = sizeof($paths);
			$id = 0;
			for($i = 0; $i < $sz; $i++) {
				$alt_name = $paths[$i];
				$alt_name = mysql_real_escape_string($alt_name);

				if($show_disabled) {
					$sql = "SELECT SQL_CACHE id FROM cms3_hierarchy WHERE rel = '{$id}' AND alt_name = '{$alt_name}' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}'";
				} else {
					$sql = "SELECT SQL_CACHE id FROM cms3_hierarchy WHERE rel = '{$id}' AND alt_name = '{$alt_name}' AND is_active='1' AND is_deleted = '0' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}'";
				}
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_ERROR);
					return false;
				}

				if(!mysql_num_rows($result)) {
					if($show_disabled) {
						$sql = "SELECT SQL_CACHE id, alt_name FROM cms3_hierarchy WHERE rel = '{$id}' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}'";
					} else {
						$sql = "SELECT SQL_CACHE id, alt_name FROM cms3_hierarchy WHERE rel = '{$id}' AND is_active = '1' AND is_deleted = '0' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}'";
					}
					$result = mysql_query($sql);

					if($err = mysql_error()) {
						trigger_error($err, E_USER_WARNING);
						return false;
					}

					$max = 0;
					$temp_id = 0;
					$res_id = 0;
					while(list($temp_id, $cstr) = mysql_fetch_row($result)) {
						if($this->autocorrectionDisabled) {
							if($alt_name == $cstr) {
								$res_id = $temp_id;
							}
						} else {
							$temp = umiHierarchy::compareStrings($alt_name, $cstr);
							if($temp > $max) {
								$max = $temp;
								$res_id = $temp_id;
							}
						}
					}

					if($max > 75) {
						$id = $res_id;
					} else {
						return false;
					}
				} else {
					if(!(list($id) = mysql_fetch_row($result))) {
						return false;
					}
				}
			}
			return $id;
		}

		public function addElement($rel_id, $hierarchy_type_id, $name, $alt_name, $type_id = false, $domain_id = false, $lang_id = false, $tpl_id = false) {
			if($type_id === false) {
				if($hierarchy_type = umiHierarchyTypesCollection::getInstance()->getType($hierarchy_type_id)) {
					$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType($hierarchy_type->getName(), $hierarchy_type->getExt());
				} else {
					trigger_error("Wrong hierarchy type id given", E_USER_WARNING);
					return false;
				}
			}
			
			if($rel_id) {
				$this->addUpdatedElementId($rel_id);
			} else {
				$this->addUpdatedElementId($this->getDefaultElementId());
			}
		
			if($object_id = $this->objects->addObject($name, $type_id)) {
				$sql = "INSERT INTO cms3_hierarchy (rel, type_id, domain_id, lang_id, tpl_id, obj_id) VALUES('{$rel_id}', '{$hierarchy_type_id}', '{$domain_id}', '{$lang_id}', '{$tpl_id}', '{$object_id}')";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				$element_id = mysql_insert_id();

				$element = $this->getElement($element_id, true);
				$element->setAltName($alt_name);


				$sql = "SELECT MAX(ord) FROM cms3_hierarchy WHERE rel = '{$rel_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				if(list($ord) = mysql_fetch_row($result)) {
					$element->setOrd( ($ord + 1) );
				}


				$element->commit();

				$this->addUpdatedElementId($rel_id);
				$this->addUpdatedElementId($element_id);

				return $element_id;
			} else {
				trigger_error("Failed to create new object for hierarchy element", E_USER_WARNING);
				return false;
			}
		}


		public function getDefaultElementId($lang_id = false, $domain_id = false) {
			if($lang_id === false) {
				$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			}
			
			if($domain_id === false) {
				$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();
			}
			
			$sql = "SELECT SQL_CACHE id FROM cms3_hierarchy WHERE is_default = '1' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($element_id) = mysql_fetch_row($result)) {
				return $element_id;
			} else {
				return false;
			}
		}


		public static function compareStrings($str1, $str2) {
			return	100 * (
				similar_text($str1, $str2) / (
					(strlen($str1) + strlen($str2))
				/ 2)
			);
		}

		public static function convertAltName($alt_name) {
			$alt_name = translit::convert($alt_name);
			$alt_name = preg_replace("/[\?\\\\\-&=]+/", "_", $alt_name);
			$alt_name = preg_replace("/[_]+/", "_", $alt_name);
			return $alt_name;
		}

		public static function getTimeStamp() {
			return time();
		}


		public function moveBefore($element_id, $rel_id, $before_id = false) {
			if(!$this->isExists($element_id)) return false;

			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

			$element_id = (int) $element_id;
			$rel_id = (int) $rel_id;

			if($before_id) {
				$before_id = (int) $before_id;

				$sql = "SELECT ord FROM cms3_hierarchy WHERE id = '{$before_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				if(list($ord) = mysql_fetch_row($result)) {
					$ord = (int) $ord;
					$sql = "UPDATE cms3_hierarchy SET ord = (ord + 1) WHERE rel = '{$rel_id}' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}' AND ord >= {$ord}";
					mysql_query($sql);

					if($err = mysql_error()) {
						trigger_error($err, E_USER_WARNING);
						return false;
					}

					$sql = "UPDATE cms3_hierarchy SET ord = '{$ord}', rel = '{$rel_id}' WHERE id = '{$element_id}'";
					mysql_query($sql);
					if($err = mysql_error()) {
						trigger_error($err, E_USER_WARNING);
						return false;
					} else {
						return true;
					}
				} else {
					return false;
				}
			} else {
				$sql = "SELECT MAX(ord) FROM cms3_hierarchy WHERE rel = '{$rel_id}' AND lang_id = '{$lang_id}' AND domain_id = '{$domain_id}'";
				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}

				if(list($ord) = mysql_fetch_row($result)) {
					++$ord;
				} else {
					$ord = 1;
				}

				$sql = "UPDATE cms3_hierarchy SET ord = '{$ord}', rel = '{$rel_id}' WHERE id = '{$element_id}'";
				mysql_query($sql);
				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				} else {
					return true;
				}
			}

		}
		

		public function moveFirst($element_id, $rel_id) {
			$element_id = (int) $element_id;
			$rel_id = (int) $rel_id;
			
			$sql = "SELECT id FROM cms3_hierarchy WHERE rel = '{$rel_id}' ORDER BY ord ASC";
			$result = mysql_query($sql);
			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				list($before_id) = mysql_fetch_row($result);
				return $this->moveBefore($element_id, $rel_id, $before_id);
			}
		}


		protected function isAllowed($element_id) {
			if($users_ext = cmsController::getInstance()->getModule('users')) {
				list($r, $e) = $users_ext->isAllowedObject($users_ext->user_id, $element_id);
				return $r;
			} else {
				return true;
			}
		}

		public function getDominantTypeId($element_id) {
			if($this->isExists($element_id)) {
				$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
				$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

				$element_id = (int) $element_id;

				$sql = <<<SQL
SELECT o.type_id, COUNT(*) AS c
	FROM cms3_hierarchy h, cms3_objects o
		WHERE h.rel = '{$element_id}' AND o.id = h.obj_id AND h.lang_id = '{$lang_id}' AND h.domain_id = '{$domain_id}'
			GROUP BY o.type_id
				ORDER BY c DESC
					LIMIT 1
SQL;
				if($type_id = (int) memcachedController::getInstance()->loadSql($sql)) {
					return $type_id;
				}

				$result = mysql_query($sql);

				if($err = mysql_error()) {
					trigger_errror($err, E_USER_WARNING);
					return false;
				}

				if(mysql_num_rows($result)) {
					list($type_id) = mysql_fetch_row($result);
					$type_id = (int) $type_id;

					memcachedController::getInstance()->saveSql($sql, $type_id);

					return $type_id;
				} else {
					return NULL;
				}
			} else {
				return false;
			}
		}
		
		
		public function addUpdatedElementId($element_id) {
			if(!in_array($element_id, $this->updatedElements)) {
				$this->updatedElements[] = $element_id;
			}
		}
		
		
		public function getUpdatedElements() {
			return $this->updatedElements;
		}
		
		
		public function __destruct() {
			if(sizeof($this->updatedElements)) {
				if(function_exists("deleteElementsRelatedPages")) {
					deleteElementsRelatedPages();
				}
			}
		}
		
		public function getCollectedElements() {
			return array_keys($this->elements);
		}
		
		
		public function unloadElement($element_id) {
			static $pid;

			if($pid === NULL) {
				$pid = cmsController::getInstance()->getCurrentElementId();
			}
			
			if($pid == $element_id) return false;
			
			if(array_key_exists($element_id, $this->elements)) {
				unset($this->elements[$element_id]);
			} else {
				return false;
			}
		}
		
		
		public function getElementsCount($module, $method = "") {
			$hierarchy_type_id = umiHierarchyTypesCollection::getInstance()->getTypeByName($module, $method)->getId();

			$sql = "SELECT COUNT(*) FROM cms3_hierarchy WHERE type_id = '{$hierarchy_type_id}'";
			$result = mysql_query($sql);
			
			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}
			
			if(list($count) = mysql_fetch_row($result)) {
				return $count;
			} else {
				return false;
			}
		}
	};
?>