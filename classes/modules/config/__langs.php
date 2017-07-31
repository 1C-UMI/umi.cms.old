<?php
	abstract class __langs_config {

		public function langs() {
			$this->load_forms();
			$params = Array();

			if($_REQUEST['names']) {
				$this->langs_update();
			}

			$langs = langsCollection::getInstance()->getList();
			$rows = "";
			foreach($langs as $lang) {
				$lang_id = $lang->getId();
				$lang_name = $lang->getTitle();
				$lang_prefix = $lang->getPrefix();
				$is_default = $lang->getIsDefault();

				$rows .= <<<END

	<row>
		<col>
			<input quant='no' style='width: 90%'>
				<name><![CDATA[names[{$lang_id}]]]></name>
				<value><![CDATA[{$lang_name}]]></value>
			</input>
		</col>

		<col>
			<input quant='no' style='width: 90%'>
				<name><![CDATA[prefixes[{$lang_id}]]]></name>
				<value><![CDATA[{$lang_prefix}]]></value>
			</input>
		</col>

		<col style='text-align: center'>
			<radio selected="{$is_default}">
				<name><![CDATA[default]]></name>
				<value><![CDATA[{$lang_id}]]></value>
			</radio>
		</col>

		<col style='text-align: center'>
			<a href="%pre_lang%/admin/config/lang_del/{$lang_id}" commit_unrestorable="?">
				<img src="/images/cms/admin/%skin_path%/ico_del.gif" alt="Удалить" title="Удалить" border="0" />
			</a>
		</col> 
	</row>

END;
			}
			$params['rows'] = $rows;
			return $this->parse_form("langs", $params);
		}


		public function langs_update() {
			$def = $_REQUEST['default'];
			$del = $_REQUEST['del'];

			$names = $_REQUEST['names'];
			$prefixes = $_REQUEST['prefixes'];


			$new_name = $_REQUEST['new_name'];
			$new_prefix = $_REQUEST['new_prefix'];


			if(is_array($names)) {
					foreach($names as $lang_id => $lang_name) {
						$lang_prefix = $prefixes[$lang_id];

						$lang = langsCollection::getInstance()->getLang($lang_id);
						$lang->setPrefix($lang_prefix);
						$lang->setTitle($lang_name);
						$lang->setIsDefault($def == $lang_id);
						$lang->commit();
						$lang->update();
					}
			}


			if($new_name && $new_prefix) {
				$lang = langsCollection::getInstance()->addLang($new_prefix, $new_name, (($def == "NEW") ? true : false));
			}

			if(is_array($del)) {
				foreach($del as $lang_id => $v) {
					langsCollection::getInstance()->delLang($lang_id);
				}
			}
		}


		public function lang_del() {
			$lang_id = (int) $_REQUEST['param0'];

			langsCollection::getInstance()->delLang($lang_id);

			$this->redirect($this->pre_lang . "/admin/config/langs/");
		}
	};
?>