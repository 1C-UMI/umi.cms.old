<?php

abstract class __config {
	public function mainpage() {
		$this->sheets_reset();

		$res = "";
		$regedit = regedit::getInstance();
		$modules_arr = $regedit->getList("modules");


		$res = "\t\t<mainpage>\n";
		$i = 0;

		foreach($modules_arr as $cm) {
			$c_name = $cm[0];

			if(!system_is_allowed($c_name, ""))
				continue;

			++$i;
			if($i == 1)
				$res .= "\t\t\t<para>\n";

			$c_ico = $regedit->getVal("//modules/" . $cm[0] . "/ico");
			$c_title = $regedit->getVal("//modules/" . $cm[0] . "/title");
			$c_desc = $regedit->getVal("//modules/" . $cm[0] . "/description");
			$c_help = $regedit->getVal("//modules/" . $cm[0] . "/help_link");
			$c_config = $regedit->getVal("//modules/" . $cm[0] . "/config");

			if($c_config)
			    $cnf = " config='" . $this->pre_lang . "/admin/" . $cm[0] . "/config/'";
			else
			    $cnf = " config=''";



			if(cmsController::getInstance()->getModule($cm[0])) {
				$c_title = cmsController::getInstance()->langs[$cm[0]]['module_title'];
				$c_desc = cmsController::getInstance()->langs[$cm[0]]['module_description'];
			}


			$c_tit = cmsController::getInstance()->langs[$cm[0]]['module_name'];

			$c_ico .= "." . "gif";

			$res .= "\t\t\t\t<module title=\"$c_tit\" ico=\"$c_ico\" link=\"" . system_gen_link($this->CMS_ENV, -1, $c_name) . "\" " . $cnf . " hlink=\"http://manual.umicms.ru/modules/" . $cm[0] . "/\" ctext='Настройки модуля' chelp='Документация по модулю'><description title=\"$c_title\">$c_desc</description></module>\r\n";

			if($i == 2) {
				$res .= "\t\t\t</para>\n\n";
				$i = 0;
			}


		}
		if($i == 1)
			$res .= "</para>\n";

		$res .= "\t\t</mainpage>\n";

		return $res." ";
	}


	public function modules() {
		$res = "";


		$modules = "";

		$regedit = regedit::getInstance();

		$m_arr = $regedit->getList("//modules");

		foreach($m_arr as $md) {

//			system_module_prepared($md[0], $this->CMS_ENV);
			$module_name = cmsController::getInstance()->langs[$md[0]]['module_name'];

			$modules .= <<<ROW
<row>
	<col>
		<a href="%pre_lang%/admin/{$md[0]}/"><![CDATA[{$module_name}]]></a>
	</col>

	<col style="width: 100px; text-align: center;">
		<a commit_unrestorable="%are_you_sured%" href="/admin/config/del_module/?target={$md[0]}">
			<img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" />
		</a>
	</col>
</row>
ROW;
		}

		$params = Array();
		$params['modules'] = $modules;
		$params['pre_lang'] = $_REQUEST['pre_lang'];

		$this->load_forms();
		$sres = $this->parse_form('settings', $params);


		$params = Array();
		$params['modules_def'] = " status=\"active\"";
		$params['settings'] = $sres;


		$params['installform'] = $this->parse_form("add_module");

		$res = $this->parse_form('mainpage', $params);

		return $res;
	}

	public function add_module() {
		$this->sheets_set_active("modules");

		$res = "";

		$params = Array();

		$this->load_forms();
		$sres = $this->parse_form('add_module', $params);


		$params = Array();
		$params['modules_def'] = " status=\"active\"";
		$params['settings'] = $sres;

		$res = $this->parse_form('mainpage', $params);

		return $res;
	}

	public function add_module_do() {
		$module_path = $_REQUEST['module_path'];

		system_module_install($module_path, $this->CMS_ENV);

		$this->redirect("admin", "config", "modules");
	}


	public function main() {
		$res = "";

		$params = Array();

		$regedit = regedit::getInstance();
		$regedit->getList("//settings");

		$params['site_name'] = $regedit->getVal("//settings/site_name");
		$params['domain'] = $regedit->getVal("//settings/domain");
		$params['title_prefix'] = $regedit->getVal("//settings/title_prefix");
		$params['admin_email'] = $regedit->getVal("//settings/admin_email");
		$params['error_email'] = $regedit->getVal("//settings/error_email");
		$params['hash'] = $regedit->getVal("//settings/hash");
		$params['cms_mode'] = $regedit->getVal("//settings/cms_mode");
		$params['rec_deep'] = $regedit->getVal("//settings/rec_deep");
		$params['chache_browser'] = $regedit->getVal("//settings/chache_browser");
		$params['keycode'] = $regedit->getVal("//settings/keycode");
		$params['disable_url_autocorrection'] = $regedit->getVal("//settings/disable_url_autocorrection");


		$this->load_forms();
		$res = $this->parse_form('globals', $params);

		return $res;
	}

	public function main_do() {
		$regedit = regedit::getInstance();

		$regedit->setVar("//settings/site_name", utf8_1251($_REQUEST['site_name']));

		$regedit->setVar("//settings/domain", $_REQUEST['domain']);
		$regedit->setVar("//settings/title_prefix", utf8_1251($_REQUEST['title_prefix']));
		$regedit->setVar("//settings/admin_email", $_REQUEST['admin_email']);
		$regedit->setVar("//settings/error_email", $_REQUEST['error_email']);
		$regedit->setVar("//settings/rec_deep", ((int) $_REQUEST['rec_deep']) );
		$regedit->setVar("//settings/chache_browser", ((int) $_REQUEST['chache_browser']) );
		$regedit->setVar("//settings/keycode", $_REQUEST['keycode'] );
		$regedit->setVar("//settings/disable_url_autocorrection", $_REQUEST['disable_url_autocorrection'] );

		$this->redirect($this->pre_lang . "/admin/config/");
	}


	public function del_module() {
		$res = "+";
		$target = $_REQUEST['target'];

		$module = cmsController::getInstance()->getModule($target);
		if($module->uninstall()) {
			$this->redirect($this->pre_lang . "/admin/config/modules/");
		} else {
			return $res;
		}
	}


	public function lang_phrases() {
		$this->sheets_reset();

		$lgs = cmsController::getInstance()->langs;

		$res = "";
		$l = strlen("core_");
		foreach($lgs as $ln => $lv) {
			if(substr($ln, 0, $l) != "core_")
				continue;

			$res .= "\t\t<" . $ln . "><![CDATA[" . $lv . "]]></" . $ln . ">\r\n";
		}

		return $res;
	}

	public function lang_list() {
		$res = " ";
		$this->sheets_reset();


		$langs = langsCollection::getInstance()->getList();
		foreach($langs as $lang) {
			$lang_prefix = $lang->getPrefix();
			$lang_title = $lang->getTitle();

			$is_current = ($lang_prefix == cmsController::getInstance()->getCurrentLang()->getPrefix()) ? " active=\"yes\"" : "";

			$link = "/{$lang_prefix}/admin/";

			if(cmsController::getInstance()->getCurrentModule()) {
				$link .= cmsController::getInstance()->getCurrentModule() . "/";
			}

			$res .= <<<END
		<lang link="{$link}" prefix="{$lang_prefix}" {$is_current}>{$lang_title}</lang>

END;
		}

		return $res;
	}
};

?>