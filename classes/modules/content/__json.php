<?php
	abstract class __json_content {

		public function json_load () {
			$res = "var responseArgs = new Array();\n";
			$request_id = $_REQUEST['param0'];

			$param = $_REQUEST['requestParam'];
			if(is_numeric($param)) {
				$param = (int) $param;

				if($element = umiHierarchy::getInstance()->getElement($param)) {
					$request_domain_id = $element->getDomainId();
				} else {
					$request_domain_id = false;
				}
			} else {
				$request_domain_id = domainsCollection::getInstance()->getDomainId($param);
				$param = 0;	//TODO: make multidomain
			}

			$request_domain_id = ($request_domain_id) ? $request_domain_id : false;

			$childs = umiHierarchy::getInstance()->getChilds($param, true, true, 1, false, $request_domain_id);


			$prev_id = 0;
			foreach($childs as $element_id => $sub_childs) {
				$element = umiHierarchy::getInstance()->getElement($element_id);

				if(!$element) continue;

				$parent_id = $element->getParentId();
				$title = mysql_escape_string($element->getObject()->getName());
				$childs_count = sizeof($sub_childs);

				$is_active = (int) $element->getIsActive();
				$is_visible = (int) $element->getIsVisible();

				$element_type_id = $element->getTypeId();
				$element_type = umiHierarchyTypesCollection::getInstance()->getType($element_type_id);

				$md = $element_type->getName();

				$link_add = false;
				$link_edit = false;

				if($module_inst = cmsController::getInstance()->getModule($md)) {
					list($link_add, $link_edit) = $module_inst->getEditLink($element_id, $element_type->getExt());
				}

				$domain_id = $element->getDomainId();
				$domain_host = domainsCollection::getInstance()->getDomain($domain_id)->getHost();

				if($request_domain_id !== false) {
					if($domain_id != $request_domain_id) continue;
				}

				$module = $element_type->getName();
				$method = $element_type->getExt();

				$view_link = umiHierarchy::getInstance()->getPathById($element_id);

				$object_type_id = $element->getObject()->getTypeId();
				$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

				$type_title = $element_type->getTitle();
				$type_name = $object_type->getName();

				if(umiHierarchy::compareStrings($type_name, $type_title) < 90) {
					$type_title .= " - " . $type_name;
				} else {
					$type_title = $type_name;
				}

				$type_title = mysql_escape_string($type_title);

				$res .= <<<END

responseArgs[responseArgs.length] = {
		"id"		: "{$element_id}",
		"rel"		: "{$parent_id}",
		"title"		: "{$title}",
		"childs_count"	: "{$childs_count}",
		"prev_page_id"	: "{$prev_id}",
		"add_link"	: "{$link_add}",
		"edit_link"	: "{$link_edit}",
		"domain"	: "{$domain_host}",
		"module"	: "{$module}",
		"method"	: "{$method}",
		"is_active"	: "{$is_active}",
		"is_visible"	: "{$is_visible}",
		"view_link"	: "{$view_link}",
		"type_title"	: "{$type_title}"
	};

END;
				$prev_id = $element_id;
			}

			$res .= "document.contentTreeInstance.reportRequest({$request_id}, responseArgs);\n";
			header("Content-type: text/javascript; charset=utf-8");
			$this->flush($res);
		}


		public function json_move () {
			$id = (int) $_REQUEST['id'];
			$rel = $_REQUEST['rel'];

			$domain = $_REQUEST['domain'];
			$domain_id = domainsCollection::getInstance()->getDomainId($domain);

			if(!is_numeric($rel)) {
				$domain = $rel;
			}
			$rel = (int) $rel;
			$before = $_REQUEST['before'];


			$element = umiHierarchy::getInstance()->getElement($id);
			if($domain_id) {
				$element->setDomainId($domain_id);
			}
			$element->commit();

			umiHierarchy::getInstance()->moveBefore($id, $rel, (($before) ? $before : false));
			header("Content-type: text/javascript; charset=utf-8");
			$this->flush();
		}

		public function json_copy() {
			$id = $_REQUEST['id'];

			$cloneMode = (bool) $_REQUEST['cloneIt'];

			$domain = $_REQUEST['domain'];
			$domain_id = domainsCollection::getInstance()->getDomainId($domain);

			$parent_id = umiHierarchy::getInstance()->getParent($id);

			$copyAll = (bool) $_REQUEST['copyAll'];

			if($cloneMode) {
				if(defined("CURRENT_VERSION_LINE")) {
					if(CURRENT_VERSION_LINE == "free") {
						$count = umiHierarchy::getInstance()->getElementsCount("content");

						if($count >= 10) {
							$res = <<<END
alert("%error_free_max_pages%");
END;
							$res = templater::getInstance()->parseInput($res);

							header("Content-type: text/javascript; charset=utf-8");
							$this->flush($res);
						}
					}
				}



				$element_id = umiHierarchy::getInstance()->cloneElement($id, $parent_id, $copyAll);
			} else {
				$element_id = umiHierarchy::getInstance()->copyElement($id, $parent_id, $copyAll);
			}

			$element = umiHierarchy::getInstance()->getElement($element_id);
			$domain_id = $element->getDomainId();

			$parent_id = $element->getParentId();

			$title = mysql_escape_string($element->getObject()->getName());

			$childs = umiHierarchy::getInstance()->getChilds($element_id, true, true, 0, false, $domain_id);
			$childs_count = sizeof($childs);

			$is_active = (int) $element->getIsActive();
			$is_visible = (int) $element->getIsVisible();

			$element_type_id = $element->getTypeId();
			$element_type = umiHierarchyTypesCollection::getInstance()->getType($element_type_id);

			$md = $element_type->getName();

			$link_add = false;
			$link_edit = false;

			if($module_inst = cmsController::getInstance()->getModule($md)) {
				list($link_add, $link_edit) = $module_inst->getEditLink($element_id, $element_type->getExt());
			}

			$module = $element_type->getName();
			$method = $element_type->getExt();

			$domain_id = $element->getDomainId();
			$domain_host = domainsCollection::getInstance()->getDomain($domain_id)->getHost();

			$view_link = umiHierarchy::getInstance()->getPathById($element_id);

			$object_type_id = $element->getObject()->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$type_title = $element_type->getTitle();
			$type_name = $object_type->getName();

			if(umiHierarchy::compareStrings($type_name, $type_title) < 90) {
				$type_title .= " - " . $type_name;
			} else {
				$type_title = $type_name;
			}

			$type_title = mysql_escape_string($type_title);


			$res = <<<END
var response = {
		"id"		: "{$element_id}",
		"rel"		: "{$parent_id}",
		"title"		: "{$title}",
		"childs_count"	: "{$childs_count}",
		"prev_page_id"	: "",
		"add_link"	: "{$link_add}",
		"edit_link"	: "{$link_edit}",
		"domain"	: "{$domain_host}",
		"module"	: "{$module}",
		"method"	: "{$method}",
		"is_active"	: "{$is_active}",
		"is_visible"	: "{$is_visible}",
		"view_link"	: "{$view_link}",
		"type_title"	: "{$type_title}"
	};

document.contentTreeInstance.reportCopy({$element_id}, response);
END;

			header("Content-type: text/javascript; charset=utf-8");
			$this->flush($res);
		}

		public function json_get_editable_blocks() {
			$requestId = (int) $_REQUEST['requestId'];
			$max_length = 50;

			if(!system_is_allowed("content", "qedit")) {
				exit();
			}

			$r = $_SERVER['HTTP_REFERER'] . " | " . "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


			$filename = ini_get("include_path") . "cache/" . md5($_SERVER['HTTP_REFERER']) . ".block";

			if(is_file($filename)) {
				$arr = unserialize(file_get_contents($filename));

//				$arr = array_unique($arr);
				// detect current user
				$this->is_auth = false;
				if($oMdlUsers = cmsController::getInstance()->getModule("users")) {
					if($oMdlUsers->is_auth()) {
						$iUserId = (int) $oMdlUsers->user_id;
						$this->is_auth = true;
						$this->user_id = $iUserId;
						if($oUserObj = umiObjectsCollection::getInstance()->getObject($iUserId)) {
							$sUsrLName = $oUserObj->getValue("lname");
							$sUsrFName = $oUserObj->getValue("fname");
							$sUsrFatherName = $oUserObj->getValue("father_name");
							$user_name = "$sUsrLName $sUsrFName $sUsrFatherName";
							$user_login = $oUserObj->getValue("login");

							$user_group_ids = $oUserObj->getValue("groups");
							$user_group_names = Array();
							foreach($user_group_ids as $user_group_id) {
								$user_group_names[] = umiObjectsCollection::getInstance()->getObject($user_group_id)->getName();
							}

							$user_groups = implode(", ", $user_group_names);
						}
					}
				}

				$res = <<<END
var response = new lLibResponse({$requestId});
response.links = new Array();
response.user_name = "{$user_name}";
response.user_login = "{$user_login}";
response.user_groups = "{$user_groups}";
response.modules = new Array();
END;
				
				// detect allow modules
				$oRegedit = regedit::getInstance();
				$arrModules = $oRegedit->getList('modules');
				for ($iI=0; $iI<count($arrModules); $iI++) {
					$arrNextMdl = $arrModules[$iI];
					if(!system_is_allowed($arrNextMdl[0], "")) continue;
					cmsController::getInstance()->getModule($arrNextMdl[0]);
					$sPath = '//modules/' . $arrNextMdl[0] . '/';
					$sMdlName = $oRegedit->getVal($sPath . 'name');
					$sMdlCaption = cmsController::getInstance()->langs[$sMdlName]['module_name'];
					$sMdlLink = $this->pre_lang."/admin/".$sMdlName;
					
					if(defined("CURRENT_VERSION_LINE")) {
						if(CURRENT_VERSION_LINE == "free" || CURRENT_VERSION_LINE == "lite" || CURRENT_VERSION_LINE == "freelance") {
							if($arrNextMdl[0] == "data") {
								continue;
							}
						}
					}
					
					
					$res .= <<<END
response.modules[response.modules.length] = new Array('{$sMdlCaption}', '{$sMdlLink}');
END;
				}

				$w = Array();
				foreach($arr as $c) {
					if(in_array(Array($c[0], $c[2]), $w)) continue;
					$w[] = Array($c[0], $c[2]);

					if($element = umiHierarchy::getInstance()->getElement($c[2])) {
						$name = $element->getName();
					} else {
						continue;
					}

					if($module = cmsController::getInstance()->getModule($c[0])) {
						list(, $link) = $module->getEditLink($c[2], $c[1]);
						if(!$link) continue;

						if(strlen($name) > $max_length) {
							$name = substr($name, 0, $max_length) . "...";
						}

						$name = mysql_escape_string($name);
						$link = mysql_escape_string($link);

						$type_id = $element->getObject()->getTypeId();
						$type = umiObjectTypesCollection::getInstance()->getType($type_id);
						$type_name = $type->getName();

						$type_link = $this->pre_lang . "/admin/data/type_edit/" . $type_id . "/";


						if($c[3]) {
							$type_name = "";
							$type_link = "";
							
						}

						$res .= <<<END
response.links[response.links.length] = new Array('{$name}', '{$link}', '{$type_name}', '{$type_link}');
END;
					} else {
						continue;
					}
				}
			$res .= <<<END
lLib.getInstance().makeResponse(response);
END;
			} else {
			}

			header("Content-type: text/javascript; charset=utf-8");
			$this->flush($res);
		}


		public function json_del () {
			$element_id = (int) $_REQUEST['id'];

			umiHierarchy::getInstance()->delElement($element_id);

			$this->flush();
		}


		public function json_set_is_active () {
			$element_id = (int) $_REQUEST['id'];

			$mode = ($_REQUEST['mode'] == "true") ? true : false;

			if($element = umiHierarchy::getInstance()->getElement($element_id)) {
				$element->setIsActive($mode);
				$element->commit();
			}

			$this->flush();
		}


		public function json_load_hierarchy () {
			header("Content-type: text/javascript; charset=utf-8");

			$res = "var responseArgs = new Array();\n";
			$request_id = $_REQUEST['requestId'];

			$param = $_REQUEST['requestParam'];
			if(is_numeric($param)) {
				$param = (int) $param;

				if($element = umiHierarchy::getInstance()->getElement($param)) {
					$request_domain_id = $element->getDomainId();
				} else {
					$request_domain_id = false;
				}
			} else {
				$request_domain_id = domainsCollection::getInstance()->getDomainId($param);
				$param = 0;	//TODO: make multidomain
			}

			$request_domain_id = ($request_domain_id) ? $request_domain_id : false;

			$childs = umiHierarchy::getInstance()->getChilds($param, true, true, 1, false, $request_domain_id);


			$prev_id = 0;
			foreach($childs as $element_id => $sub_childs) {
				$element = umiHierarchy::getInstance()->getElement($element_id);

				$parent_id = $element->getParentId();
				$title = mysql_escape_string($element->getObject()->getName());
				$childs_count = sizeof($sub_childs);

				$is_active = (int) $element->getIsActive();
				$is_visible = (int) $element->getIsVisible();

				if(!$is_active) continue;

				$element_type_id = $element->getTypeId();
				$element_type = umiHierarchyTypesCollection::getInstance()->getType($element_type_id);

				$md = $element_type->getName();

				$link_add = false;
				$link_edit = false;

				if($module_inst = cmsController::getInstance()->getModule($md)) {
					list($link_add, $link_edit) = $module_inst->getEditLink($element_id, $element_type->getExt());
				}

				$domain_id = $element->getDomainId();
				$domain_host = domainsCollection::getInstance()->getDomain($domain_id)->getHost();

				if($request_domain_id !== false) {
					if($domain_id != $request_domain_id) continue;
				}

				$module = $element_type->getName();
				$method = $element_type->getExt();

				$res .= <<<END

responseArgs[responseArgs.length] = {
		"id"		: "{$element_id}",
		"rel"		: "{$parent_id}",
		"title"		: "{$title}",
		"childs_count"	: "{$childs_count}",
		"prev_page_id"	: "{$prev_id}",
		"add_link"	: "{$link_add}",
		"edit_link"	: "{$link_edit}",
		"domain"	: "{$domain_host}",
		"module"	: "{$module}",
		"method"	: "{$method}",
		"is_active"	: "{$is_active}",
		"is_visible"	: "{$is_visible}"
	};

END;
				$prev_id = $element_id;
			}

			$res .= "jsonRequestsController.getInstance().reportRequest({$request_id}, responseArgs);\n";
			$this->flush($res);
		}

	};
?>