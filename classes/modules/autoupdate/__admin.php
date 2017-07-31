<?php
	class __autoupdate {
		public function versions () {
			$this->load_forms();
			$params = Array();

			$params['system_version'] = regedit::getInstance()->getVal("//modules/autoupdate/system_version");
			$params['system_build'] = regedit::getInstance()->getVal("//modules/autoupdate/system_build");

			$system_edition = regedit::getInstance()->getVal("//modules/autoupdate/system_edition");
			$params['system_edition'] = "%autoupdate_edition_" . $system_edition . "%";

			$last_updated = regedit::getInstance()->getVal("//modules/autoupdate/last_updated");
			$params['last_updated'] = date("Y-m-d H:i:s", $last_updated);


			return $this->parse_form("versions", $params);
		}
	};
?>
