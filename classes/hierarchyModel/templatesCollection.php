<?php
	class templatesCollection extends singleton implements iSingleton, iTemplatesCollection {
		private $templates = Array(), $def_template;

		protected function __construct() {
			$this->loadTemplates();
		}

		public static function getInstance() {
			return parent::getInstance(__CLASS__);
		}


		public function addTemplate($filename, $title, $domain_id = false, $lang_id = false, $is_default = false) {
			$domains = domainsCollection::getInstance();
			$langs = langsCollection::getInstance();

			if(!$domains->isExists($domain_id)) {
				if($domains->getDefaultDomain()) {
					$domain_id = $domains->getDefaultDomain()->getId();
				} else {
					return false;
				}
			}

			if(!$langs->isExists($lang_id)) {
				if($langs->getDefaultLang()) {
					$lang_id = $langs->getDefaultLang()->getId();
				} else {
					return false;
				}
			}

			$sql = "INSERT INTO cms3_templates VALUES()";
			$result = mysql_query($sql);

			if($err = mysql_error($err, E_USER_WARNING)) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			$template_id = mysql_insert_id();

			$template = new template($template_id);
			$template->setFilename($filename);
			$template->setTitle($title);
			$template->setDomainId($domain_id);
			$template->setLangId($lang_id);
			$template->setIsDefault($is_default);

			if($is_default) {
				$this->setDefaultTemplate($template_id);
			}
			$template->commit();


			$this->templates[$template_id] = $template;

			return $template_id;
		}

		public function setDefaultTemplate($template_id, $domain_id = false, $lang_id = false) {
			if($domain_id == false) $domain_id = domainsCollection::getInstance()->getDefaultDomain()->getId();	
			if($lang_id ==false) $lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			
			// $sql = "UPDATE cms3_templates SET is_default = 0 WHERE domain_id = {$domain_id} AND lang_id = {$lang_id}";
			// $sql = "UPDATE cms3_templates SET is_default = 1 WHERE id = {$templaet_id} AND domain_id = {$domain_id} AND lang_id = {$lang_id}";
			
			if(!$this->isExists($template_id)) {
				return false;
			}
			
			$templates = $this->getTemplatesList($domain_id,$lang_id);
			foreach ($templates as $template) {
				if($template_id == $template->getId()) {
					$template->setIsDefault(true);					
				}
				else {
					$template->setIsDefault(false);
				}
				$template->commit();
			}
			return true;
// Остатки старого кода
			if(!($template = $this->getTemplate($templateId))) {
				return false;
			}

			if($this->def_template) {
				$this->def_template->setIsDefault(false);
				$this->def_template->commit();
			}

			$this->def_template = $template;
			$this->def_template->setIsDefault(true);
			$this->def_template->commit();

			//return true;
		}

		public function delTemplate($template_id) {
			if($this->isExists($template_id)) {
				if($this->templates[$template_id]->getIsDefault()) {
					unset($this->def_template);
				}
				unset($this->templates[$template_id]);

				$o_deftpl = $this->getDefaultTemplate();
				if (!$o_deftpl || $o_deftpl->getId() == $template_id) return false;

				$upd_qry = "UPDATE cms3_hierarchy SET tpl_id = '".$o_deftpl->getId()."' WHERE tpl_id='{$template_id}'";
				mysql_query($upd_qry);
				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}


				$sql = "DELETE FROM cms3_templates WHERE id = '{$template_id}'";
				mysql_query($sql);

				if($err = mysql_error()) {
					trigger_error($err, E_USER_WARNING);
					return false;
				}
				return true;

			} else return false;
		}

		public function getTemplatesList($domain_id, $lang_id) {
			$res = Array();

			foreach($this->templates as $template) {
				if($template->getDomainId() == $domain_id && $template->getLangId() == $lang_id) {
					$res[] = $template;
				}
			}

			return $res;
		}

		public function getDefaultTemplate($domain_id = false, $lang_id = false) {
			
			if($domain_id == false) $domain_id = domainsCollection::getInstance()->getDefaultDomain()->getId();	
			if($lang_id ==false) $lang_id = cmsController::getInstance()->getCurrentLang()->getId();

			$templates = $this->getTemplatesList($domain_id, $lang_id);
			foreach($templates as $template) {
				if($template->getIsDefault() == true) {
					return $template;
				}
			}
			return false;
		}

		public function getTemplate($template_id) {
			return ($this->isExists($template_id)) ? $this->templates[$template_id] : false;
		}

		public function isExists($template_id) {
			return (bool) array_key_exists($template_id, $this->templates);
		}


		private function loadTemplates() {
			$sql = "SELECT SQL_CACHE id FROM cms3_templates";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			while(list($template_id) = mysql_fetch_row($result)) {
				if($template = memcachedController::getInstance()->load($template_id, "template")) {
				} else {
					$template = new template($template_id);
					memcachedController::getInstance()->save($template, "template");
				}
				$this->templates[$template_id] = $template;

				if($template->getIsDefault()) {
					$this->def_template = $template;
				}
			}
			return true;
		}
	}
?>